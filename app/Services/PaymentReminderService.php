<?php

namespace App\Services;

use App\Mail\PaymentReminderMail;
use App\Models\Booking;
use App\Models\PaymentReminderLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class PaymentReminderService
{
    /**
     * Scan database and dispatch payment reminders for H-7, H-3, H-1, and Overdue.
     *
     * @param bool $dryRun
     * @return array
     */
    public function sendScheduledReminders(bool $dryRun = false): array
    {
        $today = Carbon::today();
        $stats = [
            'scanned' => 0,
            'sent' => 0,
            'skipped' => 0,
            'failed' => 0,
            'details' => [],
        ];

        // 1. Ambil semua booking yang statusnya pending (belum lunas)
        $pendingBookings = Booking::with(['user', 'room'])
            ->where('status', 'pending')
            ->get();

        foreach ($pendingBookings as $booking) {
            $stats['scanned']++;

            if (!$booking->user || !$booking->user->email) {
                $stats['skipped']++;
                continue;
            }

            $dueDate = Carbon::parse($booking->due_date ?? $booking->check_in_date)->startOfDay();
            $diffDays = (int) round($today->diffInDays($dueDate, false)); // Positif jika dueDate di masa depan, negatif jika sudah lewat

            $reminderType = null;
            if ($diffDays === 7) {
                $reminderType = 'h-7';
            } elseif ($diffDays === 3) {
                $reminderType = 'h-3';
            } elseif ($diffDays === 1) {
                $reminderType = 'h-1';
            } elseif ($diffDays < 0) {
                $reminderType = 'overdue';
            }

            if (!$reminderType) {
                $stats['skipped']++;
                continue;
            }

            // Cek apakah email pengingat sudah pernah terkirim untuk tagihan & tipe yang sama
            $alreadySent = PaymentReminderLog::where('booking_id', $booking->id)
                ->where('reminder_type', $reminderType)
                ->where('due_date', $dueDate->toDateString())
                ->where('status', 'sent')
                ->exists();

            if ($alreadySent) {
                $stats['skipped']++;
                continue;
            }

            // Kirim email
            if ($dryRun) {
                $stats['sent']++;
                $stats['details'][] = [
                    'booking_id' => $booking->id,
                    'user' => $booking->user->name,
                    'email' => $booking->user->email,
                    'type' => $reminderType,
                    'due_date' => $dueDate->toDateString(),
                    'dry_run' => true,
                ];
                continue;
            }

            $result = $this->sendSingleReminder($booking, $reminderType, $dueDate->toDateString());
            if ($result['status'] === 'sent') {
                $stats['sent']++;
            } else {
                $stats['failed']++;
            }
            $stats['details'][] = $result;
        }

        // 2. Ambil penghuni aktif yang masa sewanya mendekati jatuh tempo (H-7, H-3, H-1 sebelum check_out_date)
        // dan belum melakukan pembayaran perpanjangan sewa
        $activeTenants = Booking::with(['user', 'room'])
            ->where('status', 'success')
            ->where('check_out_date', '>=', $today->toDateString())
            ->get();

        foreach ($activeTenants as $activeBooking) {
            $stats['scanned']++;

            if (!$activeBooking->user || !$activeBooking->user->email) {
                $stats['skipped']++;
                continue;
            }

            $checkoutDueDate = Carbon::parse($activeBooking->check_out_date)->startOfDay();
            $diffDays = (int) round($today->diffInDays($checkoutDueDate, false));

            $reminderType = null;
            if ($diffDays === 7) {
                $reminderType = 'h-7';
            } elseif ($diffDays === 3) {
                $reminderType = 'h-3';
            } elseif ($diffDays === 1) {
                $reminderType = 'h-1';
            }

            if (!$reminderType) {
                $stats['skipped']++;
                continue;
            }

            // Cek apakah penghuni sudah memiliki pesanan berikutnya yang SUDAH LUNAS (success)
            $hasRenewedAndPaid = Booking::where('user_id', $activeBooking->user_id)
                ->where('room_id', $activeBooking->room_id)
                ->where('status', 'success')
                ->where('check_in_date', '>=', $checkoutDueDate->toDateString())
                ->exists();

            if ($hasRenewedAndPaid) {
                // Sudah lunas, jangan kirim pengingat!
                $stats['skipped']++;
                continue;
            }

            // Cek apakah log sudah ada
            $alreadySent = PaymentReminderLog::where('booking_id', $activeBooking->id)
                ->where('reminder_type', $reminderType)
                ->where('due_date', $checkoutDueDate->toDateString())
                ->where('status', 'sent')
                ->exists();

            if ($alreadySent) {
                $stats['skipped']++;
                continue;
            }

            if ($dryRun) {
                $stats['sent']++;
                $stats['details'][] = [
                    'booking_id' => $activeBooking->id,
                    'user' => $activeBooking->user->name,
                    'email' => $activeBooking->user->email,
                    'type' => $reminderType,
                    'due_date' => $checkoutDueDate->toDateString(),
                    'dry_run' => true,
                ];
                continue;
            }

            $result = $this->sendSingleReminder($activeBooking, $reminderType, $checkoutDueDate->toDateString());
            if ($result['status'] === 'sent') {
                $stats['sent']++;
            } else {
                $stats['failed']++;
            }
            $stats['details'][] = $result;
        }

        return $stats;
    }

    /**
     * Send an email reminder for a single booking and record log.
     *
     * @param Booking $booking
     * @param string $reminderType
     * @param string|null $dueDate
     * @return array
     */
    public function sendSingleReminder(Booking $booking, string $reminderType, ?string $dueDate = null): array
    {
        $user = $booking->user;
        $room = $booking->room;
        $effectiveDueDate = $dueDate ?? ($booking->due_date ?? $booking->check_in_date);

        if (!$user || !$user->email) {
            return [
                'booking_id' => $booking->id,
                'status' => 'failed',
                'error' => 'Email pengguna tidak ditemukan.',
            ];
        }

        try {
            Mail::to($user->email)->send(
                new PaymentReminderMail($booking, $reminderType, (string) $effectiveDueDate)
            );

            $log = PaymentReminderLog::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'recipient_email' => $user->email,
                'recipient_name' => $user->name,
                'room_name' => $room->name ?? 'Kamar',
                'amount' => $booking->total_price,
                'due_date' => $effectiveDueDate,
                'reminder_type' => $reminderType,
                'status' => 'sent',
                'error_message' => null,
                'sent_at' => now(),
            ]);

            Log::info("Payment reminder ($reminderType) sent to {$user->email} for Booking #{$booking->id}");

            return [
                'booking_id' => $booking->id,
                'user' => $user->name,
                'email' => $user->email,
                'type' => $reminderType,
                'status' => 'sent',
                'log_id' => $log->id,
            ];
        } catch (Throwable $e) {
            Log::error("Failed to send payment reminder to {$user->email}: " . $e->getMessage());

            PaymentReminderLog::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'recipient_email' => $user->email,
                'recipient_name' => $user->name,
                'room_name' => $room->name ?? 'Kamar',
                'amount' => $booking->total_price,
                'due_date' => $effectiveDueDate,
                'reminder_type' => $reminderType,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => null,
            ]);

            return [
                'booking_id' => $booking->id,
                'user' => $user->name,
                'email' => $user->email,
                'type' => $reminderType,
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }
}
