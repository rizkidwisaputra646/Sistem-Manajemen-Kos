<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'uuid',
        'user_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'due_date',
        'total_price',
        'status',
        'payment_method',
        'snap_token',
    ];

    protected static function booted()
    {
        static::creating(function ($booking) {
            $booking->uuid = (string) Str::uuid();
        });
    }

    // PENTING: Beritahu Laravel pakai kolom 'uuid' untuk Route Binding
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Opsional: Tambahkan relasi agar lebih mudah memanggil data kamar/user nanti
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reminderLogs()
    {
        return $this->hasMany(PaymentReminderLog::class, 'booking_id', 'id');
    }

    public function extensions()
    {
        return $this->hasMany(RentalExtension::class, 'booking_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id', 'id');
    }

    public function activeExtension()
    {
        return $this->hasOne(RentalExtension::class, 'booking_id', 'id')
            ->whereIn('status', ['pending', 'approved'])
            ->latestOfMany();
    }

    public function getEffectiveDueDateAttribute()
    {
        return $this->due_date ?? $this->check_in_date;
    }
}
