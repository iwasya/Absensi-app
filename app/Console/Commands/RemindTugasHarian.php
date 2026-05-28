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

        $created = 0;
        $skipped = 0;

        foreach ($petugas as $user) {
            $hasReport = Tugas::where('id_user', $user->id_user)
                ->whereDate('tanggal_mulai', $targetDate->toDateString())
                ->exists();

            if ($hasReport) {
                $skipped++;
                continue;
            }

            $pesan = 'Kamu belum mengisi laporan tugas harian tanggal ' . $dateLabel . '. Silakan isi laporan sebelum hari berganti.';

            $alreadyReminded = Notifikasi::where('id_user', $user->id_user)
                ->where('judul', $judul)
                ->where('pesan', $pesan)
                ->exists();

            if ($alreadyReminded && ! $this->option('force')) {
                $skipped++;
                continue;
            }

            Notifikasi::create([
                'id_user' => $user->id_user,
                'judul' => $judul,
                'pesan' => $pesan,
                'tipe' => 'tugas',
                'status_baca' => false,
                'reference_id' => $user->id_user,
                'reference_type' => User::class,
            ]);

            $created++;
        }

        $this->info("Pengingat laporan tugas {$dateLabel} selesai. Dikirim: {$created}, dilewati: {$skipped}.");

        return Command::SUCCESS;
    }
}
