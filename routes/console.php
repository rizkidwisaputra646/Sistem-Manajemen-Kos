<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler harian untuk otomatis memeriksa dan mengirim pengingat pembayaran kontrakan pukul 08:00 WIB
Schedule::command('payment:send-reminders')->timezone('Asia/Jakarta')->dailyAt('08:00');

// Perintah membatalkan pesanan pending yang melewati batas waktu
Artisan::command('bookings:cancel-expired', function () {
    $countMidtrans = \App\Models\Booking::where('status', 'pending')
        ->where(function ($q) {
            $q->where('payment_method', '!=', 'manual')->orWhereNull('payment_method');
        })
        ->where('created_at', '<', now()->subMinutes(45))
        ->update(['status' => 'cancelled']);

    $countManual = \App\Models\Booking::where('status', 'pending')
        ->where('payment_method', 'manual')
        ->where('created_at', '<', now()->subHours(24))
        ->update(['status' => 'cancelled']);

    $total = $countMidtrans + $countManual;
    $this->info("Berhasil membatalkan {$total} pesanan pending yang kedaluwarsa.");
})->purpose('Batalkan otomatis pesanan pending yang sudah lewat batas waktu');

Schedule::command('bookings:cancel-expired')->everyFifteenMinutes();
