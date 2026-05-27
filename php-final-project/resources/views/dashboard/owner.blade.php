<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event Owner Dashboard | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    <main class="mx-auto max-w-7xl px-5 py-10 sm:px-8">
        <nav class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ url('/') }}" class="text-lg font-black tracking-wide">Refined Travel</a>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('bookings.history') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-800 hover:bg-white">Booking history</a>
                <a href="{{ route('owner.events.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-md bg-amber-400 px-4 text-sm font-black text-slate-950 hover:bg-amber-300">Add event</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-800 hover:bg-white">Logout</button>
                </form>
            </div>
        </nav>

        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase tracking-[0.24em] text-amber-700">Event Owner Dashboard</p>
                <h1 class="mt-3 text-4xl font-black leading-tight sm:text-5xl">Manage your events</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                    Add event details, review bookings, and see how many tickets people booked for each event.
                </p>
            </div>
            <a href="{{ url('/') }}#stays" class="inline-flex min-h-11 w-fit items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-800 hover:bg-white">
                View website
            </a>
        </div>

        @if (session('success'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @php
            $toRiels = function ($amount, $currency = 'USD') {
                return strtoupper((string) $currency) === 'KHR'
                    ? (float) $amount
                    : (float) $amount * 4100;
            };

            $totalEvents = $events->count();
            $totalBookings = $events->sum('bookings_count');
            $totalTickets = $events->sum(fn ($event) => (int) ($event->booked_tickets ?? 0));
            $totalSales = $events->sum(fn ($event) => $event->bookings->sum(
                fn ($booking) => $toRiels($booking->total_price, $booking->payment?->currency)
            ));
        @endphp

        <section class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Events</p>
                <p class="mt-2 text-3xl font-black">{{ $totalEvents }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Bookings</p>
                <p class="mt-2 text-3xl font-black">{{ $totalBookings }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Tickets Booked</p>
                <p class="mt-2 text-3xl font-black">{{ $totalTickets }}</p>
            </div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Sales</p>
                <p class="mt-2 text-3xl font-black">{{ number_format($totalSales, 0) }} Riels</p>
            </div>
        </section>

        <section class="mt-8 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-black">Your event table</h2>
                    <p class="mt-1 text-sm font-semibold text-slate-500">Track capacity, bookings, and ticket sales in one place.</p>
                </div>
                <a href="{{ route('owner.events.create') }}" class="inline-flex min-h-10 w-fit items-center justify-center rounded-md bg-emerald-700 px-4 text-sm font-black text-white hover:bg-emerald-800">
                    Create event
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-stone-100 text-xs font-black uppercase tracking-[0.16em] text-slate-500">
                        <tr>
                            <th scope="col" class="px-5 py-4">Event</th>
                            <th scope="col" class="px-5 py-4">Date</th>
                            <th scope="col" class="px-5 py-4">Seats</th>
                            <th scope="col" class="px-5 py-4">Booked</th>
                            <th scope="col" class="px-5 py-4">Sales</th>
                            <th scope="col" class="px-5 py-4">Status</th>
                            <th scope="col" class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($events as $event)
                            @php
                                $bookedTickets = (int) ($event->booked_tickets ?? 0);
                                $seatsLeft = max((int) $event->total_seats - $bookedTickets, 0);
                                $soldPercent = $event->total_seats > 0 ? min(100, round(($bookedTickets / $event->total_seats) * 100)) : 0;
                                $eventSales = $event->bookings->sum(
                                    fn ($booking) => $toRiels($booking->total_price, $booking->payment?->currency)
                                );
                            @endphp
                            <tr class="align-middle hover:bg-stone-50">
                                <td class="px-5 py-4">
                                    <div class="flex min-w-[260px] items-center gap-4">
                                        <img src="{{ $event->image }}" alt="{{ $event->title }}" class="h-16 w-20 rounded-md object-cover ring-1 ring-slate-200">
                                        <div>
                                            <p class="font-black text-slate-950">{{ $event->title }}</p>
                                            <p class="mt-1 text-xs font-bold uppercase tracking-[0.14em] text-amber-700">{{ $event->category?->category_name ?? 'Event' }}</p>
                                            <p class="mt-1 max-w-xs truncate text-sm font-semibold text-slate-500">{{ $event->location }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="font-bold text-slate-800">{{ $event->event_date?->format('M d, Y') }}</p>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">
                                        {{ $event->start_time ? date('g:ia', strtotime($event->start_time)) : 'No time' }}
                                        @if ($event->end_time)
                                            - {{ date('g:ia', strtotime($event->end_time)) }}
                                        @endif
                                    </p>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="font-black text-slate-950">{{ number_format($event->total_seats) }}</p>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ number_format($seatsLeft) }} left</p>
                                </td>
                                <td class="min-w-[180px] px-5 py-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="font-black text-slate-950">{{ number_format($bookedTickets) }}</span>
                                        <span class="text-xs font-bold text-slate-500">{{ $soldPercent }}%</span>
                                    </div>
                                    <div class="mt-2 h-2 rounded-full bg-slate-100">
                                        <div class="h-2 rounded-full bg-emerald-700" style="width: {{ $soldPercent }}%"></div>
                                    </div>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ $event->bookings_count }} booking(s)</p>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 font-black text-slate-950">
                                    {{ number_format($eventSales, 0) }} Riels
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-md bg-emerald-50 px-3 py-1.5 text-xs font-black uppercase tracking-[0.14em] text-emerald-700">
                                        {{ $event->status?->status_name ?? 'published' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('owner.events.show', $event->slug) }}" class="inline-flex min-h-10 items-center justify-center rounded-md bg-amber-400 px-4 text-sm font-black text-slate-950 hover:bg-amber-300">Details</a>
                                        <a href="{{ route('events.show', $event->slug) }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-800 hover:bg-white">Display</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <p class="text-sm font-black uppercase tracking-[0.2em] text-slate-400">No events yet</p>
                                    <h3 class="mt-3 text-2xl font-black text-slate-950">Create your first event</h3>
                                    <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-600">After you publish an event, it will appear on the website and booking counts will show here.</p>
                                    <a href="{{ route('owner.events.create') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-md bg-amber-400 px-5 text-sm font-black text-slate-950 hover:bg-amber-300">Add event</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
