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
        Schema::table('cuti', function (Blueprint $table) {
            if (!Schema::hasColumn('cuti', 'id_pengganti')) {
                $table->bigInteger('id_pengganti')->nullable()->after('id_user');
            }
            if (!Schema::hasColumn('cuti', 'alasan_lainnya')) {
                $table->text('alasan_lainnya')->nullable()->after('alasan');
            }
            if (!Schema::hasColumn('cuti', 'alamat_cuti')) {
                $table->text('alamat_cuti')->nullable()->after('alasan_lainnya');
            }
        });

        try {
            Schema::table('cuti', function (Blueprint $table) {
                $table->foreign('id_pengganti')->references('id_user')->on('users')->onDelete('set null');
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuti', function (Blueprint $table) {
            $table->dropForeign(['id_pengganti']);
            $table->dropColumn(['id_pengganti', 'alasan_lainnya', 'alamat_cuti']);
        });
    }
};
