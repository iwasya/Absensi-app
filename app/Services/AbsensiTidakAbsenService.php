<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Kalender;
use App\Models\Cuti;
use App\Models\Periode;
use App\Models\User;
use App\Support\QueryFilters;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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

        $holiday = $this->holidayInfo($targetDate);
        if ($holiday['is_holiday']) {
            $result['reason'] = $holiday['reason'];
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

            $leave = $this->leaveInfo($petugas, $targetDate);
            if ($leave['is_leave']) {
                $leaveResult = $this->storeLeaveAbsensi($petugas, $targetDate, $activePeriode, $leave['cuti']);
                if ($leaveResult === 'created') {
                    $result['created']++;
                } else {
                    $result['skipped']++;
                }

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

    public function syncApprovedLeave(Cuti $cuti): array
    {
        if (! in_array($cuti->status, ['approve', 'approved'], true)) {
            return ['created' => 0, 'updated' => 0, 'skipped' => 0];
        }

        $user = $cuti->user;
        if (! $user || ! $user->isPetugas()) {
            return ['created' => 0, 'updated' => 0, 'skipped' => 0];
        }

        $total = ['created' => 0, 'updated' => 0, 'skipped' => 0];
        $startDate = $cuti->tanggal_mulai->copy()->startOfDay();
        $endDate = $cuti->tanggal_selesai->copy()->startOfDay();

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if ($this->holidayInfo($date)['is_holiday']) {
                $total['skipped']++;
                continue;
            }

            $periode = $this->periodeForDate($date->toDateString());
            if (! $periode || $this->createdAfterDate($user, $date)) {
                $total['skipped']++;
                continue;
            }

            $result = $this->storeLeaveAbsensi($user, $date, $periode, $cuti);
            if ($result === 'created') {
                $total['created']++;
            } elseif ($result === 'updated') {
                $total['updated']++;
            } else {
                $total['skipped']++;
            }
        }

        return $total;
    }

    public function backfillForUserUntilYesterday(User $user): array
    {
        if (! $user->isPetugas()) {
            return ['created' => 0, 'skipped' => 0];
        }

        $cacheKey = "absensi:backfill:user:{$user->id_user}:" . today()->toDateString();
        if (Cache::has($cacheKey)) {
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

        Cache::put($cacheKey, true, now()->endOfDay());

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

        $cacheKey = "absensi:auto-today:user:{$user->id_user}:" . $now->toDateString();
        if (Cache::has($cacheKey)) {
            return ['date' => $now->toDateString(), 'created' => 0, 'skipped' => 0, 'reason' => 'Sudah diproses hari ini.'];
        }

        $result = $this->generateForDate($now->copy()->startOfDay(), $user);
        Cache::put($cacheKey, true, now()->endOfDay());

        return $result;
    }

    private function periodeForDate(string $tanggal): ?Periode
    {
        return Periode::where('tanggal_mulai', '<=', $tanggal)
            ->where('tanggal_selesai', '>=', $tanggal)
            ->where('status', 'aktif')
            ->orderByDesc('id_periode')
            ->first();
    }

    public function holidayInfo(Carbon|string $date): array
    {
        $targetDate = $date instanceof Carbon
            ? $date->copy()->startOfDay()
            : Carbon::parse($date)->startOfDay();

        if ($targetDate->isWeekend()) {
            return [
                'is_holiday' => true,
                'reason' => 'Weekend',
                'event' => null,
            ];
        }

        $event = Kalender::whereDate('tanggal', $targetDate->toDateString())
            ->whereIn('jenis_event', ['libur', 'cuti_bersama'])
            ->orderBy('id_kalender')
            ->first();

        return [
            'is_holiday' => (bool) $event,
            'reason' => $event
                ? ($event->nama_event ?: ucfirst(str_replace('_', ' ', $event->jenis_event)))
                : null,
            'event' => $event,
        ];
    }

    public function leaveInfo(User|int $user, Carbon|string $date): array
    {
        $userId = $user instanceof User ? $user->id_user : $user;
        $targetDate = $date instanceof Carbon
            ? $date->copy()->startOfDay()
            : Carbon::parse($date)->startOfDay();

        $cuti = Cuti::where('id_user', $userId)
            ->whereIn('status', ['approve', 'approved'])
            ->whereDate('tanggal_mulai', '<=', $targetDate->toDateString())
            ->whereDate('tanggal_selesai', '>=', $targetDate->toDateString())
            ->orderByDesc('id_cuti')
            ->first();

        return [
            'is_leave' => (bool) $cuti,
            'reason' => $cuti ? $this->leaveReason($cuti) : null,
            'cuti' => $cuti,
        ];
    }

    private function createdAfterDate(User $user, Carbon $date): bool
    {
        return $user->created_at
            && $user->created_at->copy()->startOfDay()->gt($date);
    }

    private function storeLeaveAbsensi(User $user, Carbon $date, Periode $periode, Cuti $cuti): string|false
    {
        $tanggal = $date->toDateString();
        $keterangan = $this->leaveReason($cuti);
        $existing = Absensi::where('id_user', $user->id_user)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if ($existing) {
            if (in_array($existing->status, ['tidak_absen', 'tidak_hadir', 'cuti'], true)
                && ! $existing->jam_masuk
                && ! $existing->jam_pulang) {
                $existing->update([
                    'id_periode' => $periode->id_periode,
                    'status' => 'cuti',
                    'keterangan' => $keterangan,
                ]);

                return $existing->wasChanged() ? 'updated' : false;
            }

            return false;
        }

        Absensi::create([
            'id_user' => $user->id_user,
            'id_periode' => $periode->id_periode,
            'tanggal' => $tanggal,
            'status' => 'cuti',
            'keterangan' => $keterangan,
        ]);

        return 'created';
    }

    private function leaveReason(Cuti $cuti): string
    {
        return 'Cuti ' . $cuti->jenis_cuti . ' disetujui';
    }
}
