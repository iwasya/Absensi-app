<?php

namespace App\Console\Commands;

use App\Models\Notifikasi;
use App\Models\Tugas;
use App\Models\User;
use App\Support\QueryFilters;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemindTugasHarian extends Command
{
    protected $signature = 'tugas:remind-harian {--date=} {--force}';
    protected $description = 'Kirim pengingat laporan tugas harian ke petugas yang belum mengisi laporan';

    public function handle(): int
    {
        $targetDate = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : today();

        $dateLabel = $targetDate->format('d/m/Y');
        $judul = 'Pengingat Laporan Tugas Harian';

        $petugas = User::with('role')
            ->whereHas('role', function ($query) {
                QueryFilters::whereRoleAlias($query, ['petugas', 'karyawan']);
            })
            ->where(function ($query) {
                $query->whereNull('status_aktif')
                    ->orWhereIn('status_aktif', ['aktif', 'active']);
            })
            ->orderBy('nama')
            ->get();

        $petugasIds = $petugas->pluck('id_user');
        $reportedUserIds = Tugas::whereIn('id_user', $petugasIds)
            ->whereDate('tanggal_mulai', $targetDate->toDateString())
            ->pluck('id_user')
            ->all();

        $pesan = 'Kamu belum mengisi laporan tugas harian tanggal ' . $dateLabel . '. Silakan isi laporan sebelum hari berganti.';
        $alreadyRemindedUserIds = Notifikasi::whereIn('id_user', $petugasIds)
            ->where('judul', $judul)
            ->where('pesan', $pesan)
            ->pluck('id_user')
            ->all();

        $reportedLookup = array_fill_keys($reportedUserIds, true);
        $remindedLookup = array_fill_keys($alreadyRemindedUserIds, true);
        $notifications = [];
        $created = 0;
        $skipped = 0;

        foreach ($petugas as $user) {
            if (isset($reportedLookup[$user->id_user])) {
                $skipped++;
                continue;
            }

            if (isset($remindedLookup[$user->id_user]) && ! $this->option('force')) {
                $skipped++;
                continue;
            }

            $notifications[] = [
                'id_user' => $user->id_user,
                'judul' => $judul,
                'pesan' => $pesan,
                'tipe' => 'tugas',
                'status_baca' => false,
                'reference_id' => $user->id_user,
                'reference_type' => User::class,
                'created_at' => now(),
            ];

            $created++;
        }

        if ($notifications !== []) {
            Notifikasi::insert($notifications);
        }

        $this->info("Pengingat laporan tugas {$dateLabel} selesai. Dikirim: {$created}, dilewati: {$skipped}.");

        return Command::SUCCESS;
    }
}
