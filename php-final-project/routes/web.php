<?php

use App\Models\Booking;
use App\Models\BookingStatus;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventStatus;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

$eventToView = function (Event $event): array {
    $event->loadMissing('category');

    $price = (float) $event->ticket_price;

    return [
        'title' => $event->title,
        'category' => $event->category?->category_name ?? 'Event',
        'date' => $event->event_date?->format('m/d/Y'),
        'startTime' => $event->start_time ? date('g:ia', strtotime($event->start_time)) : null,
        'endTime' => $event->end_time ? date('g:ia', strtotime($event->end_time)) : null,
        'location' => $event->location,
        'price' => $price > 0 ? '$' . number_format($price, 2) : 'Free',
        'ticketPrice' => $price,
        'image' => $event->image,
        'image2' => $event->image2 ?: $event->image,
        'image3' => $event->image3 ?: $event->image,
        'description' => $event->description,
        'about' => $event->about ? preg_split("/\r\n\r\n|\n\n|\r\r/", $event->about) : [$event->description],
        'whatToExpect' => $event->what_to_expect ?? [],
        'importantInformation' => $event->important_information ?? [],
        'mapUrl' => $event->map_url,
    ];
};

$bookingToView = function (Booking $booking): array {
    $booking->loadMissing(['event', 'status', 'payment', 'user']);

    $event = $booking->event;
    $user = $booking->user;
    $fullName = trim($user?->full_name ?: $user?->name ?: 'Guest User');
    $nameParts = preg_split('/\s+/', $fullName, 2);
    $payment = $booking->payment;
    $currency = $payment?->currency ?? 'USD';
    $formattedPrice = $currency === 'KHR'
        ? number_format((float) $booking->unit_price, 0) . ' ៛'
        : '$' . number_format((float) $booking->unit_price, 2);

    return [
        'code' => $booking->booking_code,
        'event_slug' => $event->slug,
        'event_title' => $event->title,
        'event_image' => $event->image,
        'event_date' => $event->event_date?->format('m/d/Y'),
        'event_time' => trim(
            ($event->start_time ? date('g:ia', strtotime($event->start_time)) : '')
            . ' - '
            . ($event->end_time ? date('g:ia', strtotime($event->end_time)) : ''),
            ' -',
        ),
        'event_location' => $event->location,
        'event_price' => $formattedPrice,
        'first_name' => $nameParts[0] ?? $fullName,
        'last_name' => $nameParts[1] ?? '',
        'email' => $user?->email,
        'phone' => $user?->phone,
        'ticket_type' => $booking->ticket_type ?? 'Standard',
        'quantity' => $booking->quantity,
        'payment_reference' => $booking->payment?->transaction_reference ?? 'Pending',
        'payment_proof_name' => $booking->payment?->payment_proof ? basename($booking->payment->payment_proof) : null,
        'status' => $booking->status?->status_name ?? 'pending',
        'booked_at' => $booking->booking_date?->format('M d, Y h:i A'),
    ];
};

$allEventViews = fn () => Event::with('category')
    ->orderBy('event_date')
    ->get()
    ->mapWithKeys(fn (Event $event) => [$event->slug => $eventToView($event)])
    ->all();

$allBookingViews = fn () => Booking::with(['event', 'status', 'payment', 'user'])
    ->latest('booking_date')
    ->get()
    ->map(fn (Booking $booking) => $bookingToView($booking))
    ->all();

$bookingViewsForUser = fn (User $user) => Booking::with(['event', 'status', 'payment', 'user'])
    ->where('user_id', $user->id)
    ->latest('booking_date')
    ->get()
    ->map(fn (Booking $booking) => $bookingToView($booking))
    ->all();

$requireRole = function (array $allowedRoles) {
    if (! Auth::check()) {
        return redirect()
            ->route('login')
            ->withErrors(['email' => 'Please sign in before opening that page.']);
    }

    $user = Auth::user()->loadMissing('role');

    if (! in_array($user->role?->role_name, $allowedRoles, true)) {
        abort(403, 'Your account is not approved for this area.');
    }

    return null;
};

