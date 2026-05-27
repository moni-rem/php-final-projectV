<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard | Refined Travel</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    @php
        $eventCount = count($events);
        $bookingCount = count($bookings);
        $ticketCount = collect($bookings)->sum(fn ($booking) => (int) ($booking['quantity'] ?? 0));
        $users = $users ?? collect();
        $roles = $roles ?? collect();
        $paymentReviews = $paymentReviews ?? collect();
        $ownerRequests = $ownerRequests ?? collect();
        $userSearch = $userSearch ?? '';
        $adminUser = $adminUser ?? null;
        $adminName = $adminUser?->full_name ?: $adminUser?->name ?: 'Admin';
        $adminEmail = $adminUser?->email ?: 'Not signed in';
        $adminInitials = collect(explode(' ', trim($adminName)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('') ?: 'A';
        $revenue = collect($bookings)->sum(function ($booking) {
            $priceText = (string) ($booking['event_price'] ?? 0);
            $price = (float) str_replace(['$', ',', 'Riels', 'KHR', '៛'], '', $priceText);

            if (! str_contains($priceText, 'Riels') && ! str_contains($priceText, 'KHR') && ! str_contains($priceText, '៛')) {
                $price *= 4100;
            }

            return $price * (int) ($booking['quantity'] ?? 0);
        });
    @endphp

    <div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
        <aside class="border-b border-slate-200 bg-white lg:min-h-screen lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between px-5 py-5 lg:block lg:px-6">
                <a href="{{ url('/') }}" class="text-lg font-black tracking-wide text-slate-950">Refined Travel</a>
                <a href="{{ route('bookings.history') }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-800 lg:hidden">Booking history</a>
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
                <a href="{{ route('admin.dashboard') }}" class="flex min-h-11 shrink-0 items-center rounded-md bg-slate-950 px-4 text-white">Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Manage users</a>
                <a href="{{ route('admin.events.index') }}" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Manage events</a>
                <a href="{{ route('owner.dashboard') }}" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Owner view</a>
                <a href="{{ url('/') }}#stays" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Website events</a>
                <a href="{{ route('bookings.history') }}" class="flex min-h-11 shrink-0 items-center rounded-md px-4 text-slate-600 hover:bg-stone-100 hover:text-slate-950">Bookings</a>
            </nav>

            <div class="hidden px-6 py-6 lg:block">
                <div class="rounded-lg bg-emerald-50 p-5 ring-1 ring-emerald-100">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-700">Admin Account</p>
                    <p class="mt-2 text-sm font-bold text-slate-700">Review users, events, and ticket activity from one dashboard.</p>
                </div>
            </div>
        </aside>

        <main class="px-5 py-8 sm:px-8 lg:px-10">
            <header class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-emerald-700">Admin Dashboard</p>
                    <h1 class="mt-3 text-4xl font-black leading-tight sm:text-5xl">Platform overview</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                        Monitor events, ticket bookings, users, and revenue across the website. Signed in as {{ $adminName }}.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-800 hover:bg-white">Manage users</a>
                    <a href="{{ route('admin.events.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-md bg-emerald-700 px-4 text-sm font-black text-white hover:bg-emerald-800">Add event</a>
                    <a href="{{ url('/') }}" class="inline-flex min-h-11 items-center justify-center rounded-md bg-amber-400 px-4 text-sm font-black text-slate-950 hover:bg-amber-300">View site</a>
                </div>
            </header>

            @if (session('success'))
                <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">
                    Please check the admin form and try again.
                </div>
            @endif

            <section class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Users</p>
                    <p class="mt-2 text-3xl font-black">{{ number_format($usersCount) }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-500">{{ number_format($ownerRequests->count()) }} owner request(s)</p>
                </article>
                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Events</p>
                    <p class="mt-2 text-3xl font-black">{{ number_format($eventCount) }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-500">Published displays</p>
                </article>
                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Bookings</p>
                    <p class="mt-2 text-3xl font-black">{{ number_format($bookingCount) }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-500">{{ number_format($ticketCount) }} ticket(s) booked</p>
                </article>
                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Revenue</p>
                    <p class="mt-2 text-3xl font-black">{{ number_format($revenue, 0) }} Riels</p>
                    <p class="mt-2 text-sm font-semibold text-slate-500">From saved bookings</p>
                </article>
            </section>

            <section class="mt-8 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-black">Payment verification</h2>
                        <p class="mt-1 text-sm font-semibold text-slate-500">Approve KHQR payment proof before users receive valid ticket codes.</p>
                    </div>
                    <span class="inline-flex w-fit rounded-md bg-amber-100 px-3 py-2 text-xs font-black uppercase tracking-[0.14em] text-amber-800">
                        {{ $paymentReviews->count() }} pending
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-stone-100 text-xs font-black uppercase tracking-[0.16em] text-slate-500">
                            <tr>
                                <th scope="col" class="px-5 py-4">Booking</th>
                                <th scope="col" class="px-5 py-4">Customer</th>
                                <th scope="col" class="px-5 py-4">Payment</th>
                                <th scope="col" class="px-5 py-4">Proof</th>
                                <th scope="col" class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($paymentReviews as $review)
                                <tr class="align-top hover:bg-stone-50">
                                    <td class="min-w-[220px] px-5 py-4">
                                        <p class="font-black text-slate-950">{{ $review->booking_code }}</p>
                                        <p class="mt-1 text-xs font-semibold text-slate-500">{{ $review->event?->title ?? 'Unknown event' }}</p>
                                        <p class="mt-1 text-xs font-semibold text-slate-500">{{ $review->booking_date?->format('M d, Y h:i A') }}</p>
                                    </td>
                                    <td class="min-w-[220px] px-5 py-4">
                                        <p class="font-bold text-slate-800">{{ $review->user?->full_name ?: $review->user?->name ?: 'Guest User' }}</p>
                                        <p class="mt-1 text-xs font-semibold text-slate-500">{{ $review->user?->email ?? 'No email' }}</p>
                                        <p class="mt-1 text-xs font-semibold text-slate-500">{{ $review->user?->phone ?? 'No phone' }}</p>
                                    </td>
                                    <td class="min-w-[220px] px-5 py-4">
                                        <p class="font-black text-slate-950">
                                            {{ $review->payment?->currency === 'KHR'
                                                ? number_format((float) $review->payment?->paid_amount, 0) . ' KHR'
                                                : '$' . number_format((float) $review->payment?->paid_amount, 2) }}
                                        </p>
                                        <p class="mt-1 break-words text-xs font-semibold text-slate-500">Ref: {{ $review->payment?->transaction_reference ?? 'Missing' }}</p>
                                        <p class="mt-1 text-xs font-black uppercase tracking-[0.12em] text-amber-700">{{ $review->payment?->status?->status_name ?? 'review' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        @if ($review->payment?->payment_proof)
                                            <button
                                                type="button"
                                                data-proof-open
                                                data-proof-url="{{ route('admin.payments.proof', $review) }}"
                                                data-proof-title="{{ $review->booking_code }} payment proof"
                                                class="inline-flex min-h-10 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-black text-slate-800 hover:bg-white">
                                                View proof
                                            </button>
                                        @else
                                            <span class="text-sm font-bold text-slate-400">No file</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.payments.approve', $review) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-md bg-emerald-700 px-4 text-sm font-black text-white hover:bg-emerald-800">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.payments.reject', $review) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-md border border-red-200 px-4 text-sm font-black text-red-700 hover:bg-red-50">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-sm font-bold text-slate-500">No payments waiting for review.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="mt-8 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-black">Owner permission requests</h2>
                        <p class="mt-1 text-sm font-semibold text-slate-500">Approve users who requested access to create and manage events.</p>
                    </div>
                    <span class="inline-flex w-fit rounded-md bg-amber-100 px-3 py-2 text-xs font-black uppercase tracking-[0.14em] text-amber-800">
                        {{ $ownerRequests->count() }} pending
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-stone-100 text-xs font-black uppercase tracking-[0.16em] text-slate-500">
                            <tr>
                                <th scope="col" class="px-5 py-4">Account</th>
                                <th scope="col" class="px-5 py-4">Requested</th>
                                <th scope="col" class="px-5 py-4">Activity</th>
                                <th scope="col" class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($ownerRequests as $requestUser)
                                <tr class="align-top hover:bg-stone-50">
                                    <td class="min-w-[240px] px-5 py-4">
                                        <p class="font-black text-slate-950">{{ $requestUser->full_name ?: $requestUser->name }}</p>
                                        <p class="mt-1 text-xs font-semibold text-slate-500">{{ $requestUser->email }}</p>
                                        <p class="mt-1 text-xs font-semibold text-slate-500">{{ $requestUser->phone ?? 'No phone' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <p class="font-bold text-slate-800">{{ $requestUser->owner_requested_at?->format('M d, Y h:i A') }}</p>
                                        <p class="mt-1 text-xs font-black uppercase tracking-[0.12em] text-amber-700">{{ $requestUser->role?->role_name ?? 'user' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-600">
                                        {{ $requestUser->bookings_count }} booking(s) / {{ $requestUser->organized_events_count }} event(s)
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.owner-requests.approve', $requestUser) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-md bg-emerald-700 px-4 text-sm font-black text-white hover:bg-emerald-800">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.owner-requests.reject', $requestUser) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-md border border-red-200 px-4 text-sm font-black text-red-700 hover:bg-red-50">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-sm font-bold text-slate-500">No owner permissions waiting for review.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="mt-8 rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-black">User management</h2>
                        <p class="mt-1 text-sm font-semibold text-slate-500">Open the sidebar Manage users page to search, add, update, and delete accounts.</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 w-fit items-center justify-center rounded-md bg-slate-950 px-4 text-sm font-black text-white hover:bg-slate-800">Manage users</a>
                </div>
            </section>

            <section class="mt-4 rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-black">Event management</h2>
                        <p class="mt-1 text-sm font-semibold text-slate-500">Open the sidebar Manage events page to create, update, and delete event records.</p>
                    </div>
                    <a href="{{ route('admin.events.index') }}" class="inline-flex min-h-11 w-fit items-center justify-center rounded-md bg-emerald-700 px-4 text-sm font-black text-white hover:bg-emerald-800">Manage events</a>
                </div>
            </section>

            <section class="mt-8 grid gap-6 xl:grid-cols-[1fr_460px]">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-4 xl:flex-row xl:items-center xl:justify-between">
                        <div>
                            <h2 class="text-xl font-black">Manage users</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">Search, add, update, and delete user accounts.</p>
                        </div>
                        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex w-full flex-col gap-2 sm:flex-row xl:max-w-md">
                            <input
                                name="user_search"
                                value="{{ $userSearch }}"
                                placeholder="Search name, email, phone, role"
                                class="min-h-11 flex-1 rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-950 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            >
                            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-md bg-slate-950 px-4 text-sm font-black text-white hover:bg-slate-800">Search</button>
                            @if ($userSearch !== '')
                                <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-800 hover:bg-stone-50">Clear</a>
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
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <h2 class="text-xl font-black">Sales point</h2>
                        <p class="mt-1 text-sm font-semibold text-slate-500">Ticket sales from every booking transaction.</p>
                    </div>

                    <div class="max-h-[520px] overflow-y-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="sticky top-0 bg-stone-100 text-xs font-black uppercase tracking-[0.16em] text-slate-500">
                                <tr>
                                    <th scope="col" class="px-5 py-4">Sale</th>
                                    <th scope="col" class="px-5 py-4">Tickets</th>
                                    <th scope="col" class="px-5 py-4 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($bookings as $booking)
                                    @php
                                        $priceText = (string) ($booking['event_price'] ?? 0);
                                        $unitPrice = (float) str_replace(['$', ',', 'Riels', 'KHR', '៛'], '', $priceText);

                                        if (! str_contains($priceText, 'Riels') && ! str_contains($priceText, 'KHR') && ! str_contains($priceText, '៛')) {
                                            $unitPrice *= 4100;
                                        }

                                        $lineTotal = $unitPrice * (int) ($booking['quantity'] ?? 0);
                                    @endphp
                                    <tr class="hover:bg-stone-50">
                                        <td class="min-w-[220px] px-5 py-4">
                                            <p class="font-black text-slate-950">{{ $booking['code'] }}</p>
                                            <p class="mt-1 truncate text-xs font-semibold text-slate-500">{{ $booking['event_title'] }}</p>
                                            <p class="mt-1 text-xs font-semibold text-slate-500">{{ $booking['booked_at'] }}</p>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 font-black text-slate-950">{{ $booking['quantity'] }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right font-black text-slate-950">{{ number_format($lineTotal, 0) }} Riels</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-10 text-center text-sm font-bold text-slate-500">No sales yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="mt-8 grid gap-6 xl:grid-cols-[1fr_420px]">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="flex flex-col gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-black">Recent bookings</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">Latest customer ticket activity.</p>
                        </div>
                        <a href="{{ route('bookings.history') }}" class="inline-flex min-h-10 w-fit items-center justify-center rounded-md border border-slate-300 px-4 text-sm font-bold text-slate-800 hover:bg-stone-50">View all</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-stone-100 text-xs font-black uppercase tracking-[0.16em] text-slate-500">
                                <tr>
                                    <th scope="col" class="px-5 py-4">Booking</th>
                                    <th scope="col" class="px-5 py-4">Customer</th>
                                    <th scope="col" class="px-5 py-4">Event</th>
                                    <th scope="col" class="px-5 py-4">Tickets</th>
                                    <th scope="col" class="px-5 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse (array_slice($bookings, 0, 6) as $booking)
                                    <tr class="hover:bg-stone-50">
                                        <td class="whitespace-nowrap px-5 py-4 font-black text-slate-950">{{ $booking['code'] }}</td>
                                        <td class="min-w-[180px] px-5 py-4">
                                            <p class="font-bold text-slate-800">{{ trim(($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? '')) ?: 'Guest User' }}</p>
                                            <p class="mt-1 text-xs font-semibold text-slate-500">{{ $booking['email'] ?? 'No email' }}</p>
                                        </td>
                                        <td class="min-w-[220px] px-5 py-4">
                                            <p class="font-bold text-slate-800">{{ $booking['event_title'] }}</p>
                                            <p class="mt-1 text-xs font-semibold text-slate-500">{{ $booking['event_date'] }} / {{ $booking['event_location'] }}</p>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 font-black text-slate-950">{{ $booking['quantity'] }}</td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="inline-flex rounded-md bg-amber-50 px-3 py-1.5 text-xs font-black uppercase tracking-[0.14em] text-amber-700">
                                                {{ $booking['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-10 text-center text-sm font-bold text-slate-500">No bookings yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-black">Events snapshot</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">Quick access to event pages.</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse (array_slice($events, 0, 5, true) as $slug => $event)
                            <article class="flex gap-4 rounded-lg border border-slate-200 p-3">
                                <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}" class="h-20 w-24 rounded-md object-cover">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-black text-slate-950">{{ $event['title'] }}</p>
                                    <p class="mt-1 text-xs font-bold uppercase tracking-[0.14em] text-emerald-700">{{ $event['category'] }}</p>
                                    <p class="mt-1 truncate text-sm font-semibold text-slate-500">{{ $event['date'] }} / {{ $event['location'] }}</p>
                                    <a href="{{ route('events.show', $slug) }}" class="mt-3 inline-flex min-h-9 items-center justify-center rounded-md bg-stone-100 px-3 text-xs font-black text-slate-800 hover:bg-stone-200">Open event</a>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-lg border border-dashed border-slate-300 p-6 text-center">
                                <p class="text-sm font-bold text-slate-500">No events yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="mt-8 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-200 px-5 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-black">All events</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">Admin overview of every event displayed on the website.</p>
                        </div>
                        <a href="{{ route('admin.events.index') }}" class="inline-flex min-h-10 w-fit items-center justify-center rounded-md bg-emerald-700 px-4 text-sm font-black text-white hover:bg-emerald-800">Manage events</a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-stone-100 text-xs font-black uppercase tracking-[0.16em] text-slate-500">
                            <tr>
                                <th scope="col" class="px-5 py-4">Event</th>
                                <th scope="col" class="px-5 py-4">Category</th>
                                <th scope="col" class="px-5 py-4">Date</th>
                                <th scope="col" class="px-5 py-4">Location</th>
                                <th scope="col" class="px-5 py-4">Price</th>
                                <th scope="col" class="px-5 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($events as $slug => $event)
                                <tr class="hover:bg-stone-50">
                                    <td class="min-w-[260px] px-5 py-4">
                                        <div class="flex items-center gap-4">
                                            <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}" class="h-14 w-20 rounded-md object-cover ring-1 ring-slate-200">
                                            <p class="font-black text-slate-950">{{ $event['title'] }}</p>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 font-bold text-slate-700">{{ $event['category'] }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 font-bold text-slate-700">{{ $event['date'] }}</td>
                                    <td class="min-w-[180px] px-5 py-4 font-bold text-slate-700">{{ $event['location'] }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 font-black text-slate-950">{{ $event['price'] }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <a href="{{ route('events.show', $slug) }}" class="inline-flex min-h-10 items-center justify-center rounded-md bg-amber-400 px-4 text-sm font-black text-slate-950 hover:bg-amber-300">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-sm font-bold text-slate-500">No events found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4 py-6" data-proof-modal>
        <div class="flex h-[82vh] w-full max-w-3xl flex-col overflow-hidden rounded-lg bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Payment proof</p>
                    <h2 class="mt-1 text-lg font-black text-slate-950" data-proof-title>Proof preview</h2>
                </div>
                <button type="button" data-proof-close class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-300 text-slate-700 hover:bg-stone-50" aria-label="Close proof preview">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <iframe data-proof-frame title="Payment proof preview" class="min-h-0 flex-1 bg-stone-100"></iframe>

            <div class="flex justify-end border-t border-slate-200 px-4 py-3">
                <a href="#" target="_blank" data-proof-new-tab class="inline-flex min-h-10 items-center justify-center rounded-md bg-slate-950 px-4 text-sm font-black text-white hover:bg-slate-800">
                    Open full page
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.querySelector('[data-proof-modal]');
            const frame = document.querySelector('[data-proof-frame]');
            const title = document.querySelector('[data-proof-title]');
            const newTab = document.querySelector('[data-proof-new-tab]');

            const closeProof = () => {
                if (! modal) {
                    return;
                }

                modal.classList.add('hidden');
                modal.classList.remove('flex');

                if (frame) {
                    frame.removeAttribute('src');
                }
            };

            document.querySelectorAll('[data-proof-open]').forEach((button) => {
                button.addEventListener('click', () => {
                    const url = button.dataset.proofUrl;

                    if (! url || ! modal) {
                        return;
                    }

                    if (title) {
                        title.textContent = button.dataset.proofTitle || 'Proof preview';
                    }

                    if (frame) {
                        frame.src = url;
                    }

                    if (newTab) {
                        newTab.href = url;
                    }

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            });

            document.querySelector('[data-proof-close]')?.addEventListener('click', closeProof);
            modal?.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeProof();
                }
            });
        });
    </script>
</body>
</html>
