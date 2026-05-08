<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Periode;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {

            // ambil semua periode (urut dari terbaru)
            $periodes = Periode::orderBy('tanggal_mulai', 'desc')->get();

            // ambil periode aktif (kalau ada)
            $activePeriode = Periode::aktif();

            // ambil dari session atau fallback ke periode aktif
            $activePeriodeId = session('global_periode_id') ?? optional($activePeriode)->id_periode;

            // cari periode yang dipilih
            $selectedPeriode = $periodes->firstWhere('id_periode', $activePeriodeId);

            $view->with('globalPeriodes', $periodes);
            $view->with('globalSelectedPeriode', $selectedPeriode);
        });
    }
}