$dashboardRouteForUser = fn (User $user) => match ($user->loadMissing('role')->role?->role_name) {
    'admin' => 'admin.dashboard',
    'event_owner' => 'owner.dashboard',
    default => 'user.dashboard',
};

$createPublishedEvent = function (Request $request, User $organizer): Event {
    $validated = $request->validate([
        'title' => ['required', 'string', 'max:200'],
        'category_name' => ['required', 'string', 'max:100'],
        'description' => ['required', 'string', 'max:3000'],
        'location' => ['required', 'string', 'max:255'],
        'event_date' => ['required', 'date'],
        'start_time' => ['required', 'date_format:H:i'],
        'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
        'total_seats' => ['required', 'integer', 'min:1'],
        'ticket_price' => ['required', 'numeric', 'min:0'],
        'image' => ['nullable', 'url', 'max:255'],
        'image2' => ['nullable', 'url', 'max:255'],
        'image3' => ['nullable', 'url', 'max:255'],
        'map_url' => ['nullable', 'url'],
        'what_to_expect' => ['nullable', 'string', 'max:2000'],
        'important_information' => ['nullable', 'string', 'max:2000'],
    ]);

    $category = EventCategory::firstOrCreate(['category_name' => $validated['category_name']]);
    $status = EventStatus::firstOrCreate(['status_name' => 'published']);
    $slugBase = Str::slug($validated['title']);
    $slug = $slugBase;
    $counter = 2;

    while (Event::where('slug', $slug)->exists()) {
        $slug = $slugBase . '-' . $counter;
        $counter++;
    }

    $splitLines = fn (?string $value, array $fallback = []) => collect(preg_split('/\r\n|\r|\n/', $value ?? ''))
        ->map(fn (string $line) => trim($line))
        ->filter()
        ->values()
        ->all() ?: $fallback;

    return Event::create([
        'organizer_id' => $organizer->id,
        'category_id' => $category->id,
        'status_id' => $status->id,
        'slug' => $slug,
        'title' => $validated['title'],
        'description' => $validated['description'],
        'about' => $validated['description'],
        'what_to_expect' => $splitLines($validated['what_to_expect'] ?? null, ['Engaging activities', 'Friendly staff', 'Memorable experience']),
        'important_information' => $splitLines($validated['important_information'] ?? null, [
            'Please arrive 15-30 minutes early for check-in',
            'Booked tickets are non-refundable and cannot be returned or canceled',
        ]),
        'location' => $validated['location'],
        'event_date' => $validated['event_date'],
        'start_time' => $validated['start_time'],
        'end_time' => $validated['end_time'] ?? null,
        'total_seats' => $validated['total_seats'],
        'ticket_price' => $validated['ticket_price'],
        'image' => $validated['image'] ?? 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1600&q=85',
        'image2' => $validated['image2'] ?? null,
        'image3' => $validated['image3'] ?? null,
        'map_url' => $validated['map_url'] ?? null,
    ]);
};

Route::get('/', function (Request $request) use ($allEventViews) {
    $eventSearch = trim((string) $request->query('event_search', ''));
    $events = $allEventViews();

    if ($eventSearch !== '') {
        $events = collect($events)
            ->filter(function (array $event) use ($eventSearch) {
                $haystack = strtolower(implode(' ', [
                    $event['title'] ?? '',
                    $event['category'] ?? '',
                    $event['location'] ?? '',
                    $event['description'] ?? '',
                    $event['date'] ?? '',
                    $event['price'] ?? '',
                ]));

                return str_contains($haystack, strtolower($eventSearch));
            })
            ->all();
    }

    $events = array_slice($events, 0, 6, true);

    return view('welcome', [
        'events' => $events,
        'eventSearch' => $eventSearch,
        'totalEventsCount' => count($allEventViews()),
    ]);
});

