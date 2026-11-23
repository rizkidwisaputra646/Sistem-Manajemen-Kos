<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentalExtension;
use App\Models\Room;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        // Cuma ambil yang masih aktif (Pending/Success & Belum Checkout)
        $query = Booking::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'success'])
            ->where('check_out_date', '>=', now()->toDateString())
            ->with(['room', 'activeExtension.latestPayment', 'extensions' => function ($q) {
                $q->latest();
            }])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('room', function ($rq) use ($search) {
                    $rq->where('name', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            });
        }

        $bookings = $query->get();

        return view('user.bookings.index', compact('bookings'));
    }

    public function history(Request $request)
    {
        // Cuma ambil yang sudah lewat atau batal
        $query = Booking::where('user_id', Auth::id())
            ->where(function ($q) {
                $q->where('check_out_date', '<', now()->toDateString())
                    ->orWhereIn('status', ['cancelled', 'expire']);
            })
            ->with('room')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('room', function ($rq) use ($search) {
                    $rq->where('name', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            });
        }

        $bookings = $query->get();

        return view('user.bookings.history', compact('bookings'));
    }


    public function checkout(Room $room)
    {
        // 1 User 1 Kamar Aktif: Cek apakah user sedang punya sewa aktif di kamar lain
        $activeRental = Booking::where('user_id', Auth::id())
            ->where('status', 'success')
            ->where('check_out_date', '>=', now()->toDateString())
            ->with('room')
            ->first();

        if ($activeRental && $activeRental->room_id !== $room->id) {
            return redirect()->route('dashboard')
                ->with('error', "Anda saat ini sedang aktif menyewa {$activeRental->room->name}. Satu akun hanya diperbolehkan menyewa satu kamar.");
        }

        // Kita ambil yang sudah sukses PASTI terkunci
        // DAN yang masih pending tapi belum lewat 2 jam (sesuaikan dengan expiry Midtrans kamu)
        $bookedDates = \App\Models\Booking::where('room_id', $room->id)
            ->where(function ($query) {
                $query->where('status', 'success')
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending')
                            ->where('created_at', '>=', now()->subMinute(45)); // Kunci selama 2 jam saja
                    });
            })
            ->get(['check_in_date', 'check_out_date'])
            ->map(function ($booking) {
                return [
                    'from' => $booking->check_in_date,
                    'to' => $booking->check_out_date
                ];
            });

        return view('user.bookings.checkout', compact('room', 'bookedDates'));
    }



    public function store(Request $request, Room $room)
    {
        // Cuma minta tanggal masuk
        $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
        ]);

        $start = Carbon::parse($request->check_in_date)->startOfDay();
        // Otomatis set 1 bulan kemudian
        $end = $start->copy()->addMonth();

        return DB::transaction(function () use ($room, $start, $end) {
            // Lock baris Room untuk penguncian utama
            $roomLocked = Room::where('id', $room->id)->lockForUpdate()->firstOrFail();

            // 1 User 1 Kamar Aktif: Cek kepemilikan sewa aktif lain
            $activeRental = Booking::where('user_id', Auth::id())
                ->where('status', 'success')
                ->where('check_out_date', '>=', now()->toDateString())
                ->with('room')
                ->first();

            if ($activeRental && $activeRental->room_id !== $roomLocked->id) {
                return back()->withErrors(['check_in_date' => "Anda saat ini sedang aktif menyewa {$activeRental->room->name}. Satu akun hanya diperbolehkan menyewa satu kamar."])->withInput();
            }

            // Validasi Tindih Tanggal (Overlap) interval [start, end)
            // A. Booking aktif atau pending yang belum hangus
            $isOverlappingBooking = Booking::where('room_id', $roomLocked->id)
                ->where(function ($query) {
                    $query->where('status', 'success')
                        ->orWhere(function ($q) {
                            $q->where('status', 'pending')
                                ->where('created_at', '>=', now()->subMinute(45));
                        });
                })
                ->where('check_in_date', '<', $end->toDateString())
                ->where('check_out_date', '>', $start->toDateString())
                ->exists();

            if ($isOverlappingBooking) {
                return back()->withErrors(['check_in_date' => 'Maaf, kamar sudah dipesan pada tanggal tersebut. Silakan pilih tanggal lain.'])->withInput();
            }

            // B. Periode yang sedang ditahan oleh perpanjangan sewa berstatus approved
            $isOverlappingExtension = RentalExtension::where('room_id', $roomLocked->id)
                ->where('status', 'approved')
                ->where('new_check_in_date', '<', $end->toDateString())
                ->where('new_check_out_date', '>', $start->toDateString())
                ->whereHas('latestPayment', function ($pq) {
                    $pq->where('status', 'pending')
                       ->where(function ($q) {
                           $q->whereNull('payment_expires_at')
                             ->orWhere('payment_expires_at', '>=', now());
                       });
                })
                ->exists();

            if ($isOverlappingExtension) {
                return back()->withErrors(['check_in_date' => 'Maaf, kamar pada periode tersebut sedang ditahan untuk perpanjangan sewa penghuni. Silakan pilih tanggal lain.'])->withInput();
            }

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'room_id' => $roomLocked->id,
                'check_in_date' => $start->toDateString(),
                'check_out_date' => $end->toDateString(),
                'total_price' => $roomLocked->price, // Harga paket 1 bulan
                'status' => 'pending',
            ]);

            return redirect()->route('user.bookings.show', $booking);
        });
    }

    public function show(Booking $booking)
    {
        // Midtrans Config (pastikan sudah ada)
        \Midtrans\Config::$serverKey = config('services.midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;


        // user lain gak bisa ngintip id orang lain
        // PERBAIKAN: Gunakan -> bukan :: dan panggil sebagai properti

        if ($booking->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Eits, mau ngintip pesanan siapa?');
        }

        if (!$booking->snap_token) {
            $params = [
                'transaction_details' => [
                    // PERBAIKAN: Buat Order ID unik untuk Midtrans (Format: KOS-ID-ACAK)
                    // Biar kalau user gagal bayar & coba lagi, Midtrans nggak komplain "ID sudah ada"
                    'order_id' => 'KOS-' . $booking->id . '-' . strtoupper(\Illuminate\Support\Str::random(5)),
                    'gross_amount' => (int) $booking->total_price,
                ],

                'expiry' => [
                    'unit' => 'minute',
                    'duration' => 30 // Pembayaran hangus dalam 2 jam
                ],



                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'item_details' => [
                    [
                        'id' => $booking->room_id,
                        'price' => (int) $booking->room->price,
                        'quantity' => 1, // Kamu bisa sesuaikan jadi jumlah hari jika mau
                        'name' => 'Sewa Kamar: ' . $booking->room->name,
                    ]
                ]
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $booking->update(['snap_token' => $snapToken]);
        }

        return view('user.bookings.show', compact('booking'));
    }


    public function callback(Request $request)
    {
        Log::info('Midtrans Callback Datang!', $request->all());
        $serverKey = config('services.midtrans.serverKey');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            Log::warning('Midtrans Signature tidak valid!', ['order_id' => $request->order_id]);
            return response('Invalid Signature', 403);
        }

        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;

        // KASUS 1: Callback Perpanjangan Sewa (EXT-...)
        if (str_starts_with($orderId, 'EXT-')) {
            return DB::transaction(function () use ($orderId, $transactionStatus, $request) {
                $payment = Payment::where('order_id', $orderId)->lockForUpdate()->first();

                if (!$payment) {
                    Log::error("Payment perpanjangan {$orderId} tidak ditemukan!");
                    return response('Payment Not Found', 404);
                }

                // IDEMPOTENSI: Jika transaksi sudah berstatus settlement, langsung return 200 tanpa memproses ulang
                if ($payment->status === 'settlement') {
                    Log::info("Payment {$orderId} sudah pernah diselesaikan sebelumnya (idempotent skip).");
                    return response('OK', 200);
                }

                $extension = RentalExtension::where('id', $payment->rental_extension_id)->lockForUpdate()->first();
                if (!$extension) {
                    return response('Extension Not Found', 404);
                }

                $room = Room::where('id', $extension->room_id)->lockForUpdate()->first();
                $booking = Booking::where('id', $extension->booking_id)->lockForUpdate()->first();

                if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                    // Penanganan Rekonsiliasi Callback Terlambat
                    $isLate = $payment->isExpired() || $extension->status === 'expired';

                    if ($isLate) {
                        $hasConflict = Booking::where('room_id', $extension->room_id)
                            ->where('id', '!=', $extension->booking_id)
                            ->where('status', 'success')
                            ->where('check_in_date', '<', $extension->new_check_out_date->toDateString())
                            ->where('check_out_date', '>', $extension->new_check_in_date->toDateString())
                            ->exists();

                        if ($hasConflict) {
                            $payment->update([
                                'status' => 'deny',
                                'payload' => array_merge($request->all(), ['reconciliation_note' => 'Pembayaran terlambat dan kamar telah terisi sewa lain. Memerlukan refund manual.']),
                            ]);
                            Log::warning("Pembayaran terlambat {$orderId} bentrok dengan sewa lain. Tidak mengaktifkan sewa perpanjangan.");
                            return response('Conflict Late Payment', 200);
                        }
                    }

                    // Pembayaran valid & berhasil
                    $payment->update([
                        'status' => 'settlement',
                        'settlement_time' => now(),
                        'payload' => $request->all(),
                    ]);

                    $extension->update(['status' => 'paid']);

                    // Perpanjang masa sewa pada penyewaan induk
                    $booking->update([
                        'check_out_date' => $extension->new_check_out_date->toDateString(),
                        'due_date' => $extension->new_check_out_date->toDateString(),
                    ]);

                    Log::info("Perpanjangan sewa #{$extension->id} berhasil dilunasi & booking #{$booking->id} diperpanjang!");
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                    $payment->update([
                        'status' => $transactionStatus,
                        'payload' => $request->all(),
                    ]);

                    $extension->update(['status' => 'expired']);
                    Log::info("Perpanjangan sewa #{$extension->id} dibatalkan/expired.");
                }

                return response('OK', 200);
            });
        }

        // KASUS 2: Callback Booking Kamar Baru (KOS-...)
        $orderParts = explode('-', $orderId);
        array_shift($orderParts); // buang 'KOS'
        array_pop($orderParts);   // buang random string
        $bookingId = implode('-', $orderParts);

        $booking = Booking::find($bookingId);

        if ($booking) {
            if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                $booking->update(['status' => 'success']);
                $booking->room->update(['status' => 'full']);

                Payment::updateOrCreate(
                    ['order_id' => $orderId],
                    [
                        'booking_id' => $booking->id,
                        'user_id' => $booking->user_id,
                        'amount' => $booking->total_price,
                        'status' => 'settlement',
                        'settlement_time' => now(),
                        'payload' => $request->all(),
                    ]
                );

                Log::info("Booking $bookingId Berhasil Dilunasi! ✅");
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $booking->update(['status' => 'cancelled']);
                $booking->room->update(['status' => 'available']);

                Payment::updateOrCreate(
                    ['order_id' => $orderId],
                    [
                        'booking_id' => $booking->id,
                        'user_id' => $booking->user_id,
                        'amount' => $booking->total_price,
                        'status' => $transactionStatus,
                        'payload' => $request->all(),
                    ]
                );

                Log::info("Booking $bookingId Dibatalkan/Expired. ❌");
            }
        } else {
            Log::error("Booking ID $bookingId tidak ditemukan di database!");
        }

        return response('OK', 200);
    }

    public function cancel(Booking $booking)
    {
        // Keamanan: Cek apakah ini memang pesanan milik si user
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Hanya bisa membatalkan yang statusnya masih pending
        if ($booking->status === 'pending') {
            $booking->update(['status' => 'cancelled']);

            return redirect()->route('user.bookings.index')
                ->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return back()->with('error', 'Pesanan ini tidak bisa dibatalkan.');
    }

    public function destroy(Booking $booking)
    {
        // Keamanan: Cek apakah ini milik user yang login
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Pastikan cuma bisa hapus yang statusnya cancelled, expire, atau success yang sudah lewat
        // Jangan sampai user hapus pesanan yang lagi jalan/pending bayar
        if (
            in_array($booking->status, ['cancelled', 'expire']) ||
            ($booking->status == 'success' && $booking->check_out_date < now()->toDateString())
        ) {

            $booking->delete();

            return back()->with('success', 'Riwayat berhasil dihapus selamanya!');
        }

        return back()->with('error', 'Pesanan ini masih aktif, nggak boleh dihapus dulu.');
    }

    public function clearHistory()
    {
        $userId = Auth::id();

        // Hapus permanen semua yang:
        // 1. Statusnya cancelled atau expire
        // 2. Statusnya success tapi sudah lewat tanggal check-out (sudah keluar)
        $deleted = Booking::where('user_id', $userId)
            ->where(function ($query) {
                $query->whereIn('status', ['cancelled', 'expire'])
                    ->orWhere(function ($q) {
                        $q->where('status', 'success')
                            ->where('check_out_date', '<', now()->toDateString());
                    });
            })
            ->delete();

        if ($deleted) {
            return back()->with('success', 'Wushhh! Semua riwayat sudah bersih.');
        }

        return back()->with('info', 'Riwayat kamu memang sudah bersih kok.');
    }



    public function downloadReceipt(Booking $booking)
    {
        // 1. Keamanan: Cek apakah pesanan ini milik user atau admin, & sudah lunas
        if ((auth()->user()->role !== 'admin' && $booking->user_id !== auth()->id()) || $booking->status !== 'success') {
            abort(403, 'Kuitansi belum tersedia atau akses ditolak.');
        }

        // 2. Load View khusus kuitansi
        $pdf = Pdf::loadView('user.bookings.receipt_pdf', compact('booking'));
        $filename = 'Kuitansi-KosKu-' . strtoupper(substr($booking->id, 0, 8)) . '.pdf';

        if (request('stream')) {
            return $pdf->stream($filename);
        }

        // 3. Download dengan nama file unik
        return $pdf->download($filename);
    }
}
