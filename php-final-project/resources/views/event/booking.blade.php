<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book {{ $event['title'] }} | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#f3efef] text-slate-950 antialiased">

@php
    $khqrImagePath = $event['qr_code_image'] ?? $event['khqrImage'] ?? 'build/image.png';
    $khqrImageUrl = preg_match('/^https?:\/\//', $khqrImagePath)
        ? $khqrImagePath
        : asset($khqrImagePath);
@endphp

<header class="border-b border-slate-200 bg-white">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8">
        <a href="{{ url('/') }}" class="text-lg font-black tracking-wide text-slate-950">
            Refined Events
        </a>

        <div class="flex items-center gap-4 text-sm font-bold">
            <a href="{{ route('events.show', $slug) }}"
               class="text-slate-600 hover:text-slate-950">
                Event detail
            </a>

            <a href="{{ route('bookings.history') }}"
               class="text-slate-600 hover:text-slate-950">
                Booking history
            </a>

            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="rounded-md border border-slate-300 px-4 py-2 text-slate-800 hover:bg-slate-100 h-14">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   class="rounded-md border border-slate-300 px-4 py-2 text-slate-800 hover:bg-slate-100">
                    Login
                </a>
            @endauth
        </div>
    </nav>
</header>

<main class="mx-auto max-w-7xl px-4 py-8">

    <!-- TITLE -->
    <div class="mb-8 text-center">
        <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-700">
            Booking
        </p>

        <h1 class="mt-3 text-4xl font-black text-slate-950">
            {{ $event['title'] }}
        </h1>
    </div>

    <!-- MAIN SIDE BY SIDE -->
    <div class="flex flex-col lg:flex-row gap-8 items-start justify-center">

        <!-- LEFT CARD -->
        <section class="w-full lg:w-[462px] rounded-2xl bg-white p-8 shadow-md">

            <h2 class="text-2xl font-black text-slate-950 mb-6">
                Confirm Your Booking
            </h2>

            <form method="POST"
                  action="{{ route('events.booking.store', $slug) }}"
                  enctype="multipart/form-data"
                  data-khqr-url="{{ route('events.booking.khqr', $slug) }}"
                  class="space-y-6">

                @csrf

                <input type="hidden" name="currency" value="KHR">
                <input type="hidden" name="khqr_md5" data-khqr-md5>
                <input type="hidden" name="khqr_transaction_id" data-khqr-transaction-id>
                <input type="hidden" name="khqr_external_reference" data-khqr-external-reference>
                <input type="hidden" name="khqr_qr_string" data-khqr-string>
                <input type="hidden" name="khqr_qr_image_url" data-khqr-image-url>

                @if ($errors->any())
                    <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                        Please check the form and fill in all required booking details.
                    </div>
                @endif

                <!-- NAME -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <div>
                        <label class="block text-sm font-bold text-slate-800">
                            First name
                        </label>

                        <input
                            id="first_name"
                            name="first_name"
                            type="text"
                            value="{{ old('first_name') }}"
                            required
                            placeholder="First name"
                            class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800">
                            Last name
                        </label>

                        <input
                            id="last_name"
                            name="last_name"
                            type="text"
                            value="{{ old('last_name') }}"
                            required
                            placeholder="Last name"
                            class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    </div>
                </div>

                <!-- EMAIL PHONE -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <div>
                        <label class="block text-sm font-bold text-slate-800">
                            Email
                        </label>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="you@example.com"
                            class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800">
                            Phone
                        </label>

                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            value="{{ old('phone') }}"
                            required
                            placeholder="+855 12 345 678"
                            class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    </div>
                </div>

                <!-- TICKET -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <div>
                        <label class="block text-sm font-bold text-slate-800">
                            Ticket Type
                        </label>

                        <select
                            id="ticket_type"
                            name="ticket_type"
                            class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">

                            <option @selected(old('ticket_type') === 'Standard')>
                                Standard
                            </option>

                            <option @selected(old('ticket_type') === 'VIP')>
                                VIP
                            </option>

                            <option @selected(old('ticket_type') === 'Group')>
                                Group
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800">
                            Quantity
                        </label>

                        <input
                            id="quantity"
                            name="quantity"
                            type="number"
                            min="1"
                            max="10"
                            value="{{ old('quantity', 1) }}"
                            required
                            class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    </div>
                </div>

                <!-- NOTES -->
                <div>
                    <label class="block text-sm font-bold text-slate-800">
                        Special requests
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        placeholder="Accessibility needs..."
                        class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('notes') }}</textarea>
                </div>

                <!-- PAYMENT -->
                <div
                    class="rounded-2xl border border-slate-200 bg-stone-50 p-6"
                    data-payment-summary
                    data-base-price="{{ $event['ticketPrice'] ?? 0 }}">

                    <div class="flex flex-col md:flex-row items-center gap-6">

                        <!-- LEFT -->
                        <div class="flex-1">

                            <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">
                                Payment Method
                            </p>

                            <h3 class="mt-2 text-2xl font-black text-slate-950">
                                Pay by KHQR
                            </h3>

                            <p class="mt-3 text-sm leading-6 text-slate-600">
                                Scan the generated KHQR code for this ticket amount. If the payment cannot be verified automatically, enter your transaction reference for admin review.
                            </p>

                            <p class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold leading-5 text-amber-800"
                               data-khqr-status>
                                Preparing KHQR payment code...
                            </p>

                            <div class="mt-5 grid grid-cols-1 gap-4">

                                <div>
                                    <label class="block text-sm font-bold text-slate-800">
                                        Transaction Reference
                                    </label>

                                    <input
                                        id="payment_reference"
                                        name="payment_reference"
                                        type="text"
                                        placeholder="KHQR Transaction ID"
                                        class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-800">
                                        Payment Proof
                                    </label>

                                    <input
                                        id="payment_proof"
                                        name="payment_proof"
                                        type="file"
                                        accept="image/*,.pdf"
                                        class="mt-2 block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm font-semibold">
                                </div>
                            </div>
                        </div>

                        <!-- QR -->
                        <div class="w-[240px] rounded-2xl bg-white p-4 text-center shadow-sm border border-slate-200">

                            <button type="button"
                                    data-khqr-open
                                    class="w-full">

                                <img
                                    src="{{ $khqrImageUrl }}"
                                    alt="KHQR payment code"
                                    data-khqr-image
                                    class="mx-auto h-48 w-48 object-contain">
                            </button>

                            <p class="mt-3 text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                                Scan to pay
                            </p>

                            <p class="mt-1 text-lg font-black text-slate-950"
                               data-payment-amount>
                                {{ $event['price'] }}
                            </p>

                            <button
                                type="button"
                                data-khqr-open
                                class="mt-4 h-11 w-full rounded-lg bg-slate-950 text-sm font-black text-white hover:bg-slate-800">
                                Open KHQR
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TERMS -->
                <label class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-slate-700">

                    <input
                        type="checkbox"
                        name="terms"
                        required
                        class="mt-1 h-4 w-4">

                    <span>
                        I understand booked tickets are non-refundable.
                    </span>
                </label>

                <!-- BUTTON -->
                <button
                    type="submit"
                    class="flex min-h-16 w-full items-center justify-center rounded-lg bg-emerald-700 px-5 text-base font-black text-white hover:bg-emerald-800">

                    Confirm Booking
                </button>
            </form>
        </section>

        <!-- RIGHT CARD -->
        <aside class="w-full space-y-6 lg:w-[420px]">

            <section class="overflow-hidden rounded-2xl bg-white shadow-md">

                <img
                    src="{{ $event['image'] }}"
                    alt="{{ $event['title'] }}"
                    class="h-64 w-full object-cover">

                <div class="p-6">

                    <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">
                        {{ $event['category'] }}
                    </p>

                    <h2 class="mt-2 text-2xl font-black text-slate-950">
                        {{ $event['title'] }}
                    </h2>

                    <div class="mt-6 space-y-4">

                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                                Location
                            </p>

                            <p class="mt-1 font-bold text-slate-800">
                                {{ $event['location'] }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                                Date
                            </p>

                            <p class="mt-1 font-bold text-slate-800">
                                {{ $event['date'] }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                                Price
                            </p>

                            <p class="mt-1 font-bold text-emerald-700">
                                {{ $event['price'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            @if (! empty($event['importantInformation']))
                <section class="rounded-2xl bg-white p-6 shadow-md">
                    <h2 class="text-xl font-black text-slate-950">
                        Important Information
                    </h2>

                    <div class="mt-4 space-y-3">
                        @foreach ($event['importantInformation'] as $information)
                            <div class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-200 text-sm font-black text-amber-900">
                                    !
                                </span>

                                <span class="text-sm font-bold leading-6 text-slate-700">
                                    {{ $information }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

        </aside>
    </div>
</main>

@include('partials.footer')

<div class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 px-4 py-8"
     data-khqr-modal>
    <div class="w-full max-w-sm rounded-2xl bg-white p-5 text-center shadow-2xl">
        <div class="flex items-center justify-between gap-4">
            <p class="text-sm font-black uppercase tracking-[0.18em] text-emerald-700">Scan KHQR</p>
            <button type="button"
                    class="rounded-md border border-slate-300 px-3 py-2 text-sm font-black text-slate-700"
                    data-khqr-close>
                Close
            </button>
        </div>

        <img src="{{ $khqrImageUrl }}"
             alt="Large KHQR payment code"
             data-khqr-modal-image
             class="mx-auto mt-5 h-80 w-80 rounded-xl border border-slate-200 bg-white object-contain p-3">

        <p class="mt-4 text-sm font-bold leading-6 text-slate-600">
            Increase your phone brightness and scan this larger code from your banking app.
        </p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form[data-khqr-url]');

        if (! form) {
            return;
        }

        const ticketType = form.querySelector('[name="ticket_type"]');
        const quantity = form.querySelector('[name="quantity"]');
        const currency = form.querySelector('[name="currency"]');
        const csrf = form.querySelector('[name="_token"]');
        const image = form.querySelector('[data-khqr-image]');
        const modal = document.querySelector('[data-khqr-modal]');
        const modalImage = document.querySelector('[data-khqr-modal-image]');
        const amount = form.querySelector('[data-payment-amount]');
        const status = form.querySelector('[data-khqr-status]');
        const hidden = {
            md5: form.querySelector('[data-khqr-md5]'),
            transactionId: form.querySelector('[data-khqr-transaction-id]'),
            externalReference: form.querySelector('[data-khqr-external-reference]'),
            qrString: form.querySelector('[data-khqr-string]'),
            imageUrl: form.querySelector('[data-khqr-image-url]'),
        };
        const fallbackImage = image?.getAttribute('src') || '';
        let timer;

        const clearKhqr = () => {
            Object.values(hidden).forEach((input) => {
                if (input) {
                    input.value = '';
                }
            });
        };

        const loadKhqr = async () => {
            clearKhqr();

            if (status) {
                status.textContent = 'Generating KHQR payment code...';
            }

            try {
                const response = await fetch(form.dataset.khqrUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf?.value || '',
                    },
                    body: JSON.stringify({
                        ticket_type: ticketType?.value || 'Standard',
                        quantity: quantity?.value || 1,
                        currency: currency?.value || 'USD',
                    }),
                });

                const data = await response.json();

                if (amount && data.formatted_amount) {
                    amount.textContent = data.formatted_amount;
                }

                if (! data.configured) {
                    if (image) {
                        image.src = fallbackImage;
                    }

                    if (status) {
                        status.textContent = data.message || 'KHQR gateway is not configured yet.';
                    }

                    return;
                }

                hidden.md5.value = data.md5 || '';
                hidden.transactionId.value = data.transaction_id || '';
                hidden.externalReference.value = data.external_reference || '';
                hidden.qrString.value = data.qr_string || '';
                hidden.imageUrl.value = data.qr_image_url || '';

                if (image && data.qr_image_url) {
                    image.src = data.qr_image_url;
                }

                if (modalImage && data.qr_image_url) {
                    modalImage.src = data.qr_image_url;
                }

                if (status) {
                    status.textContent = 'KHQR code generated. Scan it, pay the exact amount, then confirm your booking.';
                }
            } catch (error) {
                if (image) {
                    image.src = fallbackImage;
                }

                if (status) {
                    status.textContent = 'Could not generate KHQR automatically. Enter your transaction reference after paying.';
                }
            }
        };

        const scheduleKhqrLoad = () => {
            window.clearTimeout(timer);
            timer = window.setTimeout(loadKhqr, 250);
        };

        ticketType?.addEventListener('change', scheduleKhqrLoad);
        quantity?.addEventListener('input', scheduleKhqrLoad);

        document.querySelectorAll('[data-khqr-open]').forEach((button) => {
            button.addEventListener('click', () => {
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            });
        });

        document.querySelector('[data-khqr-close]')?.addEventListener('click', () => {
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });

        modal?.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });

        loadKhqr();
    });
</script>

</body>
</html>
