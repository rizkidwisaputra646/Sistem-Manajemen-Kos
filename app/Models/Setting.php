<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk memberi izin simpan data
    protected $fillable = [
        'key',
        'value',
    ];
}