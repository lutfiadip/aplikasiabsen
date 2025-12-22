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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // Relasi
            // Asumsi Anda memiliki tabel 'users'
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Relasi ke tabel schedules
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');

            // Data Kehadiran
            $table->date('attendance_date');
            $table->dateTime('check_in_time')->nullable();
            $table->dateTime('check_out_time')->nullable();

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Status dan Keterangan
            $table->boolean('is_late')->default(false);
            $table->text('notes')->nullable();

            // Indeks unik untuk mencegah double check-in pada hari yang sama
            // $table->unique(['user_id', 'attendance_date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
