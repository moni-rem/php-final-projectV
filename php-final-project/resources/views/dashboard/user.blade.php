<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Dashboard | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    <main class="mx-auto max-w-7xl px-5 py-10 sm:px-8">
        <nav class="mb-8 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-lg font-black tracking-wide">Refined Travel</a>
            <div class="flex items-center gap-3">
                <a href="{{ route('bookings.history') }}" class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-black text-white">Booking history</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-black text-slate-800 hover:bg-white">Logout</button>
                </form>
            </div>
        </nav>
        <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-700">User Dashboard</p>
        <h1 class="mt-3 text-4xl font-black">Book events and manage tickets</h1>

        @if (session('success'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <a href="{{ url('/') }}#stays" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-xl font-black">Browse Events</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Find an event and start a booking.</p>
            </a>
            <a href="{{ route('bookings.history') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-xl font-black">My Bookings</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ count($bookings) }} booking(s) saved in this session.</p>
            </a>
            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h2 class="text-xl font-black">Ticket Rule</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Booked tickets are non-refundable and cannot be returned or canceled.</p>
            </section>
        </div>
    </main>

    @include('partials.footer')
</body>
</html>
