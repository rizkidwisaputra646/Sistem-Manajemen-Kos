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
        // Auto-cancel pesanan pending Midtrans yang sudah lewat batas 45 menit
        Booking::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->where('payment_method', '!=', 'manual')->orWhereNull('payment_method');
            })
            ->where('created_at', '<', now()->subMinutes(45))
            ->update(['status' => 'cancelled']);

        // Auto-cancel pesanan manual pending yang sudah lewat batas 24 jam
        Booking::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where('payment_method', 'manual')
            ->where('created_at', '<', now()->subHours(24))
            ->update(['status' => 'cancelled']);

        // Auto-sync ke Midtrans jika ada pending booking milik user yang punya snap token
        $pendingBookings = Booking::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->where('payment_method', '!=', 'manual')->orWhereNull('payment_method');
            })
            ->where('created_at', '>=', now()->subMinutes(45))
            ->whereNotNull('snap_token')
            ->get();

        foreach ($pendingBookings as $pb) {
            $this->syncBookingWithMidtrans($pb);
        }

        // Auto-sync ke Midtrans jika ada perpanjangan sewa berstatus approved yang pembayarannya pending
        $approvedExtensions = RentalExtension::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->whereHas('latestPayment', function ($q) {
                $q->where('status', 'pending');
            })
            ->with('latestPayment')
            ->get();

        $extController = app(\App\Http\Controllers\RentalExtensionController::class);
        foreach ($approvedExtensions as $ae) {
            $extController->syncExtensionPayment($ae);
        }

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
        // Auto-cancel pesanan pending yang sudah lewat batas waktu untuk kamar ini
        Booking::where('room_id', $room->id)
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where(function ($sub) {
                        $sub->where('payment_method', '!=', 'manual')->orWhereNull('payment_method');
                    })->where('created_at', '<', now()->subMinutes(45));
                })->orWhere(function ($sq) {
                    $sq->where('payment_method', 'manual')
                        ->where('created_at', '<', now()->subHours(24));
                });
            })
            ->update(['status' => 'cancelled']);

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

        // Cek apakah user masih memiliki pesanan pending yang belum selesai di kamar lain
        $pendingBooking = Booking::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where(function ($sub) {
                        $sub->where('payment_method', '!=', 'manual')->orWhereNull('payment_method');
                    })->where('created_at', '>=', now()->subMinutes(45));
                })->orWhere(function ($sq) {
                    $sq->where('payment_method', 'manual')
                        ->where('created_at', '>=', now()->subHours(24));
                });
            })
            ->with('room')
            ->first();

        if ($pendingBooking && $pendingBooking->room_id !== $room->id) {
            return redirect()->route('user.bookings.show', $pendingBooking)
                ->with('error', "Anda masih memiliki pesanan yang belum diselesaikan untuk {$pendingBooking->room->name}. Silakan selesaikan pembayaran atau batalkan pesanan tersebut terlebih dahulu.");
        }

        // Kita ambil yang sudah sukses PASTI terkunci
        // DAN yang masih pending tapi belum hangus (45 min Midtrans / 24 jam Manual)
        $bookedDates = \App\Models\Booking::where('room_id', $room->id)
            ->where(function ($query) {
                $query->where('status', 'success')
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending')
                            ->where(function ($sq) {
                                $sq->where(function ($ssq) {
                                    $ssq->where(function ($sub) {
                                        $sub->where('payment_method', '!=', 'manual')->orWhereNull('payment_method');
                                    })->where('created_at', '>=', now()->subMinutes(45));
                                })->orWhere(function ($ssq) {
                                    $ssq->where('payment_method', 'manual')
                                        ->where('created_at', '>=', now()->subHours(24));
                                });
                            });
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
            'payment_method' => 'nullable|in:midtrans,manual',
        ]);

        $paymentMethod = $request->input('payment_method', 'midtrans');
        $start = Carbon::parse($request->check_in_date)->startOfDay();
        $end = $start->copy()->addMonth();

        return DB::transaction(function () use ($room, $start, $end, $paymentMethod) {
            // Lock baris Room untuk penguncian utama
            $roomLocked = Room::where('id', $room->id)->lockForUpdate()->firstOrFail();

            // Auto-cancel booking pending yang sudah lewat batas waktu pada kamar ini
            Booking::where('room_id', $roomLocked->id)
                ->where('status', 'pending')
                ->where(function ($q) {
                    $q->where(function ($sq) {
                        $sq->where(function ($sub) {
                            $sub->where('payment_method', '!=', 'manual')->orWhereNull('payment_method');
                        })->where('created_at', '<', now()->subMinutes(45));
                    })->orWhere(function ($sq) {
                        $sq->where('payment_method', 'manual')
                            ->where('created_at', '<', now()->subHours(24));
                    });
                })
                ->update(['status' => 'cancelled']);

            // 1 User 1 Kamar Aktif: Cek kepemilikan sewa aktif lain
            $activeRental = Booking::where('user_id', Auth::id())
                ->where('status', 'success')
                ->where('check_out_date', '>=', now()->toDateString())
                ->with('room')
                ->first();

            if ($activeRental && $activeRental->room_id !== $roomLocked->id) {
                return back()->withErrors(['check_in_date' => "Anda saat ini sedang aktif menyewa {$activeRental->room->name}. Satu akun hanya diperbolehkan menyewa satu kamar."])->withInput();
            }

            // Cek apakah user masih memiliki pesanan pending yang belum selesai di kamar lain
            $pendingBooking = Booking::where('user_id', Auth::id())
                ->where('status', 'pending')
                ->where(function ($q) {
                    $q->where(function ($sq) {
                        $sq->where(function ($sub) {
                            $sub->where('payment_method', '!=', 'manual')->orWhereNull('payment_method');
                        })->where('created_at', '>=', now()->subMinutes(45));
                    })->orWhere(function ($sq) {
                        $sq->where('payment_method', 'manual')
                            ->where('created_at', '>=', now()->subHours(24));
                    });
                })
                ->with('room')
                ->first();

            if ($pendingBooking && $pendingBooking->room_id !== $roomLocked->id) {
                return back()->withErrors(['check_in_date' => "Anda masih memiliki pesanan yang belum diselesaikan untuk {$pendingBooking->room->name}. Silakan selesaikan pembayaran atau batalkan pesanan tersebut terlebih dahulu."])->withInput();
            }

            // Validasi Tindih Tanggal (Overlap) interval [start, end)
            $isOverlappingBooking = Booking::where('room_id', $roomLocked->id)
                ->where(function ($query) {
                    $query->where('status', 'success')
                        ->orWhere(function ($q) {
                            $q->where('status', 'pending')
                                ->where(function ($sq) {
                                    $sq->where(function ($ssq) {
                                        $ssq->where(function ($sub) {
                                            $sub->where('payment_method', '!=', 'manual')->orWhereNull('payment_method');
                                        })->where('created_at', '>=', now()->subMinutes(45));
                                    })->orWhere(function ($ssq) {
                                        $ssq->where('payment_method', 'manual')
                                            ->where('created_at', '>=', now()->subHours(24));
                                    });
                                });
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
                'total_price' => $roomLocked->price,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
            ]);

            return redirect()->route('user.bookings.show', $booking);
        });
    }

    public function show(Booking $booking)
    {
        // User lain gak bisa ngintip id orang lain
        if ($booking->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Eits, mau ngintip pesanan siapa?');
        }

        // Auto-cancel jika pesanan masih pending:
        // Midtrans: > 45 menit
        // Manual: > 24 jam
        if ($booking->status === 'pending') {
            $isExpired = false;
            if ($booking->payment_method === 'manual') {
                $isExpired = $booking->created_at < now()->subHours(24);
            } else {
                $isExpired = $booking->created_at < now()->subMinutes(45);
            }

            if ($isExpired) {
                $booking->update(['status' => 'cancelled']);
            }
        }

        // Midtrans Config
        \Midtrans\Config::$serverKey = config('services.midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Auto-check status ke Midtrans jika masih pending dan punya token/payment (khusus online)
        if ($booking->status === 'pending' && $booking->snap_token && $booking->payment_method !== 'manual') {
            $this->syncBookingWithMidtrans($booking);
            $booking->refresh();
        }

        // Hanya buat Midtrans Snap token jika payment_method BUKAN manual
        if ($booking->status === 'pending' && $booking->payment_method !== 'manual' && !$booking->snap_token) {
            $remainingMinutes = max(1, 45 - (int) $booking->created_at->diffInMinutes(now()));
            $orderId = 'KOS-' . $booking->id . '-' . strtoupper(\Illuminate\Support\Str::random(5));

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $booking->total_price,
                ],
                'expiry' => [
                    'unit' => 'minute',
                    'duration' => $remainingMinutes,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'item_details' => [
                    [
                        'id' => $booking->room_id,
                        'price' => (int) $booking->room->price,
                        'quantity' => 1,
                        'name' => 'Sewa Kamar: ' . $booking->room->name,
                    ]
                ]
            ];

            try {
                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $booking->update(['snap_token' => $snapToken]);

                Payment::updateOrCreate(
                    ['booking_id' => $booking->id, 'status' => 'pending'],
                    [
                        'user_id' => $booking->user_id,
                        'order_id' => $orderId,
                        'amount' => $booking->total_price,
                        'status' => 'pending',
                        'snap_token' => $snapToken,
                        'payment_expires_at' => now()->addMinutes($remainingMinutes),
                    ]
                );
            } catch (\Throwable $e) {
                Log::warning('Gagal generate Snap Token: ' . $e->getMessage());
            }
        }

        return view('user.bookings.show', compact('booking'));
    }

    /**
     * Endpoint Sinkronisasi Status Pembayaran dari Frontend Snap (onSuccess/onPending)
     * atau tombol "Cek Status Pembayaran" di browser.
     */
    public function syncPayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $orderId = $request->input('order_id');
        $payload = $request->all();

        $synced = $this->syncBookingWithMidtrans($booking, $orderId, $payload);
        $booking->refresh();

        return response()->json([
            'success' => $synced,
            'status' => $booking->status,
            'message' => $synced ? 'Pembayaran berhasil diverifikasi dan kamar telah aktif!' : 'Status pembayaran belum berhasil.'
        ]);
    }

    /**
     * Helper sinkronisasi status booking dengan Midtrans API / payload.
     */
    public function syncBookingWithMidtrans(Booking $booking, ?string $orderId = null, ?array $payload = null): bool
    {
        if ($booking->status === 'success') {
            return true;
        }

        // Cari record payment terkait jika orderId belum diberikan
        $payment = null;
        if ($orderId) {
            $payment = Payment::where('order_id', $orderId)->first();
        }
        if (!$payment) {
            $payment = Payment::where('booking_id', $booking->id)->latest()->first();
            if ($payment) {
                $orderId = $payment->order_id;
            }
        }

        $transactionStatus = $payload['transaction_status'] ?? null;

        // Query status resmi ke Midtrans jika orderId ada
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
                Log::info("Sinkronisasi status Midtrans untuk {$orderId}: " . $e->getMessage());
            }
        }

        if (in_array($transactionStatus, ['settlement', 'capture', 'success'])) {
            DB::transaction(function () use ($booking, $orderId, $payload) {
                $booking->update(['status' => 'success']);
                Room::where('id', $booking->room_id)->update(['status' => 'full']);

                if ($orderId) {
                    Payment::updateOrCreate(
                        ['order_id' => $orderId],
                        [
                            'booking_id' => $booking->id,
                            'user_id' => $booking->user_id,
                            'amount' => $booking->total_price,
                            'status' => 'settlement',
                            'settlement_time' => now(),
                            'payload' => $payload,
                        ]
                    );
                }
            });

            Log::info("Booking {$booking->id} berhasil disinkronisasi menjadi LUNAS (success)!");
            return true;
        }

        return false;
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

        return DB::transaction(function () use ($bookingId, $orderId, $transactionStatus, $request) {
            $booking = Booking::where('id', $bookingId)->lockForUpdate()->first();

            if (!$booking) {
                Log::error("Booking ID $bookingId tidak ditemukan di database!");
                return response('Booking Not Found', 404);
            }

            // IDEMPOTENSI: Jika booking sudah lunas (success), langsung return 200 tanpa duplikasi update
            if ($booking->status === 'success') {
                Log::info("Booking $bookingId sudah lunas sebelumnya (idempotent skip).");
                return response('OK', 200);
            }

            $room = Room::where('id', $booking->room_id)->lockForUpdate()->first();

            if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                // Rekonsiliasi Callback Terlambat: jika booking sudah cancelled atau lewat 45 menit
                $isStale = $booking->status === 'cancelled' || $booking->created_at < now()->subMinutes(45);

                if ($isStale) {
                    // Cek apakah ada booking sewa lain yang bentrok dan sudah lunas
                    $hasConflict = Booking::where('room_id', $booking->room_id)
                        ->where('id', '!=', $booking->id)
                        ->where('status', 'success')
                        ->where('check_in_date', '<', $booking->check_out_date)
                        ->where('check_out_date', '>', $booking->check_in_date)
                        ->exists();

                    if ($hasConflict) {
                        Payment::updateOrCreate(
                            ['order_id' => $orderId],
                            [
                                'booking_id' => $booking->id,
                                'user_id' => $booking->user_id,
                                'amount' => $booking->total_price,
                                'status' => 'deny',
                                'payload' => array_merge($request->all(), [
                                    'reconciliation_note' => 'Pembayaran diterima setelah batas waktu 45 menit dan kamar telah disewa penghuni lain. Memerlukan refund manual.',
                                ]),
                            ]
                        );
                        Log::warning("Pembayaran terlambat booking $bookingId bentrok dengan sewa lain. Tidak mengaktifkan sewa.");
                        return response('Conflict Late Payment', 200);
                    }
                }

                $booking->update(['status' => 'success']);
                if ($room) {
                    $room->update(['status' => 'full']);
                }

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
                if ($room) {
                    $room->update(['status' => 'available']);
                }

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

            return response('OK', 200);
        });
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
            DB::transaction(function () use ($booking) {
                $extensionIds = $booking->extensions()->pluck('id');

                \App\Models\Payment::where('booking_id', $booking->id)
                    ->orWhereIn('rental_extension_id', $extensionIds)
                    ->delete();

                $booking->extensions()->delete();
                $booking->reminderLogs()->delete();
                $booking->delete();
            });

            return back()->with('success', 'Riwayat berhasil dihapus selamanya!');
        }

        return back()->with('error', 'Pesanan ini masih aktif, nggak boleh dihapus dulu.');
    }

    public function clearHistory()
    {
        $userId = Auth::id();

        // Cari semua pesanan yang memenuhi kriteria riwayat selesai/batal
        $query = Booking::where('user_id', $userId)
            ->where(function ($query) {
                $query->whereIn('status', ['cancelled', 'expire'])
                    ->orWhere(function ($q) {
                        $q->where('status', 'success')
                            ->where('check_out_date', '<', now()->toDateString());
                    });
            });

        $bookingIds = $query->pluck('id');

        if ($bookingIds->isNotEmpty()) {
            DB::transaction(function () use ($bookingIds) {
                $extensionIds = \App\Models\RentalExtension::whereIn('booking_id', $bookingIds)->pluck('id');

                // Hapus data anak terlebih dahulu agar bersih dari relasi payments & extensions
                \App\Models\Payment::whereIn('booking_id', $bookingIds)
                    ->orWhereIn('rental_extension_id', $extensionIds)
                    ->delete();

                \App\Models\RentalExtension::whereIn('booking_id', $bookingIds)->delete();
                \App\Models\PaymentReminderLog::whereIn('booking_id', $bookingIds)->delete();

                Booking::whereIn('id', $bookingIds)->delete();
            });

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
