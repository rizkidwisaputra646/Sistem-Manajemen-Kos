<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public string $reminderType; // 'h-7', 'h-3', 'h-1', 'overdue', 'manual'
    public string $dueDate;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, string $reminderType, string $dueDate)
    {
        $this->booking = $booking;
        $this->reminderType = $reminderType;
        $this->dueDate = $dueDate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $roomName = $this->booking->room->name ?? 'Kamar';
        
        $subjectPrefix = match ($this->reminderType) {
            'h-7' => '[Pengingat H-7] Tagihan Sewa ' . $roomName,
            'h-3' => '[Penting H-3] Tagihan Sewa ' . $roomName,
            'h-1' => '[Segera H-1] Batas Akhir Pembayaran Sewa ' . $roomName,
            'overdue' => '[Pemberitahuan Keterlambatan] Tagihan Sewa ' . $roomName . ' Melewati Jatuh Tempo',
            default => '[Pengingat Pembayaran] Tagihan Sewa ' . $roomName,
        };

        return new Envelope(
            subject: $subjectPrefix . ' - Jatuh Tempo ' . date('d/m/Y', strtotime($this->dueDate)),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_reminder',
            with: [
                'booking' => $this->booking,
                'reminderType' => $this->reminderType,
                'dueDate' => $this->dueDate,
                'user' => $this->booking->user,
                'room' => $this->booking->room,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
