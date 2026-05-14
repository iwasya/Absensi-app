<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_sensitive', function (Blueprint $table) {
            $table->string('nik_hash', 64)->nullable()->after('nik_encrypted');

            // Add index for fast NIK lookup during login
            $table->index('nik_hash', 'user_sensitive_nik_hash_index');
        });
    }

    public function down(): void
    {
        Schema::table('user_sensitive', function (Blueprint $table) {
            $table->dropIndex('user_sensitive_nik_hash_index');
            $table->dropColumn('nik_hash');
        });
    }
};