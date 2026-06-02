<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('CREATE INDEX IF NOT EXISTS cuti_pengganti_tanggal_status_idx ON cuti (id_pengganti, tanggal_mulai, tanggal_selesai, status)');
            DB::statement('CREATE INDEX IF NOT EXISTS cuti_replacement_status_idx ON cuti (replacement_status, id_pengganti)');
            DB::statement('CREATE INDEX IF NOT EXISTS libur_kompensasi_user_status_idx ON libur_kompensasi (id_user, status)');
            DB::statement('CREATE INDEX IF NOT EXISTS libur_kompensasi_cuti_idx ON libur_kompensasi (id_cuti)');
            return;
        }

        if ($driver === 'mysql') {
            $this->createMysqlIndexIfMissing('cuti', 'cuti_pengganti_tanggal_status_idx', ['id_pengganti', 'tanggal_mulai', 'tanggal_selesai', 'status']);
            $this->createMysqlIndexIfMissing('cuti', 'cuti_replacement_status_idx', ['replacement_status', 'id_pengganti']);
            $this->createMysqlIndexIfMissing('libur_kompensasi', 'libur_kompensasi_user_status_idx', ['id_user', 'status']);
            $this->createMysqlIndexIfMissing('libur_kompensasi', 'libur_kompensasi_cuti_idx', ['id_cuti']);
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS cuti_pengganti_tanggal_status_idx');
            DB::statement('DROP INDEX IF EXISTS cuti_replacement_status_idx');
            DB::statement('DROP INDEX IF EXISTS libur_kompensasi_user_status_idx');
            DB::statement('DROP INDEX IF EXISTS libur_kompensasi_cuti_idx');
            return;
        }

        if ($driver === 'mysql') {
            foreach ([
                ['cuti', 'cuti_pengganti_tanggal_status_idx'],
                ['cuti', 'cuti_replacement_status_idx'],
                ['libur_kompensasi', 'libur_kompensasi_user_status_idx'],
                ['libur_kompensasi', 'libur_kompensasi_cuti_idx'],
            ] as [$table, $index]) {
                if ($this->mysqlIndexExists($table, $index)) {
                    DB::statement("ALTER TABLE {$table} DROP INDEX {$index}");
                }
            }
        }
    }

    private function createMysqlIndexIfMissing(string $table, string $index, array $columns): void
    {
        if ($this->mysqlIndexExists($table, $index)) {
            return;
        }

        DB::statement("CREATE INDEX {$index} ON {$table} (" . implode(', ', $columns) . ')');
    }

    private function mysqlIndexExists(string $table, string $index): bool
    {
        return (bool) DB::selectOne(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1',
            [$table, $index]
        );
    }
};
