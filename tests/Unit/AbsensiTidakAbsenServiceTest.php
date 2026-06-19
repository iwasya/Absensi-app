<?php

namespace Tests\Unit;

use App\Models\Cuti;
use App\Models\Kalender;
use App\Models\Periode;
use App\Models\Role;
use App\Models\User;
use App\Services\AbsensiTidakAbsenService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AbsensiTidakAbsenServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $petugas;
    private AbsensiTidakAbsenService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['nama_role' => 'Petugas PPSU']);

        $this->petugas = User::create([
            'nama' => 'Petugas Test',
            'username' => 'petugas.test',
            'email' => 'petugas@example.test',
            'password' => Hash::make('Password123'),
            'id_role' => $role->id_role,
            'status_aktif' => 'aktif',
        ]);

        $this->service = new AbsensiTidakAbsenService();

        Periode::create([
            'nama_periode' => 'Periode ' . date('Y'),
            'tanggal_mulai' => date('Y-01-01'),
            'tanggal_selesai' => date('Y-12-31'),
            'status' => 'aktif',
        ]);
    }

    public function test_generate_for_date_returns_reason_when_no_active_periode(): void
    {
        Periode::where('status', 'aktif')->update(['status' => 'nonaktif']);

        $result = $this->service->generateForDate(today());

        $this->assertEquals('Tidak ada periode aktif.', $result['reason']);
        $this->assertEquals(0, $result['created']);
    }

    public function test_holiday_info_returns_weekend_as_holiday(): void
    {
        // Find next Saturday
        $saturday = today()->next(Carbon::SATURDAY);

        $result = $this->service->holidayInfo($saturday);

        $this->assertTrue($result['is_holiday']);
        $this->assertEquals('Weekend', $result['reason']);
    }

    public function test_holiday_info_returns_false_for_working_day(): void
    {
        // Find next Monday (should be a working day)
        $monday = today()->next(Carbon::MONDAY);

        // Make sure it's not a holiday by clearing any existing events
        Kalender::where('tanggal', $monday->toDateString())->delete();

        $result = $this->service->holidayInfo($monday);

        $this->assertFalse($result['is_holiday']);
        $this->assertNull($result['reason']);
    }

    public function test_weekly_off_info_returns_holiday_for_user_day_off(): void
    {
        $this->petugas->update(['hari_libur' => 0]); // Sunday

        $sunday = today()->next(Carbon::SUNDAY);

        $result = $this->service->weeklyOffInfo($this->petugas, $sunday);

        $this->assertTrue($result['is_holiday']);
        $this->assertStringContainsString('Minggu', $result['reason']);
    }

    public function test_weekly_off_info_returns_false_for_working_day(): void
    {
        $this->petugas->update(['hari_libur' => 0]); // Sunday

        $monday = today()->next(Carbon::MONDAY);

        $result = $this->service->weeklyOffInfo($this->petugas, $monday);

        $this->assertFalse($result['is_holiday']);
    }

    public function test_weekly_off_info_returns_false_when_no_day_off_set(): void
    {
        $this->petugas->update(['hari_libur' => null]);

        $result = $this->service->weeklyOffInfo($this->petugas, today());

        $this->assertFalse($result['is_holiday']);
    }

    public function test_leave_info_returns_true_for_approved_leave(): void
    {
        Cuti::create([
            'id_user' => $this->petugas->id_user,
            'tanggal_mulai' => today()->subDays(1)->toDateString(),
            'tanggal_selesai' => today()->addDays(1)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'status' => 'approve',
        ]);

        $result = $this->service->leaveInfo($this->petugas, today());

        $this->assertTrue($result['is_leave']);
        $this->assertStringContainsString('Tahunan', $result['reason']);
    }

    public function test_leave_info_returns_false_for_no_leave(): void
    {
        $result = $this->service->leaveInfo($this->petugas, today());

        $this->assertFalse($result['is_leave']);
        $this->assertNull($result['reason']);
    }

    public function test_leave_info_returns_false_for_rejected_leave(): void
    {
        Cuti::create([
            'id_user' => $this->petugas->id_user,
            'tanggal_mulai' => today()->subDays(1)->toDateString(),
            'tanggal_selesai' => today()->addDays(1)->toDateString(),
            'jenis_cuti' => 'Tahunan',
            'status' => 'reject',
        ]);

        $result = $this->service->leaveInfo($this->petugas, today());

        $this->assertFalse($result['is_leave']);
    }

    public function test_backfill_is_idempotent(): void
    {
        $result1 = $this->service->backfillForUserUntilYesterday($this->petugas);
        $result2 = $this->service->backfillForUserUntilYesterday($this->petugas);

        // Second call should not create new records due to caching
        $this->assertEquals(0, $result2['created']);
    }

    public function test_backfill_skips_non_petugas(): void
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

        $result = $this->service->backfillForUserUntilYesterday($admin);

        $this->assertEquals(0, $result['created']);
        $this->assertEquals(0, $result['skipped']);
    }
}
