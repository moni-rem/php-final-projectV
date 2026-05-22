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
                            Register as a user, or request event owner access for admin confirmation.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mt-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                            Please check the form and try again.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-5">
                        @csrf

                        <fieldset>
                            <legend class="block text-sm font-bold text-slate-800">Register as</legend>
                            <div class="mt-2 grid gap-3 sm:grid-cols-2">
                                <label class="flex cursor-pointer items-center gap-3 rounded-md border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-800 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-800">
                                    <input
                                        type="radio"
                                        name="requested_role"
                                        value="user"
                                        required
                                        @checked(old('requested_role', 'user') === 'user')
                                        class="h-4 w-4 border-slate-300 text-emerald-700 focus:ring-emerald-600"
                                    >
                                    User
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-md border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-800 transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-800">
                                    <input
                                        type="radio"
                                        name="requested_role"
                                        value="event_owner"
                                        required
                                        @checked(old('requested_role') === 'event_owner')
                                        class="h-4 w-4 border-slate-300 text-emerald-700 focus:ring-emerald-600"
                                    >
                                    Event owner
                                </label>
                            </div>
                            <p class="mt-2 text-xs font-bold text-slate-500">Event owner accounts need admin confirmation before they can manage events.</p>
                            @error('requested_role')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-800">Full name</label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                autocomplete="name"
                                required
                                placeholder="Your name"
                                value="{{ old('name') }}"
                                class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                            @error('name')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
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
                                value="{{ old('email') }}"
                                class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                            @error('email')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-bold text-slate-800">Phone number</label>
                            <input
                                id="phone"
                                name="phone"
                                type="tel"
                                autocomplete="tel"
                                placeholder="+855 12 345 678"
                                value="{{ old('phone') }}"
                                class="mt-2 min-h-12 w-full rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                            @error('phone')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
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
                            @error('password')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
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
                            I agree to create an account for this website.
                        </label>
                        @error('terms')
                            <p class="-mt-3 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror

                        <button
                            type="submit"
                            class="min-h-12 w-full rounded-md bg-emerald-700 px-5 text-sm font-black text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-300"
                        >
                            Register
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
                    <p class="text-sm font-black uppercase tracking-[0.28em] text-amber-300">Join the platform</p>
                    <h1 class="mt-4 text-5xl font-black leading-tight">Book events or request to host them.</h1>
                    <p class="mt-5 text-base leading-7 text-white/82">
                        User accounts are ready right away. Event owner access starts after admin confirmation.
                    </p>
                </div>
            </div>
        </section>
    </main>

    @include('partials.footer')
</body>
</html>
