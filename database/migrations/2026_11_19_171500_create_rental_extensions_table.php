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
        Schema::create('rental_extensions', function (Blueprint $table) {
            $table->id();
            $table->uuid('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings')->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            
            $table->date('previous_check_out_date');
            $table->date('new_check_in_date');
            $table->date('new_check_out_date');
            $table->decimal('price', 15, 2);
            
            $table->string('status')->default('pending'); // pending, approved, rejected, paid, expired
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['booking_id', 'status']);
            $table->index(['room_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_extensions');
    }
};
