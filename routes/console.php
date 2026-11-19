<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler harian untuk otomatis memeriksa dan mengirim pengingat pembayaran kontrakan pukul 08:00 WIB
Schedule::command('payment:send-reminders')->timezone('Asia/Jakarta')->dailyAt('08:00');
