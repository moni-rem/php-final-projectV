<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\BakongKhqrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BakongKhqrController extends Controller
{
    public function __construct(
        protected BakongKhqrService $khqr,
    ) {
    }

    public function generate(Request $request, string $event): JsonResponse
    {
        $eventModel = Event::where('slug', $event)->firstOrFail();

        $validated = $request->validate([
            'ticket_type' => ['required', 'string', 'max:40'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
            'currency' => ['required', 'string', 'in:USD,KHR'],
        ]);

        [, $totalPrice] = $this->calculateTicketAmount(
            $eventModel,
            $validated['ticket_type'],
            (int) $validated['quantity'],
            $validated['currency'],
        );

        $externalReference = $this->khqr->externalReference('EVT-' . $eventModel->id);
        $payment = $this->khqr->create((float) $totalPrice, $validated['currency'], $externalReference);

        if (! $payment) {
            return response()->json([
                'configured' => false,
                'message' => 'KHQR gateway is not configured yet. You can still submit payment reference/proof for manual review.',
                'amount' => $totalPrice,
                'formatted_amount' => $this->formatAmount($totalPrice, $validated['currency']),
            ]);
        }

        return response()->json([
            'configured' => true,
            'amount' => $totalPrice,
            'formatted_amount' => $this->formatAmount($totalPrice, $validated['currency']),
            'external_reference' => $payment['external_reference'],
            'transaction_id' => $payment['transaction_id'],
            'md5' => $payment['md5'],
            'qr_image_url' => $payment['qr_image_url'],
            'qr_string' => $payment['qr_string'],
        ]);
    }

    protected function calculateTicketAmount(Event $event, string $ticketType, int $quantity, string $currency): array
    {
        $unitPriceUsd = (float) $event->ticket_price;

        if (strcasecmp($ticketType, 'VIP') === 0) {
            $unitPriceUsd *= 1.5;
        } elseif (strcasecmp($ticketType, 'Group') === 0) {
            $unitPriceUsd *= 0.9;
        }

        if ($currency === 'KHR') {
            $unitPrice = round($unitPriceUsd * 4100, 2);
            $totalPrice = $unitPrice * $quantity;
        } else {
            $unitPrice = round($unitPriceUsd, 2);
            $totalPrice = $unitPrice * $quantity;
        }

        return [$unitPrice, $totalPrice];
    }

    protected function formatAmount(float $amount, string $currency): string
    {
        return $currency === 'KHR'
            ? number_format($amount, 0) . ' KHR'
            : '$' . number_format($amount, 2);
    }
}