Route::get('/events', function (Request $request) use ($allEventViews) {
    $eventSearch = trim((string) $request->query('event_search', ''));
    $events = $allEventViews();

    if ($eventSearch !== '') {
        $events = collect($events)
            ->filter(function (array $event) use ($eventSearch) {
                $haystack = strtolower(implode(' ', [
                    $event['title'] ?? '',
                    $event['category'] ?? '',
                    $event['location'] ?? '',
                    $event['description'] ?? '',
                    $event['date'] ?? '',
                    $event['price'] ?? '',
                ]));

                return str_contains($haystack, strtolower($eventSearch));
            })
            ->all();
    }

    return view('event.index', [
        'events' => $events,
        'eventSearch' => $eventSearch,
        'totalEventsCount' => count($allEventViews()),
    ]);
})->name('events.index');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route(match (Auth::user()->loadMissing('role')->role?->role_name) {
            'admin' => 'admin.dashboard',
            'event_owner' => 'owner.dashboard',
            default => 'user.dashboard',
        });
    }

    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) use ($dashboardRouteForUser) {
    $validated = $request->validate([
        'role' => ['required', Rule::in(['user', 'event_owner'])],
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $credentials = [
        'email' => $validated['email'],
        'password' => $validated['password'],
    ];

    if (! Auth::attempt($credentials)) {
        return back()
            ->withErrors(['email' => 'The email or password is incorrect.'])
            ->onlyInput('email', 'role');
    }

    $request->session()->regenerate();

    $user = Auth::user()->loadMissing('role');
    $roleName = $user->role?->role_name;

    if ($roleName !== 'admin' && $roleName !== $validated['role']) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $selectedRoleLabel = $validated['role'] === 'event_owner' ? 'an event owner' : 'a user';
        $roleError = $validated['role'] === 'event_owner'
            ? 'This account is not approved as an event owner yet. An admin must confirm it first.'
            : 'This account is not registered as ' . $selectedRoleLabel . '. Please choose the correct role.';

        return back()
            ->withErrors(['role' => $roleError])
            ->onlyInput('email', 'role');
    }

    return redirect()
        ->route($dashboardRouteForUser($user))
        ->with('success', 'Welcome back, ' . ($user->full_name ?: $user->name) . '.');
})->name('login.store');

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()
        ->route('login')
        ->with('success', 'You have been logged out.');
})->name('logout');

Route::get('/register', function () {
    if (Auth::check()) {
        return redirect()->route(match (Auth::user()->loadMissing('role')->role?->role_name) {
            'admin' => 'admin.dashboard',
            'event_owner' => 'owner.dashboard',
            default => 'user.dashboard',
        });
    }

    return view('auth.register');
})->name('register');

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'requested_role' => ['required', Rule::in(['user', 'event_owner'])],
        'name' => ['required', 'string', 'max:150'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'phone' => ['nullable', 'string', 'max:20'],
        'password' => ['required', 'string', 'min:6', 'confirmed'],
        'terms' => ['accepted'],
    ]);

    $userRole = Role::firstOrCreate(['role_name' => 'user']);
    $requestedOwner = $validated['requested_role'] === 'event_owner';

    $user = User::create([
        'name' => $validated['name'],
        'full_name' => $validated['name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'] ?? null,
        'password' => Hash::make($validated['password']),
        'role_id' => $userRole->id,
        'owner_requested_at' => $requestedOwner ? now() : null,
    ]);

    Auth::login($user);
    $request->session()->regenerate();

    return redirect()
        ->route('user.dashboard')
        ->with('success', $requestedOwner
            ? 'Account created. Your event owner request was sent to admin for confirmation.'
            : 'Account registered successfully.');
})->name('register.store');

