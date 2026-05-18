<?php

namespace App\Providers;

use App\Models\Notifikasi;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
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

        View::composer('*', function ($view) {
            $settings = Cache::remember('pengaturan:app-shell', 300, function () {
                return [
                    'app_theme' => Pengaturan::getNilai('app_theme', 'light'),
                    'app_name' => Pengaturan::getNilai('app_name', 'Absensi PPSU') ?: 'Absensi PPSU',
                    'app_logo' => Pengaturan::getNilai('app_logo'),
                    'app_brand_display' => Pengaturan::getNilai('app_brand_display', 'logo_name'),
                    'app_icon' => Pengaturan::getNilai('app_icon'),
                    'app_icon_mode' => Pengaturan::getNilai('app_icon_mode', 'upload'),
                    'app_icon_text' => Pengaturan::getNilai('app_icon_text', 'A'),
                    'app_icon_bg' => Pengaturan::getNilai('app_icon_bg', '#2563eb'),
                    'app_icon_color' => Pengaturan::getNilai('app_icon_color', '#ffffff'),
                ];
            });

            $view->with($settings);
        });

        View::composer('layouts.app', function ($view) {
            $periodes = Periode::latestCached();
            $activePeriode = Periode::aktif();
            $activePeriodeId = session('global_periode_id') ?? optional($activePeriode)->id_periode;
            $selectedPeriode = $periodes->firstWhere('id_periode', $activePeriodeId);

            $unreadNotifications = 0;
            $headerNotifications = collect();
            if (auth()->check()) {
                $userId = auth()->id();
                $unreadNotifications = Cache::remember("notifikasi:unread-count:{$userId}", 60, function () use ($userId) {
                    return Notifikasi::where('id_user', $userId)->where('status_baca', false)->count();
                });
                $headerNotifications = Cache::remember("notifikasi:header:{$userId}", 60, function () use ($userId) {
                    return Notifikasi::where('id_user', $userId)->latest('id_notifikasi')->limit(5)->get();
                });
            }

            $view->with('globalPeriodes', $periodes);
            $view->with('globalSelectedPeriode', $selectedPeriode);
            $view->with('unreadNotifications', $unreadNotifications);
            $view->with('headerNotifications', $headerNotifications);
        });
    }
}
