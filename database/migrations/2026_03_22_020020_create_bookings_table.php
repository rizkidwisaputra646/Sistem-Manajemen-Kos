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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Siapa yang pesan
            $table->foreignId('room_id')->constrained()->onDelete('cascade'); // Kamar mana
            $table->date('check_in_date'); // Kapan mau mulai masuk
            $table->date('check_out_date');
            $table->decimal('total_price', 15, 2); // Harga yang harus dibayar
            $table->string('status')->default('pending'); // pending, success, cancelled
            $table->string('snap_token')->nullable(); // Untuk Midtrans nanti
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
