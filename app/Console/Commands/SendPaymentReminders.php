<?php

namespace App\Console\Commands;

use App\Services\PaymentReminderService;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:send-reminders {--dry-run : Simulasi pengiriman tanpa mengirim email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email pengingat otomatis jatuh tempo pembayaran sewa kontrakan (H-7, H-3, H-1, & Keterlambatan)';

    /**
     * Execute the console command.
     */
    public function handle(PaymentReminderService $service): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $this->info($isDryRun ? '🔍 Menjalankan simulasi pengingat pembayaran (DRY-RUN)...' : '🚀 Memulai pengiriman pengingat pembayaran...');

        $result = $service->sendScheduledReminders($isDryRun);

        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Data Diperiksa', $result['scanned']],
                ['Email Terkirim / Lolos Kriteria', $result['sent']],
                ['Dilewati (Belum Waktunya / Sudah Lunas / Sudah Pernah Dikirim)', $result['skipped']],
                ['Gagal Kirim', $result['failed']],
            ]
        );

        if (!empty($result['details'])) {
            $this->info('Rincian Tindakan:');
            foreach ($result['details'] as $detail) {
                $status = $detail['status'] ?? ($detail['dry_run'] ? 'simulated' : 'unknown');
                $this->line(" - Booking #{$detail['booking_id']} | Penerima: {$detail['email']} | Tipe: {$detail['type']} | Status: {$status}");
            }
        }

        $this->info('✅ Selesai.');
        return Command::SUCCESS;
    }
}
