<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'ticket_code',
        'qr_code',
        'ticket_status',
        'issued_date',
        'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_date' => 'datetime',
            'checked_in_at' => 'datetime',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
