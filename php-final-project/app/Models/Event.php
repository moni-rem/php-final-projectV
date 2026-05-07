<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'organizer_id',
        'category_id',
        'status_id',
        'slug',
        'title',
        'description',
        'about',
        'what_to_expect',
        'important_information',
        'location',
        'event_date',
        'start_time',
        'end_time',
        'total_seats',
        'ticket_price',
        'image',
        'image2',
        'image3',
        'map_url',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'what_to_expect' => 'array',
            'important_information' => 'array',
            'ticket_price' => 'decimal:2',
        ];
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    public function status()
    {
        return $this->belongsTo(EventStatus::class, 'status_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
