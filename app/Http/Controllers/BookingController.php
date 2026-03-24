<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        // Cuma ambil yang masih aktif (Pending/Success & Belum Checkout)
        $bookings = Booking::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'success'])
            ->where('check_out_date', '>=', now()->toDateString())
            ->with('room')
            ->latest()
            ->get();

        return view('user.bookings.index', compact('bookings'));
    }

    public function history()
    {
        // Cuma ambil yang sudah lewat atau batal
        $bookings = Booking::where('user_id', Auth::id())
            ->where(function ($q) {
                $q->where('check_out_date', '<', now()->toDateString())
                    ->orWhereIn('status', ['cancelled', 'expire']);
            })
            ->with('room')
            ->latest()
            ->get();

        return view('user.bookings.history', compact('bookings'));
    }


    public function checkout(Room $room)
    {
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
        $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
        ]);

        // 1. HITUNG DURASI HARI
        $start = Carbon::parse($request->check_in_date);
        $end = Carbon::parse($request->check_out_date);
        $duration = $start->diffInDays($end);

        // JIKA durasi 0 (karena in & out di hari yang sama), paksa jadi 1 hari
        if ($duration < 1) {
            $duration = 1;
        }

        // 2. HITUNG TOTAL HARGA
        $totalPrice = $duration * $room->price;

        // 3. SIMPAN KE DATABASE
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'room_id' => $room->id,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        // PERBAIKAN: Jangan kirim $booking->id, kirim saja $booking. 
        // Laravel otomatis akan mengambil UUID kalau Model sudah disetting.
        return redirect()->route('user.bookings.show', $booking);
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

        // Verifikasi tanda tangan keamanan
        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                // Ambil ID Booking dari order_id (Format: BOOK-ID-TIME)
                $orderParts = explode('-', $request->order_id);
                $bookingId = $orderParts[1];

                $booking = Booking::find($bookingId);
                if ($booking) {
                    // Jika berhasil bayar
                    if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
                        $booking->update(['status' => 'success']);
                    }
                    // JIKA WAKTU HABIS ATAU DIBATALKAN
                    elseif ($request->transaction_status == 'expire' || $request->transaction_status == 'cancel') {
                        $booking->update(['status' => 'cancelled']);
                    }

                    // // Opsional: Otomatis ubah status kamar jadi 'full'
                    // $booking->room->update(['status' => 'full']);
                }
            }
        }
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
}
