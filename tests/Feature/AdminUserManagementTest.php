<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\TempatTugas;
use App\Models\User;
use App\Models\UserSensitive;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Role $adminRole;
    private Role $petugasRole;
    private Role $atasanRole;
    private TempatTugas $tempat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::firstOrCreate(['nama_role' => 'Admin']);
        $this->petugasRole = Role::firstOrCreate(['nama_role' => 'Petugas PPSU']);
        $this->atasanRole = Role::firstOrCreate(['nama_role' => 'Atasan']);
        $this->tempat = TempatTugas::create([
            'nama_tempat' => 'Kelurahan Test',
            'alamat' => 'Jl. Test No. 1',
        ]);

        $this->admin = User::create([
            'nama' => 'Admin Test',
            'username' => 'admin.test',
            'email' => 'admin@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->adminRole->id_role,
            'status_aktif' => 'aktif',
        ]);
    }

    // ==================== USER LISTING ====================

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertViewIs('admin.users');
    }

    public function test_admin_can_filter_users_by_search(): void
    {
        User::create([
            'nama' => 'John Doe',
            'username' => 'john.doe',
            'email' => 'john@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/users?search=John');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        User::create([
            'nama' => 'Petugas Test',
            'username' => 'petugas.test',
            'email' => 'petugas@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/users?role=' . $this->petugasRole->id_role);

        $response->assertStatus(200);
        $response->assertSee('Petugas Test');
    }

    public function test_non_admin_cannot_access_users_list(): void
    {
        $petugas = User::create([
            'nama' => 'Petugas Test',
            'username' => 'petugas.test',
            'email' => 'petugas@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $response = $this->actingAs($petugas)->get('/admin/users');

        $response->assertForbidden();
    }

    // ==================== USER CREATION ====================

    public function test_admin_can_view_create_user_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users/create');

        $response->assertStatus(200);
        $response->assertViewIs('admin.users-create');
    }

    public function test_admin_can_create_user_without_photo(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'nama' => 'New User',
            'username' => 'newuser',
            'email' => 'newuser@example.test',
            'password' => 'Password123',
            'id_role' => $this->petugasRole->id_role,
            'id_tempat' => $this->tempat->id_tempat,
            'status_aktif' => 'aktif',
            'jabatan' => 'Petugas',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'email' => 'newuser@example.test',
            'nama' => 'New User',
        ]);
    }

    public function test_admin_can_create_user_with_profile_photo(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'nama' => 'User With Photo',
            'username' => 'userwithphoto',
            'email' => 'withphoto@example.test',
            'password' => 'Password123',
            'id_role' => $this->petugasRole->id_role,
            'id_tempat' => $this->tempat->id_tempat,
            'status_aktif' => 'aktif',
            'jabatan' => 'Petugas',
            'foto_profil' => UploadedFile::fake()->image('profile.jpg', 640, 640),
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $user = User::where('username', 'userwithphoto')->firstOrFail();
        $this->assertNotNull($user->foto_profil);
        Storage::disk('public')->assertExists($user->foto_profil);
    }

    public function test_admin_can_create_user_with_nik(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'nama' => 'User With NIK',
            'username' => 'userwithnik',
            'email' => 'withnik@example.test',
            'password' => 'Password123',
            'nik' => '3175000000000001',
            'id_role' => $this->petugasRole->id_role,
            'id_tempat' => $this->tempat->id_tempat,
            'status_aktif' => 'aktif',
            'jabatan' => 'Petugas',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $user = User::where('username', 'userwithnik')->firstOrFail();
        $this->assertDatabaseHas('user_sensitive', [
            'id_user' => $user->id_user,
        ]);
    }

    public function test_create_user_requires_nama(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'username' => 'newuser',
            'email' => 'newuser@example.test',
            'password' => 'Password123',
            'id_role' => $this->petugasRole->id_role,
        ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_create_user_requires_unique_username(): void
    {
        Storage::fake('public');

        User::create([
            'nama' => 'Existing User',
            'username' => 'existinguser',
            'email' => 'existing@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'nama' => 'New User',
            'username' => 'existinguser',
            'email' => 'newuser@example.test',
            'password' => 'Password123',
            'id_role' => $this->petugasRole->id_role,
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_create_user_requires_unique_email(): void
    {
        Storage::fake('public');

        User::create([
            'nama' => 'Existing User',
            'username' => 'existinguser',
            'email' => 'existing@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'nama' => 'New User',
            'username' => 'newuser',
            'email' => 'existing@example.test',
            'password' => 'Password123',
            'id_role' => $this->petugasRole->id_role,
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_create_user_requires_valid_role(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'nama' => 'New User',
            'username' => 'newuser',
            'email' => 'newuser@example.test',
            'password' => 'Password123',
            'id_role' => 99999,
        ]);

        $response->assertSessionHasErrors('id_role');
    }

    public function test_nik_must_be_numeric(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'nama' => 'New User',
            'username' => 'newuser',
            'email' => 'newuser@example.test',
            'password' => 'Password123',
            'nik' => 'ABC123',
            'id_role' => $this->petugasRole->id_role,
        ]);

        $response->assertSessionHasErrors('nik');
    }

    public function test_nik_must_be_unique(): void
    {
        Storage::fake('public');

        $existingUser = User::create([
            'nama' => 'Existing NIK User',
            'username' => 'existing.nik',
            'email' => 'existing.nik@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
        ]);
        UserSensitive::make(['id_user' => $existingUser->id_user])
            ->setNik('3175000000000001')
            ->save();

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'nama' => 'Duplicate NIK User',
            'username' => 'duplicate.nik',
            'email' => 'duplicate.nik@example.test',
            'password' => 'Password123',
            'nik' => '3175000000000001',
            'id_role' => $this->petugasRole->id_role,
        ]);

        $response->assertSessionHasErrors('nik');
        $this->assertDatabaseMissing('users', ['username' => 'duplicate.nik']);
    }

    // ==================== USER UPDATE ====================

    public function test_admin_can_update_user(): void
    {
        $user = User::create([
            'nama' => 'Original Name',
            'username' => 'originaluser',
            'email' => 'original@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/users/{$user->id_user}", [
            'nama' => 'Updated Name',
            'username' => 'originaluser',
            'email' => 'original@example.test',
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id_user' => $user->id_user,
            'nama' => 'Updated Name',
        ]);
    }

    public function test_admin_can_update_user_password(): void
    {
        $user = User::create([
            'nama' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/users/{$user->id_user}", [
            'nama' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.test',
            'password' => 'NewPassword456',
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword456', $user->password));
    }

    public function test_admin_can_update_user_role(): void
    {
        $user = User::create([
            'nama' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/users/{$user->id_user}", [
            'nama' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.test',
            'id_role' => $this->atasanRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id_user' => $user->id_user,
            'id_role' => $this->atasanRole->id_role,
        ]);
    }

    public function test_update_user_requires_unique_username_on_change(): void
    {
        User::create([
            'nama' => 'User One',
            'username' => 'userone',
            'email' => 'one@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
        ]);

        $userTwo = User::create([
            'nama' => 'User Two',
            'username' => 'usertwo',
            'email' => 'two@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/users/{$userTwo->id_user}", [
            'nama' => 'User Two',
            'username' => 'userone',
            'email' => 'two@example.test',
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $response->assertSessionHasErrors('username');
    }

    // ==================== USER DELETION ====================

    public function test_admin_can_delete_user(): void
    {
        $user = User::create([
            'nama' => 'User To Delete',
            'username' => 'usertodelete',
            'email' => 'delete@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/users/{$user->id_user}");

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id_user' => $user->id_user]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete("/admin/users/{$this->admin->id_user}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id_user' => $this->admin->id_user]);
    }

    public function test_admin_can_bulk_delete_users(): void
    {
        $user1 = User::create([
            'nama' => 'User One',
            'username' => 'userone',
            'email' => 'one@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
        ]);

        $user2 = User::create([
            'nama' => 'User Two',
            'username' => 'usertwo',
            'email' => 'two@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
        ]);

        $response = $this->actingAs($this->admin)->delete('/admin/users-bulk', [
            'user_ids' => [$user1->id_user, $user2->id_user],
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id_user' => $user1->id_user]);
        $this->assertDatabaseMissing('users', ['id_user' => $user2->id_user]);
    }

    public function test_bulk_delete_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete('/admin/users-bulk', [
            'user_ids' => [$this->admin->id_user],
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id_user' => $this->admin->id_user]);
    }

    public function test_bulk_delete_with_nik_option_deletes_nik_data(): void
    {
        $user = User::create([
            'nama' => 'User With NIK',
            'username' => 'userwithnik',
            'email' => 'nik@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
        ]);

        UserSensitive::create([
            'id_user' => $user->id_user,
            'nik_hash' => hash('sha256', '3175000000000001'),
        ]);

        $response = $this->actingAs($this->admin)->delete('/admin/users-bulk', [
            'user_ids' => [$user->id_user],
            'delete_nik_data' => true,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('user_sensitive', ['id_user' => $user->id_user]);
    }

    // ==================== IMPORT/EXPORT ====================

    public function test_admin_can_view_import_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users/import');

        $response->assertStatus(200);
        $response->assertViewIs('admin.users-import');
    }

    public function test_admin_can_download_template(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users/template');

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=template_import_users.xlsx');
    }
}
