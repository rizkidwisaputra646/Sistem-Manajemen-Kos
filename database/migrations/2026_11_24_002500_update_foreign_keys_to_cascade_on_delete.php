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
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['rental_extension_id']);

            $table->foreign('booking_id')
                ->references('id')
                ->on('bookings')
                ->cascadeOnDelete();

            $table->foreign('rental_extension_id')
                ->references('id')
                ->on('rental_extensions')
                ->cascadeOnDelete();
        });

        Schema::table('rental_extensions', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);

            $table->foreign('booking_id')
                ->references('id')
                ->on('bookings')
                ->cascadeOnDelete();
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

            $table->foreign('booking_id')
                ->references('id')
                ->on('bookings')
                ->restrictOnDelete();

            $table->foreign('rental_extension_id')
                ->references('id')
                ->on('rental_extensions')
                ->restrictOnDelete();
        });

        Schema::table('rental_extensions', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);

            $table->foreign('booking_id')
                ->references('id')
                ->on('bookings')
                ->restrictOnDelete();
        });
    }
};
