<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_ketua_regu')) {
                $table->boolean('is_ketua_regu')->default(false)->after('regu');
            }
        });

        Schema::table('absensi', function (Blueprint $table) {
            if (! Schema::hasColumn('absensi', 'approval_pulang_forwarded_by')) {
                $table->unsignedBigInteger('approval_pulang_forwarded_by')->nullable()->after('approval_pulang_requested_at');
            }
            if (! Schema::hasColumn('absensi', 'approval_pulang_forwarded_at')) {
                $table->timestamp('approval_pulang_forwarded_at')->nullable()->after('approval_pulang_forwarded_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            foreach (['approval_pulang_forwarded_by', 'approval_pulang_forwarded_at'] as $column) {
                if (Schema::hasColumn('absensi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_ketua_regu')) {
                $table->dropColumn('is_ketua_regu');
            }
        });
    }
};
