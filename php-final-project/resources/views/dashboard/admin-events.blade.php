<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Events | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    @php
        $events = $events ?? collect();
        $eventSearch = $eventSearch ?? '';
        $adminUser = $adminUser ?? null;
        $adminName = $adminUser?->full_name ?: $adminUser?->name ?: 'Admin';
        $adminEmail = $adminUser?->email ?: 'Not signed in';
        $adminInitials = collect(explode(' ', trim($adminName)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('') ?: 'A';
    @endphp

    <div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
        <aside class="border-b border-slate-200 bg-white lg:min-h-screen lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between px-5 py-5 lg:block lg:px-6">
                <a href="{{ url('/') }}" class="text-lg font-black tracking-wide text-slate-950">Refined Travel</a>
                <a href="{{ route('login') }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-800 lg:hidden">Switch role</a>
            </div>

            <div class="px-5 pb-5 lg:px-4">
                <div class="flex items-center gap-3 rounded-lg bg-slate-950 p-4 text-white">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md bg-amber-400 text-sm font-black text-slate-950">
                        {{ $adminInitials }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-black">{{ $adminName }}</p>
                        <p class="truncate text-xs font-semibold text-white/70">{{ $adminEmail }}</p>
                        <p class="mt-1 text-xs font-black uppercase tracking-[0.14em] text-amber-300">{{ $adminUser?->role?->role_name ?? 'admin' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="flex min-h-10 w-full items-center justify-center rounded-md border border-slate-300 bg-white px-4 text-sm font-black text-slate-800 hover:bg-stone-100">
                        Logout
                    </button>
                </form>
            </div>

            <nav class="flex gap-2 overflow-x-auto px-5 pb-5 text-sm font-bold lg:block lg:space-y-2 lg:px-4">
                <a href="{{ route('admin.dashboard') }}" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Manage users</a>
                <a href="{{ route('admin.events.index') }}" class="flex min-h-11 shrink-0 items-center rounded-md bg-slate-950 px-4 text-white">Manage events</a>
                <a href="{{ route('owner.dashboard') }}" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Owner view</a>
                <a href="{{ url('/') }}#stays" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Website events</a>
                <a href="{{ route('bookings.history') }}" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Bookings</a>
            </nav>
        </aside>

        <main class="px-5 py-8 sm:px-8 lg:px-10">
            <header class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-700">Admin</p>
                    <h1 class="mt-3 text-4xl font-black leading-tight sm:text-5xl">Manage events</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                        Create, update, and delete event records from one admin workspace.
                    </p>
                </div>
                <a href="{{ route('admin.events.create') }}" class="inline-flex min-h-11 w-fit items-center justify-center rounded-md bg-emerald-700 px-4 text-sm font-black text-white hover:bg-emerald-800">Add event</a>
            </header>

            @if (session('success'))
                <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="mt-8 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <h2 class="text-xl font-black">Event records</h2>
                        <p class="mt-1 text-sm font-semibold text-slate-500">Search events and edit details directly in the list.</p>
                    </div>
                    <form method="GET" action="{{ route('admin.events.index') }}" class="flex w-full flex-col gap-2 sm:flex-row xl:max-w-md">
                        <input
                            name="event_search"
                            value="{{ $eventSearch }}"
                            placeholder="Search title, category, location"
                            class="min-h-11 flex-1 rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-md bg-slate-950 px-4 text-sm font-black text-white hover:bg-slate-800">Search</button>
                        @if ($eventSearch !== '')
                            <a href="{{ route('admin.events.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-800 hover:bg-stone-50">Clear</a>
                        @endif
                    </form>
                </div>

                <div class="divide-y divide-slate-200">
                    @forelse ($events as $event)
                        @php
                            $bookedTickets = (int) ($event->booked_tickets ?? 0);
                            $sales = (float) ($event->gross_sales ?? 0);
                        @endphp
                        <article class="p-5">
                            <div class="grid gap-5 xl:grid-cols-[220px_1fr]">
                                <div>
                                    <img src="{{ $event->image }}" alt="{{ $event->title }}" class="h-40 w-full rounded-lg object-cover ring-1 ring-slate-200">
                                    <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs font-bold text-slate-600">
                                        <div class="rounded-md bg-stone-100 p-2">
                                            <p class="text-slate-400">Bookings</p>
                                            <p class="mt-1 text-slate-950">{{ $event->bookings_count }}</p>
                                        </div>
                                        <div class="rounded-md bg-stone-100 p-2">
                                            <p class="text-slate-400">Tickets</p>
                                            <p class="mt-1 text-slate-950">{{ $bookedTickets }}</p>
                                        </div>
                                        <div class="rounded-md bg-stone-100 p-2">
                                            <p class="text-slate-400">Sales</p>
                                            <p class="mt-1 text-slate-950">${{ number_format($sales, 0) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <form id="event-update-{{ $event->id }}" method="POST" action="{{ route('admin.events.update', $event) }}" class="grid gap-4 lg:grid-cols-2">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label class="block text-xs font-black uppercase tracking-[0.16em] text-slate-400">Title</label>
                                            <input name="title" value="{{ old('title', $event->title) }}" required class="mt-2 min-h-11 w-full rounded-md border border-slate-300 px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-black uppercase tracking-[0.16em] text-slate-400">Category</label>
                                            <input name="category_name" value="{{ old('category_name', $event->category?->category_name ?? 'Event') }}" required class="mt-2 min-h-11 w-full rounded-md border border-slate-300 px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-black uppercase tracking-[0.16em] text-slate-400">Location</label>
                                            <input name="location" value="{{ old('location', $event->location) }}" required class="mt-2 min-h-11 w-full rounded-md border border-slate-300 px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                        </div>
                                        <div class="grid gap-3 sm:grid-cols-3">
                                            <div>
                                                <label class="block text-xs font-black uppercase tracking-[0.16em] text-slate-400">Date</label>
                                                <input name="event_date" type="date" value="{{ old('event_date', $event->event_date?->format('Y-m-d')) }}" required class="mt-2 min-h-11 w-full rounded-md border border-slate-300 px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black uppercase tracking-[0.16em] text-slate-400">Start</label>
                                                <input name="start_time" type="time" value="{{ old('start_time', $event->start_time ? date('H:i', strtotime($event->start_time)) : '') }}" required class="mt-2 min-h-11 w-full rounded-md border border-slate-300 px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black uppercase tracking-[0.16em] text-slate-400">End</label>
                                                <input name="end_time" type="time" value="{{ old('end_time', $event->end_time ? date('H:i', strtotime($event->end_time)) : '') }}" class="mt-2 min-h-11 w-full rounded-md border border-slate-300 px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            </div>
                                        </div>
                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <div>
                                                <label class="block text-xs font-black uppercase tracking-[0.16em] text-slate-400">Seats</label>
                                                <input name="total_seats" type="number" min="1" value="{{ old('total_seats', $event->total_seats) }}" required class="mt-2 min-h-11 w-full rounded-md border border-slate-300 px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black uppercase tracking-[0.16em] text-slate-400">Price USD</label>
                                                <input name="ticket_price" type="number" min="0" step="0.01" value="{{ old('ticket_price', $event->ticket_price) }}" required class="mt-2 min-h-11 w-full rounded-md border border-slate-300 px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            </div>
                                        </div>
                                        <div class="lg:col-span-2">
                                            <label class="block text-xs font-black uppercase tracking-[0.16em] text-slate-400">Description</label>
                                            <textarea name="description" rows="3" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('description', $event->description) }}</textarea>
                                        </div>
                                        <div class="lg:col-span-2 grid gap-3 lg:grid-cols-2">
                                            <input name="image" type="url" value="{{ old('image', $event->image) }}" placeholder="Main image URL" class="min-h-11 rounded-md border border-slate-300 px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            <input name="map_url" type="url" value="{{ old('map_url', $event->map_url) }}" placeholder="Map embed URL" class="min-h-11 rounded-md border border-slate-300 px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            <input name="image2" type="url" value="{{ old('image2', $event->image2) }}" placeholder="Image 2 URL" class="min-h-11 rounded-md border border-slate-300 px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            <input name="image3" type="url" value="{{ old('image3', $event->image3) }}" placeholder="Image 3 URL" class="min-h-11 rounded-md border border-slate-300 px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                        </div>
                                    </form>

                                    <div class="mt-4 flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('events.show', $event->slug) }}" class="inline-flex min-h-10 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-800 hover:bg-stone-50">View</a>
                                        <button form="event-update-{{ $event->id }}" type="submit" class="inline-flex min-h-10 items-center justify-center rounded-md bg-slate-950 px-4 text-sm font-black text-white hover:bg-slate-800">Update</button>
                                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-md border border-red-200 px-4 text-sm font-black text-red-700 hover:bg-red-50">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="px-5 py-12 text-center">
                            <p class="text-sm font-black uppercase tracking-[0.2em] text-slate-400">No events found</p>
                            <a href="{{ route('admin.events.create') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-md bg-emerald-700 px-5 text-sm font-black text-white hover:bg-emerald-800">Add event</a>
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
</body>
</html>
