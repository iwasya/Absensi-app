<?php

namespace App\Console\Commands;

use App\Services\AbsensiTidakAbsenService;
use Illuminate\Console\Command;

class GenerateTidakAbsen extends Command
{
    protected $signature = 'absensi:generate-tidak-absen {--date=}';
    protected $description = 'Generate tidak_absen records for petugas who did not check in';

    public function handle(): int
    {
        // Target date: yesterday by default, or custom date if specified
        $targetDate = $this->option('date')
            ? \Carbon\Carbon::parse($this->option('date'))
            : now()->subDay()->startOfDay();

        $result = app(AbsensiTidakAbsenService::class)->generateForDate($targetDate);

        $this->info("Memproses absensi tidak hadir untuk tanggal: {$result['date']}");

        if ($result['reason']) {
            $this->warn($result['reason'] . ' Skip.');
            return Command::SUCCESS;
        }

        $this->info("Selesai. Dibuat: {$result['created']}, Sudah ada/skip: {$result['skipped']}");

        return Command::SUCCESS;
    }
}
