<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReminderLog extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'recipient_email',
        'recipient_name',
        'room_name',
        'amount',
        'due_date',
        'reminder_type',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
