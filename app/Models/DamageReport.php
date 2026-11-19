<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamageReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'facility_name',
        'category',
        'description',
        'image',
        'status',
        'admin_response',
        'admin_id',
        'is_read_by_admin',
        'is_read_by_user',
        'resolved_at',
    ];

    protected $casts = [
        'is_read_by_admin' => 'boolean',
        'is_read_by_user' => 'boolean',
        'resolved_at' => 'datetime',
    ];

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

    public function scopeUnreadByAdmin($query)
    {
        return $query->where('is_read_by_admin', false);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Menunggu');
    }

    const STATUSES = ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'];

    const CATEGORIES = [
        'Kelistrikan',
        'Pipa / Saluran Air',
        'Furnitur / Perabot',
        'Elektronik',
        'Pintu / Jendela',
        'Atap / Kebocoran',
        'Lainnya',
    ];

    public function getPhotoPathAttribute(): ?string
    {
        return $this->image;
    }

    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'Menunggu' => 'bg-amber-100 text-amber-800 border-amber-200',
            'Diproses' => 'bg-blue-100 text-blue-800 border-blue-200',
            'Selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'Ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
        ];
        $color = $colors[$this->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border ' . $color . '">' . e($this->status) . '</span>';
    }

    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            'Menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Diproses' => 'bg-blue-50 text-blue-700 border-blue-200',
            'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }
}
