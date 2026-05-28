<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->indexes() as $index) {
            $this->createIndexIfMissing($index['table'], $index['name'], $index['columns']);
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->indexes()) as $index) {
            $this->dropIndexIfExists($index['table'], $index['name']);
        }
    }

    private function indexes(): array
    {
        return [
            [
                'table' => 'absensi',
                'name' => 'absensi_approval_masuk_status_idx',
                'columns' => ['approval_masuk_status', 'approval_masuk_requested_at'],
            ],
            [
                'table' => 'absensi',
                'name' => 'absensi_approval_pulang_status_idx',
                'columns' => ['approval_pulang_status', 'approval_pulang_requested_at'],
            ],
            [
                'table' => 'tugas',
                'name' => 'tugas_periode_latest_idx',
                'columns' => ['id_periode', 'id_tugas'],
            ],
            [
                'table' => 'tugas',
                'name' => 'tugas_tanggal_latest_idx',
                'columns' => ['tanggal_mulai', 'id_tugas'],
            ],
            [
                'table' => 'users',
                'name' => 'users_regu_ketua_idx',
                'columns' => ['regu', 'is_ketua_regu'],
            ],
            [
                'table' => 'users',
                'name' => 'users_status_role_idx',
                'columns' => ['status_aktif', 'id_role'],
            ],
        ];
    }

    private function createIndexIfMissing(string $table, string $name, array $columns): void
    {
        if ($this->indexExists($table, $name)) {
            return;
        }

        $columnList = implode(', ', array_map(fn (string $column) => $this->wrap($column), $columns));
        DB::statement('CREATE INDEX ' . $this->wrap($name) . ' ON ' . $this->wrap($table) . ' (' . $columnList . ')');
    }

    private function dropIndexIfExists(string $table, string $name): void
    {
        if (! $this->indexExists($table, $name)) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS ' . $this->wrap($name));
            return;
        }

        DB::statement('DROP INDEX ' . $this->wrap($name) . ' ON ' . $this->wrap($table));
    }

    private function indexExists(string $table, string $name): bool
    {
        if (DB::getDriverName() === 'pgsql') {
            return (bool) DB::selectOne(
                'SELECT 1 FROM pg_indexes WHERE schemaname = current_schema() AND tablename = ? AND indexname = ?',
                [$table, $name]
            );
        }

        if (DB::getDriverName() === 'mysql') {
            return (bool) DB::selectOne(
                'SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1',
                [$table, $name]
            );
        }

        return false;
    }

    private function wrap(string $identifier): string
    {
        return DB::getQueryGrammar()->wrap($identifier);
    }
};
