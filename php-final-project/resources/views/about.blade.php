<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Us | Refined Travel</title>

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-stone-50 text-slate-900 antialiased">

    <!-- Header -->
    <header class="sticky top-0 z-30 bg-slate-950 shadow-sm">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8">
            <a href="{{ url('/') }}" class="text-lg font-black tracking-wide text-white">Refined Travel</a>

            <div class="hidden items-center gap-7 text-sm font-semibold text-white/85 sm:flex">
                <a href="{{ route('events.index') }}" class="hover:text-white">Events</a>
                <a href="{{ route('about') }}" class="text-white">About us</a>
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

        <!-- Hero Section -->
        <section class="bg-stone-50 mt-10">
            <div class="mx-auto grid max-w-7xl gap-14 px-6 py-20 lg:grid-cols-2 lg:items-center">

                <div>
                   

                    <h1 class="mt-4 text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                        Simple event booking for everyone
                    </h1>

                    <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                        Discover local events, book tickets easily, and help organizers manage experiences in one clean platform.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-4">

                        <a href="{{ route('events.index') }}"
                           class="rounded-lg bg-orange-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-orange-700">
                            Browse Events
                        </a>


                    </div>
                </div>

                <div class="mx-auto w-full max-w-md lg:mx-0 lg:justify-self-end mt-20">
                    <img
                        src="https://images.unsplash.com/photo-1528605248644-14dd04022da1?auto=format&fit=crop&w=1200&q=85"
                        alt="Event team"
                        class="h-[300px] w-full rounded-2xl object-cover shadow-sm sm:h-[340px]"
                    >
                </div>

            </div>
        </section>

        <!-- Mission Section -->
        <section class="bg-white">
            <div class="mx-auto grid max-w-7xl gap-14 px-6 py-20 lg:grid-cols-2 lg:items-center">

                <div class="mx-auto w-full mt-10 mb-10 max-w-lg lg:mx-0">
                    <img
                        src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1200&q=85"
                        alt="Planning events"
                        class="h-[300px] w-full rounded-2xl object-cover shadow-sm sm:h-[340px]"
                    >
                </div>

                <div class="mt-10">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-600">
                        Our Mission
                    </p>

                    <h2 class="mt-4 text-4xl font-bold leading-tight tracking-tight">
                        Helping local experiences grow
                    </h2>

                    <p class="mt-6 text-lg leading-8 text-slate-600">
                        We help guests discover memorable events while giving organizers tools to manage bookings and understand their audience.
                    </p>

                    <p class="mt-4 text-lg leading-8 text-slate-600">
                        Better experiences for customers create better growth for event owners.
                    </p>
                </div>

            </div>
        </section>

        <!-- Features Section -->
        <section class="bg-stone-50">
            <div class="mx-auto max-w-7xl px-6 py-20">

                <div class="max-w-2xl">
                    
                    <h2 class="mt-10 text-4xl font-bold tracking-tight ">
                        Built with simplicity in mind
                    </h2>

                    <p class="mt-4 text-lg leading-8 text-slate-600">
                        Everything is designed to make event discovery and management easier.
                    </p>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-3">

                    <!-- Card -->
                    <div class="rounded-2xl bg-white p-8 shadow-sm">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-lg font-bold text-orange-600">
                            01
                        </div>

                        <h3 class="mt-6 text-xl font-semibold">
                            Easy Booking
                        </h3>

                        <p class="mt-4 leading-7 text-slate-600">
                            Find events quickly and book tickets with a smooth checkout experience.
                        </p>
                    </div>

                    <!-- Card -->
                    <div class="rounded-2xl bg-white p-8 shadow-sm">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-lg font-bold text-emerald-700">
                            02
                        </div>

                        <h3 class="mt-6 text-xl font-semibold">
                            Event Management
                        </h3>

                        <p class="mt-4 leading-7 text-slate-600">
                            Organizers can manage bookings, seats, and ticket activity in one place.
                        </p>
                    </div>

                    <!-- Card -->
                    <div class="rounded-2xl bg-white p-8 shadow-sm">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-lg font-bold text-amber-700">
                            03
                        </div>

                        <h3 class="mt-6 text-xl font-semibold">
                            Clear Insights
                        </h3>

                        <p class="mt-4 leading-7 text-slate-600">
                            Keep track of bookings and event performance with simple data tools.
                        </p>
                    </div>

                </div>

            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-white">
            <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 px-6 py-20 lg:flex-row lg:items-center">

                <div class="max-w-2xl mt-10">
                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-orange-600 mt-4">
                        Ready to explore?
                    </p>

                    <h2 class="mt-4 text-4xl font-bold tracking-tight">
                        Find your next local experience
                    </h2>

                    <p class="mt-4 max-w-2xl text-lg leading-8 text-slate-600 mb-6">
                        Browse published events and enjoy a smoother booking experience.
                    </p>
                </div>

                <a href="{{ route('events.index') }}"
                   class="rounded-lg bg-orange-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-orange-700">
                    Browse Events
                </a>

            </div>
        </section>

    </main>

    @include('partials.footer')

</body>
</html>
