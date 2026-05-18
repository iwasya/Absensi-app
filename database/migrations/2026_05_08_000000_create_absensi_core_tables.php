<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('periode')) {
            Schema::create('periode', function (Blueprint $table) {
                $table->bigIncrements('id_periode');
                $table->string('nama_periode', 100)->unique();
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai');
                $table->string('status', 20)->default('nonaktif');
            });
        }

        if (! Schema::hasTable('absensi')) {
            Schema::create('absensi', function (Blueprint $table) {
                $table->bigIncrements('id_absensi');
                $table->unsignedBigInteger('id_user');
                $table->unsignedBigInteger('id_periode')->nullable();
                $table->date('tanggal');
                $table->time('jam_masuk')->nullable();
                $table->string('foto_masuk')->nullable();
                $table->decimal('latitude_masuk', 10, 7)->nullable();
                $table->decimal('longitude_masuk', 10, 7)->nullable();
                $table->text('lokasi_masuk')->nullable();
                $table->time('jam_pulang')->nullable();
                $table->string('foto_pulang')->nullable();
                $table->decimal('latitude_pulang', 10, 7)->nullable();
                $table->decimal('longitude_pulang', 10, 7)->nullable();
                $table->text('lokasi_pulang')->nullable();
                $table->string('status', 30)->default('hadir');
                $table->text('keterangan')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->unique(['id_user', 'tanggal']);
                $table->index(['tanggal', 'status']);
                $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
                $table->foreign('id_periode')->references('id_periode')->on('periode')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('cuti')) {
            Schema::create('cuti', function (Blueprint $table) {
                $table->bigIncrements('id_cuti');
                $table->unsignedBigInteger('id_user');
                $table->unsignedBigInteger('id_periode')->nullable();
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai');
                $table->string('jenis_cuti', 50);
                $table->text('alasan')->nullable();
                $table->string('status', 20)->default('pending');
                $table->unsignedBigInteger('approver_id')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->index(['id_user', 'status']);
                $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
                $table->foreign('id_periode')->references('id_periode')->on('periode')->nullOnDelete();
                $table->foreign('approver_id')->references('id_user')->on('users')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('tugas')) {
            Schema::create('tugas', function (Blueprint $table) {
                $table->bigIncrements('id_tugas');
                $table->unsignedBigInteger('id_user');
                $table->unsignedBigInteger('id_periode')->nullable();
                $table->dateTime('tanggal_mulai');
                $table->dateTime('tanggal_selesai')->nullable();
                $table->text('uraian');
                $table->string('status', 20)->default('pending');
                $table->timestamp('created_at')->nullable();

                $table->index(['id_user', 'status']);
                $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
                $table->foreign('id_periode')->references('id_periode')->on('periode')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('sanksi')) {
            Schema::create('sanksi', function (Blueprint $table) {
                $table->bigIncrements('id_sanksi');
                $table->unsignedBigInteger('id_user');
                $table->string('jenis_sanksi', 100);
                $table->date('tanggal');
                $table->text('keterangan')->nullable();

                $table->index(['id_user', 'tanggal']);
                $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('notifikasi')) {
            Schema::create('notifikasi', function (Blueprint $table) {
                $table->bigIncrements('id_notifikasi');
                $table->unsignedBigInteger('id_user');
                $table->string('judul', 150);
                $table->text('pesan');
                $table->string('tipe', 50)->default('system');
                $table->boolean('status_baca')->default(false);
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('reference_type')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->index(['id_user', 'status_baca']);
                $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('kalender')) {
            Schema::create('kalender', function (Blueprint $table) {
                $table->bigIncrements('id_kalender');
                $table->date('tanggal');
                $table->string('nama_event', 150)->nullable();
                $table->string('jenis_event', 50);
                $table->text('keterangan')->nullable();

                $table->index('tanggal');
            });
        }

        if (! Schema::hasTable('activity_log')) {
            Schema::create('activity_log', function (Blueprint $table) {
                $table->bigIncrements('id_log');
                $table->unsignedBigInteger('id_user')->nullable();
                $table->string('aktivitas', 150);
                $table->string('modul', 100)->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('reference_type')->nullable();
                $table->string('status', 30)->default('success');
                $table->text('catatan')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('device')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->index(['id_user', 'created_at']);
                $table->foreign('id_user')->references('id_user')->on('users')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('user_sensitive')) {
            Schema::create('user_sensitive', function (Blueprint $table) {
                $table->bigIncrements('id_sensitive');
                $table->unsignedBigInteger('id_user')->unique();
                $table->text('nik_encrypted')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sensitive');
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('kalender');
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('sanksi');
        Schema::dropIfExists('tugas');
        Schema::dropIfExists('cuti');
        Schema::dropIfExists('absensi');
        Schema::dropIfExists('periode');
    }
};
