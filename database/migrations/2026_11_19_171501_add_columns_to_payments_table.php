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
        Schema::table('payments', function (Blueprint $table) {
            $table->uuid('booking_id')->nullable()->after('id');
            $table->foreign('booking_id')->references('id')->on('bookings')->restrictOnDelete();
            
            $table->foreignId('rental_extension_id')->nullable()->after('booking_id')->constrained('rental_extensions')->restrictOnDelete();
            $table->foreignId('user_id')->after('rental_extension_id')->constrained()->restrictOnDelete();
            
            $table->string('order_id')->unique()->after('user_id');
            $table->decimal('amount', 15, 2)->after('order_id');
            $table->string('status')->default('pending')->after('amount'); // pending, settlement, expire, cancel, deny
            $table->string('snap_token')->nullable()->after('status');
            $table->string('payment_type')->nullable()->after('snap_token');
            $table->timestamp('payment_expires_at')->nullable()->after('payment_type');
            $table->timestamp('settlement_time')->nullable()->after('payment_expires_at');
            $table->json('payload')->nullable()->after('settlement_time');
            
            $table->index(['booking_id', 'status']);
            $table->index(['rental_extension_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['rental_extension_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'booking_id',
                'rental_extension_id',
                'user_id',
                'order_id',
                'amount',
                'status',
                'snap_token',
                'payment_type',
                'payment_expires_at',
                'settlement_time',
                'payload',
            ]);
        });
    }
};