Route::get('/dashboard/user', function () use ($bookingViewsForUser) {
    if (! Auth::check()) {
        return redirect()
            ->route('login')
            ->withErrors(['email' => 'Please sign in before opening your dashboard.']);
    }

    return view('dashboard.user', [
        'bookings' => $bookingViewsForUser(Auth::user()),
    ]);
})->name('user.dashboard');

Route::get('/dashboard/owner', function () use ($requireRole) {
    if ($response = $requireRole(['event_owner'])) {
        return $response;
    }

    $owner = Auth::user();

    return view('dashboard.owner', [
        'events' => Event::with(['category', 'status'])
            ->withCount('bookings')
            ->withSum('bookings as booked_tickets', 'quantity')
            ->withSum('bookings as gross_sales', 'total_price')
            ->where('organizer_id', $owner->id)
            ->orderByDesc('created_at')
            ->get(),
    ]);
})->name('owner.dashboard');

Route::get('/dashboard/owner/events/create', function () use ($requireRole) {
    if ($response = $requireRole(['event_owner'])) {
        return $response;
    }

    return view('dashboard.owner-create-event');
})->name('owner.events.create');

Route::post('/dashboard/owner/events', function (Request $request) use ($requireRole) {
    if ($response = $requireRole(['event_owner'])) {
        return $response;
    }

    $validated = $request->validate([
        'title' => ['required', 'string', 'max:200'],
        'category_name' => ['required', 'string', 'max:100'],
        'description' => ['required', 'string', 'max:3000'],
        'location' => ['required', 'string', 'max:255'],
        'event_date' => ['required', 'date'],
        'start_time' => ['required', 'date_format:H:i'],
        'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
        'total_seats' => ['required', 'integer', 'min:1'],
        'ticket_price' => ['required', 'numeric', 'min:0'],
        'image' => ['nullable', 'url', 'max:255'],
        'image2' => ['nullable', 'url', 'max:255'],
        'image3' => ['nullable', 'url', 'max:255'],
        'map_url' => ['nullable', 'url'],
        'what_to_expect' => ['nullable', 'string', 'max:2000'],
        'important_information' => ['nullable', 'string', 'max:2000'],
    ]);

    $owner = Auth::user();

    $category = EventCategory::firstOrCreate(['category_name' => $validated['category_name']]);
    $status = EventStatus::firstOrCreate(['status_name' => 'published']);
    $slugBase = Str::slug($validated['title']);
    $slug = $slugBase;
    $counter = 2;

    while (Event::where('slug', $slug)->exists()) {
        $slug = $slugBase . '-' . $counter;
        $counter++;
    }

    $splitLines = fn (?string $value, array $fallback = []) => collect(preg_split('/\r\n|\r|\n/', $value ?? ''))
        ->map(fn (string $line) => trim($line))
        ->filter()
        ->values()
        ->all() ?: $fallback;

    Event::create([
        'organizer_id' => $owner->id,
        'category_id' => $category->id,
        'status_id' => $status->id,
        'slug' => $slug,
        'title' => $validated['title'],
        'description' => $validated['description'],
        'about' => $validated['description'],
        'what_to_expect' => $splitLines($validated['what_to_expect'] ?? null, ['Engaging activities', 'Friendly staff', 'Memorable experience']),
        'important_information' => $splitLines($validated['important_information'] ?? null, [
            'Please arrive 15-30 minutes early for check-in',
            'Booked tickets are non-refundable and cannot be returned or canceled',
        ]),
        'location' => $validated['location'],
        'event_date' => $validated['event_date'],
        'start_time' => $validated['start_time'],
        'end_time' => $validated['end_time'] ?? null,
        'total_seats' => $validated['total_seats'],
        'ticket_price' => $validated['ticket_price'],
        'image' => $validated['image'] ?? 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1600&q=85',
        'image2' => $validated['image2'] ?? null,
        'image3' => $validated['image3'] ?? null,
        'map_url' => $validated['map_url'] ?? null,
    ]);

    return redirect()
        ->route('owner.dashboard')
        ->with('success', 'Event published to the main page.');
})->name('owner.events.store');

