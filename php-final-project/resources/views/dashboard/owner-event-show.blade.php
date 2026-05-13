<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }} Bookings | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    <main class="mx-auto max-w-7xl px-5 py-10 sm:px-8">
        <nav class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('owner.dashboard') }}" class="text-lg font-black tracking-wide">Refined Travel</a>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('owner.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-800 hover:bg-white">Back to dashboard</a>
                <a href="{{ route('events.show', $event->slug) }}" class="inline-flex min-h-11 items-center justify-center rounded-md bg-amber-400 px-4 text-sm font-black text-slate-950 hover:bg-amber-300">View display</a>
            </div>
        </nav>

        @php
            $bookedTickets = (int) ($event->booked_tickets ?? 0);
            $seatsLeft = max((int) $event->total_seats - $bookedTickets, 0);
            $soldPercent = $event->total_seats > 0 ? min(100, round(($bookedTickets / $event->total_seats) * 100)) : 0;
        @endphp

        <section class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <div class="grid gap-0 lg:grid-cols-[320px_1fr]">
                <img src="{{ $event->image }}" alt="{{ $event->title }}" class="h-64 w-full object-cover lg:h-full">
                <div class="p-6 sm:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-amber-700">{{ $event->category?->category_name ?? 'Event' }}</p>
                    <h1 class="mt-3 text-4xl font-black leading-tight sm:text-5xl">{{ $event->title }}</h1>
                    <div class="mt-4 flex flex-wrap gap-3 text-sm font-bold text-slate-600">
                        <span>{{ $event->location }}</span>
                        <span aria-hidden="true">/</span>
                        <span>{{ $event->event_date?->format('M d, Y') }}</span>
                        <span aria-hidden="true">/</span>
                        <span>
                            {{ $event->start_time ? date('g:ia', strtotime($event->start_time)) : 'No time' }}
                            @if ($event->end_time)
                                - {{ date('g:ia', strtotime($event->end_time)) }}
                            @endif
                        </span>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-lg bg-stone-50 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Bookings</p>
                            <p class="mt-2 text-2xl font-black">{{ $event->bookings_count }}</p>
                        </div>
                        <div class="rounded-lg bg-stone-50 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Tickets</p>
                            <p class="mt-2 text-2xl font-black">{{ number_format($bookedTickets) }}</p>
                        </div>
                        <div class="rounded-lg bg-stone-50 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Seats Left</p>
                            <p class="mt-2 text-2xl font-black">{{ number_format($seatsLeft) }}</p>
                        </div>
                        <div class="rounded-lg bg-stone-50 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Sales</p>
                            <p class="mt-2 text-2xl font-black">${{ number_format((float) ($event->gross_sales ?? 0), 2) }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="flex items-center justify-between text-xs font-black uppercase tracking-[0.16em] text-slate-500">
                            <span>Capacity booked</span>
                            <span>{{ $soldPercent }}%</span>
                        </div>
                        <div class="mt-2 h-3 rounded-full bg-slate-100">
                            <div class="h-3 rounded-full bg-emerald-700" style="width: {{ $soldPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-8 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-xl font-black">People who booked this event</h2>
                <p class="mt-1 text-sm font-semibold text-slate-500">Every booking row includes attendee contact, ticket count, payment reference, and status.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-stone-100 text-xs font-black uppercase tracking-[0.16em] text-slate-500">
                        <tr>
                            <th scope="col" class="px-5 py-4">Booking</th>
                            <th scope="col" class="px-5 py-4">Attendee</th>
                            <th scope="col" class="px-5 py-4">Tickets</th>
                            <th scope="col" class="px-5 py-4">Total</th>
                            <th scope="col" class="px-5 py-4">Payment</th>
                            <th scope="col" class="px-5 py-4">Status</th>
                            <th scope="col" class="px-5 py-4">Booked At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($bookings as $booking)
                            <tr class="align-middle hover:bg-stone-50">
                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="font-black text-slate-950">{{ $booking->booking_code }}</p>
                                </td>
                                <td class="min-w-[220px] px-5 py-4">
                                    <p class="font-black text-slate-950">{{ $booking->user?->full_name ?: $booking->user?->name ?: 'Guest User' }}</p>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ $booking->user?->email }}</p>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ $booking->user?->phone }}</p>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 font-black text-slate-950">
                                    {{ $booking->quantity }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 font-black text-slate-950">
                                    ${{ number_format((float) $booking->total_price, 2) }}
                                </td>
                                <td class="min-w-[180px] px-5 py-4">
                                    <p class="break-words font-bold text-slate-800">{{ $booking->payment?->transaction_reference ?? 'Pending' }}</p>
                                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ $booking->payment?->status?->status_name ?? 'No payment' }}</p>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-md bg-amber-50 px-3 py-1.5 text-xs font-black uppercase tracking-[0.14em] text-amber-700">
                                        {{ $booking->status?->status_name ?? 'pending' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-600">
                                    {{ $booking->booking_date?->format('M d, Y h:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <p class="text-sm font-black uppercase tracking-[0.2em] text-slate-400">No bookings yet</p>
                                    <h3 class="mt-3 text-2xl font-black text-slate-950">This event has no ticket bookings</h3>
                                    <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-600">When visitors book tickets from the event page, their details will appear in this table.</p>
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
