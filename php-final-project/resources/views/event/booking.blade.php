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

<!-- HEADER -->
<header class="border-b border-slate-200 bg-white">

    <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8">

        <a href="{{ url('/') }}"
           class="text-lg font-black tracking-wide text-slate-950">
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
                            class="h-14 rounded-md border border-slate-300 px-4 py-2 text-slate-800 hover:bg-slate-100">
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

<!-- MAIN -->
<main class="mx-auto max-w-[1480px] px-4 py-8 sm:px-6 lg:px-8">

    <!-- TITLE -->
    <div class="mb-8 text-center">


        <h1 class="mt-3 mb-10 text-4xl font-black text-slate-950">
            {{ $event['title'] }}
        </h1>

    </div>

    <!-- BOX BOX -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:items-start">

        <!-- LEFT CARD -->
        <section class="h-full w-full rounded-2xl bg-white p-6 shadow-xl shadow-black/5 sm:p-8">

            <h2 class="mb-6 text-2xl font-black text-slate-950">
                Confirm Your Booking
            </h2>

            <form method="POST"
                  action="{{ route('events.booking.store', $slug) }}"
                  enctype="multipart/form-data"
                  class="space-y-6">

                @csrf

                <!-- NAME -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <div>

                        <label class="block text-sm font-bold text-slate-800">
                            First name
                        </label>

                        <input
                            type="text"
                            name="first_name"
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
                            type="text"
                            name="last_name"
                            value="{{ old('last_name') }}"
                            required
                            placeholder="Last name"
                            class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">

                    </div>

                </div>

                <!-- EMAIL PHONE -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <div>

                        <label class="block text-sm font-bold text-slate-800">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
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
                            type="tel"
                            name="phone"
                            value="{{ old('phone') }}"
                            required
                            placeholder="+855 12 345 678"
                            class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">

                    </div>

                </div>

                <!-- TICKET -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <div>

                        <label class="block text-sm font-bold text-slate-800">
                            Ticket Type
                        </label>

                        <select
                            name="ticket_type"
                            class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">

                            <option>Standard</option>
                            <option>VIP</option>
                            <option>Group</option>

                        </select>

                    </div>

                    <div>

                        <label class="block text-sm font-bold text-slate-800">
                            Quantity
                        </label>

                        <input
                            type="number"
                            name="quantity"
                            min="1"
                            max="10"
                            value="1"
                            class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">

                    </div>

                </div>

                <!-- NOTES -->
                <div>

                    <label class="block text-sm font-bold text-slate-800">
                        Special requests
                    </label>

                    <textarea
                        name="notes"
                        rows="4"
                        placeholder="Accessibility needs..."
                        class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"></textarea>

                </div>

                <!-- PAYMENT -->
                <div class="rounded-2xl border border-slate-200 bg-stone-50 p-6">

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
                                Scan the generated KHQR code for this ticket amount using ABA, ACLEDA, Wing, Bakong, or supported banking apps.
                            </p>

                            <!-- TRANSACTION -->
                            <div class="mt-5">

                                <label class="block text-sm font-bold text-slate-800">
                                    Transaction Reference
                                </label>

                                <input
                                    type="text"
                                    name="payment_reference"
                                    placeholder="KHQR Transaction ID"
                                    class="mt-2 h-12 w-full rounded-lg border border-slate-300 px-4 font-semibold focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">

                            </div>

                            <!-- PROOF -->
                            <div class="mt-5">

                                <label class="block text-sm font-bold text-slate-800">
                                    Payment Proof
                                </label>

                                <input
                                    type="file"
                                    name="payment_proof"
                                    accept="image/*,.pdf"
                                    class="mt-2 block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm font-semibold">

                            </div>

                        </div>

                        <!-- QR -->
                        <div class="w-[240px] rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm">

                            <img
                                src="{{ $khqrImageUrl }}"
                                alt="KHQR payment code"
                                class="mx-auto h-48 w-48 object-contain">

                            <p class="mt-3 text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                                Scan to pay
                            </p>

                            <p class="mt-1 text-lg font-black text-slate-950">
                                {{ $event['price'] }}
                            </p>

                            <button
                                type="button"
                                class="mt-4 inline-flex h-11 w-full items-center justify-center rounded-lg bg-slate-950 text-sm font-black text-white hover:bg-slate-800">

                                Scan QR

                            </button>

                        </div>

                    </div>

                </div>

                <!-- TERMS -->
                <label class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-slate-700">

                    <input
                        type="checkbox"
                        required
                        class="mt-1 h-4 w-4">

                    <span>
                        I understand booked tickets are non-refundable.
                    </span>

                </label>

                <!-- BUTTON -->
                <button
    type="submit"
    class="flex min-h-[80px] w-full items-center justify-center rounded-lg bg-emerald-700 px-5 text-base font-black text-white hover:bg-emerald-800">

    Confirm Booking

</button>

            </form>

        </section>

        <!-- RIGHT CARD -->
        <aside class="h-full w-full">

            <section class="h-full overflow-hidden rounded-2xl bg-white shadow-xl shadow-black/5">

                <!-- IMAGE -->
                <img
                    src="{{ $event['image'] }}"
                    alt="{{ $event['title'] }}"
                    class="h-64 w-full object-cover">

                <!-- CONTENT -->
                <div class="p-6">

                    <!-- CATEGORY -->
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">
                        {{ $event['category'] }}
                    </p>

                    <!-- TITLE -->
                    <h2 class="mt-2 text-3xl font-black text-slate-950">
                        {{ $event['title'] }}
                    </h2>

                    <!-- DESCRIPTION -->
                    <p class="mt-4 leading-7 text-slate-600">
                        {{ $event['description'] ?? 'Experience an unforgettable event with premium atmosphere and smooth digital booking.' }}
                    </p>

                    <!-- EVENT DETAILS -->
                    <div class="mt-8 grid gap-4">

                        <!-- LOCATION -->
                        <div class="rounded-xl border border-slate-200 bg-stone-50 p-4">

                            <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                                Location
                            </p>

                            <p class="mt-1 font-bold text-slate-800">
                                {{ $event['location'] }}
                            </p>

                        </div>

                        <!-- DATE -->
                        <div class="rounded-xl border border-slate-200 bg-stone-50 p-4">

                            <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">
                                Date
                            </p>

                            <p class="mt-1 font-bold text-slate-800">
                                {{ $event['date'] }}
                            </p>

                        </div>

                        <!-- PRICE -->
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">

                            <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">
                                Ticket Price
                            </p>

                            <p class="mt-1 text-xl font-black text-emerald-700">
                                {{ $event['price'] }}
                            </p>


                        </div>


                        <!-- IMPORTANT INFORMATION -->
@if (! empty($event['importantInformation']))

    <div class="mt-6">

        <h3 class="text-lg font-black text-slate-950">
            Important Information
        </h3>

        <div class="mt-4 space-y-3">

            @foreach ($event['importantInformation'] as $information)

                <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">

                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-200 text-sm font-black text-amber-900">
                        !
                    </span>

                    <p class="text-sm font-bold leading-6 text-slate-700">
                        {{ $information }}
                    </p>

                </div>

            @endforeach

        </div>

    </div>

@endif
                    </div>

                </div>

            </section>

        </aside>

    </div>

</main>

@include('partials.footer')

</body>
</html>