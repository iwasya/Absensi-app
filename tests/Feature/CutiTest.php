<?php

namespace Tests\Feature;

use App\Models\Cuti;
use App\Models\Periode;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CutiTest extends TestCase
{
    use RefreshDatabase;

    private User $petugas;
    private User $pengganti;
    private User $atasan;
    private Role $petugasRole;
    private Role $atasanRole;
    private Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->petugasRole = Role::firstOrCreate(['nama_role' => 'Petugas PPSU']);
        $this->atasanRole = Role::firstOrCreate(['nama_role' => 'Atasan']);
        $this->adminRole = Role::firstOrCreate(['nama_role' => 'Admin']);

        $this->petugas = User::create([
            'nama' => 'Petugas Test',
            'username' => 'petugas.test',
            'email' => 'petugas@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $this->pengganti = User::create([
            'nama' => 'Pengganti Test',
            'username' => 'pengganti.test',
            'email' => 'pengganti@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $this->atasan = User::create([
            'nama' => 'Atasan Test',
            'username' => 'atasan.test',
            'email' => 'atasan@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->atasanRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        Periode::create([
            'nama_periode' => 'Periode ' . date('Y'),
            'tanggal_mulai' => date('Y-01-01'),
            'tanggal_selesai' => date('Y-12-31'),
            'status' => 'aktif',
        ]);
    }

    // ==================== INDEX PAGE ====================

    public function test_petugas_can_view_cuti_page(): void
    {
        $response = $this->actingAs($this->petugas)->get('/petugas/cuti');

        $response->assertStatus(200);
        $response->assertViewIs('petugas.cuti');
    }

    public function test_unauthenticated_user_cannot_access_cuti_page(): void
    {
        $response = $this->get('/petugas/cuti');

        $response->assertRedirect('/login');
    }

    // ==================== CREATE CUTI ====================

    public function test_petugas_can_submit_cuti_request(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->petugas)->post('/petugas/cuti', [
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $this->pengganti->id_user,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cuti', [
            'id_user' => $this->petugas->id_user,
            'id_pengganti' => $this->pengganti->id_user,
            'jenis_cuti' => 'Tahunan',
            'status' => 'pending',
        ]);
    }

    public function test_cuti_requires_tanggal_mulai(): void
    {
        $response = $this->actingAs($this->petugas)->post('/petugas/cuti', [
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $this->pengganti->id_user,
        ]);

        $response->assertSessionHasErrors('tanggal_mulai');
    }

    public function test_cuti_requires_tanggal_selesai(): void
    {
        $response = $this->actingAs($this->petugas)->post('/petugas/cuti', [
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $this->pengganti->id_user,
        ]);

        $response->assertSessionHasErrors('tanggal_selesai');
    }

    public function test_cuti_tanggal_selesai_must_be_after_tanggal_mulai(): void
    {
        $response = $this->actingAs($this->petugas)->post('/petugas/cuti', [
            'tanggal_mulai' => now()->addDays(7)->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $this->pengganti->id_user,
        ]);

        $response->assertSessionHasErrors('tanggal_selesai');
    }

    public function test_cuti_requires_jenis_cuti(): void
    {
        $response = $this->actingAs($this->petugas)->post('/petugas/cuti', [
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $this->pengganti->id_user,
        ]);

        $response->assertSessionHasErrors('jenis_cuti');
    }

    public function test_cuti_jenis_cuti_must_be_valid(): void
    {
        $response = $this->actingAs($this->petugas)->post('/petugas/cuti', [
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'InvalidType',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $this->pengganti->id_user,
        ]);

        $response->assertSessionHasErrors('jenis_cuti');
    }

    public function test_cuti_requires_alasan(): void
    {
        $response = $this->actingAs($this->petugas)->post('/petugas/cuti', [
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'id_pengganti' => $this->pengganti->id_user,
        ]);

        $response->assertSessionHasErrors('alasan');
    }

    public function test_cuti_requires_id_pengganti(): void
    {
        $response = $this->actingAs($this->petugas)->post('/petugas/cuti', [
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
        ]);

        $response->assertSessionHasErrors('id_pengganti');
    }

    public function test_cuti_sakit_requires_dokumen(): void
    {
        $response = $this->actingAs($this->petugas)->post('/petugas/cuti', [
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Sakit',
            'alasan' => 'Sakit',
            'id_pengganti' => $this->pengganti->id_user,
        ]);

        $response->assertSessionHas('error', 'Cuti sakit wajib melampirkan bukti dokumen.');
    }

    public function test_cuti_sakit_with_dokumen_succeeds(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->petugas)->post('/petugas/cuti', [
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Sakit',
            'alasan' => 'Sakit',
            'id_pengganti' => $this->pengganti->id_user,
            'dokumen' => UploadedFile::fake()->image('surat_sakit.jpg'),
        ]);

        $response->assertSessionHas('success');
    }

    public function test_cuti_tahunan_limit_is_12_per_year(): void
    {
        Storage::fake('public');

        $pengganti = User::create([
            'nama' => 'Pengganti Test 2',
            'username' => 'pengganti.test.2',
            'email' => 'pengganti2@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        // Create 12 cuti requests for this year
        for ($i = 0; $i < 12; $i++) {
            Cuti::create([
                'id_user' => $this->petugas->id_user,
                'id_periode' => Periode::aktif()->id_periode,
                'tanggal_mulai' => date('Y') . '-01-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'tanggal_selesai' => date('Y') . '-01-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'jenis_cuti' => 'Tahunan',
                'alasan' => 'Cuti ke-' . ($i + 1),
                'id_pengganti' => $pengganti->id_user,
                'status' => 'pending',
            ]);
        }

        // Try to create 13th cuti
        $response = $this->actingAs($this->petugas)->post('/petugas/cuti', [
            'tanggal_mulai' => date('Y') . '-12-25',
            'tanggal_selesai' => date('Y') . '-12-26',
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Cuti ke-13',
            'id_pengganti' => $pengganti->id_user,
        ]);

        // Verify that the error message contains quota-related text
        $errorMessage = session('error');
        $this->assertTrue(
            str_contains($errorMessage ?? '', 'kuota') || str_contains($errorMessage ?? '', '12'),
            "Expected quota error message but got: " . ($errorMessage ?? 'no error')
        );
    }

    // ==================== REPLACEMENT ====================

    public function test_pengganti_can_accept_replacement(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $this->pengganti->id_user,
            'replacement_status' => 'pending',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->pengganti)->post("/petugas/cuti/{$cuti->id_cuti}/pengganti/terima");

        $response->assertSessionHas('success');

        $cuti->refresh();
        $this->assertEquals('accepted', $cuti->replacement_status);
    }

    public function test_pengganti_can_reject_replacement(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $this->pengganti->id_user,
            'replacement_status' => 'pending',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->pengganti)->post("/petugas/cuti/{$cuti->id_cuti}/pengganti/tolak", [
            'replacement_note' => 'Saya juga sedang cuti',
        ]);

        $response->assertSessionHas('success');

        $cuti->refresh();
        $this->assertEquals('rejected', $cuti->replacement_status);
    }

    public function test_non_pengganti_cannot_accept_replacement(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $this->pengganti->id_user,
            'replacement_status' => 'pending',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->atasan)->post("/petugas/cuti/{$cuti->id_cuti}/pengganti/terima");

        $response->assertForbidden();
    }

    public function test_non_pengganti_cannot_reject_replacement(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $this->pengganti->id_user,
            'replacement_status' => 'pending',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->atasan)->post("/petugas/cuti/{$cuti->id_cuti}/pengganti/tolak");

        $response->assertForbidden();
    }

    // ==================== PRINT ====================

    public function test_owner_can_view_cuti_print(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $this->pengganti->id_user,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->petugas)->get("/petugas/cuti/{$cuti->id_cuti}/print");

        $response->assertStatus(200);
        $response->assertViewIs('petugas.cuti_print');
    }

    public function test_atasan_can_view_cuti_print(): void
    {
        // Atasan can only view cuti print if they have the same tempat_tugas
        // or if they are the owner. In this test, we give the atasan the same tempat
        $this->atasan->update(['id_tempat' => $this->petugas->id_tempat]);

        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $this->pengganti->id_user,
            'status' => 'pending',
        ]);

        // For atasan to view cuti print, they need to be either admin, atasan, or owner
        // The controller checks isAtasan() which should work
        $response = $this->actingAs($this->atasan)->get("/petugas/cuti/{$cuti->id_cuti}/print");

        // Since atasan can view cuti print according to controller logic
        $response->assertStatus(200);
    }

    public function test_petugas_cannot_view_other_user_cuti_print(): void
    {
        $otherPetugas = User::create([
            'nama' => 'Other Petugas',
            'username' => 'other.petugas',
            'email' => 'other@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $cuti = Cuti::create([
            'id_user' => $otherPetugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $this->pengganti->id_user,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->petugas)->get("/petugas/cuti/{$cuti->id_cuti}/print");

        $response->assertForbidden();
    }
}
