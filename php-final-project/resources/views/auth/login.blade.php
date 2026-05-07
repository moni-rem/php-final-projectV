<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Choose Login | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    <main class="mx-auto flex min-h-screen max-w-7xl items-center px-5 py-10 sm:px-8">
        <div class="w-full">
            <div class="mb-9 flex items-center justify-between">
                <a href="{{ url('/') }}" class="text-lg font-black tracking-wide text-slate-950">Refined Travel</a>
                <a href="{{ url('/') }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-800">Back home</a>
            </div>

            <div class="mb-8 max-w-3xl">
                <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-700">Login type</p>
                <h1 class="mt-3 text-4xl font-black leading-tight text-slate-950 sm:text-5xl">Choose your account role</h1>
                <p class="mt-4 text-base leading-7 text-slate-600">
                    Choose User if you want to book tickets, or Event Owner if you want to manage and display your events.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <a href="{{ route('login.user') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-md">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">User</p>
                    <h2 class="mt-3 text-2xl font-black text-slate-950">Book events</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">For customers who want to book tickets and view booking history.</p>
                    <span class="mt-6 inline-flex min-h-11 items-center justify-center rounded-md bg-emerald-700 px-5 text-sm font-black text-white">User login</span>
                </a>

                <a href="{{ route('login.owner') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-md">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-amber-700">Event Owner</p>
                    <h2 class="mt-3 text-2xl font-black text-slate-950">Manage events</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">For organizers who create event pages, update details, and review bookings.</p>
                    <span class="mt-6 inline-flex min-h-11 items-center justify-center rounded-md bg-amber-400 px-5 text-sm font-black text-slate-950">Owner login</span>
                </a>
            </div>
        </div>
    </main>
</body>
</html>
