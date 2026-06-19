<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_cannot_access_admin_and_atasan_pages(): void
    {
        $petugas = $this->userWithRole('Petugas PPSU', 'petugas@example.test');

        $this->actingAs($petugas)->get('/admin/users')->assertForbidden();
        $this->actingAs($petugas)->get('/atasan/absensi')->assertForbidden();
    }

    public function test_admin_cannot_access_petugas_and_atasan_pages(): void
    {
        $admin = $this->userWithRole('Admin', 'admin@example.test');

        $this->actingAs($admin)->get('/petugas/absensi')->assertForbidden();
        $this->actingAs($admin)->get('/atasan/absensi')->assertForbidden();
    }

    private function userWithRole(string $roleName, string $email): User
    {
        $role = Role::firstOrCreate(['nama_role' => $roleName]);

        return User::create([
            'nama' => $roleName . ' Test',
            'username' => str_replace(['@', '.'], '-', $email),
            'email' => $email,
            'password' => Hash::make('Password123'),
            'id_role' => $role->id_role,
            'status_aktif' => 'aktif',
        ]);
    }
}
