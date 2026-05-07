<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[0.95fr_1.05fr]">
        <section class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8">
            <div class="w-full max-w-md">
                <div class="mb-9 flex items-center justify-between">
                    <a href="{{ url('/') }}" class="text-lg font-black tracking-wide text-slate-950">Refined Travel</a>
                    <a href="{{ url('/') }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-800">Back home</a>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-emerald-700">Register</p>
                        <h2 class="mt-2 text-3xl font-black text-slate-950">Create your account</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Join Refined Travel to save favorite stays and collect trip ideas.
                        </p>
                    </div>

                    <form method="POST" action="#" class="mt-8 space-y-5">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-800">Full name</label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                autocomplete="name"
                                required
                                placeholder="Your name"
                                class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-800">Email address</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="email"
                                required
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
                                autocomplete="new-password"
                                required
                                placeholder="Create a password"
                                class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-slate-800">Confirm password</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                required
                                placeholder="Repeat your password"
                                class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                        </div>

                        <label class="flex items-start gap-3 text-sm font-semibold leading-6 text-slate-700">
                            <input type="checkbox" name="terms" required class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600">
                            I agree to receive travel updates and account emails.
                        </label>

                        <button
                            type="submit"
                            class="min-h-12 w-full rounded-md bg-emerald-700 px-5 text-sm font-black text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-300"
                        >
                            Create account
                        </button>
                    </form>

                    <p class="mt-7 text-center text-sm font-semibold text-slate-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-black text-emerald-700 hover:text-emerald-800">Sign in</a>
                    </p>
                </div>
            </div>
        </section>

        <section class="relative hidden overflow-hidden lg:block">
            <img
                src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1600&q=85"
                alt="Elegant hotel terrace with ocean view"
                class="absolute inset-0 h-full w-full object-cover"
            >
            <div class="absolute inset-0 bg-slate-950/45"></div>
            <div class="relative z-10 flex h-full flex-col justify-between p-10 text-white">
                <a href="{{ url('/') }}" class="text-lg font-black tracking-wide">Refined Travel</a>
                <div class="max-w-xl">
                    <p class="text-sm font-black uppercase tracking-[0.28em] text-amber-300">Start exploring</p>
                    <h1 class="mt-4 text-5xl font-black leading-tight">Keep every trip idea close at hand.</h1>
                    <p class="mt-5 text-base leading-7 text-white/82">
                        Build shortlists, save local guides, and return whenever inspiration strikes.
                    </p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
