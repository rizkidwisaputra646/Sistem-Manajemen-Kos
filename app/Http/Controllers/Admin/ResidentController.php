<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ResidentController extends Controller
{
    public function index()
    {
        $today = \Carbon\Carbon::today()->toDateString();

        // Mari kita buat query yang lebih luas:
        // Tampilkan semua yang statusnya 'success' dan BELUM melewati tanggal check-out
        $residents = Booking::with(['user', 'room'])
            ->where('status', 'success')
            ->where('check_out_date', '>=', $today) // Yang belum keluar (termasuk yang keluar hari ini)
            ->latest()
            ->get();

        return view('admin.residents.index', compact('residents'));
    }
}
