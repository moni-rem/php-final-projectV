<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-950 antialiased">
    @php
        $bookingCount = count($bookings);
        $eventCount = count($events);
        $pendingBookings = collect($bookings)->filter(fn ($booking) => strtolower($booking['status']) !== 'confirmed')->count();
        $ticketCount = collect($bookings)->sum('quantity');
    @endphp

    <div class="flex min-h-screen">
        <aside class="hidden w-72 border-r border-slate-200 bg-slate-950 px-6 py-8 text-white lg:block">
            <a href="{{ url('/') }}" class="text-xl font-black tracking-wide">Refined Travel</a>
            <p class="mt-2 text-sm font-semibold text-slate-400">Admin Control Center</p>

            <nav class="mt-10 space-y-2 text-sm font-bold">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between rounded-md bg-white px-4 py-3 text-slate-950">
                    Dashboard
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                </a>
                <a href="{{ url('/') }}#stays" class="block rounded-md px-4 py-3 text-slate-300 hover:bg-white/10 hover:text-white">Events</a>
                <a href="{{ route('bookings.history') }}" class="block rounded-md px-4 py-3 text-slate-300 hover:bg-white/10 hover:text-white">Bookings</a>
                <a href="{{ route('login') }}" class="block rounded-md px-4 py-3 text-slate-300 hover:bg-white/10 hover:text-white">Switch Role</a>
            </nav>

            <div class="mt-10 rounded-lg border border-white/10 bg-white/5 p-4">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-amber-300">Ticket Policy</p>
                <p class="mt-2 text-sm font-semibold leading-6 text-slate-300">
                    Booked tickets are non-refundable and cannot be returned or canceled.
                </p>
            </div>
        </aside>

        <main class="flex-1">
            <header class="border-b border-slate-200 bg-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-red-700">Admin Dashboard</p>
                        <h1 class="mt-1 text-2xl font-black text-slate-950 sm:text-3xl">System Overview</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ url('/') }}" class="hidden text-sm font-bold text-slate-600 hover:text-slate-950 sm:inline">View Site</a>
                        <a href="{{ route('login') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-800 hover:bg-slate-100">Switch Role</a>
                    </div>
                </div>
            </header>

            <section class="mx-auto max-w-7xl px-5 py-8 sm:px-8">
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Total Events</p>
                        <div class="mt-4 flex items-end justify-between">
                            <h2 class="text-4xl font-black">{{ $eventCount }}</h2>
                            <span class="rounded-md bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800">Published</span>
                        </div>
                    </section>

                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Total Bookings</p>
                        <div class="mt-4 flex items-end justify-between">
                            <h2 class="text-4xl font-black">{{ $bookingCount }}</h2>
                            <span class="rounded-md bg-amber-100 px-3 py-1 text-xs font-black text-amber-800">{{ $pendingBookings }} review</span>
                        </div>
                    </section>

                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Users</p>
                        <div class="mt-4 flex items-end justify-between">
                            <h2 class="text-4xl font-black">{{ $usersCount ?? 0 }}</h2>
                            <span class="rounded-md bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">All roles</span>
                        </div>
                    </section>

                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Tickets</p>
                        <div class="mt-4 flex items-end justify-between">
                            <h2 class="text-4xl font-black">{{ $ticketCount }}</h2>
                            <span class="rounded-md bg-red-100 px-3 py-1 text-xs font-black text-red-800">No return</span>
                        </div>
                    </section>
                </div>

                <div class="mt-8 grid gap-8 xl:grid-cols-[1.2fr_0.8fr]">
                    <section class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                        <div class="border-b border-slate-200 p-6">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">Event Management</p>
                                    <h2 class="mt-2 text-2xl font-black text-slate-950">Live Event Displays</h2>
                                </div>
                                <a href="{{ url('/') }}#stays" class="inline-flex min-h-10 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-black text-slate-800 hover:bg-slate-100">
                                    View public page
                                </a>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[720px] text-left text-sm">
                                <thead class="bg-slate-50 text-xs font-black uppercase tracking-[0.16em] text-slate-500">
                                    <tr>
                                        <th class="px-6 py-4">Event</th>
                                        <th class="px-6 py-4">Location</th>
                                        <th class="px-6 py-4">Date</th>
                                        <th class="px-6 py-4">Price</th>
                                        <th class="px-6 py-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @foreach ($events as $slug => $event)
                                        <tr class="hover:bg-stone-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-4">
                                                    <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}" class="h-14 w-20 rounded-md object-cover">
                                                    <div>
                                                        <p class="font-black text-slate-950">{{ $event['title'] }}</p>
                                                        <p class="mt-1 text-xs font-bold text-emerald-700">{{ $event['category'] }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 font-bold text-slate-600">{{ $event['location'] }}</td>
                                            <td class="px-6 py-4 font-bold text-slate-600">{{ $event['date'] }}</td>
                                            <td class="px-6 py-4 font-black text-slate-950">{{ $event['price'] }}</td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('events.show', $slug) }}" class="inline-flex min-h-10 items-center justify-center rounded-md bg-slate-950 px-4 text-xs font-black text-white hover:bg-slate-800">
                                                    Open
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-black uppercase tracking-[0.22em] text-amber-700">Payment Review</p>
                                <h2 class="mt-2 text-2xl font-black text-slate-950">Recent Bookings</h2>
                            </div>
                            <span class="rounded-md bg-amber-100 px-3 py-2 text-xs font-black uppercase tracking-[0.14em] text-amber-800">KHQR</span>
                        </div>

                        <div class="mt-6 space-y-4">
                            @forelse ($bookings as $booking)
                                <article class="rounded-lg border border-slate-200 bg-stone-50 p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">{{ $booking['code'] }}</p>
                                            <h3 class="mt-1 font-black text-slate-950">{{ $booking['event_title'] }}</h3>
                                            <p class="mt-1 text-sm font-bold text-slate-600">{{ $booking['first_name'] }} {{ $booking['last_name'] }}</p>
                                        </div>
                                        <span class="rounded-md bg-white px-3 py-1 text-xs font-black text-slate-700 ring-1 ring-slate-200">{{ $booking['status'] }}</span>
                                    </div>
                                    <div class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                                        <div>
                                            <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">Tickets</p>
                                            <p class="mt-1 font-bold text-slate-800">{{ $booking['quantity'] }} x {{ $booking['ticket_type'] }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-400">KHQR Ref</p>
                                            <p class="mt-1 break-words font-bold text-slate-800">{{ $booking['payment_reference'] }}</p>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="rounded-lg border border-dashed border-slate-300 bg-stone-50 p-8 text-center">
                                    <p class="text-sm font-black uppercase tracking-[0.18em] text-slate-400">No bookings</p>
                                    <h3 class="mt-2 text-xl font-black text-slate-950">Nothing to review yet</h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">KHQR payment submissions will appear here after users book events.</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
