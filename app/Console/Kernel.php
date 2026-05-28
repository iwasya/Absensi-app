<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Generate tidak_absen records setiap hari jam 08:00
        // untuk petugas yang tidak absen hari sebelumnya
        $schedule->command('absensi:generate-tidak-absen')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/tidak-absen.log'));

        // Ingatkan petugas mengisi laporan tugas harian menjelang akhir hari kerja.
        $schedule->command('tugas:remind-harian')
            ->dailyAt('17:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/tugas-reminder.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
