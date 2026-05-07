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
        <nav class="mb-8 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-lg font-black tracking-wide">Refined Travel</a>
            <div class="flex items-center gap-4">
                <a href="{{ route('owner.events.create') }}" class="rounded-md bg-amber-400 px-4 py-2 text-sm font-black text-slate-950">Add event</a>
                <a href="{{ route('login') }}" class="text-sm font-bold text-emerald-700">Switch role</a>
            </div>
        </nav>
        <p class="text-sm font-black uppercase tracking-[0.24em] text-amber-700">Event Owner Dashboard</p>
        <h1 class="mt-3 text-4xl font-black">Manage your event displays</h1>

        @if (session('success'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-8 rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
            @foreach ($events as $slug => $event)
                <div class="grid gap-4 border-b border-slate-200 p-5 last:border-b-0 md:grid-cols-[120px_1fr_auto] md:items-center">
                    <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}" class="h-24 w-full rounded-md object-cover md:w-28">
                    <div>
                        <h2 class="text-xl font-black">{{ $event['title'] }}</h2>
                        <p class="mt-1 text-sm font-bold text-slate-600">{{ $event['location'] }} / {{ $event['date'] }}</p>
                    </div>
                    <a href="{{ route('events.show', $slug) }}" class="inline-flex min-h-11 items-center justify-center rounded-md bg-amber-400 px-5 text-sm font-black text-slate-950">
                        View display
                    </a>
                </div>
            @endforeach
        </div>
    </main>
</body>
</html>
