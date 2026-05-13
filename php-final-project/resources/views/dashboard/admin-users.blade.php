<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Users | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    @php
        $users = $users ?? collect();
        $roles = $roles ?? collect();
        $userSearch = $userSearch ?? '';
        $adminUser = $adminUser ?? null;
        $adminName = $adminUser?->full_name ?: $adminUser?->name ?: 'Admin';
        $adminEmail = $adminUser?->email ?: 'Not signed in';
        $adminInitials = collect(explode(' ', trim($adminName)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('') ?: 'A';
    @endphp

    <div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
        <aside class="border-b border-slate-200 bg-white lg:min-h-screen lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between px-5 py-5 lg:block lg:px-6">
                <a href="{{ url('/') }}" class="text-lg font-black tracking-wide text-slate-950">Refined Travel</a>
                <a href="{{ route('login') }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-800 lg:hidden">Switch role</a>
            </div>

            <div class="px-5 pb-5 lg:px-4">
                <div class="flex items-center gap-3 rounded-lg bg-slate-950 p-4 text-white">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md bg-amber-400 text-sm font-black text-slate-950">
                        {{ $adminInitials }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-black">{{ $adminName }}</p>
                        <p class="truncate text-xs font-semibold text-white/70">{{ $adminEmail }}</p>
                        <p class="mt-1 text-xs font-black uppercase tracking-[0.14em] text-amber-300">{{ $adminUser?->role?->role_name ?? 'admin' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="flex min-h-10 w-full items-center justify-center rounded-md border border-slate-300 bg-white px-4 text-sm font-black text-slate-800 hover:bg-stone-100">
                        Logout
                    </button>
                </form>
            </div>

            <nav class="flex gap-2 overflow-x-auto px-5 pb-5 text-sm font-bold lg:block lg:space-y-2 lg:px-4">
                <a href="{{ route('admin.dashboard') }}" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="flex min-h-11 shrink-0 items-center rounded-md bg-slate-950 px-4 text-white">Manage users</a>
                <a href="{{ route('admin.events.index') }}" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Manage events</a>
                <a href="{{ route('owner.dashboard') }}" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Owner view</a>
                <a href="{{ url('/') }}#stays" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Website events</a>
                <a href="{{ route('bookings.history') }}" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Bookings</a>
            </nav>

            <div class="hidden px-6 py-6 lg:block">
                <div class="rounded-lg bg-emerald-50 p-5 ring-1 ring-emerald-100">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">Users</p>
                    <p class="mt-2 text-sm font-bold text-slate-700">{{ number_format($usersCount) }} total account(s) in the system.</p>
                </div>
            </div>
        </aside>

        <main class="px-5 py-8 sm:px-8 lg:px-10">
            <header class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-700">Admin</p>
                    <h1 class="mt-3 text-4xl font-black leading-tight sm:text-5xl">Manage users</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                        Search, add, update, and delete user accounts from one admin workspace.
                    </p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-11 w-fit items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-800 hover:bg-white">Back to dashboard</a>
            </header>

            @if (session('success'))
                <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="mt-8 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-4 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <h2 class="text-xl font-black">User accounts</h2>
                        <p class="mt-1 text-sm font-semibold text-slate-500">Use the sidebar to open this user-management page any time.</p>
                    </div>
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex w-full flex-col gap-2 sm:flex-row xl:max-w-md">
                        <input
                            name="user_search"
                            value="{{ $userSearch }}"
                            placeholder="Search name, email, phone, role"
                            class="min-h-11 flex-1 rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                        >
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-md bg-slate-950 px-4 text-sm font-black text-white hover:bg-slate-800">Search</button>
                        @if ($userSearch !== '')
                            <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-800 hover:bg-stone-50">Clear</a>
                        @endif
                    </form>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}" class="grid gap-3 border-b border-slate-200 bg-stone-50 px-5 py-4 md:grid-cols-2 xl:grid-cols-[1fr_1.25fr_0.85fr_0.85fr_0.8fr_auto]">
                    @csrf
                    <input name="name" value="{{ old('name') }}" required placeholder="Full name" class="min-h-11 rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    <input name="email" type="email" value="{{ old('email') }}" required placeholder="Email" class="min-h-11 rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    <input name="phone" value="{{ old('phone') }}" placeholder="Phone" class="min-h-11 rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    <input name="password" type="password" required placeholder="Password" class="min-h-11 rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                    <select name="role_id" required class="min-h-11 rounded-md border border-slate-300 bg-white px-3 text-sm font-bold text-slate-800 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-md bg-emerald-700 px-4 text-sm font-black text-white hover:bg-emerald-800">Add user</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-stone-100 text-xs font-black uppercase tracking-[0.16em] text-slate-500">
                            <tr>
                                <th scope="col" class="px-5 py-4">Account</th>
                                <th scope="col" class="px-5 py-4">Role</th>
                                <th scope="col" class="px-5 py-4">Activity</th>
                                <th scope="col" class="px-5 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($users as $user)
                                <tr class="align-middle hover:bg-stone-50">
                                    <td class="min-w-[420px] px-5 py-4">
                                        <form id="user-update-{{ $user->id }}" method="POST" action="{{ route('admin.users.update', $user) }}" class="grid gap-2 md:grid-cols-2">
                                            @csrf
                                            @method('PATCH')
                                            <input name="name" value="{{ old('name', $user->full_name ?: $user->name) }}" required class="min-h-10 rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            <input name="email" type="email" value="{{ old('email', $user->email) }}" required class="min-h-10 rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Phone" class="min-h-10 rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            <input name="password" type="password" placeholder="New password optional" class="min-h-10 rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                        </form>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <select form="user-update-{{ $user->id }}" name="role_id" class="min-h-10 rounded-md border border-slate-300 bg-white px-3 text-sm font-bold text-slate-800 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" @selected($user->role_id === $role->id)>{{ $role->role_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-600">
                                        {{ $user->bookings_count }} booking(s) / {{ $user->organized_events_count }} event(s)
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button form="user-update-{{ $user->id }}" type="submit" class="inline-flex min-h-10 items-center justify-center rounded-md bg-slate-950 px-4 text-sm font-black text-white hover:bg-slate-800">Update</button>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-md border border-red-200 px-4 text-sm font-black text-red-700 hover:bg-red-50">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-sm font-bold text-slate-500">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
