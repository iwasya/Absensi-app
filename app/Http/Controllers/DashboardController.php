<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\ActivityLog;
use App\Models\Cuti;
use App\Models\Kalender;
use App\Models\Notifikasi;
use App\Models\Periode;
use App\Models\Role;
use App\Models\Tugas;
use App\Models\User;
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
            $petugasRoleId = Role::where('nama_role', 'like', '%Petugas%')->value('id_role') ?? 1;

            return view('admin.dashboard', [
                'user' => $user,
                'totalUsers' => User::count(),
                'totalPetugas' => User::where('id_role', $petugasRoleId)->count(),
                'totalAbsensiHariIni' => Absensi::whereDate('tanggal', today())->count(),
                'cutiPending' => Cuti::where('status', 'pending')->count(),
                'tugasPending' => Tugas::where('status', 'pending')->count(),
                'periodeAktif' => Periode::aktif(),
            ]);
        }

        if ($user->isAtasan()) {
            $calendar = $this->dashboardCalendar($request);
            $absensiByDate = Absensi::whereBetween('tanggal', [
                    $calendar['calendarStart']->toDateString(),
                    $calendar['calendarEnd']->toDateString(),
                ])
                ->get()
                ->groupBy(fn (Absensi $item) => $item->tanggal->format('Y-m-d'))
                ->map(fn (Collection $items) => $items->count());
            $tugasKalender = Tugas::with('user')
                ->whereHas('user', function ($query) use ($user) {
                    $query->whereHas('role', function ($roleQuery) {
                        $roleQuery->where('nama_role', 'like', '%petugas%')
                            ->orWhere('nama_role', 'like', '%karyawan%');
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
            $tugasByDate = collect($this->spreadTugasByDate($tugasKalender))
                ->map(fn (Collection $items) => $items->count());
            $aktivitasPetugas = ActivityLog::with('user.tempatTugas')
                ->whereIn('modul', ['absensi', 'cuti', 'tugas'])
                ->whereHas('user', function ($query) use ($user) {
                    $query->whereHas('role', function ($roleQuery) {
                        $roleQuery->where('nama_role', 'like', '%petugas%')
                            ->orWhere('nama_role', 'like', '%karyawan%');
                    });

                    if ($user->id_tempat) {
                        $query->where('id_tempat', $user->id_tempat);
                    }
                })
                ->latest('id_log')
                ->limit(8)
                ->get();

            return view('atasan.dashboard', [
                'user' => $user,
                'absensiHariIni' => Absensi::with('user')->whereDate('tanggal', today())->latest('id_absensi')->limit(10)->get(),
                'cutiPending' => Cuti::with('user')->where('status', 'pending')->latest('id_cuti')->limit(10)->get(),
                'tugasPending' => Tugas::with('user')->where('status', 'pending')->latest('id_tugas')->limit(10)->get(),
                'absensiBulanIni' => Absensi::whereBetween('tanggal', [
                    $calendar['currentMonth']->copy()->startOfMonth()->toDateString(),
                    $calendar['currentMonth']->copy()->endOfMonth()->toDateString(),
                ])->count(),
                'currentMonth' => $calendar['currentMonth'],
                'previousMonth' => $calendar['previousMonth'],
                'nextMonth' => $calendar['nextMonth'],
                'days' => $calendar['days'],
                'events' => $calendar['events'],
                'absensiByDate' => $absensiByDate,
                'tugasByDate' => $tugasByDate,
                'aktivitasTerbaru' => $aktivitasPetugas,
            ]);
        }

        $calendar = $this->dashboardCalendar($request);
        $cutiTerpakaiTahunIni = Cuti::where('id_user', $user->id_user)
            ->whereYear('tanggal_mulai', now()->year)
            ->whereIn('status', ['pending', 'approve'])
            ->count();
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

        return view('petugas.dashboard', [
            'user' => $user,
            'absensiHariIni' => Absensi::where('id_user', $user->id_user)->whereDate('tanggal', today())->first(),
            'cutiTerakhir' => Cuti::where('id_user', $user->id_user)->latest('id_cuti')->limit(5)->get(),
            'tugasTerakhir' => Tugas::where('id_user', $user->id_user)->latest('id_tugas')->limit(5)->get(),
            'notifikasiBelumBaca' => Notifikasi::where('id_user', $user->id_user)->where('status_baca', false)->count(),
            'kalender' => Kalender::whereDate('tanggal', '>=', today())->orderBy('tanggal')->limit(5)->get(),
            'cutiTerpakaiTahunIni' => $cutiTerpakaiTahunIni,
            'sisaCutiTahunIni' => max(12 - $cutiTerpakaiTahunIni, 0),
            'aktivitasTerbaru' => ActivityLog::where('id_user', $user->id_user)
                ->whereIn('modul', ['absensi', 'cuti', 'tugas'])
                ->latest('id_log')
                ->limit(8)
                ->get(),
            'currentMonth' => $calendar['currentMonth'],
            'previousMonth' => $calendar['previousMonth'],
            'nextMonth' => $calendar['nextMonth'],
            'days' => $calendar['days'],
            'events' => $calendar['events'],
            'tugasByDate' => $this->spreadTugasByDate($userTugas),
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
