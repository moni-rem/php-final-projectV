<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Helpers\Utils;
use KHQR\Models\IndividualInfo;

class BakongKhqrService
{
    public function configured(): bool
    {
        return filled(config('services.bakong_khqr.account_id'));
    }

    public function create(float $amount, string $currency, string $externalReference): ?array
    {
        if (filled(config('services.bakong_khqr.static_image'))) {
            $staticImage = ltrim(config('services.bakong_khqr.static_image'), '/');

            if (! is_file(public_path($staticImage))) {
                Log::warning('Bakong KHQR static image was configured but not found.', [
                    'path' => public_path($staticImage),
                ]);

                return null;
            }

            return [
                'provider' => config('services.bakong_khqr.provider'),
                'qr_image_url' => asset($staticImage),
                'qr_string' => null,
                'md5' => null,
                'transaction_id' => null,
                'external_reference' => $externalReference,
                'raw' => null,
            ];
        }

        if (! $this->configured() || $amount <= 0) {
            return null;
        }

        try {
            $response = BakongKHQR::generateIndividual(new IndividualInfo(
                bakongAccountID: config('services.bakong_khqr.account_id'),
                merchantName: config('services.bakong_khqr.merchant_name'),
                merchantCity: config('services.bakong_khqr.merchant_city'),
                currency: strtoupper($currency) === 'KHR' ? KHQRData::CURRENCY_KHR : KHQRData::CURRENCY_USD,
            ));

            $qrString = $this->withAmount(
                $this->removeTimestampTag(data_get($response->data, 'qr')),
                $amount,
                $currency,
            );
            $md5 = md5($qrString);

            return [
                'provider' => config('services.bakong_khqr.provider'),
                'qr_image_url' => $this->qrImageUrl($qrString),
                'qr_string' => $qrString,
                'md5' => $md5,
                'transaction_id' => null,
                'external_reference' => $externalReference,
                'raw' => [
                    'status' => $response->status,
                    'data' => $response->data,
                ],
            ];
        } catch (\Throwable $exception) {
            Log::warning('Bakong KHQR generation failed.', [
                'message' => $exception->getMessage(),
                'reference' => $externalReference,
            ]);

            return null;
        }
    }

    public function checkByMd5(?string $md5): array
    {
        if (blank(config('services.bakong_khqr.token')) || blank($md5)) {
            return ['paid' => false, 'raw' => null];
        }

        try {
            $response = (new BakongKHQR(config('services.bakong_khqr.token')))
                ->checkTransactionByMD5($md5, (bool) config('services.bakong_khqr.test_mode'));

            return [
                'paid' => $this->looksPaid($response),
                'raw' => $response,
            ];
        } catch (\Throwable $exception) {
            Log::warning('Bakong KHQR status check failed.', [
                'message' => $exception->getMessage(),
                'md5' => $md5,
            ]);

            return ['paid' => false, 'raw' => null];
        }
    }

    public function externalReference(string $prefix = 'EVT'): string
    {
        $cleanPrefix = Str::upper(preg_replace('/[^A-Z0-9]/i', '', $prefix) ?: 'EVT');

        return Str::limit($cleanPrefix, 6, '')
            . now()->format('His')
            . Str::upper(Str::random(6));
    }

    protected function client()
    {
        $client = Http::acceptJson()->timeout(15);

        if (filled(config('services.bakong_khqr.token'))) {
            $client = $client->withToken(config('services.bakong_khqr.token'));
        }

        return $client;
    }

    protected function url(string $path): string
    {
        return rtrim(config('services.bakong_khqr.base_url'), '/') . $path;
    }

    protected function qrImageUrl(?string $qrString): ?string
    {
        if (blank($qrString)) {
            return null;
        }

        return 'https://api.qrserver.com/v1/create-qr-code/?size=420x420&margin=20&ecc=H&data=' . rawurlencode($qrString);
    }

    protected function removeTimestampTag(?string $qrString): ?string
    {
        if (blank($qrString)) {
            return null;
        }

        $withoutCrc = '';
        $remaining = $qrString;

        while (strlen($remaining) >= 4) {
            $tag = substr($remaining, 0, 2);
            $length = (int) substr($remaining, 2, 2);
            $segmentLength = 4 + $length;
            $segment = substr($remaining, 0, $segmentLength);

            if ($tag === '63') {
                break;
            }

            if ($tag !== '99') {
                $withoutCrc .= $segment;
            }

            $remaining = substr($remaining, $segmentLength);
        }

        $payload = $withoutCrc . '6304';

        return $payload . Utils::crc16($payload);
    }

    protected function withAmount(?string $qrString, float $amount, string $currency): ?string
    {
        if (blank($qrString)) {
            return null;
        }

        $amountValue = strtoupper($currency) === 'KHR'
            ? (string) round($amount)
            : rtrim(rtrim(number_format($amount, 2, '.', ''), '0'), '.');
        $amountTag = '54' . str_pad((string) strlen($amountValue), 2, '0', STR_PAD_LEFT) . $amountValue;
        $countryTag = '5802KH';
        $withoutCrc = '';
        $inserted = false;
        $remaining = $qrString;

        while (strlen($remaining) >= 4) {
            $tag = substr($remaining, 0, 2);
            $length = (int) substr($remaining, 2, 2);
            $segmentLength = 4 + $length;
            $segment = substr($remaining, 0, $segmentLength);

            if ($tag === '63') {
                break;
            }

            if ($tag === '54') {
                $remaining = substr($remaining, $segmentLength);
                continue;
            }

            if ($segment === $countryTag && ! $inserted) {
                $withoutCrc .= $amountTag;
                $inserted = true;
            }

            $withoutCrc .= $segment;
            $remaining = substr($remaining, $segmentLength);
        }

        if (! $inserted) {
            $withoutCrc .= $amountTag;
        }

        $payload = $withoutCrc . '6304';

        return $payload . Utils::crc16($payload);
    }

    protected function looksPaid(array $response): bool
    {
        $statusValues = collect([
            data_get($response, 'paid'),
            data_get($response, 'is_paid'),
            data_get($response, 'success'),
            data_get($response, 'responseCode'),
            data_get($response, 'status'),
            data_get($response, 'payment_status'),
            data_get($response, 'data.paid'),
            data_get($response, 'data.status'),
            data_get($response, 'data.payment_status'),
        ])->filter(fn ($value) => $value !== null);

        if ($statusValues->contains(true)) {
            return true;
        }

        if ($statusValues->contains(0) || $statusValues->contains('0')) {
            return true;
        }

        return $statusValues
            ->map(fn ($value) => Str::lower((string) $value))
            ->contains(fn (string $value) => in_array($value, ['paid', 'success', 'successful', 'completed', 'confirmed'], true));
    }
}
