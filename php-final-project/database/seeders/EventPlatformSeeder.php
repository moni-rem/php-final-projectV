<?php

namespace Database\Seeders;

use App\Models\BookingStatus;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventStatus;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EventPlatformSeeder extends Seeder
{
    public function run(): void
    {
        $roles = collect(['user', 'event_owner', 'admin'])->mapWithKeys(
            fn (string $name) => [$name => Role::firstOrCreate(['role_name' => $name])]
        );

        $categories = collect(['Event', 'Market'])->mapWithKeys(
            fn (string $name) => [$name => EventCategory::firstOrCreate(['category_name' => $name])]
        );

        $eventStatuses = collect(['draft', 'published', 'cancelled'])->mapWithKeys(
            fn (string $name) => [$name => EventStatus::firstOrCreate(['status_name' => $name])]
        );

        collect(['pending', 'confirmed', 'cancelled'])->each(
            fn (string $name) => BookingStatus::firstOrCreate(['status_name' => $name])
        );

        collect(['KHQR', 'Cash', 'Card'])->each(
            fn (string $name) => PaymentMethod::firstOrCreate(['method_name' => $name])
        );

        collect(['pending', 'paid', 'failed', 'review'])->each(
            fn (string $name) => PaymentStatus::firstOrCreate(['status_name' => $name])
        );

        $owner = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Event Owner',
                'full_name' => 'Event Owner',
                'password' => Hash::make('password'),
                'phone' => '+855 12 000 001',
                'role_id' => $roles['event_owner']->id,
            ],
        );

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'full_name' => 'Admin User',
                'password' => Hash::make('password'),
                'phone' => '+855 12 000 002',
                'role_id' => $roles['admin']->id,
            ],
        );

        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Booking User',
                'full_name' => 'Booking User',
                'password' => Hash::make('password'),
                'phone' => '+855 12 000 003',
                'role_id' => $roles['user']->id,
            ],
        );

        $events = [
            [
                'slug' => 'camm-met-gala',
                'category' => 'Event',
                'title' => 'CamM Met Gala',
                'description' => 'Join us for an incredible day of live music featuring top artists from around the world.',
                'about' => "Join us for an incredible day of live music featuring top artists from around the world.\n\nJoin us for an unforgettable experience! This event brings together people from all walks of life to celebrate, learn, and connect. Whether you're a first-timer or a seasoned attendee, you'll find something special waiting for you.",
                'location' => 'Phnom Penh, Cambodia',
                'event_date' => '2029-12-12',
                'start_time' => '17:00:00',
                'end_time' => '23:00:00',
                'total_seats' => 500,
                'ticket_price' => 25.00,
                'image' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1600&q=85',
                'image2' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&w=1200&q=85',
                'image3' => 'https://images.unsplash.com/photo-1505236858219-8359eb29e329?auto=format&fit=crop&w=1200&q=85',
                'map_url' => 'https://maps.google.com/maps?q=Phnom%20Penh%2C%20Cambodia&output=embed',
                'what_to_expect' => [
                    'Engaging activities and entertainment',
                    'Networking opportunities with like-minded individuals',
                    'Professional organization and friendly staff',
                    'Memorable experiences and lasting connections',
                ],
            ],
            [
                'slug' => 'future-tech-conference',
                'category' => 'Event',
                'title' => 'Future Tech Conference',
                'description' => 'Talks, product demos, and networking sessions with builders, founders, designers, and technology teams.',
                'about' => 'Talks, product demos, and networking sessions with builders, founders, designers, and technology teams.',
                'location' => 'Siem Reap',
                'event_date' => '2026-04-05',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'total_seats' => 250,
                'ticket_price' => 49.00,
                'image' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1200&q=80',
                'image2' => 'https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=1200&q=80',
                'image3' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1200&q=80',
                'map_url' => 'https://maps.google.com/maps?q=Siem%20Reap%2C%20Cambodia&output=embed',
                'what_to_expect' => ['Keynote talks', 'Product demos', 'Networking', 'Workshop rooms', 'Coffee breaks', 'Founder sessions'],
            ],
            [
                'slug' => 'night-food-market',
                'category' => 'Market',
                'title' => 'Night Food Market',
                'description' => 'Street food, crafts, and family-friendly performances in a lively evening market setting.',
                'about' => 'Street food, crafts, and family-friendly performances in a lively evening market setting.',
                'location' => 'Battambang',
                'event_date' => '2026-04-12',
                'start_time' => '18:00:00',
                'end_time' => '22:00:00',
                'total_seats' => 1000,
                'ticket_price' => 0.00,
                'image' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1200&q=80',
                'image2' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=80',
                'image3' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80',
                'map_url' => 'https://maps.google.com/maps?q=Battambang%2C%20Cambodia&output=embed',
                'what_to_expect' => ['Local food', 'Craft stalls', 'Live music', 'Family friendly', 'Outdoor seating', 'Evening performances'],
            ],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(
                ['slug' => $event['slug']],
                [
                    'organizer_id' => $owner->id,
                    'category_id' => $categories[$event['category']]->id,
                    'status_id' => $eventStatuses['published']->id,
                    'title' => $event['title'],
                    'description' => $event['description'],
                    'about' => $event['about'],
                    'what_to_expect' => $event['what_to_expect'],
                    'important_information' => [
                        'Please arrive 15-30 minutes early for check-in',
                        'Booked tickets are non-refundable and cannot be returned or canceled',
                        'Age restrictions may apply - please check event requirements',
                        'Parking and accessibility information will be sent via email after booking',
                    ],
                    'location' => $event['location'],
                    'event_date' => $event['event_date'],
                    'start_time' => $event['start_time'],
                    'end_time' => $event['end_time'],
                    'total_seats' => $event['total_seats'],
                    'ticket_price' => $event['ticket_price'],
                    'image' => $event['image'],
                    'image2' => $event['image2'],
                    'image3' => $event['image3'],
                    'map_url' => $event['map_url'],
                ],
            );
        }
    }
}
