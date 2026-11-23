<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentalExtension;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    /**
     * Endpoint Sinkronisasi Status Pembayaran Perpanjangan Sewa dari Snap Frontend
     */
    public function syncPayment(Request $request, RentalExtension $rentalExtension)
    {
        if ($rentalExtension->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $orderId = $request->input('order_id');
        $payload = $request->all();

        $synced = $this->syncExtensionPayment($rentalExtension, $orderId, $payload);
        $rentalExtension->refresh();

        return response()->json([
            'success' => $synced,
            'status' => $rentalExtension->status,
            'message' => $synced ? 'Pembayaran perpanjangan berhasil diverifikasi dan masa sewa telah diperpanjang!' : 'Status pembayaran belum berhasil.'
        ]);
    }

    /**
     * Helper sinkronisasi pembayaran perpanjangan sewa dengan Midtrans API / payload.
     */
    public function syncExtensionPayment(RentalExtension $rentalExtension, ?string $orderId = null, ?array $payload = null): bool
    {
        if ($rentalExtension->status === 'paid') {
            return true;
        }

        $payment = $rentalExtension->latestPayment;
        if (!$payment && $orderId) {
            $payment = Payment::where('order_id', $orderId)->first();
        }
        if ($payment && !$orderId) {
            $orderId = $payment->order_id;
        }

        $transactionStatus = $payload['transaction_status'] ?? null;

        if ($orderId) {
            \Midtrans\Config::$serverKey = config('services.midtrans.serverKey');
            \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            try {
                $statusObj = \Midtrans\Transaction::status($orderId);
                $transactionStatus = $statusObj->transaction_status ?? $transactionStatus;
                if (!$payload) {
                    $payload = (array) $statusObj;
                }
            } catch (\Throwable $e) {
                Log::info("Sinkronisasi extension {$orderId}: " . $e->getMessage());
            }
        }

        if (in_array($transactionStatus, ['settlement', 'capture', 'success'])) {
            DB::transaction(function () use ($rentalExtension, $payment, $payload) {
                if ($payment) {
                    $payment->update([
                        'status' => 'settlement',
                        'settlement_time' => now(),
                        'payload' => $payload,
                    ]);
                }

                $rentalExtension->update(['status' => 'paid']);

                $booking = Booking::find($rentalExtension->booking_id);
                if ($booking) {
                    $newDate = Carbon::parse($rentalExtension->new_check_out_date)->toDateString();
                    $booking->update([
                        'check_out_date' => $newDate,
                        'due_date' => $newDate,
                    ]);
                }
            });

            Log::info("Perpanjangan sewa #{$rentalExtension->id} berhasil diverifikasi LUNAS & masa sewa diperpanjang!");
            return true;
        }

        return false;
    }
}
