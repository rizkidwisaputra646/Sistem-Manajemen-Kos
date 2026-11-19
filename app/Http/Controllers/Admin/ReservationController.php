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

        // Fitur Search Nama, Email, Kamar, & ID Pesanan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('room', function ($rq) use ($search) {
                    $rq->where('name', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
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

    public function downloadReceipt(Booking $booking)
    {
        if ($booking->status !== 'success') {
            return back()->with('error', 'Kuitansi hanya dapat dicetak untuk pesanan yang telah lunas (success).');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('user.bookings.receipt_pdf', compact('booking'));
        $filename = 'Kuitansi-KosKu-' . strtoupper(substr($booking->id, 0, 8)) . '.pdf';

        if (request('stream')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
