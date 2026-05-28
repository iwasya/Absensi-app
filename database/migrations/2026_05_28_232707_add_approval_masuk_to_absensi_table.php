<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            if (! Schema::hasColumn('absensi', 'approval_masuk_status')) {
                $table->string('approval_masuk_status', 30)->nullable()->after('keterangan');
            }

            if (! Schema::hasColumn('absensi', 'approval_masuk_requested_at')) {
                $table->timestamp('approval_masuk_requested_at')->nullable()->after('approval_masuk_status');
            }

            if (! Schema::hasColumn('absensi', 'approval_masuk_forwarded_by')) {
                $table->unsignedBigInteger('approval_masuk_forwarded_by')->nullable()->after('approval_masuk_requested_at');
            }

            if (! Schema::hasColumn('absensi', 'approval_masuk_forwarded_at')) {
                $table->timestamp('approval_masuk_forwarded_at')->nullable()->after('approval_masuk_forwarded_by');
            }

            if (! Schema::hasColumn('absensi', 'approval_masuk_approved_by')) {
                $table->unsignedBigInteger('approval_masuk_approved_by')->nullable()->after('approval_masuk_forwarded_at');
            }

            if (! Schema::hasColumn('absensi', 'approval_masuk_reason')) {
                $table->text('approval_masuk_reason')->nullable()->after('approval_masuk_approved_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $columns = [
                'approval_masuk_status',
                'approval_masuk_requested_at',
                'approval_masuk_forwarded_by',
                'approval_masuk_forwarded_at',
                'approval_masuk_approved_by',
                'approval_masuk_reason',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('absensi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
