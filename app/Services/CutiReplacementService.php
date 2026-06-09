<?php

namespace App\Services;

use App\Models\Cuti;
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

    public function validateNewRequest(User $user, User $pengganti, Carbon $startDate, Carbon $endDate, string $jenisCuti): ?string
    {
        if ($pengganti->id_user === $user->id_user || ! $pengganti->isPetugas()) {
            return 'Petugas pengganti harus dipilih dari petugas lain.';
        }

        if ($user->regu && $pengganti->regu && $pengganti->regu !== $user->regu) {
            return 'Petugas pengganti harus dari regu yang sama.';
        }

        $penggantiSedangCuti = Cuti::where('id_user', $pengganti->id_user)
            ->whereIn('status', ['pending', 'approve', 'approved'])
            ->where('tanggal_mulai', '<=', $endDate->toDateString())
            ->where('tanggal_selesai', '>=', $startDate->toDateString())
            ->exists();

        if ($penggantiSedangCuti) {
            return $pengganti->nama . ' tidak bisa dipilih karena punya pengajuan cuti pada tanggal tersebut.';
        }

        $penggantiSudahBertugas = Cuti::where('id_pengganti', $pengganti->id_user)
            ->whereIn('status', ['pending', 'approve', 'approved'])
            ->where('tanggal_mulai', '<=', $endDate->toDateString())
            ->where('tanggal_selesai', '>=', $startDate->toDateString())
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
        $this->notifyAtasanReplacementAccepted($cuti);

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
        if ($status === 'approve' && $cuti->replacement_status !== 'accepted') {
            return 'Petugas pengganti harus menerima permintaan terlebih dahulu.';
        }

        return null;
    }

    public function afterApproved(Cuti $cuti): void
    {
        $cuti->loadMissing(['user', 'pengganti']);
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
                'judul' => 'Pengganti Cuti Menerima',
                'pesan' => 'Petugas ' . ($cuti->user->nama ?? '-') . ' mengajukan cuti dan pengganti sudah menerima. Approval dilakukan oleh atasan.',
                'tipe' => 'cuti',
                'status_baca' => false,
                'reference_id' => $cuti->id_cuti,
                'reference_type' => Cuti::class,
            ]);
        }
    }

    private function notifyAtasanReplacementAccepted(Cuti $cuti): void
    {
        $cuti->loadMissing('user');

        $atasans = User::whereHas('role', function ($query) {
                QueryFilters::whereRoleAlias($query, ['atasan', 'manager', 'menejer']);
            })
            ->when($cuti->user?->id_tempat, fn ($query) => $query->where('id_tempat', $cuti->user->id_tempat))
            ->get();

        foreach ($atasans as $atasan) {
            Notifikasi::create([
                'id_user' => $atasan->id_user,
                'judul' => 'Approval Cuti Menunggu',
                'pesan' => 'Pengajuan cuti ' . ($cuti->user->nama ?? 'petugas') . ' sudah dikonfirmasi pengganti dan menunggu keputusan atasan.',
                'tipe' => 'cuti',
                'status_baca' => false,
                'reference_id' => $cuti->id_cuti,
                'reference_type' => Cuti::class,
            ]);
        }
    }

}
