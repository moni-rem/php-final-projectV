<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event['title'] }} | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8">
            <a href="{{ url('/') }}" class="text-lg font-black tracking-wide text-slate-950">Refined Travel</a>
            <div class="flex items-center gap-4 text-sm font-bold">
                <a href="{{ url('/') }}" class="text-slate-600 hover:text-slate-950">Home</a>
                <a href="{{ route('bookings.history') }}" class="text-slate-600 hover:text-slate-950">Booking history</a>
                <a href="{{ route('login') }}" class="rounded-md border border-slate-300 px-4 py-2 text-slate-800 hover:bg-slate-100">Login</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="mx-auto max-w-7xl px-5 py-10 sm:px-8">
            <div class="mb-8">
                <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-700">{{ $event['category'] }}</p>
                <h1 class="mt-3 text-4xl font-black leading-tight text-slate-950 sm:text-5xl">{{ $event['title'] }}</h1>
                <div class="mt-4 flex flex-wrap gap-3 text-sm font-bold text-slate-600">
                    <span>{{ $event['location'] }}</span>
                    <span aria-hidden="true">/</span>
                    <span>{{ $event['date'] }}</span>
                    @if (! empty($event['startTime']) && ! empty($event['endTime']))
                        <span aria-hidden="true">/</span>
                        <span>{{ $event['startTime'] }} - {{ $event['endTime'] }}</span>
                    @endif
                    <span aria-hidden="true">/</span>
                    <span class="text-emerald-700">{{ $event['price'] }}</span>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200 lg:col-span-2">
                    <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}" class="h-[340px] w-full object-cover sm:h-[480px]">
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                    <img src="{{ $event['image2'] }}" alt="{{ $event['title'] }} detail image" class="h-56 w-full rounded-lg object-cover shadow-sm ring-1 ring-slate-200 sm:h-full lg:h-[232px]">
                    <img src="{{ $event['image3'] }}" alt="{{ $event['title'] }} second detail image" class="h-56 w-full rounded-lg object-cover shadow-sm ring-1 ring-slate-200 sm:h-full lg:h-[232px]">
                </div>
            </div>

            <div class="mt-10 grid gap-8 lg:grid-cols-[1fr_360px]">
                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <h2 class="text-2xl font-black text-slate-950">About This Event</h2>
                    <div class="mt-4 space-y-4 text-base leading-8 text-slate-600">
                        @foreach ($event['about'] ?? [$event['description']] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        <h3 class="text-xl font-black text-slate-950">What to Expect</h3>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            @foreach ($event['whatToExpect'] ?? $event['amenities'] as $amenity)
                                <div class="flex items-center gap-3 rounded-md border border-slate-200 bg-stone-50 px-4 py-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-emerald-100 text-sm font-black text-emerald-700">✓</span>
                                    <span class="text-sm font-bold text-slate-700">{{ $amenity }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if (! empty($event['importantInformation']))
                        <div class="mt-8">
                            <h3 class="text-xl font-black text-slate-950">Important Information</h3>
                            <div class="mt-4 space-y-3">
                                @foreach ($event['importantInformation'] as $information)
                                    <div class="flex items-start gap-3 rounded-md border border-amber-200 bg-amber-50 px-4 py-3">
                                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-amber-200 text-sm font-black text-amber-900">!</span>
                                        <span class="text-sm font-bold leading-6 text-slate-700">{{ $information }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>

                <aside class="h-fit rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">Event Details</p>
                    <div class="mt-3 flex items-end justify-between border-b border-slate-200 pb-5">
                        <h2 class="text-3xl font-black text-slate-950">{{ $event['price'] }}</h2>
                        <span class="text-sm font-bold text-slate-500">{{ $event['category'] === 'Hotel' ? 'per night' : 'entry' }}</span>
                    </div>
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
                    </dl>
                    <a href="{{ route('events.booking', $slug) }}" class="mt-6 flex min-h-12 w-full items-center justify-center rounded-md bg-emerald-700 px-5 text-sm font-black text-white hover:bg-emerald-800">
                        Book now
                    </a>
                    <a href="{{ url('/') }}#stays" class="mt-3 flex min-h-12 w-full items-center justify-center rounded-md border border-slate-300 px-5 text-sm font-black text-slate-800 hover:bg-slate-100">
                        Back to events
                    </a>

                    @if (! empty($event['mapUrl']))
                        <div class="mt-6 overflow-hidden rounded-lg border border-slate-200">
                            <div class="bg-stone-50 p-4">
                                <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">Google Map</p>
                                <h3 class="mt-1 text-base font-black text-slate-950">{{ $event['location'] }}</h3>
                            </div>
                            <iframe
                                src="{{ $event['mapUrl'] }}"
                                width="100%"
                                height="260"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                            ></iframe>
                        </div>
                    @endif
                </aside>
            </div>
        </section>
    </main>
</body>
</html>
