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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->time('start_time')->comment('Waktu masuk yang direncanakan');
            $table->time('end_time')->comment('Waktu keluar yang direncanakan');
            $table->unsignedSmallInteger('grace_period_minutes')->default(15)
                ->comment('Tolerensi keterlambatan dalam menit');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
