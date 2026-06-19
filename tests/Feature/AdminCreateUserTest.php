<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\TempatTugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCreateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user_without_profile_photo(): void
    {
        Storage::fake('public');
        [$admin, $petugasRole, $tempat] = $this->createAdminContext();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'nama' => 'Petugas Tanpa Foto',
            'nik' => '3175000000000001',
            'username' => 'petugas.tanpa.foto',
            'email' => 'tanpa-foto@example.test',
            'password' => 'Password123',
            'id_role' => $petugasRole->id_role,
            'id_tempat' => $tempat->id_tempat,
            'status_aktif' => 'aktif',
            'jabatan' => 'Petugas',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'username' => 'petugas.tanpa.foto',
            'email' => 'tanpa-foto@example.test',
            'foto_profil' => null,
        ]);
    }

    public function test_admin_can_create_user_with_optional_profile_photo(): void
    {
        Storage::fake('public');
        [$admin, $petugasRole, $tempat] = $this->createAdminContext();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'nama' => 'Petugas Dengan Foto',
            'nik' => '3175000000000002',
            'username' => 'petugas.dengan.foto',
            'email' => 'dengan-foto@example.test',
            'password' => 'Password123',
            'foto_profil' => UploadedFile::fake()->image('profile.jpg', 640, 640),
            'id_role' => $petugasRole->id_role,
            'id_tempat' => $tempat->id_tempat,
            'status_aktif' => 'aktif',
            'jabatan' => 'Petugas',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $user = User::where('username', 'petugas.dengan.foto')->firstOrFail();

        $this->assertNotNull($user->foto_profil);
        $this->assertStringStartsWith('profil/', $user->foto_profil);
        Storage::disk('public')->assertExists($user->foto_profil);
    }

    /**
     * @return array{0: User, 1: Role, 2: TempatTugas}
     */
    private function createAdminContext(): array
    {
        $adminRole = Role::firstOrCreate(['nama_role' => 'Admin']);
        $petugasRole = Role::firstOrCreate(['nama_role' => 'Petugas PPSU']);
        $tempat = TempatTugas::create([
            'nama_tempat' => 'Kelurahan Pisangan Baru',
            'alamat' => 'Jl. Pisangan Baru',
        ]);

        $admin = User::create([
            'nama' => 'Admin Test',
            'username' => 'admin.test',
            'email' => 'admin@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $adminRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        return [$admin, $petugasRole, $tempat];
    }
}
