<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'room'])->latest();

        // Fitur Filter Status
        if ($request->filled('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Fitur Search Nama
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $bookings = $query->paginate(10); // Pakai paginasi agar tidak berat

        // dd($bookings);
        return view('admin.reservations.index', compact('bookings'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $booking->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status pesanan #' . $booking->id . ' berhasil diupdate!');
    }

    public function cancel(Booking $booking)
    {
        // Ubah status jadi cancelled
        $booking->update(['status' => 'cancelled']);

        // Ubah status kamar kembali jadi available agar bisa dipesan orang lain
        $booking->room->update(['status' => 'available']);

        return back()->with('success', 'Pesanan #' . $booking->id . ' telah dibatalkan oleh Admin.');
    }
}
