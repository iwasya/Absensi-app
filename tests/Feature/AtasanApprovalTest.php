<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Periode;
use App\Models\Role;
use App\Models\TempatTugas;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AtasanApprovalTest extends TestCase
{
    use RefreshDatabase;

    private User $atasan;
    private User $petugas;
    private Role $atasanRole;
    private Role $petugasRole;
    private TempatTugas $tempat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->atasanRole = Role::firstOrCreate(['nama_role' => 'Atasan']);
        $this->petugasRole = Role::firstOrCreate(['nama_role' => 'Petugas PPSU']);

        $this->tempat = TempatTugas::create([
            'nama_tempat' => 'Kantor Test',
            'alamat' => 'Jl. Test No. 1',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);

        $this->atasan = User::create([
            'nama' => 'Atasan Test',
            'username' => 'atasan.test',
            'email' => 'atasan@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->atasanRole->id_role,
            'id_tempat' => $this->tempat->id_tempat,
            'status_aktif' => 'aktif',
        ]);

        $this->petugas = User::create([
            'nama' => 'Petugas Test',
            'username' => 'petugas.test',
            'email' => 'petugas@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'id_tempat' => $this->tempat->id_tempat,
            'status_aktif' => 'aktif',
        ]);

        Periode::create([
            'nama_periode' => 'Periode ' . date('Y'),
            'tanggal_mulai' => date('Y-01-01'),
            'tanggal_selesai' => date('Y-12-31'),
            'status' => 'aktif',
        ]);
    }

    // ==================== ABSENSI APPROVAL ====================

    public function test_atasan_can_view_absensi_list(): void
    {
        $response = $this->actingAs($this->atasan)->get('/atasan/absensi');

        $response->assertStatus(200);
        $response->assertViewIs('atasan.absensi');
    }

    public function test_atasan_can_approve_masuk_request(): void
    {
        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->subDays(2),
            'approval_masuk_status' => 'pending_atasan',
            'approval_masuk_reason' => 'Terlambat karena macet',
        ]);

        $response = $this->actingAs($this->atasan)->post("/atasan/absensi/{$absensi->id_absensi}/approve-masuk");

        $response->assertSessionHas('success');

        $absensi->refresh();
        $this->assertEquals('approved', $absensi->approval_masuk_status);
        $this->assertEquals('akses_dibuka', $absensi->status);
    }

    public function test_atasan_can_reject_masuk_request(): void
    {
        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->subDays(2),
            'approval_masuk_status' => 'pending_atasan',
            'approval_masuk_reason' => 'Terlambat karena macet',
        ]);

        $response = $this->actingAs($this->atasan)->post("/atasan/absensi/{$absensi->id_absensi}/reject-masuk");

        $response->assertSessionHas('success');

        $absensi->refresh();
        $this->assertEquals('rejected', $absensi->approval_masuk_status);
    }

    public function test_atasan_can_approve_pulang_request(): void
    {
        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->toDateString(),
            'jam_masuk' => '08:00:00',
            'approval_pulang_status' => 'pending_atasan',
            'approval_pulang_reason' => 'Terlupa absen pulang',
        ]);

        $response = $this->actingAs($this->atasan)->post("/atasan/absensi/{$absensi->id_absensi}/approve-pulang");

        $response->assertSessionHas('success');

        $absensi->refresh();
        $this->assertEquals('approved', $absensi->approval_pulang_status);
    }

    public function test_atasan_can_reject_pulang_request(): void
    {
        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->toDateString(),
            'jam_masuk' => '08:00:00',
            'approval_pulang_status' => 'pending_atasan',
            'approval_pulang_reason' => 'Terlupa absen pulang',
        ]);

        $response = $this->actingAs($this->atasan)->post("/atasan/absensi/{$absensi->id_absensi}/reject-pulang");

        $response->assertSessionHas('success');

        $absensi->refresh();
        $this->assertEquals('rejected', $absensi->approval_pulang_status);
    }

    public function test_atasan_cannot_approve_already_processed_masuk(): void
    {
        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->subDays(2),
            'approval_masuk_status' => 'approved',
        ]);

        $response = $this->actingAs($this->atasan)->post("/atasan/absensi/{$absensi->id_absensi}/approve-masuk");

        $response->assertSessionHas('error');
    }

    public function test_atasan_cannot_approve_outside_tempat(): void
    {
        $otherTempat = TempatTugas::create([
            'nama_tempat' => 'Kantor Lain',
            'alamat' => 'Jl. Lain',
        ]);

        $otherPetugas = User::create([
            'nama' => 'Other Petugas',
            'username' => 'other.petugas',
            'email' => 'other@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'id_tempat' => $otherTempat->id_tempat,
            'status_aktif' => 'aktif',
        ]);

        $absensi = Absensi::create([
            'id_user' => $otherPetugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->subDays(2),
            'approval_masuk_status' => 'pending_atasan',
        ]);

        $response = $this->actingAs($this->atasan)->post("/atasan/absensi/{$absensi->id_absensi}/approve-masuk");

        $response->assertForbidden();
    }

    // ==================== CUTI APPROVAL ====================

    public function test_atasan_can_view_cuti_list(): void
    {
        $response = $this->actingAs($this->atasan)->get('/atasan/cuti');

        $response->assertStatus(200);
        $response->assertViewIs('atasan.cuti');
    }

    public function test_atasan_can_approve_cuti(): void
    {
        $pengganti = User::create([
            'nama' => 'Pengganti Test',
            'username' => 'pengganti.test',
            'email' => 'pengganti@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'id_tempat' => $this->tempat->id_tempat,
            'status_aktif' => 'aktif',
        ]);

        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $pengganti->id_user,
            'replacement_status' => 'accepted',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->atasan)->post("/atasan/cuti/{$cuti->id_cuti}/approve");

        $response->assertSessionHas('success');

        $cuti->refresh();
        $this->assertEquals('approve', $cuti->status);
    }

    public function test_atasan_can_reject_cuti(): void
    {
        $pengganti = User::create([
            'nama' => 'Pengganti Test',
            'username' => 'pengganti.test',
            'email' => 'pengganti@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'id_tempat' => $this->tempat->id_tempat,
            'status_aktif' => 'aktif',
        ]);

        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'id_pengganti' => $pengganti->id_user,
            'replacement_status' => 'accepted',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->atasan)->post("/atasan/cuti/{$cuti->id_cuti}/reject");

        $response->assertSessionHas('success');

        $cuti->refresh();
        $this->assertEquals('reject', $cuti->status);
    }

    public function test_atasan_cannot_approve_already_processed_cuti(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'alasan' => 'Urusan keluarga',
            'status' => 'approve',
        ]);

        $response = $this->actingAs($this->atasan)->post("/atasan/cuti/{$cuti->id_cuti}/approve");

        $response->assertSessionHas('error');
    }

    // ==================== TUGAS APPROVAL ====================

    public function test_atasan_can_view_tugas_list(): void
    {
        $response = $this->actingAs($this->atasan)->get('/atasan/tugas');

        $response->assertStatus(200);
        $response->assertViewIs('atasan.tugas');
    }

    public function test_atasan_can_approve_tugas(): void
    {
        $tugas = Tugas::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'uraian' => 'Melakukan pembersihan area',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->atasan)->post("/atasan/tugas/{$tugas->id_tugas}/approve");

        $response->assertSessionHas('success');

        $tugas->refresh();
        $this->assertEquals('approve', $tugas->status);
    }

    public function test_atasan_can_reject_tugas(): void
    {
        $tugas = Tugas::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'uraian' => 'Melakukan pembersihan area',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->atasan)->post("/atasan/tugas/{$tugas->id_tugas}/reject");

        $response->assertSessionHas('success');

        $tugas->refresh();
        $this->assertEquals('reject', $tugas->status);
    }

    public function test_atasan_can_remind_tugas(): void
    {
        $tugas = Tugas::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'uraian' => 'Melakukan pembersihan area',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->atasan)->post("/atasan/tugas/{$tugas->id_tugas}/remind");

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('notifikasi', [
            'id_user' => $this->petugas->id_user,
            'judul' => 'Pengingat Laporan Tugas',
        ]);
    }

    public function test_atasan_can_export_tugas(): void
    {
        $response = $this->actingAs($this->atasan)->get('/atasan/tugas/export');

        $response->assertStatus(200);
        $this->assertMatchesRegularExpression(
            '/^attachment; filename=tugas_atasan_\d{8}_\d{6}\.csv$/',
            $response->headers->get('Content-Disposition') ?? ''
        );
    }

    // ==================== REGU MANAGEMENT ====================

    public function test_atasan_can_view_regu_page(): void
    {
        $response = $this->actingAs($this->atasan)->get('/atasan/regu');

        $response->assertStatus(200);
        $response->assertViewIs('atasan.regu');
    }

    public function test_atasan_can_create_regu(): void
    {
        // Create 5 additional petugas for the regu
        $petugasList = [];
        for ($i = 1; $i <= 5; $i++) {
            $petugasList[] = User::create([
                'nama' => "Petugas {$i}",
                'username' => "petugas{$i}",
                'email' => "petugas{$i}@example.test",
                'password' => Hash::make('Password123'),
                'id_role' => $this->petugasRole->id_role,
                'id_tempat' => $this->tempat->id_tempat,
                'status_aktif' => 'aktif',
            ]);
        }

        $ketua = $petugasList[0];
        $anggotaIds = array_map(fn ($p) => $p->id_user, $petugasList);

        $response = $this->actingAs($this->atasan)->post('/atasan/regu', [
            'nama_regu' => 'Regu Baru',
            'anggota_ids' => $anggotaIds,
            'ketua_id' => $ketua->id_user,
        ]);

        $response->assertSessionHas('success');

        foreach ($petugasList as $p) {
            $p->refresh();
            $this->assertEquals('Regu Baru', $p->regu);
        }
        $this->assertTrue($ketua->is_ketua_regu);
    }

    public function test_regu_must_have_exactly_5_members(): void
    {
        $petugasList = [];
        for ($i = 1; $i <= 4; $i++) {
            $petugasList[] = User::create([
                'nama' => "Petugas {$i}",
                'username' => "petugas{$i}",
                'email' => "petugas{$i}@example.test",
                'password' => Hash::make('Password123'),
                'id_role' => $this->petugasRole->id_role,
                'id_tempat' => $this->tempat->id_tempat,
                'status_aktif' => 'aktif',
            ]);
        }

        $ketua = $petugasList[0];
        $anggotaIds = array_map(fn ($p) => $p->id_user, $petugasList);

        $response = $this->actingAs($this->atasan)->post('/atasan/regu', [
            'nama_regu' => 'Regu Kurang',
            'anggota_ids' => $anggotaIds,
            'ketua_id' => $ketua->id_user,
        ]);

        $response->assertSessionHasErrors('anggota_ids');
    }

    public function test_ketua_must_be_in_anggota(): void
    {
        $petugasList = [];
        for ($i = 1; $i <= 5; $i++) {
            $petugasList[] = User::create([
                'nama' => "Petugas {$i}",
                'username' => "petugas{$i}",
                'email' => "petugas{$i}@example.test",
                'password' => Hash::make('Password123'),
                'id_role' => $this->petugasRole->id_role,
                'id_tempat' => $this->tempat->id_tempat,
                'status_aktif' => 'aktif',
            ]);
        }

        $ketua = $petugasList[0];
        $nonAnggota = User::create([
            'nama' => 'Non Anggota',
            'username' => 'non.anggota',
            'email' => 'non@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'id_tempat' => $this->tempat->id_tempat,
            'status_aktif' => 'aktif',
        ]);

        $anggotaIds = array_map(fn ($p) => $p->id_user, array_slice($petugasList, 1, 4));
        $anggotaIds[] = $nonAnggota->id_user;

        $response = $this->actingAs($this->atasan)->post('/atasan/regu', [
            'nama_regu' => 'Regu Salah',
            'anggota_ids' => $anggotaIds,
            'ketua_id' => $ketua->id_user,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_atasan_can_set_ketua_regu(): void
    {
        $petugas = $this->petugas;
        $petugas->update(['regu' => 'Regu A']);

        $response = $this->actingAs($this->atasan)->post('/atasan/regu/ketua', [
            'id_user' => $petugas->id_user,
        ]);

        $response->assertSessionHas('success');

        $petugas->refresh();
        $this->assertTrue($petugas->is_ketua_regu);
    }

    public function test_atasan_can_update_regu_operasional(): void
    {
        $petugas = $this->petugas;
        $petugas->update(['regu' => 'Regu A']);

        $response = $this->actingAs($this->atasan)->post('/atasan/regu/update-operasional', [
            'nama_regu' => 'Regu A',
            'id_tempat' => $this->tempat->id_tempat,
            'shifts' => [$petugas->id_user => 'Shift 1'],
            'hari_libur' => [$petugas->id_user => 0],
        ]);

        $response->assertSessionHas('success');

        $petugas->refresh();
        $this->assertEquals('Shift 1', $petugas->shift);
        $this->assertEquals(0, $petugas->hari_libur);
    }

    // ==================== KALENDER ====================

    public function test_atasan_can_view_kalender(): void
    {
        $response = $this->actingAs($this->atasan)->get('/atasan/kalender');

        $response->assertStatus(200);
        $response->assertViewIs('atasan.kalender');
    }

    // ==================== PRINT ====================

    public function test_atasan_can_print_absensi(): void
    {
        $response = $this->actingAs($this->atasan)->get('/atasan/absensi/print');

        $response->assertStatus(200);
        $response->assertViewIs('atasan.absensi_print');
    }
}
