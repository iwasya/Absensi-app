<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Role $role;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::firstOrCreate(['nama_role' => 'Petugas PPSU']);

        $this->user = User::create([
            'nama' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->role->id_role,
            'status_aktif' => 'aktif',
        ]);
    }

    // ==================== PROFILE PAGE ====================

    public function test_user_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->user)->get('/profile');

        $response->assertStatus(200);
        $response->assertViewIs('profile.index');
    }

    public function test_unauthenticated_user_cannot_view_profile(): void
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }

    // ==================== UPDATE PROFILE ====================

    public function test_user_can_update_profile(): void
    {
        $response = $this->actingAs($this->user)->post('/profile', [
            'nama' => 'Updated Name',
            'username' => 'testuser',
            'email' => 'test@example.test',
        ]);

        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Updated Name', $this->user->nama);
    }

    public function test_user_can_update_username(): void
    {
        $response = $this->actingAs($this->user)->post('/profile', [
            'nama' => 'Test User',
            'username' => 'newusername',
            'email' => 'test@example.test',
        ]);

        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('newusername', $this->user->username);
    }

    public function test_user_can_update_email(): void
    {
        $response = $this->actingAs($this->user)->post('/profile', [
            'nama' => 'Test User',
            'username' => 'testuser',
            'email' => 'newemail@example.test',
        ]);

        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('newemail@example.test', $this->user->email);
    }

    public function test_user_can_update_profile_photo(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user)->post('/profile', [
            'nama' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.test',
            'foto_profil' => UploadedFile::fake()->image('new-profile.jpg', 640, 640),
        ]);

        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertNotNull($this->user->foto_profil);
        Storage::disk('public')->assertExists($this->user->foto_profil);
    }

    public function test_update_profile_requires_nama(): void
    {
        $response = $this->actingAs($this->user)->post('/profile', [
            'username' => 'testuser',
            'email' => 'test@example.test',
        ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_update_profile_requires_username(): void
    {
        $response = $this->actingAs($this->user)->post('/profile', [
            'nama' => 'Test User',
            'email' => 'test@example.test',
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_update_profile_requires_email(): void
    {
        $response = $this->actingAs($this->user)->post('/profile', [
            'nama' => 'Test User',
            'username' => 'testuser',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_update_profile_requires_valid_email(): void
    {
        $response = $this->actingAs($this->user)->post('/profile', [
            'nama' => 'Test User',
            'username' => 'testuser',
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_update_profile_requires_unique_username(): void
    {
        User::create([
            'nama' => 'Other User',
            'username' => 'otheruser',
            'email' => 'other@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->role->id_role,
        ]);

        $response = $this->actingAs($this->user)->post('/profile', [
            'nama' => 'Test User',
            'username' => 'otheruser',
            'email' => 'test@example.test',
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_update_profile_requires_unique_email(): void
    {
        User::create([
            'nama' => 'Other User',
            'username' => 'otheruser',
            'email' => 'other@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->role->id_role,
        ]);

        $response = $this->actingAs($this->user)->post('/profile', [
            'nama' => 'Test User',
            'username' => 'testuser',
            'email' => 'other@example.test',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_profile_photo_must_be_valid_image(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user)->post('/profile', [
            'nama' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.test',
            'foto_profil' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('foto_profil');
    }

    // ==================== UPDATE PASSWORD ====================

    public function test_user_can_change_password(): void
    {
        $response = $this->actingAs($this->user)->post('/profile/password', [
            'current_password' => 'Password123',
            'password' => 'NewPassword456',
            'password_confirmation' => 'NewPassword456',
        ]);

        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertTrue(Hash::check('NewPassword456', $this->user->password));
    }

    public function test_change_password_requires_current_password(): void
    {
        $response = $this->actingAs($this->user)->post('/profile/password', [
            'password' => 'NewPassword456',
            'password_confirmation' => 'NewPassword456',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_change_password_requires_correct_current_password(): void
    {
        $response = $this->actingAs($this->user)->post('/profile/password', [
            'current_password' => 'WrongPassword',
            'password' => 'NewPassword456',
            'password_confirmation' => 'NewPassword456',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_change_password_requires_password(): void
    {
        $response = $this->actingAs($this->user)->post('/profile/password', [
            'current_password' => 'Password123',
            'password_confirmation' => 'NewPassword456',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_change_password_requires_password_confirmation(): void
    {
        $response = $this->actingAs($this->user)->post('/profile/password', [
            'current_password' => 'Password123',
            'password' => 'NewPassword456',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_change_password_requires_passwords_to_match(): void
    {
        $response = $this->actingAs($this->user)->post('/profile/password', [
            'current_password' => 'Password123',
            'password' => 'NewPassword456',
            'password_confirmation' => 'DifferentPassword789',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_change_password_requires_minimum_length(): void
    {
        $response = $this->actingAs($this->user)->post('/profile/password', [
            'current_password' => 'Password123',
            'password' => 'Short1',
            'password_confirmation' => 'Short1',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_change_password_requires_mixed_case(): void
    {
        $response = $this->actingAs($this->user)->post('/profile/password', [
            'current_password' => 'Password123',
            'password' => 'alllowercase1',
            'password_confirmation' => 'alllowercase1',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_change_password_requires_numbers(): void
    {
        $response = $this->actingAs($this->user)->post('/profile/password', [
            'current_password' => 'Password123',
            'password' => 'NoNumbersHere',
            'password_confirmation' => 'NoNumbersHere',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