Route::get('/dashboard/owner/events/{event}', function (string $event) use ($requireRole) {
    if ($response = $requireRole(['event_owner'])) {
        return $response;
    }

    $owner = Auth::user();

    $eventModel = Event::with(['category', 'status'])
        ->withCount('bookings')
        ->withSum('bookings as booked_tickets', 'quantity')
        ->withSum('bookings as gross_sales', 'total_price')
        ->where('slug', $event)
        ->where('organizer_id', $owner->id)
        ->firstOrFail();

    return view('dashboard.owner-event-show', [
        'event' => $eventModel,
        'bookings' => $eventModel->bookings()
            ->with(['user', 'status', 'payment.status'])
            ->latest('booking_date')
            ->get(),
    ]);
})->name('owner.events.show');

Route::get('/dashboard/admin/events/create', function () use ($requireRole) {
    if ($response = $requireRole(['admin'])) {
        return $response;
    }

    return view('dashboard.owner-create-event', [
        'dashboardRoute' => 'admin.events.index',
        'formAction' => route('admin.events.store'),
        'sectionLabel' => 'Admin Event Management',
        'pageTitle' => 'Add event as admin',
        'submitLabel' => 'Publish event as admin',
    ]);
})->name('admin.events.create');

Route::post('/dashboard/admin/events', function (Request $request) use ($createPublishedEvent, $requireRole) {
    if ($response = $requireRole(['admin'])) {
        return $response;
    }

    $adminRole = Role::firstOrCreate(['role_name' => 'admin']);
    $admin = Auth::user() ?? User::firstOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'Admin User',
            'full_name' => 'Admin User',
            'password' => Hash::make('password'),
            'phone' => '+855 12 000 002',
            'role_id' => $adminRole->id,
        ],
    );

    $createPublishedEvent($request, $admin);

    return redirect()
        ->route('admin.events.index')
        ->with('success', 'Admin published a new event to the website.');
})->name('admin.events.store');

Route::patch('/dashboard/admin/events/{event}', function (Request $request, Event $event) use ($requireRole) {
    if ($response = $requireRole(['admin'])) {
        return $response;
    }

    $validated = $request->validate([
        'title' => ['required', 'string', 'max:200'],
        'category_name' => ['required', 'string', 'max:100'],
        'description' => ['required', 'string', 'max:3000'],
        'location' => ['required', 'string', 'max:255'],
        'event_date' => ['required', 'date'],
        'start_time' => ['required', 'date_format:H:i'],
        'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
        'total_seats' => ['required', 'integer', 'min:1'],
        'ticket_price' => ['required', 'numeric', 'min:0'],
        'image' => ['nullable', 'url', 'max:255'],
        'image2' => ['nullable', 'url', 'max:255'],
        'image3' => ['nullable', 'url', 'max:255'],
        'map_url' => ['nullable', 'url'],
    ]);

    $category = EventCategory::firstOrCreate(['category_name' => $validated['category_name']]);

    $event->update([
        'category_id' => $category->id,
        'title' => $validated['title'],
        'description' => $validated['description'],
        'about' => $validated['description'],
        'location' => $validated['location'],
        'event_date' => $validated['event_date'],
        'start_time' => $validated['start_time'],
        'end_time' => $validated['end_time'] ?? null,
        'total_seats' => $validated['total_seats'],
        'ticket_price' => $validated['ticket_price'],
        'image' => $validated['image'] ?: $event->image,
        'image2' => $validated['image2'] ?? null,
        'image3' => $validated['image3'] ?? null,
        'map_url' => $validated['map_url'] ?? null,
    ]);

    return redirect()
        ->route('admin.events.index')
        ->with('success', 'Event updated.');
})->name('admin.events.update');

