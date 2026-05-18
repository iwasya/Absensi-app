<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->indexes() as $statement) {
            DB::statement($statement);
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->indexNames() as $indexName) {
            DB::statement("DROP INDEX IF EXISTS {$indexName}");
        }
    }

    private function indexes(): array
    {
        return [
            'CREATE INDEX IF NOT EXISTS absensi_user_tanggal_latest_idx ON absensi (id_user, tanggal DESC, id_absensi DESC)',
            'CREATE INDEX IF NOT EXISTS absensi_tanggal_latest_idx ON absensi (tanggal DESC, id_absensi DESC)',
            'CREATE INDEX IF NOT EXISTS absensi_status_idx ON absensi (status)',
            'CREATE INDEX IF NOT EXISTS cuti_user_tanggal_status_idx ON cuti (id_user, tanggal_mulai DESC, status)',
            'CREATE INDEX IF NOT EXISTS cuti_status_latest_idx ON cuti (status, id_cuti DESC)',
            'CREATE INDEX IF NOT EXISTS cuti_periode_idx ON cuti (id_periode)',
            'CREATE INDEX IF NOT EXISTS tugas_user_tanggal_status_idx ON tugas (id_user, tanggal_mulai DESC, status)',
            'CREATE INDEX IF NOT EXISTS tugas_status_latest_idx ON tugas (status, id_tugas DESC)',
            'CREATE INDEX IF NOT EXISTS tugas_periode_idx ON tugas (id_periode)',
            'CREATE INDEX IF NOT EXISTS notifikasi_user_status_latest_idx ON notifikasi (id_user, status_baca, id_notifikasi DESC)',
            'CREATE INDEX IF NOT EXISTS activity_log_user_modul_latest_idx ON activity_log (id_user, modul, id_log DESC)',
            'CREATE INDEX IF NOT EXISTS activity_log_modul_latest_idx ON activity_log (modul, id_log DESC)',
            'CREATE INDEX IF NOT EXISTS kalender_tanggal_latest_idx ON kalender (tanggal, id_kalender)',
            'CREATE INDEX IF NOT EXISTS periode_aktif_tanggal_idx ON periode (status, tanggal_mulai, tanggal_selesai, id_periode DESC)',
            'CREATE INDEX IF NOT EXISTS users_role_idx ON users (id_role)',
            'CREATE INDEX IF NOT EXISTS users_tempat_idx ON users (id_tempat)',
            'CREATE INDEX IF NOT EXISTS users_created_at_idx ON users (created_at)',
        ];
    }

    private function indexNames(): array
    {
        return [
            'absensi_user_tanggal_latest_idx',
            'absensi_tanggal_latest_idx',
            'absensi_status_idx',
            'cuti_user_tanggal_status_idx',
            'cuti_status_latest_idx',
            'cuti_periode_idx',
            'tugas_user_tanggal_status_idx',
            'tugas_status_latest_idx',
            'tugas_periode_idx',
            'notifikasi_user_status_latest_idx',
            'activity_log_user_modul_latest_idx',
            'activity_log_modul_latest_idx',
            'kalender_tanggal_latest_idx',
            'periode_aktif_tanggal_idx',
            'users_role_idx',
            'users_tempat_idx',
            'users_created_at_idx',
        ];
    }
};
