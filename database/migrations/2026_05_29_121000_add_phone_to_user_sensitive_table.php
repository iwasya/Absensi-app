<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_sensitive')) {
            return;
        }

        Schema::table('user_sensitive', function (Blueprint $table) {
            if (! Schema::hasColumn('user_sensitive', 'no_hp_encrypted')) {
                $table->text('no_hp_encrypted')->nullable()->after('nik_hash');
            }

            if (! Schema::hasColumn('user_sensitive', 'no_hp_hash')) {
                $table->string('no_hp_hash', 64)->nullable()->after('no_hp_encrypted');
                $table->index('no_hp_hash', 'user_sensitive_no_hp_hash_index');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('user_sensitive')) {
            return;
        }

        Schema::table('user_sensitive', function (Blueprint $table) {
            if (Schema::hasColumn('user_sensitive', 'no_hp_hash')) {
                $table->dropIndex('user_sensitive_no_hp_hash_index');
                $table->dropColumn('no_hp_hash');
            }

            if (Schema::hasColumn('user_sensitive', 'no_hp_encrypted')) {
                $table->dropColumn('no_hp_encrypted');
            }
        });
    }
};
