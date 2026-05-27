<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking History | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8">
            <a href="{{ url('/') }}" class="text-lg font-black tracking-wide text-slate-950">Refined Travel</a>
            <div class="flex items-center gap-4 text-sm font-bold">
                <a href="{{ url('/') }}#stays" class="text-slate-600 hover:text-slate-950">Events</a>
                <a href="{{ route('bookings.history') }}" class="text-slate-600 hover:text-slate-950">Booking history</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md border border-slate-300 px-4 py-2 text-slate-800 hover:bg-slate-100">Logout</button>
                </form>
            </div>
        </nav>
    </header>

    <main class="mx-auto max-w-7xl px-5 py-10 sm:px-8">
        <div class="mb-8">
            <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-700">Account</p>
            <h1 class="mt-3 text-4xl font-black leading-tight text-slate-950 sm:text-5xl">Booking History</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                Review submitted bookings, KHQR payment references, and ticket details.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold leading-6 text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (empty($bookings))
            <section class="rounded-lg bg-white p-8 text-center shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-black uppercase tracking-[0.22em] text-slate-400">No bookings yet</p>
                <h2 class="mt-3 text-2xl font-black text-slate-950">Your booking history is empty</h2>
                <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-600">
                    Choose an event, pay by KHQR, and confirm your booking to see it here.
                </p>
                <a href="{{ url('/') }}#stays" class="mt-6 inline-flex min-h-12 items-center justify-center rounded-md bg-emerald-700 px-6 text-sm font-black text-white hover:bg-emerald-800">
                    Browse events
                </a>
            </section>
        @else
            <div class="space-y-5">
                @foreach ($bookings as $booking)
                    <article class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                        <div class="grid gap-0 lg:grid-cols-[260px_1fr]">
                            <img src="{{ $booking['event_image'] }}" alt="{{ $booking['event_title'] }}" class="h-56 w-full object-cover lg:h-full">
                            <div class="p-6">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-700">{{ $booking['code'] }}</p>
                                        <h2 class="mt-2 text-2xl font-black text-slate-950">{{ $booking['event_title'] }}</h2>
                                        <p class="mt-2 text-sm font-bold text-slate-600">
                                            {{ $booking['event_location'] }} / {{ $booking['event_date'] }}
                                            @if (! empty($booking['event_time']))
                                                / {{ $booking['event_time'] }}
                                            @endif
                                        </p>
                                    </div>
                                    <span class="inline-flex w-fit rounded-md bg-amber-100 px-3 py-2 text-xs font-black uppercase tracking-[0.16em] text-amber-800">
                                        {{ $booking['status'] }}
                                    </span>
                                </div>

                                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    <div class="rounded-md bg-stone-50 p-4">
                                        <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Attendee</p>
                                        <p class="mt-1 text-sm font-bold text-slate-800">{{ $booking['first_name'] }} {{ $booking['last_name'] }}</p>
                                    </div>
                                    <div class="rounded-md bg-stone-50 p-4">
                                        <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Ticket</p>
                                        <p class="mt-1 text-sm font-bold text-slate-800">{{ $booking['quantity'] }} x {{ $booking['ticket_type'] }}</p>
                                    </div>
                                    <div class="rounded-md bg-stone-50 p-4">
                                        <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">KHQR Ref</p>
                                        <p class="mt-1 break-words text-sm font-bold text-slate-800">{{ $booking['payment_reference'] }}</p>
                                        <p class="mt-1 text-xs font-black uppercase tracking-[0.12em] text-slate-400">{{ $booking['payment_status'] }}</p>
                                    </div>
                                    <div class="rounded-md bg-stone-50 p-4">
                                        <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Booked At</p>
                                        <p class="mt-1 text-sm font-bold text-slate-800">{{ $booking['booked_at'] }}</p>
                                    </div>
                                </div>

                                <div class="mt-5 rounded-md border border-slate-200 bg-white p-4">
                                    <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Tickets</p>
                                    @if (! empty($booking['ticket_codes']))
                                        <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                            @foreach ($booking['ticket_codes'] as $ticketCode)
                                                <span class="rounded-md bg-emerald-50 px-3 py-2 text-sm font-black text-emerald-800">
                                                    {{ $ticketCode }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="mt-2 text-sm font-bold text-amber-700">
                                            Tickets will appear here after admin verifies your KHQR payment.
                                        </p>
                                    @endif
                                </div>

                                <div class="mt-5 flex flex-col gap-3 border-t border-slate-200 pt-5 text-sm font-bold text-slate-600 sm:flex-row sm:items-center sm:justify-between">
                                    <p>Tickets are non-refundable and cannot be returned or canceled.</p>
                                    <a href="{{ route('events.show', $booking['event_slug']) }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-slate-300 px-4 text-slate-800 hover:bg-slate-100">
                                        View event
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </main>

    @include('partials.footer')
</body>
</html>
