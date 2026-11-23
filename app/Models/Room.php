<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'facilities',
        'image',
        'status',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function damageReports()
    {
        return $this->hasMany(DamageReport::class);
    }

    public function extensions()
    {
        return $this->hasMany(RentalExtension::class);
    }

    // Accessor: $room->is_currently_booked
    public function getIsCurrentlyBookedAttribute()
    {
        // Jika status DB diubah manual jadi unavailable, langsung anggap penuh
        if ($this->status !== 'available') {
            return true;
        }

        // Cek apakah HARI INI masuk dalam rentang tanggal booking yang sukses/pending
        $today = now()->toDateString();
        $hasActiveBooking = $this->bookings()
            ->where(function ($query) {
                $query->where('status', 'success')
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending')
                            ->where('created_at', '>=', now()->subMinute(45));
                    });
            })
            ->where('check_in_date', '<=', $today)
            ->where('check_out_date', '>=', $today)
            ->exists();

        return $hasActiveBooking;
    }
}
