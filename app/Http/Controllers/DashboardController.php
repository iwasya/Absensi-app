<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\ActivityLog;
use App\Models\Cuti;
use App\Models\Kalender;
use App\Models\Notifikasi;
use App\Models\Periode;
use App\Models\Tugas;
use App\Models\User;
use App\Services\AbsensiTidakAbsenService;
use App\Support\QueryFilters;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('role', 'tempatTugas');

        if ($user->isAdmin()) {
            return view('admin.dashboard', [
                'user' => $user,
                'totalUsers' => User::count(),
                'totalPetugas' => User::whereHas('role', function ($query) {
                    QueryFilters::whereRoleAlias($query, ['petugas', 'karyawan']);
                })->count(),
                'totalAbsensiHariIni' => Absensi::whereDate('tanggal', today())->count(),
                'cutiPending' => Cuti::where('status', 'pending')->count(),
                'tugasPending' => Tugas::where('status', 'pending')->count(),
                'periodeAktif' => Periode::aktif(),
            ]);
        }

        if ($user->isAtasan()) {
            $calendar = $this->dashboardCalendar($request);
            $absensiKalender = Absensi::with('user')->whereBetween('tanggal', [
                    $calendar['calendarStart']->toDateString(),
                    $calendar['calendarEnd']->toDateString(),
                ])
                ->get();
            $absensiByDate = $absensiKalender
                ->groupBy(fn (Absensi $item) => $item->tanggal->format('Y-m-d'))
                ->map(fn (Collection $items) => $items->count());
            $tugasKalender = Tugas::with('user')
                ->whereHas('user', function ($query) use ($user) {
                    $query->whereHas('role', function ($roleQuery) {
                        QueryFilters::whereRoleAlias($roleQuery, ['petugas', 'karyawan']);
                    });

                    if ($user->id_tempat) {
                        $query->where('id_tempat', $user->id_tempat);
                    }
                })
                ->where(function ($query) use ($calendar) {
                    $query->whereBetween('tanggal_mulai', [
                            $calendar['calendarStart']->copy()->startOfDay(),
                            $calendar['calendarEnd']->copy()->endOfDay(),
                        ])
                        ->orWhereBetween('tanggal_selesai', [
                            $calendar['calendarStart']->copy()->startOfDay(),
                            $calendar['calendarEnd']->copy()->endOfDay(),
                        ])
                        ->orWhere(function ($q) use ($calendar) {
                            $q->where('tanggal_mulai', '<=', $calendar['calendarStart']->copy()->startOfDay())
                                ->whereNotNull('tanggal_selesai')
                                ->where('tanggal_selesai', '>=', $calendar['calendarEnd']->copy()->endOfDay());
                        });
                })
                ->get();
            $tugasByDateItems = collect($this->spreadTugasByDate($tugasKalender));
            $tugasByDate = $tugasByDateItems
                ->map(fn (Collection $items) => $items->count());
            $absensiCalendarDetails = $absensiKalender
                ->groupBy(fn (Absensi $item) => $item->tanggal->format('Y-m-d'))
                ->map(fn (Collection $items) => $items->take(5)->map(fn (Absensi $item) => [
                    'nama' => $item->user->nama ?? '-',
                    'status' => ucfirst(str_replace('_', ' ', $item->status)),
                    'waktu' => 'Masuk ' . ($item->jam_masuk ?? '--:--') . ' - Pulang ' . ($item->jam_pulang ?? '--:--'),
                ])->values());
            $tugasCalendarDetails = $tugasByDateItems
                ->map(fn (Collection $items) => $items->take(5)->map(fn (Tugas $item) => [
                    'nama' => $item->user->nama ?? '-',
                    'status' => ucfirst(str_replace('_', ' ', $item->status)),
                    'waktu' => $item->tanggal_mulai?->format('H:i') ?? '--:--',
                ])->values());
            $eventCalendarDetails = $calendar['events']
                ->map(fn (Collection $items) => $items->map(fn (Kalender $item) => [
                    'nama' => $item->nama_event,
                    'status' => ucfirst(str_replace('_', ' ', $item->jenis_event)),
                    'waktu' => 'Kalender',
                ])->values());
            $aktivitasPetugas = ActivityLog::with('user.tempatTugas')
                ->whereIn('modul', ['absensi', 'cuti', 'tugas'])
                ->whereHas('user', function ($query) use ($user) {
                    $query->whereHas('role', function ($roleQuery) {
                        QueryFilters::whereRoleAlias($roleQuery, ['petugas', 'karyawan']);
                    });

                    if ($user->id_tempat) {
                        $query->where('id_tempat', $user->id_tempat);
                    }
                })
                ->latest('id_log')
                ->limit(5)
                ->get();

            return view('atasan.dashboard', [
                'user' => $user,
                'absensiHariIni' => Absensi::with('user')->whereDate('tanggal', today())->latest('id_absensi')->limit(5)->get(),
                'cutiPending' => Cuti::with('user')->where('status', 'pending')->latest('id_cuti')->limit(5)->get(),
                'tugasPending' => Tugas::with('user')->where('status', 'pending')->latest('id_tugas')->limit(5)->get(),
                'absensiBulanIni' => Absensi::whereBetween('tanggal', [
                    $calendar['currentMonth']->copy()->startOfMonth()->toDateString(),
                    $calendar['currentMonth']->copy()->endOfMonth()->toDateString(),
                ])->count(),
                'kalender' => Kalender::whereDate('tanggal', '>=', today())->orderBy('tanggal')->limit(5)->get(),
                'currentMonth' => $calendar['currentMonth'],
                'previousMonth' => $calendar['previousMonth'],
                'nextMonth' => $calendar['nextMonth'],
                'days' => $calendar['days'],
                'events' => $calendar['events'],
                'absensiByDate' => $absensiByDate,
                'tugasByDate' => $tugasByDate,
                'absensiCalendarDetails' => $absensiCalendarDetails,
                'tugasCalendarDetails' => $tugasCalendarDetails,
                'eventCalendarDetails' => $eventCalendarDetails,
                'aktivitasTerbaru' => $aktivitasPetugas,
            ]);
        }

        $absensiTidakAbsen = app(AbsensiTidakAbsenService::class);
        $absensiTidakAbsen->backfillForUserUntilYesterday($user);
        $absensiTidakAbsen->generateTodayForUserAfterCutoff($user);

        $calendar = $this->dashboardCalendar($request);
        $cutiTerpakaiTahunIni = Cuti::where('id_user', $user->id_user)
            ->whereYear('tanggal_mulai', now()->year)
            ->whereIn('status', ['pending', 'approve'])
            ->count();

        // Rekap absensi bulan ini
        $startOfMonth = $calendar['currentMonth']->copy()->startOfMonth();
        $endOfMonth = $calendar['currentMonth']->copy()->endOfMonth();
        
        $absensiUser = Absensi::where('id_user', $user->id_user)
            ->whereBetween('tanggal', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get();
        
        $totalHadir = $absensiUser->where('status', 'hadir')->count();
        $totalTelat = $absensiUser->where('status', 'telat')->count();
        $totalTidakHadir = $absensiUser->whereIn('status', ['tidak_hadir', 'tidak_absen'])->count();
        
        // Hitung hari kerja (Senin-Jumat) dalam bulan ini
        $hariKerja = 0;
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            if ($date->isWeekday()) {
                $hariKerja++;
            }
        }
        
        $rekapBulan = [
            'hadir' => $totalHadir,
            'telat' => $totalTelat,
            'tidak_hadir' => $totalTidakHadir,
            'hari_kerja' => $hariKerja,
        ];
        
        // Data untuk kalender (tanggal dalam bulan ini)
        $kalenderHadir = $absensiUser->where('status', 'hadir')->pluck('tanggal')->map(fn($d) => (int)$d->format('d'))->toArray();
        $kalenderTelat = $absensiUser->where('status', 'telat')->pluck('tanggal')->map(fn($d) => (int)$d->format('d'))->toArray();
        $kalenderAbsen = $absensiUser->whereIn('status', ['tidak_hadir', 'tidak_absen'])->pluck('tanggal')->map(fn($d) => (int)$d->format('d'))->toArray();
        
        $userTugas = Tugas::where('id_user', $user->id_user)
            ->where(function ($query) use ($calendar) {
                $query->whereBetween('tanggal_mulai', [
                        $calendar['calendarStart']->copy()->startOfDay(),
                        $calendar['calendarEnd']->copy()->endOfDay(),
                    ])
                    ->orWhereBetween('tanggal_selesai', [
                        $calendar['calendarStart']->copy()->startOfDay(),
                        $calendar['calendarEnd']->copy()->endOfDay(),
                    ])
                    ->orWhere(function ($q) use ($calendar) {
                        $q->where('tanggal_mulai', '<=', $calendar['calendarStart']->copy()->startOfDay())
                            ->whereNotNull('tanggal_selesai')
                            ->where('tanggal_selesai', '>=', $calendar['calendarEnd']->copy()->endOfDay());
                    });
            })
            ->get();
        $tugasByDate = $this->spreadTugasByDate($userTugas);

        $tugasBulanIni = Tugas::where('id_user', $user->id_user)
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('tanggal_mulai', [
                        $startOfMonth->copy()->startOfDay(),
                        $endOfMonth->copy()->endOfDay(),
                    ])
                    ->orWhereBetween('tanggal_selesai', [
                        $startOfMonth->copy()->startOfDay(),
                        $endOfMonth->copy()->endOfDay(),
                    ])
                    ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('tanggal_mulai', '<=', $startOfMonth->copy()->startOfDay())
                            ->whereNotNull('tanggal_selesai')
                            ->where('tanggal_selesai', '>=', $endOfMonth->copy()->endOfDay());
                    });
            })
            ->get();
        $absensiCalendarDetails = $absensiUser
            ->groupBy(fn (Absensi $item) => $item->tanggal->format('Y-m-d'))
            ->map(fn (Collection $items) => $items->map(fn (Absensi $item) => [
                'nama' => ucfirst(str_replace('_', ' ', $item->status)),
                'status' => $item->keterangan ?: 'Absensi',
                'waktu' => 'Masuk ' . ($item->jam_masuk ?? '--:--') . ' - Pulang ' . ($item->jam_pulang ?? '--:--'),
            ])->values());
        $tugasCalendarDetails = collect($tugasByDate)
            ->map(fn (Collection $items) => $items->map(fn (Tugas $item) => [
                'nama' => $item->uraian,
                'status' => ucfirst(str_replace('_', ' ', $item->status)),
                'waktu' => $item->tanggal_mulai?->format('H:i') ?? '--:--',
            ])->values());
        $eventCalendarDetails = $calendar['events']
            ->map(fn (Collection $items) => $items->map(fn (Kalender $item) => [
                'nama' => $item->nama_event,
                'status' => ucfirst(str_replace('_', ' ', $item->jenis_event)),
                'waktu' => 'Kalender',
            ])->values());

        return view('petugas.dashboard', [
            'user' => $user,
            'absensiHariIni' => Absensi::where('id_user', $user->id_user)->whereDate('tanggal', today())->first(),
            'tugasHariIni' => Tugas::where('id_user', $user->id_user)
                ->where('tanggal_mulai', '<=', today()->endOfDay())
                ->where(function ($query) {
                    $query->whereNull('tanggal_selesai')
                        ->orWhere('tanggal_selesai', '>=', today()->startOfDay());
                })
                ->latest('id_tugas')
                ->first(),
            'cutiTerakhir' => Cuti::where('id_user', $user->id_user)->latest('id_cuti')->limit(5)->get(),
            'tugasTerakhir' => Tugas::where('id_user', $user->id_user)->latest('id_tugas')->limit(5)->get(),
            'notifikasiBelumBaca' => Notifikasi::where('id_user', $user->id_user)->where('status_baca', false)->count(),
            'kalender' => Kalender::whereDate('tanggal', '>=', today())->orderBy('tanggal')->limit(5)->get(),
            'cutiTerpakaiTahunIni' => $cutiTerpakaiTahunIni,
            'sisaCutiTahunIni' => max(12 - $cutiTerpakaiTahunIni, 0),
            'aktivitasTerbaru' => ActivityLog::where('id_user', $user->id_user)
                ->whereIn('modul', ['absensi', 'cuti', 'tugas'])
                ->latest('id_log')
                ->limit(5)
                ->get(),
            'currentMonth' => $calendar['currentMonth'],
            'previousMonth' => $calendar['previousMonth'],
            'nextMonth' => $calendar['nextMonth'],
            'days' => $calendar['days'],
            'events' => $calendar['events'],
            'tugasByDate' => $tugasByDate,
            'rekapBulan' => $rekapBulan,
            'kalenderHadir' => $kalenderHadir,
            'kalenderTelat' => $kalenderTelat,
            'kalenderAbsen' => $kalenderAbsen,
            'absensiCalendarDetails' => $absensiCalendarDetails,
            'tugasCalendarDetails' => $tugasCalendarDetails,
            'eventCalendarDetails' => $eventCalendarDetails,
            'totalTugas' => $tugasBulanIni->count(),
            'tugasDisetujui' => $tugasBulanIni->whereIn('status', ['approve', 'approved'])->count(),
        ]);
    }

    private function dashboardCalendar(Request $request): array
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        $currentMonth = Carbon::create($year, $month, 1)->startOfDay();
        $calendarStart = $currentMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $currentMonth->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $events = Kalender::whereBetween('tanggal', [
                $calendarStart->toDateString(),
                $calendarEnd->toDateString(),
            ])
            ->orderBy('tanggal')
            ->orderBy('id_kalender')
            ->get()
            ->groupBy(fn (Kalender $item) => $item->tanggal->format('Y-m-d'));

        $days = collect();
        for ($day = $calendarStart->copy(); $day->lte($calendarEnd); $day->addDay()) {
            $days->push($day->copy());
        }

        return [
            'currentMonth' => $currentMonth,
            'previousMonth' => $currentMonth->copy()->subMonth(),
            'nextMonth' => $currentMonth->copy()->addMonth(),
            'calendarStart' => $calendarStart,
            'calendarEnd' => $calendarEnd,
            'days' => $days,
            'events' => $events,
        ];
    }

    private function spreadTugasByDate(Collection $tugasItems): array
    {
        $tugasByDate = [];

        foreach ($tugasItems as $tugas) {
            $start = $tugas->tanggal_mulai->copy()->startOfDay();
            $end = $tugas->tanggal_selesai ? $tugas->tanggal_selesai->copy()->startOfDay() : $start->copy();

            for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
                $dateKey = $day->format('Y-m-d');

                if (! isset($tugasByDate[$dateKey])) {
                    $tugasByDate[$dateKey] = collect();
                }

                $tugasByDate[$dateKey]->push($tugas);
            }
        }

        return $tugasByDate;
    }
}
