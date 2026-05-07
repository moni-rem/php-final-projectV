<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book {{ $event['title'] }} | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8">
            <a href="{{ url('/') }}" class="text-lg font-black tracking-wide text-slate-950">Refined Travel</a>
            <div class="flex items-center gap-4 text-sm font-bold">
                <a href="{{ route('events.show', $slug) }}" class="text-slate-600 hover:text-slate-950">Event detail</a>
                <a href="{{ route('bookings.history') }}" class="text-slate-600 hover:text-slate-950">Booking history</a>
                <a href="{{ route('login') }}" class="rounded-md border border-slate-300 px-4 py-2 text-slate-800 hover:bg-slate-100">Login</a>
            </div>
        </nav>
    </header>

    <main class="mx-auto max-w-7xl px-5 py-10 sm:px-8">
        <div class="mb-8">
            <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-700">Booking</p>
            <h1 class="mt-3 text-4xl font-black leading-tight text-slate-950 sm:text-5xl">{{ $event['title'] }}</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                Reserve your spot, pay by KHQR, and keep the booking in your history.
            </p>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                <h2 class="text-2xl font-black text-slate-950">Attendee Information</h2>

                <form method="POST" action="{{ route('events.booking.store', $slug) }}" enctype="multipart/form-data" class="mt-8 space-y-6">
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold leading-6 text-red-700">
                            Please check the form and fill in all required booking details.
                        </div>
                    @endif

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="first_name" class="block text-sm font-bold text-slate-800">First name</label>
                            <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required placeholder="First name" class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-bold text-slate-800">Last name</label>
                            <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required placeholder="Last name" class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-800">Email address</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required placeholder="you@example.com" class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-bold text-slate-800">Phone number</label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required placeholder="+855 12 345 678" class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        </div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="ticket_type" class="block text-sm font-bold text-slate-800">Ticket type</label>
                            <select id="ticket_type" name="ticket_type" class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                <option @selected(old('ticket_type') === 'Standard')>Standard</option>
                                <option @selected(old('ticket_type') === 'VIP')>VIP</option>
                                <option @selected(old('ticket_type') === 'Group')>Group</option>
                            </select>
                        </div>
                        <div>
                            <label for="quantity" class="block text-sm font-bold text-slate-800">Quantity</label>
                            <input id="quantity" name="quantity" type="number" min="1" max="10" value="{{ old('quantity', 1) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-bold text-slate-800">Special requests</label>
                        <textarea id="notes" name="notes" rows="4" placeholder="Accessibility needs, group details, or notes for staff" class="mt-2 w-full rounded-md border border-slate-300 bg-white px-4 py-3 text-sm font-semibold outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('notes') }}</textarea>
                    </div>

                    <section class="rounded-lg border border-slate-200 bg-stone-50 p-5">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start">
                            <div class="flex-1">
                                <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">Payment Method</p>
                                <h3 class="mt-2 text-2xl font-black text-slate-950">Pay by KHQR</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600">
                                    Scan the KHQR code with your banking app, complete the payment, then enter the transaction reference below.
                                </p>

                                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="payment_reference" class="block text-sm font-bold text-slate-800">Transaction reference</label>
                                        <input id="payment_reference" name="payment_reference" type="text" value="{{ old('payment_reference') }}" required placeholder="KHQR transaction ID" class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                    </div>
                                    <div>
                                        <label for="payment_proof" class="block text-sm font-bold text-slate-800">Payment proof</label>
                                        <input id="payment_proof" name="payment_proof" type="file" accept="image/*,.pdf" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-emerald-700 file:px-4 file:py-2 file:text-sm file:font-black file:text-white hover:file:bg-emerald-800">
                                    </div>
                                </div>
                            </div>

                            <div class="w-full max-w-[240px] shrink-0 self-center rounded-lg bg-white p-4 text-center shadow-sm ring-1 ring-slate-200">
                                <img src="{{ asset('images/khqr-placeholder.svg') }}" alt="KHQR payment code" class="mx-auto h-48 w-48 object-contain">
                                <p class="mt-3 text-xs font-black uppercase tracking-[0.16em] text-slate-400">Scan to pay</p>
                                <p class="mt-1 text-sm font-black text-slate-950">{{ $event['price'] }}</p>
                            </div>
                        </div>
                    </section>

                    <label class="flex items-start gap-3 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold leading-6 text-slate-700">
                        <input type="checkbox" name="terms" required class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600">
                        I understand booked tickets are non-refundable and cannot be returned or canceled. Event information may be sent by email.
                    </label>

                    <button type="submit" class="min-h-12 w-full rounded-md bg-emerald-700 px-5 text-sm font-black text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                        Confirm booking
                    </button>
                </form>
            </section>

            <aside class="space-y-6">
                <section class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                    <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}" class="h-52 w-full object-cover">
                    <div class="p-6">
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">{{ $event['category'] }}</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">{{ $event['title'] }}</h2>
                        <dl class="mt-5 space-y-4 text-sm">
                            <div>
                                <dt class="font-black uppercase tracking-[0.16em] text-slate-400">Location</dt>
                                <dd class="mt-1 font-bold text-slate-800">{{ $event['location'] }}</dd>
                            </div>
                            <div>
                                <dt class="font-black uppercase tracking-[0.16em] text-slate-400">Date</dt>
                                <dd class="mt-1 font-bold text-slate-800">{{ $event['date'] }}</dd>
                            </div>
                            @if (! empty($event['startTime']) && ! empty($event['endTime']))
                                <div>
                                    <dt class="font-black uppercase tracking-[0.16em] text-slate-400">Time</dt>
                                    <dd class="mt-1 font-bold text-slate-800">{{ $event['startTime'] }} - {{ $event['endTime'] }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="font-black uppercase tracking-[0.16em] text-slate-400">Price</dt>
                                <dd class="mt-1 font-bold text-emerald-700">{{ $event['price'] }}</dd>
                            </div>
                        </dl>
                    </div>
                </section>

                @if (! empty($event['importantInformation']))
                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <h2 class="text-xl font-black text-slate-950">Important Information</h2>
                        <div class="mt-4 space-y-3">
                            @foreach ($event['importantInformation'] as $information)
                                <div class="flex items-start gap-3 rounded-md border border-amber-200 bg-amber-50 px-4 py-3">
                                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-amber-200 text-sm font-black text-amber-900">!</span>
                                    <span class="text-sm font-bold leading-6 text-slate-700">{{ $information }}</span>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </aside>
        </div>
    </main>
</body>
</html>
