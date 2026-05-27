<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-900 antialiased">
    @php
        $eventSearch = $eventSearch ?? '';
        $events = $events ?? [];
        $totalEventsCount = $totalEventsCount ?? count($events);
        $visibleEventsCount = count($events);
    @endphp

    <header class="absolute inset-x-0 top-0 z-20">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8 bg-black/30 rounded-lg">
            <a href="/" class="text-lg font-black tracking-wide text-white">Refined Travel</a>
            <div class="hidden items-center gap-7 text-sm font-semibold text-white/85 sm:flex">
                <a href="{{ route('events.index') }}" class="hover:text-white">Events</a>
                <a href="{{ route('about') }}" class="hover:text-white">About us</a>
              {{--  <a href="#subscribe" class="hover:text-white">Subscribe</a> --}}
                <a href="{{ route('bookings.history') }}" class="hover:text-white">Booking History</a>
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-md border border-white/35 px-4 py-2 font-black text-white hover:bg-white/12">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-md border border-white/35 px-4 py-2 font-black text-white hover:bg-white/12">Login</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-amber-400 px-4 py-2 font-black text-slate-950 hover:bg-amber-300">Register</a>
                @endauth
            </div>
        </nav>
    </header>

    <main>
        <section class="relative min-h-[88vh] overflow-hidden">
            <img
                src="https://images.unsplash.com/photo-1517457373958-b7bdd4587205?q=80&w=1469&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                alt="Sunlit boutique hotel bedroom"
                class="absolute inset-0 h-full w-full object-cover"
            >
            <div class="absolute inset-0 bg-slate-950/55"></div>
            <div class="relative z-10 mx-auto flex min-h-[88vh] max-w-7xl items-end px-5 pb-20 pt-28 sm:px-8 lg:pb-24">
                <div class="max-w-3xl text-white">
                    <p class="mb-4 text-sm font-bold uppercase tracking-[0.28em] text-amber-300">Cambodia stays and experiences</p>
                    <h1 class="text-5xl font-black leading-tight sm:text-6xl lg:text-7xl">Discover the art of refined Bookings</h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-white/82">
                        Find memorable Events, local events, and weekend escapes curated for comfort, style, and easy planning.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('events.index') }}" class="inline-flex items-center justify-center rounded-md bg-amber-400 px-6 py-3 text-sm font-black text-slate-950 hover:bg-amber-300">
                            Explore events
                        </a>
                        <a href="{{ route('about') }}" class="inline-flex items-center justify-center rounded-md border border-white/45 px-6 py-3 text-sm font-black text-white hover:bg-white/12">
                            Learn more about us
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="stays" class="mx-auto max-w-7xl px-5 py-16 sm:px-8">
            <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-700">Featured</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950 sm:text-4xl">Trending places this season</h2>
                </div>
                <p class="max-w-xl text-sm leading-6 text-slate-600">
                    Events published by owners appear here automatically.
                </p>
            </div>

            <form method="GET" action="{{ url('/') }}#stays" class="mb-8 rounded-lg  p-4 shadow-sm ring-1 ring-slate-200">
                <div class="grid gap-3 lg:grid-cols-[1fr_auto_auto] lg:items-center">
                    <div class="relative">
                        <label for="event_search" class="block text-xs font-black uppercase tracking-[0.18em] text-slate-400">Search events</label>
                        <input
                            id="event_search"
                            name="event_search"
                            value="{{ $eventSearch }}"
                            placeholder="Search by title, category, location, date, or price"
                            class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >
                        <div id="event_search_results" class="absolute left-0 right-0 z-30 mt-2 hidden max-h-80 overflow-y-auto rounded-lg bg-white shadow-lg ring-1 ring-slate-200"></div>
                    </div>
                    <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-md bg-emerald-700 px-6 text-sm font-black text-white hover:bg-emerald-800">
                        Search
                    </button>
                    @if ($eventSearch !== '')
                        <a href="{{ url('/') }}#stays" class="inline-flex min-h-12 items-center justify-center rounded-md border border-slate-300 px-6 text-sm font-black text-slate-800 hover:bg-stone-50">
                            Clear
                        </a>
                    @endif
                </div>
                <p class="mt-3 text-sm font-semibold text-slate-500">
                    @if ($eventSearch !== '')
                        Showing {{ $visibleEventsCount }} of {{ $totalEventsCount }} event(s) for "{{ $eventSearch }}".
                    @else
                        {{-- Showing {{ $visibleEventsCount }} event(s). --}}
                    @endif
                </p>
            </form>

            <div class="grid gap-6 md:grid-cols-3">
                @forelse ($events as $slug => $event)
                    <a href="{{ route('events.show', $slug) }}" class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-md">
                        <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}" class="h-56 w-full object-cover">
                        <div class="p-5">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-700">{{ $event['category'] }}</p>
                            <h3 class="mt-2 text-xl font-black">{{ $event['title'] }}</h3>
                            <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $event['description'] }}</p>
                            <div class="mt-5 flex items-center justify-between gap-4 text-sm font-bold">
                                <span class="truncate">{{ $event['location'] }}</span>
                                <span class="shrink-0 text-emerald-700">{{ $event['price'] }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center md:col-span-3">
                        <p class="text-sm font-black uppercase tracking-[0.2em] text-slate-400">No events</p>
                        <h3 class="mt-2 text-2xl font-black text-slate-950">
                            {{ $eventSearch !== '' ? 'No events match your search' : 'No owner events published yet' }}
                        </h3>
                        @if ($eventSearch !== '')
                            <a href="{{ url('/') }}#stays" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-md bg-emerald-700 px-5 text-sm font-black text-white hover:bg-emerald-800">
                                Clear search
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>
        </section>

        <section id="guides" class="bg-white py-16">
            <div class="mx-auto grid max-w-7xl gap-10 px-5 sm:px-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-700">Editorial insights</p>
                    <h2 class="mt-3 text-3xl font-black text-slate-950 sm:text-4xl">Plan calmer trips with sharper local notes</h2>
                    <p class="mt-4 text-base leading-7 text-slate-600">
                        Save time choosing where to stay, what to book, and which neighborhoods make sense for your travel style.
                    </p>
                </div>
                <img
                    src="https://i.pinimg.com/736x/f3/fe/02/f3fe02a5e8c9d96b468cc0b0178b5107.jpg"
                    alt="Traveler looking over a scenic destination"
                    class="h-[360px] w-full rounded-lg object-cover shadow-sm"
                >
            </div>
        </section>

        <section id="subscribe" class="mx-auto max-w-7xl px-5 py-16 sm:px-8">
            <div class="grid gap-6 rounded-lg bg-slate-950 p-6 text-white sm:p-8 lg:grid-cols-[1fr_auto] lg:items-center">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-amber-300">Weekly guides</p>
                    <h2 class="mt-2 text-2xl font-black sm:text-3xl">Get curated travel ideas in your inbox</h2>
                </div>
                <form class="flex w-full flex-col gap-3 sm:w-auto sm:min-w-[420px] sm:flex-row">
                    <input
                        type="email"
                        placeholder="Enter your email"
                        class="min-h-12 flex-1 rounded-md border border-white/20 bg-white px-4 text-sm font-semibold text-slate-950 outline-none focus:ring-2 focus:ring-amber-300"
                    >
                    <button type="submit" class="min-h-12 rounded-md bg-amber-400 px-6 text-sm font-black text-slate-950 hover:bg-amber-300">
                        Subscribe
                    </button>
                </form>
            </div>
        </section>
    </main>

    @include('partials.footer')

    @php
        $searchableEvents = collect($events)
            ->map(fn ($event, $slug) => [
                'title' => $event['title'] ?? '',
                'category' => $event['category'] ?? '',
                'location' => $event['location'] ?? '',
                'url' => route('events.show', $slug),
            ])
            ->values();
    @endphp

    <script>
        const eventSearchInput = document.getElementById('event_search');
        const eventSearchResults = document.getElementById('event_search_results');
        const searchableEvents = @json($searchableEvents);

        function escapeHtml(value) {
            return String(value).replace(/[&<>"']/g, (character) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[character]));
        }

        function renderEventResults(query) {
            const term = query.trim().toLowerCase();

            if (! term) {
                eventSearchResults.classList.add('hidden');
                eventSearchResults.innerHTML = '';
                return;
            }

            const matches = searchableEvents
                .filter((event) => [event.title, event.category, event.location].join(' ').toLowerCase().includes(term))
                .slice(0, 6);

            eventSearchResults.classList.remove('hidden');

            if (matches.length === 0) {
                eventSearchResults.innerHTML = '<div class="px-4 py-3 text-sm font-bold text-slate-500">No matching events</div>';
                return;
            }

            eventSearchResults.innerHTML = matches.map((event) => `
                <a href="${event.url}" class="block px-4 py-3 hover:bg-stone-50">
                    <span class="block text-sm font-black text-slate-950">${escapeHtml(event.title)}</span>
                    <span class="mt-1 block text-xs font-bold text-slate-500">${escapeHtml(event.category)} - ${escapeHtml(event.location)}</span>
                </a>
            `).join('');
        }

        eventSearchInput?.addEventListener('input', (event) => renderEventResults(event.target.value));
        eventSearchInput?.addEventListener('focus', (event) => renderEventResults(event.target.value));
        document.addEventListener('click', (event) => {
            if (! eventSearchResults.contains(event.target) && event.target !== eventSearchInput) {
                eventSearchResults.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
