<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            if (! Schema::hasColumn('tugas', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('tugas', 'is_late_input')) {
                $table->boolean('is_late_input')->default(false)->after('submitted_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            foreach (['submitted_at', 'is_late_input'] as $column) {
                if (Schema::hasColumn('tugas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
