<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('absensi') || ! Schema::hasColumn('absensi', 'status')) {
            return;
        }

        match (DB::getDriverName()) {
            'mysql' => DB::statement("ALTER TABLE absensi MODIFY COLUMN status ENUM('hadir','telat','tidak_hadir','diluar_area','tidak_absen','akses_dibuka') DEFAULT 'hadir'"),
            'pgsql' => DB::statement("ALTER TABLE absensi ALTER COLUMN status TYPE VARCHAR(30), ALTER COLUMN status SET DEFAULT 'hadir'"),
            default => null,
        };
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('absensi') || ! Schema::hasColumn('absensi', 'status')) {
            return;
        }

        match (DB::getDriverName()) {
            'mysql' => DB::statement("ALTER TABLE absensi MODIFY COLUMN status ENUM('hadir','telat','tidak_hadir','diluar_area') DEFAULT 'hadir'"),
            'pgsql' => DB::statement("ALTER TABLE absensi ALTER COLUMN status TYPE VARCHAR(30), ALTER COLUMN status SET DEFAULT 'hadir'"),
            default => null,
        };
    }
};