Route::delete('/dashboard/admin/events/{event}', function (Event $event) use ($requireRole) {
    if ($response = $requireRole(['admin'])) {
        return $response;
    }

    $event->loadCount('bookings');

    if ($event->bookings_count > 0) {
        return redirect()
            ->route('admin.events.index')
            ->withErrors(['event' => 'This event already has bookings, so delete is blocked to protect booking history.']);
    }

    $event->delete();

    return redirect()
        ->route('admin.events.index')
        ->with('success', 'Event deleted.');
})->name('admin.events.destroy');

Route::get('/dashboard/admin/events', function (Request $request) use ($requireRole) {
    if ($response = $requireRole(['admin'])) {
        return $response;
    }

    $eventSearch = trim((string) $request->query('event_search', ''));

    return view('dashboard.admin-events', [
        'adminUser' => Auth::user()?->loadMissing('role'),
        'events' => Event::with(['category', 'status', 'organizer.role'])
            ->withCount('bookings')
            ->withSum('bookings as booked_tickets', 'quantity')
            ->withSum('bookings as gross_sales', 'total_price')
            ->when($eventSearch !== '', function ($query) use ($eventSearch) {
                $query->where(function ($query) use ($eventSearch) {
                    $query->where('title', 'like', '%' . $eventSearch . '%')
                        ->orWhere('location', 'like', '%' . $eventSearch . '%')
                        ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('category_name', 'like', '%' . $eventSearch . '%'))
                        ->orWhereHas('organizer', function ($organizerQuery) use ($eventSearch) {
                            $organizerQuery->where('name', 'like', '%' . $eventSearch . '%')
                                ->orWhere('full_name', 'like', '%' . $eventSearch . '%')
                                ->orWhere('email', 'like', '%' . $eventSearch . '%');
                        });
                });
            })
            ->latest('id')
            ->get(),
        'eventSearch' => $eventSearch,
    ]);
})->name('admin.events.index');

Route::post('/dashboard/admin/users', function (Request $request) use ($requireRole) {
    if ($response = $requireRole(['admin'])) {
        return $response;
    }

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:150'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'phone' => ['nullable', 'string', 'max:20'],
        'role_id' => ['required', Rule::exists('roles', 'id')],
        'password' => ['required', 'string', 'min:6'],
    ]);

    User::create([
        'name' => $validated['name'],
        'full_name' => $validated['name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'] ?? null,
        'password' => Hash::make($validated['password']),
        'role_id' => $validated['role_id'],
    ]);

    return redirect()
        ->route('admin.users.index')
        ->with('success', 'User account created.');
})->name('admin.users.store');

Route::patch('/dashboard/admin/users/{user}', function (Request $request, User $user) use ($requireRole) {
    if ($response = $requireRole(['admin'])) {
        return $response;
    }

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:150'],
        'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        'phone' => ['nullable', 'string', 'max:20'],
        'role_id' => ['required', Rule::exists('roles', 'id')],
        'password' => ['nullable', 'string', 'min:6'],
    ]);

    $attributes = [
        'name' => $validated['name'],
        'full_name' => $validated['name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'] ?? null,
        'role_id' => $validated['role_id'],
    ];

    $selectedRole = Role::find($validated['role_id']);

    if ($selectedRole?->role_name === 'event_owner') {
        $attributes['owner_requested_at'] = null;
    }

    if (! empty($validated['password'])) {
        $attributes['password'] = Hash::make($validated['password']);
    }

    $user->update($attributes);

    return redirect()
        ->route('admin.users.index')
        ->with('success', 'User account updated.');
})->name('admin.users.update');

