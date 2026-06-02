<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuti', function (Blueprint $table) {
            if (! Schema::hasColumn('cuti', 'replacement_status')) {
                $table->string('replacement_status', 20)->default('pending')->after('id_pengganti');
            }
            if (! Schema::hasColumn('cuti', 'replacement_confirmed_at')) {
                $table->timestamp('replacement_confirmed_at')->nullable()->after('replacement_status');
            }
            if (! Schema::hasColumn('cuti', 'replacement_note')) {
                $table->text('replacement_note')->nullable()->after('replacement_confirmed_at');
            }
        });

        if (! Schema::hasTable('libur_kompensasi')) {
            Schema::create('libur_kompensasi', function (Blueprint $table) {
                $table->bigIncrements('id_libur_kompensasi');
                $table->unsignedBigInteger('id_user');
                $table->unsignedBigInteger('id_cuti');
                $table->date('tanggal_kerja');
                $table->date('tanggal_dipakai')->nullable();
                $table->string('status', 20)->default('tersedia');
                $table->text('keterangan')->nullable();
                $table->timestamps();

                $table->unique(['id_user', 'id_cuti', 'tanggal_kerja'], 'libur_kompensasi_user_cuti_tanggal_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('libur_kompensasi');

        Schema::table('cuti', function (Blueprint $table) {
            foreach (['replacement_note', 'replacement_confirmed_at', 'replacement_status'] as $column) {
                if (Schema::hasColumn('cuti', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
