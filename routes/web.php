<?php

use App\Http\Controllers\ProfileController;
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
    
    // Jika bukan admin, tampilkan dashboard user biasa
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

});

// 4. Jalur Profile (Bawaan Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';