Route::delete('/dashboard/admin/users/{user}', function (User $user) use ($requireRole) {
    if ($response = $requireRole(['admin'])) {
        return $response;
    }

    $user->loadCount(['bookings', 'organizedEvents']);

    if ($user->bookings_count > 0 || $user->organized_events_count > 0) {
        return redirect()
            ->route('admin.users.index')
            ->withErrors(['user' => 'This user has bookings or events, so delete is blocked to protect existing records.']);
    }

    if (Auth::id() === $user->id) {
        return redirect()
            ->route('admin.users.index')
            ->withErrors(['user' => 'You cannot delete the account you are currently using.']);
    }

    $user->delete();

    return redirect()
        ->route('admin.users.index')
        ->with('success', 'User account deleted.');
})->name('admin.users.destroy');

Route::get('/dashboard/admin/users', function (Request $request) use ($requireRole) {
    if ($response = $requireRole(['admin'])) {
        return $response;
    }

    $userSearch = trim((string) $request->query('user_search', ''));

    return view('dashboard.admin-users', [
        'adminUser' => Auth::user()?->loadMissing('role'),
        'usersCount' => User::count(),
        'users' => User::with('role')
            ->withCount(['bookings', 'organizedEvents'])
            ->when($userSearch !== '', function ($query) use ($userSearch) {
                $query->where(function ($query) use ($userSearch) {
                    $query->where('name', 'like', '%' . $userSearch . '%')
                        ->orWhere('full_name', 'like', '%' . $userSearch . '%')
                        ->orWhere('email', 'like', '%' . $userSearch . '%')
                        ->orWhere('phone', 'like', '%' . $userSearch . '%')
                        ->orWhereHas('role', fn ($roleQuery) => $roleQuery->where('role_name', 'like', '%' . $userSearch . '%'));
                });
            })
            ->latest('id')
            ->get(),
        'roles' => Role::orderBy('role_name')->get(),
        'userSearch' => $userSearch,
    ]);
})->name('admin.users.index');

Route::get('/dashboard/admin', function (Request $request) use ($allEventViews, $allBookingViews, $requireRole) {
    if ($response = $requireRole(['admin'])) {
        return $response;
    }

    $userSearch = trim((string) $request->query('user_search', ''));

    return view('dashboard.admin', [
        'adminUser' => Auth::user()?->loadMissing('role'),
        'events' => $allEventViews(),
        'bookings' => $allBookingViews(),
        'usersCount' => User::count(),
        'users' => User::with('role')
            ->withCount(['bookings', 'organizedEvents'])
            ->when($userSearch !== '', function ($query) use ($userSearch) {
                $query->where(function ($query) use ($userSearch) {
                    $query->where('name', 'like', '%' . $userSearch . '%')
                        ->orWhere('full_name', 'like', '%' . $userSearch . '%')
                        ->orWhere('email', 'like', '%' . $userSearch . '%')
                        ->orWhere('phone', 'like', '%' . $userSearch . '%')
                        ->orWhereHas('role', fn ($roleQuery) => $roleQuery->where('role_name', 'like', '%' . $userSearch . '%'));
                });
            })
            ->latest('id')
            ->get(),
        'roles' => Role::orderBy('role_name')->get(),
        'userSearch' => $userSearch,
    ]);
})->name('admin.dashboard');

Route::get('/booking-history', function () use ($bookingViewsForUser) {
    if (! Auth::check()) {
        return redirect()
            ->route('login')
            ->withErrors(['email' => 'Please sign in before opening your booking history.']);
    }

    return view('event.history', [
        'bookings' => $bookingViewsForUser(Auth::user()),
    ]);
})->name('bookings.history');

Route::get('/events/{event}/booking', function (string $event) use ($eventToView) {
    $eventModel = Event::where('slug', $event)->firstOrFail();

    return view('event.booking', [
        'event' => $eventToView($eventModel),
        'slug' => $event,
    ]);
})->name('events.booking');

