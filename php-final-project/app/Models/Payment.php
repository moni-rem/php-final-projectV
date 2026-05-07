<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'payment_method_id',
        'payment_status_id',
        'paid_amount',
        'currency',
        'transaction_reference',
        'payment_proof',
        'payment_date',
    ];

    protected function casts(): array
    {
        return [
            'paid_amount' => 'decimal:2',
            'payment_date' => 'datetime',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function status()
    {
        return $this->belongsTo(PaymentStatus::class, 'payment_status_id');
    }
}
