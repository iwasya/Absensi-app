<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'regu')) {
                $table->string('regu', 20)->nullable()->after('id_tempat');
            }
            if (! Schema::hasColumn('users', 'shift')) {
                $table->string('shift', 30)->nullable()->after('regu');
            }
            if (! Schema::hasColumn('users', 'status_aktif')) {
                $table->string('status_aktif', 20)->default('aktif')->after('shift');
            }
            if (! Schema::hasColumn('users', 'no_hp')) {
                $table->string('no_hp', 30)->nullable()->after('status_aktif');
            }
            if (! Schema::hasColumn('users', 'alamat')) {
                $table->text('alamat')->nullable()->after('no_hp');
            }
            if (! Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan', 100)->nullable()->after('alamat');
            }
        });

        Schema::table('absensi', function (Blueprint $table) {
            if (! Schema::hasColumn('absensi', 'shift')) {
                $table->string('shift', 30)->nullable()->after('tanggal');
            }
            if (! Schema::hasColumn('absensi', 'jam_istirahat_mulai')) {
                $table->time('jam_istirahat_mulai')->nullable()->after('lokasi_masuk');
            }
            if (! Schema::hasColumn('absensi', 'jam_istirahat_selesai')) {
                $table->time('jam_istirahat_selesai')->nullable()->after('jam_istirahat_mulai');
            }
            if (! Schema::hasColumn('absensi', 'approval_pulang_status')) {
                $table->string('approval_pulang_status', 30)->nullable()->after('keterangan');
            }
            if (! Schema::hasColumn('absensi', 'approval_pulang_requested_at')) {
                $table->timestamp('approval_pulang_requested_at')->nullable()->after('approval_pulang_status');
            }
            if (! Schema::hasColumn('absensi', 'approval_pulang_approved_by')) {
                $table->unsignedBigInteger('approval_pulang_approved_by')->nullable()->after('approval_pulang_requested_at');
            }
            if (! Schema::hasColumn('absensi', 'approval_pulang_reason')) {
                $table->text('approval_pulang_reason')->nullable()->after('approval_pulang_approved_by');
            }
        });

        Schema::table('cuti', function (Blueprint $table) {
            if (! Schema::hasColumn('cuti', 'dokumen_path')) {
                $table->string('dokumen_path')->nullable()->after('alamat_cuti');
            }
            if (! Schema::hasColumn('cuti', 'admin_status')) {
                $table->string('admin_status', 20)->default('pending')->after('dokumen_path');
            }
            if (! Schema::hasColumn('cuti', 'admin_approver_id')) {
                $table->unsignedBigInteger('admin_approver_id')->nullable()->after('admin_status');
            }
            if (! Schema::hasColumn('cuti', 'admin_processed_at')) {
                $table->timestamp('admin_processed_at')->nullable()->after('admin_approver_id');
            }
        });

        Schema::table('sanksi', function (Blueprint $table) {
            if (! Schema::hasColumn('sanksi', 'acknowledged_at')) {
                $table->timestamp('acknowledged_at')->nullable()->after('keterangan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sanksi', function (Blueprint $table) {
            if (Schema::hasColumn('sanksi', 'acknowledged_at')) {
                $table->dropColumn('acknowledged_at');
            }
        });

        Schema::table('cuti', function (Blueprint $table) {
            $columns = ['dokumen_path', 'admin_status', 'admin_approver_id', 'admin_processed_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('cuti', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('absensi', function (Blueprint $table) {
            $columns = [
                'shift',
                'jam_istirahat_mulai',
                'jam_istirahat_selesai',
                'approval_pulang_status',
                'approval_pulang_requested_at',
                'approval_pulang_approved_by',
                'approval_pulang_reason',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('absensi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            foreach (['regu', 'shift', 'status_aktif', 'no_hp', 'alamat', 'jabatan'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
