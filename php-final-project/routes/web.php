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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
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
        'event_price' => '$' . number_format((float) $booking->unit_price, 2),
        'first_name' => $nameParts[0] ?? $fullName,
        'last_name' => $nameParts[1] ?? '',
        'email' => $user?->email,
        'phone' => $user?->phone,
        'ticket_type' => 'Standard',
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

Route::get('/', function () use ($allEventViews) {
    return view('welcome', [
        'events' => $allEventViews(),
    ]);
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/login/user', function () {
    return view('auth.role-login', [
        'role' => 'User',
        'routeName' => 'user.dashboard',
        'headline' => 'Book events and manage your tickets.',
        'description' => 'Use this login for normal customers who want to book events and view booking history.',
    ]);
})->name('login.user');

Route::get('/login/owner', function () {
    return view('auth.role-login', [
        'role' => 'Event Owner',
        'routeName' => 'owner.dashboard',
        'headline' => 'Create and display your events.',
        'description' => 'Use this login for event organizers who need to publish events, update details, and monitor bookings.',
    ]);
})->name('login.owner');

Route::get('/login/admin', function () {
    return view('auth.role-login', [
        'role' => 'Admin',
        'routeName' => 'admin.dashboard',
        'headline' => 'See what is happening in the system.',
        'description' => 'Use this login for administrators who review users, bookings, events, and platform activity.',
    ]);
})->name('login.admin');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/dashboard/user', function () use ($allBookingViews) {
    return view('dashboard.user', [
        'bookings' => $allBookingViews(),
    ]);
})->name('user.dashboard');

Route::get('/dashboard/owner', function () use ($allEventViews) {
    return view('dashboard.owner', [
        'events' => $allEventViews(),
    ]);
})->name('owner.dashboard');

Route::get('/dashboard/owner/events/create', function () {
    return view('dashboard.owner-create-event');
})->name('owner.events.create');

Route::post('/dashboard/owner/events', function (Request $request) {
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

    $ownerRole = Role::firstOrCreate(['role_name' => 'event_owner']);
    $owner = User::firstOrCreate(
        ['email' => 'owner@example.com'],
        [
            'name' => 'Event Owner',
            'full_name' => 'Event Owner',
            'password' => Hash::make('password'),
            'phone' => '+855 12 000 001',
            'role_id' => $ownerRole->id,
        ],
    );

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

Route::get('/dashboard/admin', function () use ($allEventViews, $allBookingViews) {
    return view('dashboard.admin', [
        'events' => $allEventViews(),
        'bookings' => $allBookingViews(),
        'usersCount' => User::count(),
    ]);
})->name('admin.dashboard');

Route::get('/booking-history', function () use ($allBookingViews) {
    return view('event.history', [
        'bookings' => $allBookingViews(),
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
        'notes' => ['nullable', 'string', 'max:1000'],
        'payment_reference' => ['required', 'string', 'max:120'],
        'payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        'terms' => ['accepted'],
    ]);

    DB::transaction(function () use ($request, $validated, $eventModel) {
        $userRole = Role::firstOrCreate(['role_name' => 'user']);
        $bookingStatus = BookingStatus::firstOrCreate(['status_name' => 'pending']);
        $paymentMethod = PaymentMethod::firstOrCreate(['method_name' => 'KHQR']);
        $paymentStatus = PaymentStatus::firstOrCreate(['status_name' => 'review']);
        $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);
        $quantity = (int) $validated['quantity'];
        $unitPrice = (float) $eventModel->ticket_price;
        $totalPrice = $unitPrice * $quantity;

        $user = User::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $fullName,
                'full_name' => $fullName,
                'password' => Hash::make('password'),
                'phone' => $validated['phone'],
                'role_id' => $userRole->id,
            ],
        );

        $booking = Booking::create([
            'booking_code' => 'BK-' . strtoupper(Str::random(8)),
            'user_id' => $user->id,
            'event_id' => $eventModel->id,
            'booking_status_id' => $bookingStatus->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
        ]);

        $proofPath = $request->file('payment_proof')?->store('payment-proofs', 'public');

        $booking->payment()->create([
            'payment_method_id' => $paymentMethod->id,
            'payment_status_id' => $paymentStatus->id,
            'paid_amount' => $totalPrice,
            'currency' => 'USD',
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
    });

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
