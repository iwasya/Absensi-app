<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Kalender;
use App\Models\Periode;
use App\Models\Shift;
use App\Models\Tugas;
use App\Models\User;
use App\Support\ActivityLogger;
use App\Support\QueryFilters;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Mengelola laporan tugas harian petugas: input laporan,
 * riwayat laporan, kalender tugas, cetak laporan, dan penanda input telat.
 */
class TugasController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('petugas.tugas.input');
    }

    public function input(Request $request): View
    {
        $user = $request->user();
        $assignedShift = $this->shiftForUser($user);

        $defaultAbsensi = Absensi::where('id_user', $user->id_user)
            ->whereDate('tanggal', today())
            ->whereNotNull('jam_masuk')
            ->first();

        if (! $defaultAbsensi) {
            $defaultAbsensi = Absensi::where('id_user', $user->id_user)
                ->whereNotNull('jam_masuk')
                ->latest('tanggal')
                ->latest('id_absensi')
                ->first();
        }

        $defaultDate = $defaultAbsensi?->tanggal ?? today();
        $shiftBounds = $assignedShift ? $this->shiftBounds($assignedShift, $defaultDate) : null;

        if ($defaultAbsensi?->jam_masuk) {
            $defaultTanggalMulai = $defaultDate->copy()->setTimeFromTimeString($defaultAbsensi->jam_masuk);
        } elseif ($shiftBounds) {
            $defaultTanggalMulai = $shiftBounds['mulai'];
        } else {
            $defaultTanggalMulai = now();
        }

        if ($defaultAbsensi?->jam_pulang) {
            $defaultTanggalSelesai = $defaultDate->copy()->setTimeFromTimeString($defaultAbsensi->jam_pulang);
        } elseif (! $defaultAbsensi && $shiftBounds) {
            $defaultTanggalSelesai = $shiftBounds['selesai'];
        } else {
            $defaultTanggalSelesai = now();
        }

        $todayAbsensi = Absensi::where('id_user', $user->id_user)
            ->whereDate('tanggal', today())
            ->first();

        return view('petugas.tugas-input', [
            'periodeAktif' => Periode::aktif(),
            'jadwalHariIni' => Kalender::whereDate('tanggal', today())->orderBy('id_kalender')->get(),
            'todayAbsensi' => $todayAbsensi,
            'defaultAbsensi' => $defaultAbsensi,
            'assignedShift' => $assignedShift,
            'shiftBounds' => $shiftBounds,
            'defaultTanggalMulai' => $defaultTanggalMulai->format('Y-m-d\TH:i'),
            'defaultTanggalSelesai' => $defaultTanggalSelesai->format('Y-m-d\TH:i'),
        ]);
    }

    public function laporan(Request $request): View
    {
        $user = $request->user();
        $items = Tugas::where('id_user', $user->id_user);

        if ($request->filled('month')) {
            $items->whereMonth('tanggal_mulai', $request->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $items->where(function($q) use ($search) {
                QueryFilters::whereAnyLike($q, ['uraian', 'status'], $search);
            });
        }

        if (!$request->filled('month') && !$request->filled('search')) {
            $activePeriodeId = session('global_periode_id') ?? optional(Periode::aktif())->id_periode;
            $selectedPeriode = Periode::find($activePeriodeId);
            if ($selectedPeriode) {
                $items->where(function ($query) use ($selectedPeriode) {
                    $query->where('id_periode', $selectedPeriode->id_periode)
                        ->orWhereBetween('tanggal_mulai', [
                            $selectedPeriode->tanggal_mulai->startOfDay(),
                            $selectedPeriode->tanggal_selesai->endOfDay(),
                        ]);
                });
            }
        }

        return view('petugas.tugas-laporan', [
            'items' => $items->latest('id_tugas')->paginate($request->get("per_page", 15))->withQueryString(),
            'periodeAktif' => Periode::aktif(),
        ]);
    }

    public function printLaporan(Request $request): View
    {
        $user = $request->user();
        $items = Tugas::where('id_user', $user->id_user);

        if ($request->filled('month')) {
            $items->whereMonth('tanggal_mulai', $request->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $items->where(function($q) use ($search) {
                QueryFilters::whereAnyLike($q, ['uraian', 'status'], $search);
            });
        }

        return view('petugas.tugas-laporan-print', [
            'items' => $items->latest('tanggal_mulai')->get(),
            'user' => $user,
            'month' => $request->month,
        ]);
    }

    public function kalender(Request $request): View
    {
        $user = $request->user();
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

        $userTugas = Tugas::where('id_user', $request->user()->id_user)
            ->where(function($query) use ($calendarStart, $calendarEnd) {
                $query->whereBetween('tanggal_mulai', [$calendarStart->copy()->startOfDay(), $calendarEnd->copy()->endOfDay()])
                      ->orWhereBetween('tanggal_selesai', [$calendarStart->copy()->startOfDay(), $calendarEnd->copy()->endOfDay()])
                      ->orWhere(function($q) use ($calendarStart, $calendarEnd) {
                          $q->where('tanggal_mulai', '<=', $calendarStart->copy()->startOfDay())
                            ->whereNotNull('tanggal_selesai')
                            ->where('tanggal_selesai', '>=', $calendarEnd->copy()->endOfDay());
                      });
            })
            ->get();

        $absensiUser = Absensi::where('id_user', $user->id_user)
            ->whereBetween('tanggal', [
                $currentMonth->copy()->startOfMonth()->toDateString(),
                $currentMonth->copy()->endOfMonth()->toDateString(),
            ])
            ->get();

        $kalenderHadir = $absensiUser->where('status', 'hadir')->pluck('tanggal')->map(fn($date) => (int) $date->format('d'))->values()->all();
        $kalenderTelat = $absensiUser->where('status', 'telat')->pluck('tanggal')->map(fn($date) => (int) $date->format('d'))->values()->all();
        $kalenderAbsen = $absensiUser->whereIn('status', ['tidak_hadir', 'tidak_absen'])->pluck('tanggal')->map(fn($date) => (int) $date->format('d'))->values()->all();
        $kalenderCuti = $absensiUser->where('status', 'cuti')->pluck('tanggal')->map(fn($date) => (int) $date->format('d'))->values()->all();

        $absensiCalendarDetails = $absensiUser
            ->groupBy(fn (Absensi $item) => $item->tanggal->format('Y-m-d'))
            ->map(fn ($items) => $items->map(fn (Absensi $item) => [
                'nama' => ucfirst(str_replace('_', ' ', $item->status)),
                'status' => $item->keterangan ?: 'Absensi',
                'waktu' => 'Masuk ' . ($item->jam_masuk ?? '--:--') . ' - Pulang ' . ($item->jam_pulang ?? '--:--'),
            ])->values());

        $tugasByDate = [];
        $todayTugas = collect();
        $todayKey = today()->format('Y-m-d');

        foreach ($userTugas as $tugas) {
            $start = $tugas->tanggal_mulai->copy()->startOfDay();
            $end = $tugas->tanggal_selesai ? $tugas->tanggal_selesai->copy()->startOfDay() : $start->copy();
            
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $dateKey = $d->format('Y-m-d');
                if (!isset($tugasByDate[$dateKey])) {
                    $tugasByDate[$dateKey] = collect();
                }
                $tugasByDate[$dateKey]->push($tugas);
                
                if ($dateKey === $todayKey) {
                    $todayTugas->push($tugas);
                }
            }
        }

        $replacementCuti = Cuti::with('user')
            ->where('id_pengganti', $user->id_user)
            ->where('replacement_status', 'accepted')
            ->whereIn('status', ['approve', 'approved'])
            ->whereDate('tanggal_mulai', '<=', $calendarEnd->toDateString())
            ->whereDate('tanggal_selesai', '>=', $calendarStart->toDateString())
            ->get();

        $replacementCutiByDate = [];
        $todayReplacementCuti = collect();
        $replacementCalendarDetails = collect();

        foreach ($replacementCuti as $cuti) {
            $start = $cuti->tanggal_mulai->copy()->startOfDay();
            $end = $cuti->tanggal_selesai->copy()->startOfDay();

            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $dateKey = $d->format('Y-m-d');
                if (! isset($replacementCutiByDate[$dateKey])) {
                    $replacementCutiByDate[$dateKey] = collect();
                }

                $replacementCutiByDate[$dateKey]->push($cuti);

                if ($dateKey === $todayKey) {
                    $todayReplacementCuti->push($cuti);
                }

                $replacementCalendarDetails->put($dateKey, $replacementCalendarDetails->get($dateKey, collect())->push([
                    'nama' => 'Pengganti Cuti',
                    'status' => ($cuti->user->nama ?? '-') . ' - ' . $cuti->jenis_cuti,
                    'waktu' => 'Jadwal pengganti',
                ]));
            }
        }

        $days = collect();
        for ($day = $calendarStart->copy(); $day->lte($calendarEnd); $day->addDay()) {
            $days->push($day->copy());
        }

        $weeklyOffByDate = collect();
        $weeklyOffCalendarDetails = collect();
        if ($user->hari_libur !== null) {
            foreach ($days as $day) {
                if ((int) $user->hari_libur !== $day->dayOfWeek) {
                    continue;
                }

                $weeklyOffByDate->put($day->format('Y-m-d'), [
                    'title' => 'Libur Mingguan',
                    'description' => $user->hariLiburLabel() . ' - tidak wajib absen',
                ]);
                $weeklyOffCalendarDetails->put($day->format('Y-m-d'), collect([[
                    'nama' => 'Libur Mingguan',
                    'status' => $user->hariLiburLabel(),
                    'waktu' => 'Tidak wajib absen',
                ]]));
            }
        }

        $tugasCalendarDetails = collect($tugasByDate)
            ->map(fn ($items) => $items->map(fn (Tugas $item) => [
                'nama' => $item->uraian,
                'status' => ucfirst(str_replace('_', ' ', $item->status)),
                'waktu' => $item->tanggal_mulai?->format('H:i') ?? '--:--',
            ])->values());

        $eventCalendarDetails = $events
            ->map(fn ($items) => $items->map(fn (Kalender $item) => [
                'nama' => $item->nama_event,
                'status' => ucfirst(str_replace('_', ' ', $item->jenis_event)),
                'waktu' => 'Kalender',
            ])->values());

        $kalenderLiburMingguan = $weeklyOffByDate
            ->keys()
            ->map(fn (string $date) => Carbon::parse($date))
            ->filter(fn (Carbon $date) => $date->month === $currentMonth->month && $date->year === $currentMonth->year)
            ->map(fn (Carbon $date) => (int) $date->format('d'))
            ->values()
            ->all();

        return view('petugas.kalender', [
            'todayItems' => Kalender::whereDate('tanggal', today())->orderBy('id_kalender')->get(),
            'todayTugas' => $todayTugas,
            'todayReplacementCuti' => $todayReplacementCuti,
            'todayWeeklyOff' => $weeklyOffByDate->get(today()->format('Y-m-d')),
            'currentMonth' => $currentMonth,
            'previousMonth' => $currentMonth->copy()->subMonth(),
            'nextMonth' => $currentMonth->copy()->addMonth(),
            'days' => $days,
            'events' => $events,
            'tugasByDate' => $tugasByDate,
            'replacementCutiByDate' => $replacementCutiByDate,
            'weeklyOffByDate' => $weeklyOffByDate,
            'kalenderHadir' => $kalenderHadir,
            'kalenderTelat' => $kalenderTelat,
            'kalenderAbsen' => $kalenderAbsen,
            'kalenderCuti' => $kalenderCuti,
            'kalenderLiburMingguan' => $kalenderLiburMingguan,
            'absensiCalendarDetails' => $absensiCalendarDetails,
            'tugasCalendarDetails' => $tugasCalendarDetails,
            'eventCalendarDetails' => $eventCalendarDetails,
            'weeklyOffCalendarDetails' => $weeklyOffCalendarDetails,
            'replacementCalendarDetails' => $replacementCalendarDetails,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'uraian' => ['required', 'string'],
        ]);

        $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
        $tanggalSelesai = ! empty($validated['tanggal_selesai'])
            ? Carbon::parse($validated['tanggal_selesai'])
            : null;
        $submittedAt = now();
        $isLateInput = $tanggalMulai->toDateString() < $submittedAt->toDateString();
        $absensi = Absensi::where('id_user', $user->id_user)
            ->whereDate('tanggal', $tanggalMulai->toDateString())
            ->first();
        $assignedShift = $this->shiftForUser($user);
        $shiftBounds = $assignedShift ? $this->shiftBoundsForDateTime($assignedShift, $tanggalMulai) : null;

        if ($absensi?->jam_masuk) {
            $batasMulai = $absensi->tanggal->copy()->setTimeFromTimeString($absensi->jam_masuk);
            $batasSelesai = $absensi->jam_pulang
                ? $this->combineDateAndTime($absensi->tanggal, $absensi->jam_pulang, $batasMulai)
                : now();
        } elseif ($shiftBounds) {
            $batasMulai = $shiftBounds['mulai'];
            $batasSelesai = $shiftBounds['selesai'];
        } else {
            $batasMulai = null;
            $batasSelesai = null;
        }

        if ($batasMulai && $batasSelesai) {
            if ($tanggalMulai->lt($batasMulai)) {
                return back()->withInput()->with('error', 'Waktu mulai tugas tidak boleh sebelum jam masuk ' . ($absensi?->jam_masuk ? 'absensi' : 'shift') . '.');
            }

            if ($tanggalMulai->gt($batasSelesai) || ($tanggalSelesai && $tanggalSelesai->gt($batasSelesai))) {
                return back()->withInput()->with('error', 'Waktu tugas tidak boleh melewati jam pulang ' . ($absensi?->jam_masuk ? 'absensi' : 'shift') . '.');
            }
        }

        $tugas = Tugas::create([
            'id_user' => $user->id_user,
            'id_periode' => optional(Periode::aktif())->id_periode,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'uraian' => $validated['uraian'],
            'status' => 'pending',
            'submitted_at' => $submittedAt,
            'is_late_input' => $isLateInput,
        ]);

        ActivityLogger::log($request, $isLateInput ? 'Mengirim laporan tugas terlambat' : 'Mengirim laporan tugas', 'tugas', $tugas->id_tugas, Tugas::class);

        return back()->with('success', $isLateInput
            ? 'Laporan tugas berhasil dikirim dan ditandai telat input.'
            : 'Laporan tugas berhasil dikirim.');
    }

    private function shiftForUser(User $user): ?Shift
    {
        if (! $user->shift) {
            return null;
        }

        return Shift::aktif()->where('nama_shift', $user->shift)->first();
    }

    private function shiftBounds(Shift $shift, Carbon|string $date): array
    {
        $mulai = Carbon::parse($date)
            ->startOfDay()
            ->setTimeFromTimeString(Carbon::parse($shift->jam_masuk)->format('H:i:s'));

        $selesai = $this->combineDateAndTime($mulai, $shift->jam_pulang, $mulai);

        return [
            'mulai' => $mulai,
            'selesai' => $selesai,
        ];
    }

    private function shiftBoundsForDateTime(Shift $shift, Carbon $dateTime): array
    {
        $bounds = $this->shiftBounds($shift, $dateTime);

        if ($bounds['selesai']->isSameDay($bounds['mulai']) || $dateTime->gte($bounds['mulai'])) {
            return $bounds;
        }

        return $this->shiftBounds($shift, $dateTime->copy()->subDay());
    }

    private function combineDateAndTime(Carbon|string $date, Carbon|string $time, ?Carbon $minimum = null): Carbon
    {
        $dateTime = Carbon::parse($date)
            ->startOfDay()
            ->setTimeFromTimeString(Carbon::parse($time)->format('H:i:s'));

        if ($minimum && $dateTime->lt($minimum)) {
            $dateTime->addDay();
        }

        return $dateTime;
    }
}
