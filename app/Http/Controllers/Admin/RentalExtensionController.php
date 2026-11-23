<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentalExtension;
use App\Models\Room;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class RentalExtensionController extends Controller
{
    /**
     * Tampilkan daftar permintaan perpanjangan sewa.
     */
    public function index(Request $request)
    {
        $query = RentalExtension::with(['user', 'room', 'booking', 'admin', 'latestPayment'])->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

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

        $extensions = $query->paginate(15);

        $counts = [
            'all' => RentalExtension::count(),
            'pending' => RentalExtension::where('status', 'pending')->count(),
            'approved' => RentalExtension::where('status', 'approved')->count(),
            'paid' => RentalExtension::where('status', 'paid')->count(),
            'rejected' => RentalExtension::where('status', 'rejected')->count(),
            'expired' => RentalExtension::where('status', 'expired')->count(),
        ];

        return view('admin.extensions.index', compact('extensions', 'counts'));
    }

    /**
     * Admin menyetujui pengajuan perpanjangan sewa kamar.
     * Menggunakan Database Transaction & Row Locking pada Room untuk mencegah race condition.
     */
    public function approve(Request $request, RentalExtension $rentalExtension, MidtransService $midtransService)
    {
        return DB::transaction(function () use ($rentalExtension, $midtransService) {
            // 1. Lock baris Room sebagai mekanisme penguncian utama
            $room = Room::where('id', $rentalExtension->room_id)->lockForUpdate()->firstOrFail();

            // 2. Lock baris RentalExtension untuk memastikan belum pernah diproses
            $extension = RentalExtension::where('id', $rentalExtension->id)->lockForUpdate()->firstOrFail();

            if ($extension->status !== 'pending') {
                return back()->with('error', 'Pengajuan perpanjangan ini sudah diproses sebelumnya dan tidak dapat diubah.');
            }

            $newStart = Carbon::parse($extension->new_check_in_date)->startOfDay();
            $newEnd = Carbon::parse($extension->new_check_out_date)->startOfDay();

            // 3. Validasi bentrok periode [new_start, new_end)
            // A. Cek bentrok dengan booking aktif lain (bukan booking induk yang sedang diperpanjang)
            $hasBookingConflict = Booking::where('room_id', $room->id)
                ->where('id', '!=', $extension->booking_id)
                ->where('status', 'success')
                ->where('check_in_date', '<', $newEnd->toDateString())
                ->where('check_out_date', '>', $newStart->toDateString())
                ->exists();

            if ($hasBookingConflict) {
                return back()->with('error', 'Gagal menyetujui: Kamar sudah memiliki reservasi terkonfirmasi lain pada periode tersebut.');
            }

            // B. Cek bentrok dengan perpanjangan approved lain yang pembayarannya belum kedaluwarsa
            $hasApprovedExtensionConflict = RentalExtension::where('room_id', $room->id)
                ->where('id', '!=', $extension->id)
                ->where('status', 'approved')
                ->where('new_check_in_date', '<', $newEnd->toDateString())
                ->where('new_check_out_date', '>', $newStart->toDateString())
                ->whereHas('latestPayment', function ($pq) {
                    $pq->where('status', 'pending')
                       ->where(function ($q) {
                           $q->whereNull('payment_expires_at')
                             ->orWhere('payment_expires_at', '>=', now());
                       });
                })
                ->exists();

            if ($hasApprovedExtensionConflict) {
                return back()->with('error', 'Gagal menyetujui: Periode kamar sedang ditahan oleh perpanjangan lain yang menunggu pembayaran.');
            }

            // 4. Update status perpanjangan menjadi approved
            $extension->update([
                'status' => 'approved',
                'admin_id' => Auth::id(),
                'approved_at' => now(),
            ]);

            // 5. Terbitkan data transaksi pembayaran di tabel payments (Batas bayar 1 jam)
            $orderId = 'EXT-' . $extension->id . '-' . strtoupper(Str::random(5));
            $paymentExpiresAt = now()->addHour();

            $payment = Payment::create([
                'booking_id' => $extension->booking_id,
                'rental_extension_id' => $extension->id,
                'user_id' => $extension->user_id,
                'order_id' => $orderId,
                'amount' => $extension->price,
                'status' => 'pending',
                'payment_expires_at' => $paymentExpiresAt,
            ]);

            // 6. Buat Snap Token Midtrans
            $snapToken = null;
            try {
                $params = [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => (int) $extension->price,
                    ],
                    'expiry' => [
                        'unit' => 'minute',
                        'duration' => 60,
                    ],
                    'customer_details' => [
                        'first_name' => $extension->user->name,
                        'email' => $extension->user->email,
                    ],
                    'item_details' => [
                        [
                            'id' => 'EXT-' . $room->id,
                            'price' => (int) $extension->price,
                            'quantity' => 1,
                            'name' => 'Perpanjangan Sewa ' . $room->name . ' (1 Bulan)',
                        ]
                    ]
                ];

                $snapToken = $midtransService->createSnapToken($params);
                $payment->update(['snap_token' => $snapToken]);
            } catch (Throwable $e) {
                Log::warning('Midtrans Snap Token warning: ' . $e->getMessage());
            }

            return back()->with('success', "Permintaan perpanjangan sewa #{$extension->id} berhasil disetujui! Tagihan pembayaran telah terbit untuk penghuni (Batas bayar: 1 jam).");
        });
    }

    /**
     * Admin menolak pengajuan perpanjangan sewa.
     */
    public function reject(Request $request, RentalExtension $rentalExtension)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request, $rentalExtension) {
            $extension = RentalExtension::where('id', $rentalExtension->id)->lockForUpdate()->firstOrFail();

            if ($extension->status !== 'pending') {
                return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
            }

            $extension->update([
                'status' => 'rejected',
                'admin_id' => Auth::id(),
                'admin_notes' => $request->input('admin_notes', 'Ditolak oleh admin.'),
            ]);

            return back()->with('success', "Permintaan perpanjangan sewa #{$extension->id} telah ditolak.");
        });
    }

    /**
     * Admin mengonfirmasi pembayaran perpanjangan sewa secara manual / cash (tanpa Midtrans).
     */
    public function markPaid(Request $request, RentalExtension $rentalExtension)
    {
        return DB::transaction(function () use ($rentalExtension) {
            $extension = RentalExtension::where('id', $rentalExtension->id)->lockForUpdate()->firstOrFail();

            if (!in_array($extension->status, ['pending', 'approved'])) {
                return back()->with('error', 'Perpanjangan ini sudah berstatus ' . $extension->status);
            }

            $payment = $extension->latestPayment;
            if ($payment) {
                $payment->update([
                    'status' => 'settlement',
                    'payment_type' => 'cash / manual',
                    'settlement_time' => now(),
                ]);
            } else {
                Payment::create([
                    'booking_id' => $extension->booking_id,
                    'rental_extension_id' => $extension->id,
                    'user_id' => $extension->user_id,
                    'order_id' => 'EXT-CASH-' . $extension->id,
                    'amount' => $extension->price,
                    'status' => 'settlement',
                    'payment_type' => 'cash / manual',
                    'settlement_time' => now(),
                ]);
            }

            $extension->update([
                'status' => 'paid',
                'admin_id' => Auth::id(),
                'approved_at' => $extension->approved_at ?? now(),
            ]);

            $booking = Booking::find($extension->booking_id);
            if ($booking) {
                $newDate = Carbon::parse($extension->new_check_out_date)->toDateString();
                $booking->update([
                    'check_out_date' => $newDate,
                    'due_date' => $newDate,
                ]);
            }

            return back()->with('success', "Perpanjangan sewa #{$extension->id} berhasil dikonfirmasi lunas secara manual (Cash/Transfer). Masa sewa telah diperpanjang!");
        });
    }
}
