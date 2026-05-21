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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_shift', 50); // Shift 1, Shift 2, Shift 3, etc.
            $table->time('jam_masuk');          // Jam mulai kerja
            $table->time('jam_pulang');          // Jam selesai kerja
            $table->tinyInteger('durasi_jam')->default(8); // Durasi jam kerja (default 8)
            $table->string('warna', 7)->default('#3B82F6'); // Hex color untuk UI
            $table->boolean('status')->default(true); // Aktif/nonaktif
            $table->integer('urutan')->default(0); // Urutan tampil
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
