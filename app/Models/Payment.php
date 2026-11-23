<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'rental_extension_id',
        'user_id',
        'order_id',
        'amount',
        'status', // pending, settlement, expire, cancel, deny
        'snap_token',
        'payment_type',
        'payment_expires_at',
        'settlement_time',
        'payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_expires_at' => 'datetime',
        'settlement_time' => 'datetime',
        'payload' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    public function rentalExtension()
    {
        return $this->belongsTo(RentalExtension::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isSettled(): bool
    {
        return in_array($this->status, ['settlement', 'capture', 'success']);
    }

    public function isExpired(): bool
    {
        if ($this->status === 'expire') {
            return true;
        }

        if ($this->status === 'pending' && $this->payment_expires_at && now()->greaterThan($this->payment_expires_at)) {
            return true;
        }

        return false;
    }
}
