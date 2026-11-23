<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\RentalExtension;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalExtensionController extends Controller
{
    /**
     * Penghuni mengajukan perpanjangan sewa dari halaman Pesanan Saya.
     */
    public function store(Request $request, Booking $booking)
    {
        // 1. Keamanan: Pastikan hanya pemilik yang bisa mengajukan perpanjangan
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda bukan pemilik penyewaan ini.');
        }

        // 2. Pastikan penyewaan berstatus aktif / success
        if ($booking->status !== 'success') {
            return back()->with('error', 'Hanya penyewaan aktif yang dapat diajukan perpanjangan.');
        }

        // 3. Pastikan tidak ada pengajuan perpanjangan yang sedang berjalan (pending/approved)
        $hasActiveExtension = RentalExtension::where('booking_id', $booking->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasActiveExtension) {
            return back()->with('error', 'Anda sudah memiliki pengajuan perpanjangan yang sedang diproses atau menunggu pembayaran.');
        }

        // 4. Hitung tanggal periode baru secara otomatis
        $newStart = Carbon::parse($booking->check_out_date)->startOfDay();
        $newEnd = $newStart->copy()->addMonth();

        // 5. Simpan pengajuan perpanjangan
        RentalExtension::create([
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'room_id' => $booking->room_id,
            'previous_check_out_date' => $booking->check_out_date,
            'new_check_in_date' => $newStart->toDateString(),
            'new_check_out_date' => $newEnd->toDateString(),
            'price' => $booking->room->price,
            'status' => 'pending', // Menunggu persetujuan admin
        ]);

        return back()->with('success', 'Pengajuan perpanjangan sewa berhasil dikirim! Menunggu persetujuan dari pengelola kontrakan.');
    }

    /**
     * Batalkan pengajuan perpanjangan (hanya jika masih pending).
     */
    public function cancel(RentalExtension $rentalExtension)
    {
        if ($rentalExtension->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($rentalExtension->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini tidak dapat dibatalkan.');
        }

        $rentalExtension->update(['status' => 'rejected', 'admin_notes' => 'Dibatalkan oleh penghuni.']);

        return back()->with('success', 'Pengajuan perpanjangan sewa telah dibatalkan.');
    }
}
