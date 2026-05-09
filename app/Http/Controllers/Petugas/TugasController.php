<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Kalender;
use App\Models\Periode;
use App\Models\Tugas;
use App\Support\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TugasController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('petugas.tugas.input');
    }

    public function input(): View
    {
        return view('petugas.tugas-input', [
            'periodeAktif' => Periode::aktif(),
            'jadwalHariIni' => Kalender::whereDate('tanggal', today())->orderBy('id_kalender')->get(),
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
                $q->where('uraian', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
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
            'items' => $items->latest('id_tugas')->paginate(15)->withQueryString(),
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
                $q->where('uraian', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
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
                $query->whereBetween('tanggal_mulai', [$calendarStart->startOfDay(), $calendarEnd->endOfDay()])
                      ->orWhereBetween('tanggal_selesai', [$calendarStart->startOfDay(), $calendarEnd->endOfDay()])
                      ->orWhere(function($q) use ($calendarStart, $calendarEnd) {
                          $q->where('tanggal_mulai', '<=', $calendarStart->startOfDay())
                            ->whereNotNull('tanggal_selesai')
                            ->where('tanggal_selesai', '>=', $calendarEnd->endOfDay());
                      });
            })
            ->get();

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

        $days = collect();
        for ($day = $calendarStart->copy(); $day->lte($calendarEnd); $day->addDay()) {
            $days->push($day->copy());
        }

        return view('petugas.kalender', [
            'todayItems' => Kalender::whereDate('tanggal', today())->orderBy('id_kalender')->get(),
            'todayTugas' => $todayTugas,
            'currentMonth' => $currentMonth,
            'previousMonth' => $currentMonth->copy()->subMonth(),
            'nextMonth' => $currentMonth->copy()->addMonth(),
            'days' => $days,
            'events' => $events,
            'tugasByDate' => $tugasByDate,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'uraian' => ['required', 'string'],
        ]);

        $tugas = Tugas::create([
            'id_user' => $request->user()->id_user,
            'id_periode' => optional(Periode::aktif())->id_periode,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'uraian' => $validated['uraian'],
            'status' => 'pending',
        ]);

        ActivityLogger::log($request, 'Mengirim laporan tugas', 'tugas', $tugas->id_tugas, Tugas::class);

        return back()->with('success', 'Laporan tugas berhasil dikirim.');
    }
}
