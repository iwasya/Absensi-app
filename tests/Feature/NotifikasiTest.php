<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotifikasiTest extends TestCase
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

    // ==================== INDEX PAGE ====================

    public function test_user_can_view_notifikasi_page(): void
    {
        $response = $this->actingAs($this->user)->get('/notifikasi');

        $response->assertStatus(200);
        $response->assertViewIs('notifikasi');
    }

    public function test_unauthenticated_user_cannot_view_notifikasi(): void
    {
        $response = $this->get('/notifikasi');

        $response->assertRedirect('/login');
    }

    public function test_user_only_sees_own_notifikasi(): void
    {
        Notifikasi::create([
            'id_user' => $this->user->id_user,
            'judul' => 'Notifikasi Saya',
            'pesan' => 'Ini notifikasi saya',
            'tipe' => 'system',
        ]);

        $otherUser = User::create([
            'nama' => 'Other User',
            'username' => 'otheruser',
            'email' => 'other@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->role->id_role,
            'status_aktif' => 'aktif',
        ]);

        Notifikasi::create([
            'id_user' => $otherUser->id_user,
            'judul' => 'Notifikasi Orang Lain',
            'pesan' => 'Ini notifikasi orang lain',
            'tipe' => 'system',
        ]);

        $response = $this->actingAs($this->user)->get('/notifikasi');

        $response->assertSee('Notifikasi Saya');
        $response->assertDontSee('Notifikasi Orang Lain');
    }

    // ==================== MARK AS READ ====================

    public function test_user_can_mark_notifikasi_as_read(): void
    {
        $notifikasi = Notifikasi::create([
            'id_user' => $this->user->id_user,
            'judul' => 'Test Notifikasi',
            'pesan' => 'Test pesan',
            'tipe' => 'system',
            'status_baca' => false,
        ]);

        $response = $this->actingAs($this->user)->post("/notifikasi/{$notifikasi->id_notifikasi}/read");

        $response->assertSessionHas('success');

        $notifikasi->refresh();
        $this->assertTrue((bool) $notifikasi->status_baca);
    }

    public function test_user_can_mark_notifikasi_as_read_via_json(): void
    {
        $notifikasi = Notifikasi::create([
            'id_user' => $this->user->id_user,
            'judul' => 'Test Notifikasi',
            'pesan' => 'Test pesan',
            'tipe' => 'system',
            'status_baca' => false,
        ]);

        $response = $this->actingAs($this->user)->postJson("/notifikasi/{$notifikasi->id_notifikasi}/read");

        $response->assertJson([
            'success' => true,
        ]);

        $notifikasi->refresh();
        $this->assertTrue((bool) $notifikasi->status_baca);
    }

    public function test_user_cannot_mark_other_user_notifikasi_as_read(): void
    {
        $otherUser = User::create([
            'nama' => 'Other User',
            'username' => 'otheruser',
            'email' => 'other@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->role->id_role,
            'status_aktif' => 'aktif',
        ]);

        $notifikasi = Notifikasi::create([
            'id_user' => $otherUser->id_user,
            'judul' => 'Test Notifikasi',
            'pesan' => 'Test pesan',
            'tipe' => 'system',
            'status_baca' => false,
        ]);

        $response = $this->actingAs($this->user)->post("/notifikasi/{$notifikasi->id_notifikasi}/read");

        // The query updates 0 rows since the user doesn't own this notification
        // So we verify the notification is still unread
        $notifikasi->refresh();
        $this->assertFalse((bool) $notifikasi->status_baca);
    }

    // ==================== MARK ALL AS READ ====================

    public function test_user_can_mark_all_notifikasi_as_read(): void
    {
        for ($i = 0; $i < 3; $i++) {
            Notifikasi::create([
                'id_user' => $this->user->id_user,
                'judul' => "Notifikasi {$i}",
                'pesan' => "Pesan {$i}",
                'tipe' => 'system',
                'status_baca' => false,
            ]);
        }

        $response = $this->actingAs($this->user)->post('/notifikasi/read-all');

        $response->assertSessionHas('success');

        $unreadCount = Notifikasi::where('id_user', $this->user->id_user)
            ->where('status_baca', false)
            ->count();

        $this->assertEquals(0, $unreadCount);
    }

    public function test_user_can_mark_all_notifikasi_as_read_via_json(): void
    {
        for ($i = 0; $i < 3; $i++) {
            Notifikasi::create([
                'id_user' => $this->user->id_user,
                'judul' => "Notifikasi {$i}",
                'pesan' => "Pesan {$i}",
                'tipe' => 'system',
                'status_baca' => false,
            ]);
        }

        $response = $this->actingAs($this->user)->postJson('/notifikasi/read-all');

        $response->assertJson([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    // ==================== UNREAD COUNT ====================

    public function test_unread_count_returns_correct_value(): void
    {
        for ($i = 0; $i < 5; $i++) {
            Notifikasi::create([
                'id_user' => $this->user->id_user,
                'judul' => "Notifikasi {$i}",
                'pesan' => "Pesan {$i}",
                'tipe' => 'system',
                'status_baca' => $i < 2, // First 2 are read
            ]);
        }

        $response = $this->actingAs($this->user)->postJson('/notifikasi/read-all');

        $response->assertJson([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}
