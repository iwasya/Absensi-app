<?php

use App\Models\Role;
use App\Models\User;
use App\Support\QueryFilters;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasTable('roles') || User::count() > 0) {
            return;
        }

        $roleId = QueryFilters::whereRoleAlias(Role::query(), ['admin'])->value('id_role');

        if (! $roleId) {
            $roleId = Role::create(['nama_role' => 'Admin'])->id_role;
        }

        User::create([
            'nama' => env('INITIAL_ADMIN_NAME', 'Administrator'),
            'username' => env('INITIAL_ADMIN_USERNAME', 'admin'),
            'email' => env('INITIAL_ADMIN_EMAIL', 'admin@local.test'),
            'password' => Hash::make(env('INITIAL_ADMIN_PASSWORD', 'Admin12345')),
            'id_role' => $roleId,
            'status_aktif' => 'aktif',
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        User::where('username', env('INITIAL_ADMIN_USERNAME', 'admin'))
            ->where('email', env('INITIAL_ADMIN_EMAIL', 'admin@local.test'))
            ->delete();
    }
};
