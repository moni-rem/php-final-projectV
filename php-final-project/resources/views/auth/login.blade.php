<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[0.92fr_1.08fr]">
        <section class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8">
            <div class="w-full max-w-md">
                <div class="mb-9 flex items-center justify-between">
                    <a href="{{ url('/') }}" class="text-lg font-black tracking-wide text-slate-950">Refined Travel</a>
                    <a href="{{ url('/') }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-800">Back home</a>
                </div>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">Account Login</p>
                        <h1 class="mt-2 text-3xl font-black leading-tight text-slate-950">Sign in to your dashboard</h1>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Use one email and password. The system will open the correct dashboard for your account role.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mt-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mt-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-800">Email address</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="email"
                                required
                                value="{{ old('email') }}"
                                placeholder="you@example.com"
                                class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-800">Password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                placeholder="Enter your password"
                                class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                        </div>

                        <button
                            type="submit"
                            class="min-h-12 w-full rounded-md bg-emerald-700 px-5 text-sm font-black text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-300"
                        >
                            Sign in
                        </button>
                    </form>

                    {{-- <div class="mt-7 rounded-md bg-stone-50 p-4 text-sm font-semibold leading-6 text-slate-600 ring-1 ring-slate-200">
                        <p class="font-black text-slate-950">Demo accounts</p>
                        <p class="mt-1">Admin: admin@example.com / password</p>
                        <p>Owner: owner@example.com / password</p>
                        <p>User: user@example.com / password</p>
                    </div> --}}

                    <p class="mt-7 text-center text-sm font-semibold text-slate-600">
                        Need an admin account?
                        <a href="{{ route('register') }}" class="font-black text-emerald-700 hover:text-emerald-800">Register</a>
                    </p>
                </section>
            </div>
        </section>

        <section class="relative hidden overflow-hidden lg:block">
            <img
                src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1600&q=85"
                alt="People working at an event table"
                class="absolute inset-0 h-full w-full object-cover"
            >
            <div class="absolute inset-0 bg-slate-950/50"></div>
            <div class="relative z-10 flex h-full flex-col justify-between p-10 text-white">
                <a href="{{ url('/') }}" class="text-lg font-black tracking-wide">Refined Travel</a>
                <div class="max-w-xl">
                    <p class="text-sm font-black uppercase tracking-[0.28em] text-amber-300">One login</p>
                    <h2 class="mt-4 text-5xl font-black leading-tight">Your role decides where you go next.</h2>
                    <p class="mt-5 text-base leading-7 text-white/82">
                        Customers manage tickets, owners manage events, and admins review the whole platform from the same sign-in page.
                    </p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
