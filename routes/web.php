<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinancialReportController;
use App\Http\Controllers\Admin\PaymentReminderController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\ResidentController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DamageReportController as AdminDamageReportController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DamageReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// 1. Publik (Tanpa Login)
Route::get('/', function () {
    $settings = \App\Models\Setting::pluck('value', 'key');
    $rooms = \App\Models\Room::where('status', 'available')->take(3)->get();
    return view('welcome', compact('settings', 'rooms'));
});

// 2. Midtrans Callback (Wajib di Luar Middleware)
Route::post('/midtrans-callback', [BookingController::class, 'callback'])->name('midtrans.callback');

// --- KELOMPOK USER (Sudah Login) ---
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        if (auth()->user()->role == 'admin') return redirect()->route('admin.dashboard');
        
        $query = \App\Models\Room::query();
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('facilities', 'like', "%{$search}%")
                  ->orWhere('price', 'like', "%{$search}%");
            });
        }
        
        $rooms = $query->get();
        return view('dashboard', compact('rooms'));
    })->name('dashboard');

    // Route Kamar
    Route::get('/rooms/{room}', function (\App\Models\Room $room) {
        return view('rooms.show', compact('room'));
    })->name('rooms.show');
    Route::get('/rooms/{room}/checkout', [BookingController::class, 'checkout'])->name('rooms.checkout');
    Route::post('/rooms/{room}/book', [BookingController::class, 'store'])->name('rooms.store');

    // --- MANAJEMEN PESANAN (Prefix: my-bookings) ---
    Route::prefix('my-bookings')->name('user.bookings.')->group(function () {
        // 1. Taruh yang statis/pasti di atas
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/history', [BookingController::class, 'history'])->name('history');

        // Taruh ini di atas {booking}
        Route::delete('/clear-all-history', [BookingController::class, 'clearHistory'])->name('clear-history');

        // 2. Baru taruh yang pakai variabel {booking} di bawah
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
        Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        Route::post('/{booking}/renew', [BookingController::class, 'renew'])->name('renew');
        Route::delete('/{booking}', [BookingController::class, 'destroy'])->name('destroy');
    });

    // Profile Bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // pdf
    Route::get('/my-bookings/{booking}/receipt', [BookingController::class, 'downloadReceipt'])->name('user.bookings.receipt');

    // --- PELAPORAN KERUSAKAN FASILITAS PENGHUNI ---
    Route::prefix('damage-reports')->name('user.damage-reports.')->group(function () {
        Route::get('/', [DamageReportController::class, 'index'])->name('index');
        Route::get('/create', [DamageReportController::class, 'create'])->name('create');
        Route::post('/', [DamageReportController::class, 'store'])->name('store');
        Route::get('/{damageReport}', [DamageReportController::class, 'show'])->name('show');
    });
});

// 4. Khusus Admin
Route::middleware(['auth', 'admin']) // Pastikan kamu punya middleware 'admin'
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings/export', [DashboardController::class, 'export'])->name('bookings.export');

        Route::resource('rooms', RoomController::class);
        Route::patch('/rooms/{room}/toggle-status', [RoomController::class, 'toggleStatus'])->name('rooms.toggle-status');

        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/{booking}/receipt', [ReservationController::class, 'downloadReceipt'])->name('reservations.receipt');
        Route::patch('/reservations/{booking}/status', [ReservationController::class, 'updateStatus'])->name('reservations.update-status');
        Route::patch('/reservations/{booking}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

        Route::get('/residents', [ResidentController::class, 'index'])->name('residents.index');
        
        // Pelaporan Keuangan & Transaksi (Tetap dipertahankan)
        Route::get('/reports', [FinancialReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [FinancialReportController::class, 'exportPdf'])->name('reports.pdf');

        // Pelaporan Kerusakan Fasilitas (Fitur Baru)
        Route::get('/damage-reports', [AdminDamageReportController::class, 'index'])->name('damage-reports.index');
        Route::get('/damage-reports/{damageReport}', [AdminDamageReportController::class, 'show'])->name('damage-reports.show');
        Route::match(['put', 'patch'], '/damage-reports/{damageReport}', [AdminDamageReportController::class, 'update'])->name('damage-reports.update');
        Route::delete('/damage-reports/{damageReport}', [AdminDamageReportController::class, 'destroy'])->name('damage-reports.destroy');

        // Pengingat Email
        Route::get('/reminders', [PaymentReminderController::class, 'index'])->name('reminders.index');
        Route::post('/reminders/run', [PaymentReminderController::class, 'runReminders'])->name('reminders.run');
        Route::post('/reminders/{booking}/send', [PaymentReminderController::class, 'sendSingle'])->name('reminders.send-single');
        Route::post('/reminders/test', [PaymentReminderController::class, 'sendTestEmail'])->name('reminders.test');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

require __DIR__ . '/auth.php';
