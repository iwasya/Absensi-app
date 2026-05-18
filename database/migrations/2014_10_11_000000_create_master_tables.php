<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->bigIncrements('id_role');
                $table->string('nama_role', 50)->unique();
            });
        }

        if (! Schema::hasTable('tempat_tugas')) {
            Schema::create('tempat_tugas', function (Blueprint $table) {
                $table->bigIncrements('id_tempat');
                $table->string('nama_tempat', 150);
                $table->text('alamat')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
            });
        }

        if (Schema::hasTable('roles') && DB::table('roles')->count() === 0) {
            DB::table('roles')->insert([
                ['nama_role' => 'Admin'],
                ['nama_role' => 'Atasan'],
                ['nama_role' => 'Petugas PPSU'],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tempat_tugas');
        Schema::dropIfExists('roles');
    }
};
