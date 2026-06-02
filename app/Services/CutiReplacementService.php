<?php

namespace App\Services;

use App\Models\Cuti;
use App\Models\LiburKompensasi;
use App\Models\Notifikasi;
use App\Models\User;
use App\Support\QueryFilters;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CutiReplacementService
{
    public function replacementCandidatesFor(User $user): Collection
    {
        return User::whereHas('role', function ($query) {
                QueryFilters::whereRoleAlias($query, ['petugas', 'karyawan']);
            })
            ->where('id_user', '!=', $user->id_user)
            ->when($user->regu, function ($query) use ($user) {
                $query->where(function ($reguQuery) use ($user) {
                    $reguQuery->where('regu', $user->regu)
                        ->orWhereNull('regu');
                });
            })
            ->orderBy('regu')
            ->orderBy('nama')
            ->get();
    }

    public function pendingRequestsFor(User $user): Collection
    {
        return Cuti::with('user')
            ->where('id_pengganti', $user->id_user)
            ->where('replacement_status', 'pending')
            ->whereIn('status', ['pending', 'approve', 'approved'])
            ->latest('id_cuti')
            ->get();
    }

    public function availableCreditCountFor(User $user): int
    {
        return LiburKompensasi::where('id_user', $user->id_user)
            ->where('status', 'tersedia')
            ->count();
    }

    public function validateNewRequest(User $user, User $pengganti, Carbon $startDate, Carbon $endDate, string $jenisCuti): ?string
    {
        if ($jenisCuti === 'Kompensasi') {
            $jumlahHariCuti = $startDate->diffInDays($endDate) + 1;
            $saldoKompensasi = $this->availableCreditCountFor($user);

            if ($saldoKompensasi < $jumlahHariCuti) {
                return 'Saldo libur kompensasi tidak cukup. Tersedia ' . $saldoKompensasi . ' hari.';
            }
        }

        if ($pengganti->id_user === $user->id_user || ! $pengganti->isPetugas()) {
            return 'Petugas pengganti harus dipilih dari petugas lain.';
        }

        if ($user->regu && $pengganti->regu && $pengganti->regu !== $user->regu) {
            return 'Petugas pengganti harus dari regu yang sama.';
        }

        $penggantiSedangCuti = Cuti::where('id_user', $pengganti->id_user)
            ->whereIn('status', ['pending', 'approve', 'approved'])
            ->whereDate('tanggal_mulai', '<=', $endDate->toDateString())
            ->whereDate('tanggal_selesai', '>=', $startDate->toDateString())
            ->exists();

        if ($penggantiSedangCuti) {
            return $pengganti->nama . ' tidak bisa dipilih karena punya pengajuan cuti pada tanggal tersebut.';
        }

        $penggantiSudahBertugas = Cuti::where('id_pengganti', $pengganti->id_user)
            ->whereIn('status', ['pending', 'approve', 'approved'])
            ->whereDate('tanggal_mulai', '<=', $endDate->toDateString())
            ->whereDate('tanggal_selesai', '>=', $startDate->toDateString())
            ->exists();

        if ($penggantiSudahBertugas) {
            return $pengganti->nama . ' sudah ditunjuk sebagai pengganti cuti pada tanggal tersebut.';
        }

        return null;
    }

    public function notifyReplacementRequested(Cuti $cuti): void
    {
        $cuti->loadMissing(['user', 'pengganti']);

        if (! $cuti->pengganti) {
            return;
        }

        Notifikasi::create([
            'id_user' => $cuti->pengganti->id_user,
            'judul' => 'Ditunjuk Sebagai Pengganti Cuti',
            'pesan' => ($cuti->user->nama ?? 'Petugas') . ' menunjuk kamu sebagai pengganti cuti tanggal ' . $cuti->tanggal_mulai->translatedFormat('d F Y') . ' sampai ' . $cuti->tanggal_selesai->translatedFormat('d F Y') . '.',
            'tipe' => 'cuti',
            'status_baca' => false,
            'reference_id' => $cuti->id_cuti,
            'reference_type' => Cuti::class,
        ]);
    }

    public function accept(Cuti $cuti, User $user): ?string
    {
        if ((int) $cuti->id_pengganti !== (int) $user->id_user) {
            return 'Kamu bukan petugas pengganti untuk pengajuan ini.';
        }

        if ($cuti->replacement_status !== 'pending') {
            return 'Permintaan pengganti ini sudah diproses.';
        }

        if (! in_array($cuti->status, ['pending', 'approve', 'approved'], true)) {
            return 'Pengajuan cuti ini sudah tidak aktif.';
        }

        $cuti->update([
            'replacement_status' => 'accepted',
            'replacement_confirmed_at' => now(),
            'replacement_note' => null,
        ]);

        Notifikasi::create([
            'id_user' => $cuti->id_user,
            'judul' => 'Pengganti Cuti Menerima',
            'pesan' => $user->nama . ' menerima permintaan sebagai pengganti cuti kamu.',
            'tipe' => 'cuti',
            'status_baca' => false,
            'reference_id' => $cuti->id_cuti,
            'reference_type' => Cuti::class,
        ]);

        $this->notifyAdminsReplacementAccepted($cuti);

        return null;
    }

    public function reject(Cuti $cuti, User $user, ?string $note = null): ?string
    {
        if ((int) $cuti->id_pengganti !== (int) $user->id_user) {
            return 'Kamu bukan petugas pengganti untuk pengajuan ini.';
        }

        if ($cuti->replacement_status !== 'pending') {
            return 'Permintaan pengganti ini sudah diproses.';
        }

        $cuti->update([
            'replacement_status' => 'rejected',
            'replacement_confirmed_at' => now(),
            'replacement_note' => $note,
            'status' => 'rejected',
        ]);

        Notifikasi::create([
            'id_user' => $cuti->id_user,
            'judul' => 'Pengganti Cuti Menolak',
            'pesan' => $user->nama . ' menolak permintaan sebagai pengganti cuti. Silakan ajukan ulang dengan pengganti lain.',
            'tipe' => 'cuti',
            'status_baca' => false,
            'reference_id' => $cuti->id_cuti,
            'reference_type' => Cuti::class,
        ]);

        return null;
    }

    public function approvalBlocker(Cuti $cuti, string $status): ?string
    {
        if ($cuti->replacement_status !== 'accepted') {
            return 'Petugas pengganti harus menerima permintaan terlebih dahulu.';
        }

        if ($status === 'approve' && $cuti->jenis_cuti === 'Kompensasi') {
            $needed = $cuti->tanggal_mulai->diffInDays($cuti->tanggal_selesai) + 1;
            $availableCredits = LiburKompensasi::where('id_user', $cuti->id_user)
                ->where('status', 'tersedia')
                ->count();

            if ($availableCredits < $needed) {
                return 'Saldo libur kompensasi petugas tidak cukup untuk menyetujui cuti ini.';
            }
        }

        return null;
    }

    public function afterApproved(Cuti $cuti): void
    {
        $cuti->loadMissing(['user', 'pengganti']);

        $this->consumeCredits($cuti);
        $this->syncReplacementCredits($cuti);
    }

    private function notifyAdminsReplacementAccepted(Cuti $cuti): void
    {
        $cuti->loadMissing('user');

        $admins = User::whereHas('role', function($q) {
            QueryFilters::whereRoleAlias($q, ['admin']);
        })->get();

        foreach ($admins as $admin) {
            Notifikasi::create([
                'id_user' => $admin->id_user,
                'judul' => 'Pengajuan Cuti Baru',
                'pesan' => 'Petugas ' . ($cuti->user->nama ?? '-') . ' mengajukan cuti dan pengganti sudah menerima. Mohon approval admin sebelum diteruskan ke atasan.',
                'tipe' => 'cuti',
                'status_baca' => false,
                'reference_id' => $cuti->id_cuti,
                'reference_type' => Cuti::class,
            ]);
        }
    }

    private function syncReplacementCredits(Cuti $cuti): void
    {
        $pengganti = $cuti->pengganti;

        if (! $pengganti || $pengganti->hari_libur === null || $cuti->replacement_status !== 'accepted') {
            return;
        }

        $created = 0;
        for ($date = $cuti->tanggal_mulai->copy()->startOfDay(); $date->lte($cuti->tanggal_selesai); $date->addDay()) {
            if ((int) $pengganti->hari_libur !== $date->dayOfWeek) {
                continue;
            }

            $kompensasi = LiburKompensasi::firstOrCreate(
                [
                    'id_user' => $pengganti->id_user,
                    'id_cuti' => $cuti->id_cuti,
                    'tanggal_kerja' => $date->toDateString(),
                ],
                [
                    'status' => 'tersedia',
                    'keterangan' => 'Kompensasi karena menggantikan cuti ' . ($cuti->user->nama ?? 'petugas') . ' pada hari libur mingguan.',
                ]
            );

            if ($kompensasi->wasRecentlyCreated) {
                $created++;
            }
        }

        if ($created > 0) {
            Notifikasi::create([
                'id_user' => $pengganti->id_user,
                'judul' => 'Libur Kompensasi Ditambahkan',
                'pesan' => 'Kamu mendapat ' . $created . ' hari libur kompensasi karena menggantikan cuti pada hari libur mingguan.',
                'tipe' => 'cuti',
                'status_baca' => false,
                'reference_id' => $cuti->id_cuti,
                'reference_type' => Cuti::class,
            ]);
        }
    }

    private function consumeCredits(Cuti $cuti): void
    {
        if ($cuti->jenis_cuti !== 'Kompensasi') {
            return;
        }

        $needed = $cuti->tanggal_mulai->diffInDays($cuti->tanggal_selesai) + 1;
        $credits = LiburKompensasi::where('id_user', $cuti->id_user)
            ->where('status', 'tersedia')
            ->oldest('tanggal_kerja')
            ->limit($needed)
            ->get();

        foreach ($credits as $index => $credit) {
            $credit->update([
                'status' => 'dipakai',
                'tanggal_dipakai' => $cuti->tanggal_mulai->copy()->addDays($index)->toDateString(),
            ]);
        }
    }
}
