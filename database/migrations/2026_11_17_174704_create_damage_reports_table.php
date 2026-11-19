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
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('facility_name');
            $table->string('category'); // Kelistrikan, Pipa/Air, Furnitur, Elektronik, Bangunan, Lainnya
            $table->text('description');
            $table->string('image')->nullable();
            $table->string('status')->default('Menunggu'); // Menunggu, Diproses, Selesai, Ditolak
            $table->text('admin_response')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_read_by_admin')->default(false);
            $table->boolean('is_read_by_user')->default(true);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['room_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damage_reports');
    }
};
