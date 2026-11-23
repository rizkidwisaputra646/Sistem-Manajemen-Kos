<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalExtension extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'room_id',
        'previous_check_out_date',
        'new_check_in_date',
        'new_check_out_date',
        'price',
        'status', // pending, approved, rejected, paid, expired
        'admin_id',
        'admin_notes',
        'approved_at',
    ];

    protected $casts = [
        'previous_check_out_date' => 'date',
        'new_check_in_date' => 'date',
        'new_check_out_date' => 'date',
        'approved_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">Menunggu Persetujuan</span>',
            'approved' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">Disetujui (Menunggu Bayar)</span>',
            'paid' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Lunas / Selesai</span>',
            'rejected' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">Ditolak</span>',
            'expired' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">Kedaluwarsa</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">' . e($this->status) . '</span>',
        };
    }
}
