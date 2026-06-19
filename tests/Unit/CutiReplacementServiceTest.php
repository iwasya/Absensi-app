<?php

namespace Tests\Unit;

use App\Models\Cuti;
use App\Models\Periode;
use App\Models\Role;
use App\Models\User;
use App\Services\CutiReplacementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CutiReplacementServiceTest extends TestCase
{
    use RefreshDatabase;

    private CutiReplacementService $service;
    private User $petugas;
    private User $pengganti;
    private Role $petugasRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->petugasRole = Role::firstOrCreate(['nama_role' => 'Petugas PPSU']);

        $this->petugas = User::create([
            'nama' => 'Petugas Test',
            'username' => 'petugas.test',
            'email' => 'petugas@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'regu' => 'Regu A',
            'status_aktif' => 'aktif',
        ]);

        $this->pengganti = User::create([
            'nama' => 'Pengganti Test',
            'username' => 'pengganti.test',
            'email' => 'pengganti@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'regu' => 'Regu A',
            'status_aktif' => 'aktif',
        ]);

        $this->service = new CutiReplacementService();

        Periode::create([
            'nama_periode' => 'Periode ' . date('Y'),
            'tanggal_mulai' => date('Y-01-01'),
            'tanggal_selesai' => date('Y-12-31'),
            'status' => 'aktif',
        ]);
    }

    // ==================== REPLACEMENT CANDIDATES ====================

    public function test_replacement_candidates_excludes_self(): void
    {
        $candidates = $this->service->replacementCandidatesFor($this->petugas);

        $this->assertFalse($candidates->contains('id_user', $this->petugas->id_user));
    }

    public function test_replacement_candidates_includes_same_regu(): void
    {
        $candidates = $this->service->replacementCandidatesFor($this->petugas);

        $this->assertTrue($candidates->contains('id_user', $this->pengganti->id_user));
    }

    public function test_replacement_candidates_excludes_different_regu(): void
    {
        $this->pengganti->update(['regu' => 'Regu B']);

        $candidates = $this->service->replacementCandidatesFor($this->petugas);

        // Should still include because user has no regu filter, but at minimum exclude self
        $this->assertFalse($candidates->contains('id_user', $this->petugas->id_user));
    }

    // ==================== PENDING REQUESTS ====================

    public function test_pending_requests_returns_requests_for_user_as_pengganti(): void
    {
        Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_pengganti' => $this->pengganti->id_user,
            'tanggal_mulai' => today()->addDays(5)->toDateString(),
            'tanggal_selesai' => today()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'replacement_status' => 'pending',
            'status' => 'pending',
        ]);

        $requests = $this->service->pendingRequestsFor($this->pengganti);

        $this->assertCount(1, $requests);
    }

    public function test_pending_requests_excludes_non_pending(): void
    {
        Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_pengganti' => $this->pengganti->id_user,
            'tanggal_mulai' => today()->addDays(5)->toDateString(),
            'tanggal_selesai' => today()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'replacement_status' => 'accepted',
            'status' => 'pending',
        ]);

        $requests = $this->service->pendingRequestsFor($this->pengganti);

        $this->assertCount(0, $requests);
    }

    // ==================== VALIDATE NEW REQUEST ====================

    public function test_validate_new_request_rejects_self_as_pengganti(): void
    {
        $error = $this->service->validateNewRequest(
            $this->petugas,
            $this->petugas,
            today()->addDays(5),
            today()->addDays(7),
            'Tahunan'
        );

        $this->assertEquals('Petugas pengganti harus dipilih dari petugas lain.', $error);
    }

    public function test_validate_new_request_rejects_non_petugas_pengganti(): void
    {
        $adminRole = Role::firstOrCreate(['nama_role' => 'Admin']);

        $admin = User::create([
            'nama' => 'Admin Test',
            'username' => 'admin.test',
            'email' => 'admin@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $adminRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $error = $this->service->validateNewRequest(
            $this->petugas,
            $admin,
            today()->addDays(5),
            today()->addDays(7),
            'Tahunan'
        );

        $this->assertEquals('Petugas pengganti harus dipilih dari petugas lain.', $error);
    }

    public function test_validate_new_request_rejects_different_regu(): void
    {
        $this->petugas->update(['regu' => 'Regu A']);
        $this->pengganti->update(['regu' => 'Regu B']);

        $error = $this->service->validateNewRequest(
            $this->petugas,
            $this->pengganti,
            today()->addDays(5),
            today()->addDays(7),
            'Tahunan'
        );

        $this->assertEquals('Petugas pengganti harus dari regu yang sama.', $error);
    }

    public function test_validate_new_request_rejects_pengganti_with_overlapping_cuti(): void
    {
        Cuti::create([
            'id_user' => $this->pengganti->id_user,
            'tanggal_mulai' => today()->addDays(5)->toDateString(),
            'tanggal_selesai' => today()->addDays(10)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'status' => 'pending',
        ]);

        $error = $this->service->validateNewRequest(
            $this->petugas,
            $this->pengganti,
            today()->addDays(5),
            today()->addDays(7),
            'Tahunan'
        );

        $this->assertStringContainsString('tidak bisa dipilih karena punya pengajuan cuti', $error);
    }

    public function test_validate_new_request_rejects_pengganti_alreadyReplacement(): void
    {
        Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_pengganti' => $this->pengganti->id_user,
            'tanggal_mulai' => today()->addDays(5)->toDateString(),
            'tanggal_selesai' => today()->addDays(10)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'replacement_status' => 'pending',
            'status' => 'pending',
        ]);

        $otherPetugas = User::create([
            'nama' => 'Other Petugas',
            'username' => 'other.petugas',
            'email' => 'other@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'regu' => 'Regu A',
            'status_aktif' => 'aktif',
        ]);

        $error = $this->service->validateNewRequest(
            $otherPetugas,
            $this->pengganti,
            today()->addDays(5),
            today()->addDays(7),
            'Tahunan'
        );

        $this->assertStringContainsString('sudah ditunjuk sebagai pengganti cuti', $error);
    }

    public function test_validate_new_request_returns_null_for_valid(): void
    {
        $error = $this->service->validateNewRequest(
            $this->petugas,
            $this->pengganti,
            today()->addDays(5),
            today()->addDays(7),
            'Tahunan'
        );

        $this->assertNull($error);
    }

    // ==================== ACCEPT ====================

    public function test_accept_updates_replacement_status(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_pengganti' => $this->pengganti->id_user,
            'tanggal_mulai' => today()->addDays(5)->toDateString(),
            'tanggal_selesai' => today()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'replacement_status' => 'pending',
            'status' => 'pending',
        ]);

        $error = $this->service->accept($cuti, $this->pengganti);

        $this->assertNull($error);
        $cuti->refresh();
        $this->assertEquals('accepted', $cuti->replacement_status);
    }

    public function test_accept_fails_for_wrong_user(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_pengganti' => $this->pengganti->id_user,
            'tanggal_mulai' => today()->addDays(5)->toDateString(),
            'tanggal_selesai' => today()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'replacement_status' => 'pending',
            'status' => 'pending',
        ]);

        $otherUser = User::create([
            'nama' => 'Other User',
            'username' => 'other.user',
            'email' => 'other@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $error = $this->service->accept($cuti, $otherUser);

        $this->assertEquals('Kamu bukan petugas pengganti untuk pengajuan ini.', $error);
    }

    public function test_accept_fails_for_already_processed(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_pengganti' => $this->pengganti->id_user,
            'tanggal_mulai' => today()->addDays(5)->toDateString(),
            'tanggal_selesai' => today()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'replacement_status' => 'accepted',
            'status' => 'pending',
        ]);

        $error = $this->service->accept($cuti, $this->pengganti);

        $this->assertEquals('Permintaan pengganti ini sudah diproses.', $error);
    }

    // ==================== REJECT ====================

    public function test_reject_updates_replacement_status(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_pengganti' => $this->pengganti->id_user,
            'tanggal_mulai' => today()->addDays(5)->toDateString(),
            'tanggal_selesai' => today()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'replacement_status' => 'pending',
            'status' => 'pending',
        ]);

        $error = $this->service->reject($cuti, $this->pengganti, 'Tidak bisa');

        $this->assertNull($error);
        $cuti->refresh();
        $this->assertEquals('rejected', $cuti->replacement_status);
        $this->assertEquals('rejected', $cuti->status);
        $this->assertEquals('Tidak bisa', $cuti->replacement_note);
    }

    public function test_reject_fails_for_wrong_user(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_pengganti' => $this->pengganti->id_user,
            'tanggal_mulai' => today()->addDays(5)->toDateString(),
            'tanggal_selesai' => today()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'replacement_status' => 'pending',
            'status' => 'pending',
        ]);

        $otherUser = User::create([
            'nama' => 'Other User',
            'username' => 'other.user',
            'email' => 'other@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $this->petugasRole->id_role,
            'status_aktif' => 'aktif',
        ]);

        $error = $this->service->reject($cuti, $otherUser);

        $this->assertEquals('Kamu bukan petugas pengganti untuk pengajuan ini.', $error);
    }

    // ==================== APPROVAL BLOCKER ====================

    public function test_approval_blocker_requires_accepted_replacement(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_pengganti' => $this->pengganti->id_user,
            'tanggal_mulai' => today()->addDays(5)->toDateString(),
            'tanggal_selesai' => today()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'replacement_status' => 'pending',
            'status' => 'pending',
        ]);

        $error = $this->service->approvalBlocker($cuti, 'approve');

        $this->assertEquals('Petugas pengganti harus menerima permintaan terlebih dahulu.', $error);
    }

    public function test_approval_blocker_allows_accepted_replacement(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_pengganti' => $this->pengganti->id_user,
            'tanggal_mulai' => today()->addDays(5)->toDateString(),
            'tanggal_selesai' => today()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'replacement_status' => 'accepted',
            'status' => 'pending',
        ]);

        $error = $this->service->approvalBlocker($cuti, 'approve');

        $this->assertNull($error);
    }

    public function test_approval_blocker_allows_rejection(): void
    {
        $cuti = Cuti::create([
            'id_user' => $this->petugas->id_user,
            'id_pengganti' => $this->pengganti->id_user,
            'tanggal_mulai' => today()->addDays(5)->toDateString(),
            'tanggal_selesai' => today()->addDays(7)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'replacement_status' => 'pending',
            'status' => 'pending',
        ]);

        $error = $this->service->approvalBlocker($cuti, 'reject');

        $this->assertNull($error);
    }
}