<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Event | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    @php
        $dashboardRoute = $dashboardRoute ?? 'owner.dashboard';
        $formAction = $formAction ?? route('owner.events.store');
        $sectionLabel = $sectionLabel ?? 'Event Owner';
        $pageTitle = $pageTitle ?? 'Add a new event';
        $submitLabel = $submitLabel ?? 'Publish event to main page';
    @endphp

    <main class="mx-auto max-w-5xl px-5 py-10 sm:px-8">
        <nav class="mb-8 flex items-center justify-between">
            <a href="{{ route($dashboardRoute) }}" class="text-lg font-black tracking-wide">Refined Travel</a>
            <a href="{{ route($dashboardRoute) }}" class="text-sm font-bold text-emerald-700">Back to dashboard</a>
        </nav>

        <div class="mb-8">
            <p class="text-sm font-black uppercase tracking-[0.24em] text-amber-700">{{ $sectionLabel }}</p>
            <h1 class="mt-3 text-4xl font-black">{{ $pageTitle }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                After you submit this form, the event is published to the main page automatically.
            </p>
        </div>

        <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ $formAction }}" class="space-y-6">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="title" class="block text-sm font-bold text-slate-800">Event title</label>
                        <input id="title" name="title" value="{{ old('title') }}" required placeholder="CamM Met Gala" class="mt-2 min-h-12 w-full rounded-md border border-slate-300 px-4 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        @error('title')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="category_name" class="block text-sm font-bold text-slate-800">Category</label>
                        <input id="category_name" name="category_name" value="{{ old('category_name', 'Event') }}" required placeholder="Event" class="mt-2 min-h-12 w-full rounded-md border border-slate-300 px-4 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        @error('category_name')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-slate-800">Description</label>
                    <textarea id="description" name="description" rows="5" required placeholder="Tell visitors what this event is about" class="mt-2 w-full rounded-md border border-slate-300 px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="location" class="block text-sm font-bold text-slate-800">Location</label>
                        <input id="location" name="location" value="{{ old('location') }}" required placeholder="Phnom Penh, Cambodia" class="mt-2 min-h-12 w-full rounded-md border border-slate-300 px-4 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        @error('location')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="event_date" class="block text-sm font-bold text-slate-800">Date</label>
                        <input id="event_date" name="event_date" type="date" value="{{ old('event_date') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-300 px-4 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        @error('event_date')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-4">
                    <div>
                        <label for="start_time" class="block text-sm font-bold text-slate-800">Start time</label>
                        <input id="start_time" name="start_time" type="time" value="{{ old('start_time') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-300 px-4 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        @error('start_time')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-bold text-slate-800">End time</label>
                        <input id="end_time" name="end_time" type="time" value="{{ old('end_time') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-300 px-4 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        @error('end_time')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="total_seats" class="block text-sm font-bold text-slate-800">Seats</label>
                        <input id="total_seats" name="total_seats" type="number" min="1" value="{{ old('total_seats', 100) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-300 px-4 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        @error('total_seats')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="ticket_price" class="block text-sm font-bold text-slate-800">Price USD</label>
                        <input id="ticket_price" name="ticket_price" type="number" min="0" step="0.01" value="{{ old('ticket_price', 0) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-300 px-4 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        @error('ticket_price')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-3">
                    <div>
                        <label for="image" class="block text-sm font-bold text-slate-800">Main image URL</label>
                        <input id="image" name="image" type="url" value="{{ old('image') }}" placeholder="https://..." class="mt-2 min-h-12 w-full rounded-md border border-slate-300 px-4 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        @error('image')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="image2" class="block text-sm font-bold text-slate-800">Image 2 URL</label>
                        <input id="image2" name="image2" type="url" value="{{ old('image2') }}" placeholder="https://..." class="mt-2 min-h-12 w-full rounded-md border border-slate-300 px-4 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        @error('image2')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="image3" class="block text-sm font-bold text-slate-800">Image 3 URL</label>
                        <input id="image3" name="image3" type="url" value="{{ old('image3') }}" placeholder="https://..." class="mt-2 min-h-12 w-full rounded-md border border-slate-300 px-4 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        @error('image3')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="map_url" class="block text-sm font-bold text-slate-800">Google Map embed URL</label>
                    <input id="map_url" name="map_url" type="url" value="{{ old('map_url') }}" placeholder="https://maps.google.com/maps?q=...&output=embed" class="mt-2 min-h-12 w-full rounded-md border border-slate-300 px-4 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    @error('map_url')
                        <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="what_to_expect" class="block text-sm font-bold text-slate-800">What to expect</label>
                        <textarea id="what_to_expect" name="what_to_expect" rows="5" placeholder="One item per line" class="mt-2 w-full rounded-md border border-slate-300 px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('what_to_expect') }}</textarea>
                        @error('what_to_expect')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="important_information" class="block text-sm font-bold text-slate-800">Important information</label>
                        <textarea id="important_information" name="important_information" rows="5" placeholder="One item per line" class="mt-2 w-full rounded-md border border-slate-300 px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">{{ old('important_information') }}</textarea>
                        @error('important_information')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="min-h-12 w-full rounded-md bg-amber-400 px-5 text-sm font-black text-slate-950 hover:bg-amber-300">
                    {{ $submitLabel }}
                </button>
            </form>
        </section>
    </main>
</body>
</html>
