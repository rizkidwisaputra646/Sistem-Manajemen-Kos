<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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

        $start = Carbon::parse($request->check_in_date);
        // Otomatis set 1 bulan kemudian
        $end = $start->copy()->addMonth();

        // Validasi Tindih Tanggal (Overlap)
        $isOverlapping = Booking::where('room_id', $room->id)
            ->where(function ($query) {
                $query->where('status', 'success')
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending')
                            ->where('created_at', '>=', now()->subMinute(45)); // Sesuaikan dengan expiry midtrans
                    });
            })
            ->where('check_in_date', '<', $end)
            ->where('check_out_date', '>', $start)
            ->exists();

        if ($isOverlapping) {
            return back()->withErrors(['check_in_date' => 'Maaf, kamar sudah dipesan pada tanggal tersebut. Silakan pilih tanggal lain.'])->withInput();
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'room_id' => $room->id,
            'check_in_date' => $start,
            'check_out_date' => $end,
            'total_price' => $room->price, // Harga paket 1 bulan
            'status' => 'pending',
        ]);

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

        if ($hashed == $request->signature_key) {
            // Ambil bagian tengah dari Order ID (Format: KOS-{UUID}-{RANDOM})
            $orderParts = explode('-', $request->order_id);

            // Buang 'KOS' (bagian pertama)
            array_shift($orderParts);
            // Buang 'RANDOM' (bagian terakhir)
            array_pop($orderParts);

            // Gabungkan sisanya kembali menggunakan '-' agar menjadi UUID utuh
            $bookingId = implode('-', $orderParts);

            $booking = Booking::find($bookingId);

            if ($booking) {
                $status = $request->transaction_status;

                if ($status == 'capture' || $status == 'settlement') {
                    $booking->update(['status' => 'success']);
                    $booking->room->update(['status' => 'unavailable']);
                    Log::info("Booking $bookingId Berhasil Dilunasi! ✅");
                } elseif ($status == 'deny' || $status == 'expire' || $status == 'cancel') {
                    $booking->update(['status' => 'cancelled']);
                    // Kalau batal, kamarnya dibikin tersedia lagi
                    $booking->room->update(['status' => 'available']);
                    Log::info("Booking $bookingId Dibatalkan/Expired. ❌");
                }
            } else {
                Log::error("Booking ID $bookingId tidak ditemukan di database!");
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



    public function renew(Booking $booking) // Laravel otomatis mencari data berdasarkan UUID di URL
    {
        // Pastikan hanya pemilik yang bisa perpanjang
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $newStart = \Carbon\Carbon::parse($booking->check_out_date);
        $newEnd = $newStart->copy()->addMonth();

        // Validasi Tindih Tanggal (Overlap) untuk perpanjangan
        $isOverlapping = Booking::where('room_id', $booking->room_id)
            ->where(function ($query) {
                $query->where('status', 'success')
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending')
                            ->where('created_at', '>=', now()->subMinute(45));
                    });
            })
            ->where('check_in_date', '<', $newEnd)
            ->where('check_out_date', '>', $newStart)
            ->exists();

        if ($isOverlapping) {
            return back()->with('error', 'Maaf, kamar ini sudah dipesan oleh orang lain untuk periode tersebut.');
        }

        $newBooking = Booking::create([
            'user_id' => auth()->id(),
            'room_id' => $booking->room_id,
            'check_in_date' => $newStart,
            'check_out_date' => $newEnd,
            'total_price' => $booking->room->price,
            'status' => 'pending',
        ]);

        return redirect()->route('user.bookings.show', $newBooking);
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
