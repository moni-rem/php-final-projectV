<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Events | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-900 antialiased">
    @php
        $eventSearch = $eventSearch ?? '';
        $events = $events ?? [];
        $totalEventsCount = $totalEventsCount ?? count($events);
        $visibleEventsCount = count($events);
    @endphp

    <header class="bg-slate-950">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8">
            <a href="{{ url('/') }}" class="text-lg font-black tracking-wide text-white">Refined Travel</a>
            <div class="hidden items-center gap-7 text-sm font-semibold text-white/85 sm:flex">
                <a href="{{ route('events.index') }}" class="text-white">Events</a>
                <a href="{{ route('about') }}" class="hover:text-white">About us</a>
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
        <section class="mx-auto max-w-7xl px-5 py-12 sm:px-8">
            <form method="GET" action="{{ route('events.index') }}" class="mb-8 rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200">
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
                        <a href="{{ route('events.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-md border border-slate-300 px-6 text-sm font-black text-slate-800 hover:bg-stone-50">
                            Clear
                        </a>
                    @endif
                </div>
                <p class="mt-3 text-sm font-semibold text-slate-500">
                    @if ($eventSearch !== '')
                        Showing {{ $visibleEventsCount }} of {{ $totalEventsCount }} event(s) for "{{ $eventSearch }}".
                    @else
                        Showing {{ $visibleEventsCount }} event(s).
                    @endif
                </p>
            </form>

            <div class="grid gap-6 md:grid-cols-3">
                @forelse ($events as $slug => $event)
                    <a href="{{ route('events.show', $slug) }}" class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-md">
                        <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}" class="h-56 w-full object-cover">
                        <div class="p-5">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-700">{{ $event['category'] }}</p>
                            <h2 class="mt-2 text-xl font-black">{{ $event['title'] }}</h2>
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
                        <h2 class="mt-2 text-2xl font-black text-slate-950">
                            {{ $eventSearch !== '' ? 'No events match your search' : 'No events published yet' }}
                        </h2>
                    </div>
                @endforelse
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
