<?php

use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\ProfileController;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Halaman Landing (Bisa diakses siapa saja)
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    // Jika yang login adalah admin, lempar ke dashboard admin
    if (Auth::user()->role == 'admin') {
        return redirect()->route('admin.dashboard');
    }

    $rooms = Room::all();

    // Ambil semua kamar yang statusnya 'available' untuk ditampilkan ke User
    // $rooms = Room::where('status', 'available')->get();

    // Jika bukan admin, tampilkan dashboard user biasa
    return view('dashboard', compact('rooms'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Tambahkan di bawah route /dashboard
Route::get('/rooms/{room}', function (\App\Models\Room $room) {
    return view('rooms.show', compact('room'));
})->middleware(['auth'])->name('rooms.show');


// Tambahkan di bawah route rooms.show
Route::get('/rooms/{room}/checkout', [App\Http\Controllers\BookingController::class, 'checkout'])->name('rooms.checkout');
Route::post('/rooms/{room}/book', [App\Http\Controllers\BookingController::class, 'store'])->name('rooms.store');

Route::delete('/bookings/clear-all-history', [App\Http\Controllers\BookingController::class, 'clearHistory'])->name('user.bookings.clear-history');

// Halaman Detail/Ringkasan Booking setelah klik Pesan
Route::get('/bookings/{booking}', [App\Http\Controllers\BookingController::class, 'show'])->name('user.bookings.show');
Route::delete('/bookings/{booking}', [App\Http\Controllers\BookingController::class, 'destroy'])->name('user.bookings.destroy');

// Tambahkan di dalam group middleware 'auth'
Route::get('/my-bookings', [App\Http\Controllers\BookingController::class, 'index'])->name('user.bookings.index');
Route::get('/my-bookings/history', [App\Http\Controllers\BookingController::class, 'history'])->name('user.bookings.history');
// Tambahkan di dalam group middleware auth
Route::post('/bookings/{booking}/cancel', [App\Http\Controllers\BookingController::class, 'cancel'])->name('user.bookings.cancel');

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings/export', [App\Http\Controllers\Admin\DashboardController::class, 'export'])->name('bookings.export');
        Route::patch('/rooms/{room}/toggle-status', [App\Http\Controllers\Admin\RoomController::class, 'toggleStatus'])->name('rooms.toggle-status');
        Route::resource('rooms', RoomController::class);
        Route::get('/reservations', [App\Http\Controllers\Admin\ReservationController::class, 'index'])->name('reservations.index');
        Route::patch('/reservations/{booking}/status', [App\Http\Controllers\Admin\ReservationController::class, 'updateStatus'])->name('reservations.update-status');
        Route::patch('/reservations/{booking}/cancel', [App\Http\Controllers\Admin\ReservationController::class, 'cancel'])->name('reservations.cancel');
        Route::get('/residents', [App\Http\Controllers\Admin\ResidentController::class, 'index'])->name('residents.index');
        Route::get('/reports', [App\Http\Controllers\Admin\FinancialReportController::class, 'index'])->name('reports.index');
    });

// 4. Jalur Profile (Bawaan Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route untuk Callback Midtrans (Taruh di paling bawah atau luar middleware)
Route::post('/midtrans-callback', [App\Http\Controllers\BookingController::class, 'callback'])->name('midtrans.callback');

require __DIR__ . '/auth.php';
