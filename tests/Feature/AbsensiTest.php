<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Periode;
use App\Models\Role;
use App\Models\Shift;
use App\Models\User;
use App\Models\UserSensitive;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AbsensiTest extends TestCase
{
    use RefreshDatabase;

    private User $petugas;
    private Role $petugasRole;
    private Role $adminRole;
    private Role $atasanRole;

    private function validPhotoDataUrl(): string
    {
        // A real 1x1 PNG. Attendance validation checks the decoded image bytes,
        // not only the data-URL prefix.
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Rate limiting is covered separately; it must not make this feature
        // suite order-dependent when many attendance requests share one IP.
        $this->withoutMiddleware(ThrottleRequests::class);

        $this->petugasRole = Role::firstOrCreate(['nama_role' => 'Petugas PPSU']);
        $this->adminRole = Role::firstOrCreate(['nama_role' => 'Admin']);
        $this->atasanRole = Role::firstOrCreate(['nama_role' => 'Atasan']);

        $this->petugas = User::create([
            'nama' => 'Petugas Test',
            'username' => 'petugas.test',
            'email' => 'petugas@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
            'regu' => 'Regu A',
        ]);

        // Create profile photo for face verification
        Storage::fake('public');
        Storage::disk('public')->put('profil/test.jpg', 'test-image-content');

        $this->petugas->update(['foto_profil' => 'profil/test.jpg']);

        // Create active period - set it to cover 2026
        Periode::create([
            'nama_periode' => 'Periode 2026',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-12-31',
            'status' => 'aktif',
        ]);

        // Set current date to Monday 2026-06-15 to avoid weekend issues
        Carbon::setTestNow(Carbon::parse('2026-06-15 08:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset
        parent::tearDown();
    }

    // ==================== INDEX PAGE ====================

    public function test_petugas_can_view_absensi_page(): void
    {
        $response = $this->actingAs($this->petugas)->get('/petugas/absensi');

        $response->assertStatus(200);
        $response->assertViewIs('petugas.absensi');
    }

    public function test_non_petugas_cannot_access_absensi_page(): void
    {
        $admin = User::create([
            'nama' => 'Admin Test',
            'username' => 'admin.test',
            'email' => 'admin@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->adminRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->get('/petugas/absensi');

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_access_absensi_page(): void
    {
        $response = $this->get('/petugas/absensi');

        $response->assertRedirect('/login');
    }

    // ==================== ABSENSI DETAIL ====================

    public function test_petugas_can_view_own_absensi_detail(): void
    {
        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->toDateString(),
            'shift' => 'Pagi',
            'jam_masuk' => '08:00:00',
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($this->petugas)->get("/absensi/{$absensi->id_absensi}/detail");

        $response->assertStatus(200);
        $response->assertViewIs('petugas.absensi-detail');
    }

    public function test_petugas_cannot_view_other_user_absensi_detail(): void
    {
        $otherPetugas = User::create([
            'nama' => 'Other Petugas',
            'username' => 'other.petugas',
            'email' => 'other@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $absensi = Absensi::create([
            'id_user' => $otherPetugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->toDateString(),
            'shift' => 'Pagi',
            'jam_masuk' => '08:00:00',
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($this->petugas)->get("/absensi/{$absensi->id_absensi}/detail");

        $response->assertForbidden();
    }

    // ==================== ABSEN MASUK ====================

    public function test_petugas_can_absen_masuk_with_valid_photo(): void
    {
        config([
            'absensi.face_verification.enabled' => false,
            'absensi.jarak_maks_meter' => 1000,
        ]);

        $base64Photo = $this->validPhotoDataUrl();

        $response = $this->actingAs($this->petugas)->post('/petugas/absensi/masuk', [
            'foto_masuk' => $base64Photo,
            'latitude_masuk' => -6.2,
            'longitude_masuk' => 106.8,
            'lokasi_masuk' => 'Test Location',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('absensi', ['id_user' => $this->petugas->id_user]);
    }

    public function test_petugas_cannot_absen_masuk_twice_same_day(): void
    {
        config([
            'absensi.face_verification.enabled' => false,
            'absensi.jarak_maks_meter' => 1000,
        ]);

        Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->toDateString(),
            'jam_masuk' => '08:00:00',
            'status' => 'hadir',
        ]);

        $base64Photo = $this->validPhotoDataUrl();

        $response = $this->actingAs($this->petugas)->post('/petugas/absensi/masuk', [
            'foto_masuk' => $base64Photo,
        ]);

        $response->assertSessionHas('error', 'Kamu sudah absen masuk hari ini.');
    }

    public function test_absen_masuk_fails_with_invalid_photo_format(): void
    {
        config(['absensi.face_verification.enabled' => false]);

        $response = $this->actingAs($this->petugas)->post('/petugas/absensi/masuk', [
            'foto_masuk' => 'not-valid-base64-image',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_absen_masuk_requires_foto_masuk(): void
    {
        $response = $this->actingAs($this->petugas)->post('/petugas/absensi/masuk', [
            'latitude_masuk' => -6.2,
            'longitude_masuk' => 106.8,
        ]);

        $response->assertSessionHasErrors('foto_masuk');
    }

    public function test_absen_masuk_rejects_photo_that_does_not_match_profile(): void
    {
        config([
            'absensi.face_verification.enabled' => true,
            'absensi.face_verification.endpoint' => 'https://face.test/verify',
            'absensi.face_verification.threshold' => 0.75,
            'absensi.face_verification.fail_open' => false,
            'absensi.jarak_maks_meter' => 1000,
        ]);

        Http::fake([
            'face.test/verify' => Http::response([
                'match' => false,
                'confidence' => 0.41,
            ]),
        ]);

        $response = $this->actingAs($this->petugas)->post('/petugas/absensi/masuk', [
            'foto_masuk' => $this->validPhotoDataUrl(),
            'latitude_masuk' => -6.2,
            'longitude_masuk' => 106.8,
            'lokasi_masuk' => 'Test Location',
        ]);

        $response->assertSessionHas('error', 'Foto tidak sesuai dengan foto profil. Gunakan wajah sendiri untuk absen.');

        $this->assertDatabaseMissing('absensi', [
            'id_user' => $this->petugas->id_user,
            'tanggal' => today()->toDateString(),
        ]);
    }

    // ==================== FACE VERIFICATION ====================

    public function test_face_verification_returns_matched_for_valid_face(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('profil/test.jpg', 'reference-image');

        config([
            'absensi.face_verification.enabled' => true,
            'absensi.face_verification.endpoint' => 'https://face.test/verify',
            'absensi.face_verification.threshold' => 0.75,
        ]);

        Http::fake([
            'face.test/verify' => Http::response([
                'match' => true,
                'confidence' => 0.93,
            ]),
        ]);

        $base64Photo = 'data:image/jpeg;base64,' . base64_encode('candidate-binary');

        $response = $this->actingAs($this->petugas)->postJson('/petugas/absensi/verifikasi-wajah', [
            'foto' => $base64Photo,
            'jenis' => 'masuk',
        ]);

        $response->assertJson([
            'status' => 'matched',
            'is_verified' => true,
        ]);
    }

    public function test_face_verification_returns_mismatched_for_invalid_face(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('profil/test.jpg', 'reference-image');

        config([
            'absensi.face_verification.enabled' => true,
            'absensi.face_verification.endpoint' => 'https://face.test/verify',
            'absensi.face_verification.threshold' => 0.75,
        ]);

        Http::fake([
            'face.test/verify' => Http::response([
                'match' => false,
                'confidence' => 0.41,
            ]),
        ]);

        $base64Photo = 'data:image/jpeg;base64,' . base64_encode('wrong-face');

        $response = $this->actingAs($this->petugas)->postJson('/petugas/absensi/verifikasi-wajah', [
            'foto' => $base64Photo,
            'jenis' => 'masuk',
        ]);

        $response->assertJson([
            'status' => 'mismatched',
            'is_verified' => false,
        ]);
    }

    // ==================== ABSEN PULANG ====================

    public function test_petugas_can_absen_pulang_after_masuk(): void
    {
        config([
            'absensi.face_verification.enabled' => false,
            'absensi.jarak_maks_meter' => 1000,
        ]);

        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->toDateString(),
            'shift' => 'Pagi',
            'jam_masuk' => '08:00:00',
            'status' => 'hadir',
        ]);

        $base64Photo = $this->validPhotoDataUrl();

        // Travel time forward by 8 hours
        Carbon::setTestNow(Carbon::now()->addHours(9));

        $response = $this->actingAs($this->petugas)->post('/petugas/absensi/pulang', [
            'foto_pulang' => $base64Photo,
            'latitude_pulang' => -6.2,
            'longitude_pulang' => 106.8,
            'lokasi_pulang' => 'Test Location',
        ]);

        Carbon::setTestNow(); // Reset

        $response->assertSessionHas('success');

        $absensi->refresh();
        $this->assertNotNull($absensi->jam_pulang);
    }

    public function test_petugas_cannot_absen_pulang_before_masuk(): void
    {
        $base64Photo = $this->validPhotoDataUrl();

        $response = $this->actingAs($this->petugas)->post('/petugas/absensi/pulang', [
            'foto_pulang' => $base64Photo,
        ]);

        $response->assertSessionHas('error', 'Absen masuk dulu sebelum absen pulang.');
    }

    public function test_petugas_cannot_absen_pulang_twice(): void
    {
        config([
            'absensi.face_verification.enabled' => false,
            'absensi.jarak_maks_meter' => 1000,
        ]);

        Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->toDateString(),
            'shift' => 'Pagi',
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '17:00:00',
            'status' => 'hadir',
        ]);

        $base64Photo = $this->validPhotoDataUrl();

        $response = $this->actingAs($this->petugas)->post('/petugas/absensi/pulang', [
            'foto_pulang' => $base64Photo,
        ]);

        $response->assertSessionHas('error', 'Kamu sudah absen pulang hari ini.');
    }

    public function test_absen_pulang_rejects_photo_that_does_not_match_profile(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-15 17:00:00'));

        config([
            'absensi.face_verification.enabled' => true,
            'absensi.face_verification.endpoint' => 'https://face.test/verify',
            'absensi.face_verification.threshold' => 0.75,
            'absensi.face_verification.fail_open' => false,
            'absensi.jarak_maks_meter' => 1000,
        ]);

        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->toDateString(),
            'shift' => 'Pagi',
            'jam_masuk' => '08:00:00',
            'status' => 'hadir',
        ]);

        Http::fake([
            'face.test/verify' => Http::response([
                'match' => false,
                'confidence' => 0.39,
            ]),
        ]);

        $response = $this->actingAs($this->petugas)->post('/petugas/absensi/pulang', [
            'foto_pulang' => $this->validPhotoDataUrl(),
            'latitude_pulang' => -6.2,
            'longitude_pulang' => 106.8,
            'lokasi_pulang' => 'Test Location',
        ]);

        $response->assertSessionHas('error', 'Foto tidak sesuai dengan foto profil. Gunakan wajah sendiri untuk absen.');

        $absensi->refresh();
        $this->assertNull($absensi->jam_pulang);
        $this->assertNull($absensi->foto_pulang);
    }

    // ==================== REQUEST APPROVAL ====================

    public function test_petugas_can_request_masuk_approval_for_past_date(): void
    {
        $pastDate = today()->subDays(3);

        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => $pastDate->toDateString(),
            'status' => 'tidak_absen',
            'keterangan' => 'Tidak hadir (otomatis sistem)',
        ]);

        $response = $this->actingAs($this->petugas)->post("/petugas/absensi/{$absensi->id_absensi}/request-masuk", [
            'approval_masuk_reason' => 'Terlambat karena kondisi darurat',
        ]);

        $response->assertSessionHas('success');

        $absensi->refresh();
        $this->assertNotNull($absensi->approval_masuk_requested_at);
        $this->assertContains($absensi->approval_masuk_status, ['pending_ketua', 'pending_atasan']);
    }

    public function test_petugas_can_request_pulang_approval(): void
    {
        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->toDateString(),
            'shift' => 'Pagi',
            'jam_masuk' => '08:00:00',
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($this->petugas)->post("/petugas/absensi/{$absensi->id_absensi}/request-pulang", [
            'approval_pulang_reason' => 'Terlupa menekan absen pulang',
        ]);

        $response->assertSessionHas('success');

        $absensi->refresh();
        $this->assertNotNull($absensi->approval_pulang_requested_at);
    }

    public function test_request_masuk_requires_reason(): void
    {
        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->subDays(2),
            'status' => 'tidak_absen',
        ]);

        $response = $this->actingAs($this->petugas)->post("/petugas/absensi/{$absensi->id_absensi}/request-masuk", []);

        $response->assertSessionHasErrors('approval_masuk_reason');
    }

    public function test_request_pulang_requires_reason(): void
    {
        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->toDateString(),
            'jam_masuk' => '08:00:00',
        ]);

        $response = $this->actingAs($this->petugas)->post("/petugas/absensi/{$absensi->id_absensi}/request-pulang", []);

        $response->assertSessionHasErrors('approval_pulang_reason');
    }

    // ==================== APPROVAL REGU (KETUA REGU) ====================

    public function test_ketua_regu_can_view_approval_regu_page(): void
    {
        $ketuaRegu = User::create([
            'nama' => 'Ketua Regu Test',
            'username' => 'ketua.regu',
            'email' => 'ketua@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
            'regu' => 'Regu A',
            'is_ketua_regu' => true,
        ]);

        $response = $this->actingAs($ketuaRegu)->get('/petugas/approval-regu');

        $response->assertStatus(200);
        $response->assertViewIs('petugas.approval-regu');
    }

    public function test_regular_petugas_cannot_view_approval_regu_page(): void
    {
        $response = $this->actingAs($this->petugas)->get('/petugas/approval-regu');

        $response->assertForbidden();
    }

    public function test_ketua_regu_can_forward_masuk_approval(): void
    {
        $ketuaRegu = User::create([
            'nama' => 'Ketua Regu Test',
            'username' => 'ketua.regu',
            'email' => 'ketua@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
            'regu' => 'Regu A',
            'is_ketua_regu' => true,
        ]);

        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->subDays(2),
            'approval_masuk_status' => 'pending_ketua',
            'approval_masuk_reason' => 'Test reason',
        ]);

        $response = $this->actingAs($ketuaRegu)->post("/petugas/approval-regu/{$absensi->id_absensi}/forward-masuk");

        $response->assertSessionHas('success');

        $absensi->refresh();
        $this->assertEquals('pending_atasan', $absensi->approval_masuk_status);
    }

    public function test_ketua_regu_can_reject_masuk_approval(): void
    {
        $ketuaRegu = User::create([
            'nama' => 'Ketua Regu Test',
            'username' => 'ketua.regu',
            'email' => 'ketua@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
            'regu' => 'Regu A',
            'is_ketua_regu' => true,
        ]);

        $absensi = Absensi::create([
            'id_user' => $this->petugas->id_user,
            'id_periode' => Periode::aktif()->id_periode,
            'tanggal' => today()->subDays(2),
            'approval_masuk_status' => 'pending_ketua',
            'approval_masuk_reason' => 'Test reason',
        ]);

        $response = $this->actingAs($ketuaRegu)->post("/petugas/approval-regu/{$absensi->id_absensi}/reject-masuk");

        $response->assertSessionHas('success');

        $absensi->refresh();
        $this->assertEquals('rejected_ketua', $absensi->approval_masuk_status);
    }

    // ==================== LOCATION VALIDATION ====================

    public function test_absen_masuk_fails_when_outside_location(): void
    {
        config([
            'absensi.face_verification.enabled' => false,
            'absensi.jarak_maks_meter' => 100,
        ]);

        // Set tempat tugas location
        $tempat = \App\Models\TempatTugas::create([
            'nama_tempat' => 'Kantor Test',
            'alamat' => 'Jl. Test',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);

        $this->petugas->update(['id_tempat' => $tempat->id_tempat]);

        // Try to absen from different location (far away)
        $base64Photo = $this->validPhotoDataUrl();

        $response = $this->actingAs($this->petugas)->post('/petugas/absensi/masuk', [
            'foto_masuk' => $base64Photo,
            'latitude_masuk' => -6.5, // Far from office
            'longitude_masuk' => 107.0,
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('di luar area kantor', session('error'));
    }

    public function test_absen_masuk_succeeds_when_within_location(): void
    {
        config([
            'absensi.face_verification.enabled' => false,
            'absensi.jarak_maks_meter' => 1000,
        ]);

        // Set tempat tugas location
        $tempat = \App\Models\TempatTugas::create([
            'nama_tempat' => 'Kantor Test',
            'alamat' => 'Jl. Test',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);

        $this->petugas->update(['id_tempat' => $tempat->id_tempat]);

        $base64Photo = $this->validPhotoDataUrl();

        $response = $this->actingAs($this->petugas)->post('/petugas/absensi/masuk', [
            'foto_masuk' => $base64Photo,
            'latitude_masuk' => -6.2001, // Close to office
            'longitude_masuk' => 106.8001,
        ]);

        $response->assertSessionHas('success');
    }

    // ==================== PRINT ====================

    public function test_petugas_can_view_print_page(): void
    {
        $response = $this->actingAs($this->petugas)->get('/petugas/absensi/print');

        $response->assertStatus(200);
        $response->assertViewIs('petugas.absensi_print');
    }
}
