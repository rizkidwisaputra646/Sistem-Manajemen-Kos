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
        $status = $request->status;

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($booking, $status) {
                if ($status === 'success') {
                    // Validasi: Cegah double booking jika kamar sudah terlanjur diisi penyewa lain yang sudah lunas
                    $conflict = Booking::where('room_id', $booking->room_id)
                        ->where('id', '!=', $booking->id)
                        ->where('status', 'success')
                        ->where('check_in_date', '<', $booking->check_out_date)
                        ->where('check_out_date', '>', $booking->check_in_date)
                        ->with('user')
                        ->first();

                    if ($conflict) {
                        throw new \Exception("Gagal: Kamar ini sudah terlanjur dipesan dan lunas oleh penyewa lain ({$conflict->user->name}) untuk periode tersebut. Tidak dapat mengaktifkan pesanan ini untuk mencegah double booking.");
                    }

                    $booking->room->update(['status' => 'full']);

                    \App\Models\Payment::updateOrCreate(
                        ['booking_id' => $booking->id],
                        [
                            'user_id' => $booking->user_id,
                            'order_id' => 'CASH-' . $booking->id,
                            'amount' => $booking->total_price,
                            'status' => 'settlement',
                            'payment_type' => 'cash / manual',
                            'settlement_time' => now(),
                        ]
                    );
                } elseif ($status === 'cancelled') {
                    $booking->room->update(['status' => 'available']);
                }

                $booking->update([
                    'status' => $status
                ]);
            });

            return back()->with('success', 'Status pesanan #' . $booking->id . ' berhasil diupdate menjadi ' . strtoupper($status) . '!');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
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
