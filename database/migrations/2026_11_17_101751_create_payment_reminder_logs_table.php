<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('recipient_email');
            $table->string('recipient_name');
            $table->string('room_name');
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->string('reminder_type'); // h-7, h-3, h-1, overdue, manual
            $table->string('status')->default('sent'); // sent, failed
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'reminder_type', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_reminder_logs');
    }
};
