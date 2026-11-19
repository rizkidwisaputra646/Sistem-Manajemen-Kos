<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PaymentReminderLog;
use App\Services\PaymentReminderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentReminderController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentReminderLog::with(['booking', 'user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('reminder_type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_email', 'like', "%{$search}%")
                  ->orWhere('room_name', 'like', "%{$search}%")
                  ->orWhere('booking_id', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(15);

        $totalSent = PaymentReminderLog::where('status', 'sent')->count();
        $totalFailed = PaymentReminderLog::where('status', 'failed')->count();
        $lastSent = PaymentReminderLog::where('status', 'sent')->latest('sent_at')->first();

        return view('admin.reminders.index', compact('logs', 'totalSent', 'totalFailed', 'lastSent'));
    }

    public function runReminders(PaymentReminderService $service)
    {
        $result = $service->sendScheduledReminders(false);

        return back()->with('success', "Proses pengingat selesai! Terkirim: {$result['sent']}, Dilewati: {$result['skipped']}, Gagal: {$result['failed']}.");
    }

    public function sendSingle(Request $request, Booking $booking, PaymentReminderService $service)
    {
        $reminderType = $request->input('reminder_type', 'manual');
        $dueDate = $request->input('due_date', $booking->effective_due_date);

        $result = $service->sendSingleReminder($booking, $reminderType, (string) $dueDate);

        if ($result['status'] === 'sent') {
            return back()->with('success', "Email pengingat berhasil dikirimkan ke {$result['email']}!");
        }

        return back()->with('error', "Gagal mengirimkan email pengingat: " . ($result['error'] ?? 'Terjadi kesalahan'));
    }

    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            $recipient = $request->test_email;
            Mail::raw("Halo! Ini adalah email uji coba dari sistem Website Kontrakan KosKu.\nKonfigurasi SMTP Anda berfungsi dengan baik dan siap mengirimkan notifikasi pengingat pembayaran ke seluruh penghuni.\n\nWaktu Kirim: " . now()->format('d F Y, H:i:s') . ' WIB', function ($m) use ($recipient) {
                $m->to($recipient)->subject('[Tes Sistem] Uji Coba Pengiriman Email Kontrakan');
            });

            return back()->with('success', "Email uji coba berhasil dikirim ke {$recipient}!");
        } catch (\Throwable $e) {
            return back()->with('error', "Gagal mengirim email uji coba: " . $e->getMessage());
        }
    }
}
