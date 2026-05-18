<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Kalender;
use App\Models\Periode;
use App\Models\User;
use App\Support\QueryFilters;
use Carbon\Carbon;

class AbsensiTidakAbsenService
{
    public function generateForDate(Carbon|string $date, ?User $onlyUser = null): array
    {
        $targetDate = $date instanceof Carbon
            ? $date->copy()->startOfDay()
            : Carbon::parse($date)->startOfDay();

        $tanggalString = $targetDate->toDateString();
        $result = [
            'date' => $tanggalString,
            'created' => 0,
            'skipped' => 0,
            'reason' => null,
        ];

        $activePeriode = $this->periodeForDate($tanggalString);
        if (! $activePeriode) {
            $result['reason'] = 'Tidak ada periode aktif.';
            return $result;
        }

        if ($this->isHoliday($targetDate)) {
            $result['reason'] = 'Tanggal libur atau weekend.';
            return $result;
        }

        $users = $onlyUser
            ? collect([$onlyUser])
            : User::whereHas('role', function ($query) {
                QueryFilters::whereRoleAlias($query, ['petugas', 'karyawan']);
            })->get();

        foreach ($users as $petugas) {
            if (! $petugas->isPetugas() || $this->createdAfterDate($petugas, $targetDate)) {
                $result['skipped']++;
                continue;
            }

            $exists = Absensi::where('id_user', $petugas->id_user)
                ->whereDate('tanggal', $tanggalString)
                ->exists();

            if ($exists) {
                $result['skipped']++;
                continue;
            }

            Absensi::create([
                'id_user' => $petugas->id_user,
                'id_periode' => $activePeriode->id_periode,
                'tanggal' => $tanggalString,
                'status' => 'tidak_absen',
                'keterangan' => 'Tidak hadir (otomatis sistem)',
            ]);

            $result['created']++;
        }

        return $result;
    }

    public function backfillForUserUntilYesterday(User $user): array
    {
        if (! $user->isPetugas()) {
            return ['created' => 0, 'skipped' => 0];
        }

        $periode = Periode::aktif();
        if (! $periode) {
            return ['created' => 0, 'skipped' => 0];
        }

        $today = now()->startOfDay();
        $endDate = $today->copy()->subDay();
        if ($endDate->lt($periode->tanggal_mulai)) {
            return ['created' => 0, 'skipped' => 0];
        }

        $lastAbsensiDate = Absensi::where('id_user', $user->id_user)
            ->whereDate('tanggal', '<', $today->toDateString())
            ->max('tanggal');

        $startDate = $lastAbsensiDate
            ? Carbon::parse($lastAbsensiDate)->addDay()->startOfDay()
            : $periode->tanggal_mulai->copy()->startOfDay();

        if ($user->created_at && $user->created_at->gt($startDate)) {
            $startDate = $user->created_at->copy()->startOfDay();
        }

        if ($startDate->lt($periode->tanggal_mulai)) {
            $startDate = $periode->tanggal_mulai->copy()->startOfDay();
        }

        if ($endDate->gt($periode->tanggal_selesai)) {
            $endDate = $periode->tanggal_selesai->copy()->startOfDay();
        }

        $total = ['created' => 0, 'skipped' => 0];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $result = $this->generateForDate($date, $user);
            $total['created'] += $result['created'];
            $total['skipped'] += $result['skipped'];
        }

        return $total;
    }

    public function generateTodayForUserAfterCutoff(User $user): array
    {
        if (! $user->isPetugas()) {
            return ['date' => today()->toDateString(), 'created' => 0, 'skipped' => 0, 'reason' => 'Bukan petugas.'];
        }

        $now = now();
        $cutoff = config(
            'absensi.batas_otomatis_tidak_absen',
            config('absensi.jam_masuk_tutup', '07:15:00')
        );
        $cutoffTime = $now->copy()->setTimeFromTimeString($cutoff);

        if ($now->lte($cutoffTime)) {
            return ['date' => $now->toDateString(), 'created' => 0, 'skipped' => 1, 'reason' => 'Belum melewati batas absen masuk.'];
        }

        return $this->generateForDate($now->copy()->startOfDay(), $user);
    }

    private function periodeForDate(string $tanggal): ?Periode
    {
        return Periode::where('tanggal_mulai', '<=', $tanggal)
            ->where('tanggal_selesai', '>=', $tanggal)
            ->where('status', 'aktif')
            ->orderByDesc('id_periode')
            ->first();
    }

    private function isHoliday(Carbon $date): bool
    {
        return $date->isWeekend()
            || Kalender::whereDate('tanggal', $date->toDateString())->exists();
    }

    private function createdAfterDate(User $user, Carbon $date): bool
    {
        return $user->created_at
            && $user->created_at->copy()->startOfDay()->gt($date);
    }
}