Route::post('/events/{event}/booking', function (Request $request, string $event) {
    $eventModel = Event::where('slug', $event)->firstOrFail();

    $validated = $request->validate([
        'first_name' => ['required', 'string', 'max:100'],
        'last_name' => ['required', 'string', 'max:100'],
        'email' => ['required', 'email', 'max:255'],
        'phone' => ['required', 'string', 'max:40'],
        'ticket_type' => ['required', 'string', 'max:40'],
        'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        'currency' => ['required', 'string', 'in:USD,KHR'],
        'notes' => ['nullable', 'string', 'max:1000'],
        'payment_reference' => ['required', 'string', 'max:120'],
        'payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        'terms' => ['accepted'],
    ]);

    if (! Auth::check() && User::where('email', $validated['email'])->exists()) {
        return back()
            ->withErrors(['email' => 'This email already has an account. Please log in before booking.'])
            ->withInput();
    }

    $bookingUser = DB::transaction(function () use ($request, $validated, $eventModel) {
        $userRole = Role::firstOrCreate(['role_name' => 'user']);
        $bookingStatus = BookingStatus::firstOrCreate(['status_name' => 'pending']);
        $paymentMethod = PaymentMethod::firstOrCreate(['method_name' => 'KHQR']);
        $paymentStatus = PaymentStatus::firstOrCreate(['status_name' => 'review']);
        $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);
        
        $quantity = (int) $validated['quantity'];
        $ticketType = $validated['ticket_type'];
        $currency = $validated['currency'] ?? 'USD';

        $basePriceUsd = (float) $eventModel->ticket_price;
        $unitPriceUsd = $basePriceUsd;

        if (strcasecmp($ticketType, 'VIP') === 0) {
            $unitPriceUsd *= 1.5;
        } elseif (strcasecmp($ticketType, 'Group') === 0) {
            $unitPriceUsd *= 0.9;
        }

        if ($currency === 'KHR') {
            $unitPrice = round($unitPriceUsd * 4100, 2);
            $totalPrice = $unitPrice * $quantity;
        } else {
            $unitPrice = round($unitPriceUsd, 2);
            $totalPrice = $unitPrice * $quantity;
        }

        $user = Auth::user();

        if ($user) {
            $user->update([
                'name' => $fullName,
                'full_name' => $fullName,
                'phone' => $validated['phone'],
            ]);
        } else {
            $user = User::firstOrNew(['email' => $validated['email']]);
            $user->name = $fullName;
            $user->full_name = $fullName;
            $user->phone = $validated['phone'];

            if (! $user->exists) {
                $user->password = Hash::make('password');
                $user->role_id = $userRole->id;
            }

            $user->save();
        }

        $booking = Booking::create([
            'booking_code' => 'BK-' . strtoupper(Str::random(8)),
            'user_id' => $user->id,
            'event_id' => $eventModel->id,
            'booking_status_id' => $bookingStatus->id,
            'ticket_type' => $ticketType,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
        ]);

        $proofPath = $request->file('payment_proof')?->store('payment-proofs', 'public');

        $booking->payment()->create([
            'payment_method_id' => $paymentMethod->id,
            'payment_status_id' => $paymentStatus->id,
            'paid_amount' => $totalPrice,
            'currency' => $currency,
            'transaction_reference' => $validated['payment_reference'],
            'payment_proof' => $proofPath,
        ]);

        for ($i = 1; $i <= $quantity; $i++) {
            Ticket::create([
                'booking_id' => $booking->id,
                'ticket_code' => 'TCK-' . strtoupper(Str::random(10)),
                'qr_code' => $booking->booking_code . '-' . $i,
            ]);
        }

        return $user;
    });

    if (! Auth::check()) {
        Auth::login($bookingUser);
        $request->session()->regenerate();
    }

    return redirect()
        ->route('bookings.history')
        ->with('success', 'Booking saved to the database. Your ticket is non-refundable and cannot be returned or canceled.');
})->name('events.booking.store');

Route::get('/events/{event}', function (string $event) use ($eventToView) {
    $eventModel = Event::where('slug', $event)->firstOrFail();

    return view('event.show', [
        'event' => $eventToView($eventModel),
        'slug' => $event,
    ]);
})->name('events.show');
