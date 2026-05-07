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
        $periodes = Periode::orderByDesc('tanggal_mulai')->get();
        $selectedPeriode = $periodes->firstWhere('id_periode', (int) $request->query('id_periode'));
        $items = Tugas::where('id_user', $request->user()->id_user);

        if ($selectedPeriode) {
            $items->where(function ($query) use ($selectedPeriode) {
                $query->where('id_periode', $selectedPeriode->id_periode)
                    ->orWhereBetween('tanggal_mulai', [
                        $selectedPeriode->tanggal_mulai->startOfDay(),
                        $selectedPeriode->tanggal_selesai->endOfDay(),
                    ]);
            });
        }

        return view('petugas.tugas-laporan', [
            'items' => $items->latest('id_tugas')->paginate(15)->withQueryString(),
            'periodeAktif' => Periode::aktif(),
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
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

        $days = collect();
        for ($day = $calendarStart->copy(); $day->lte($calendarEnd); $day->addDay()) {
            $days->push($day->copy());
        }

        return view('petugas.kalender', [
            'todayItems' => Kalender::whereDate('tanggal', today())->orderBy('id_kalender')->get(),
            'currentMonth' => $currentMonth,
            'previousMonth' => $currentMonth->copy()->subMonth(),
            'nextMonth' => $currentMonth->copy()->addMonth(),
            'days' => $days,
            'events' => $events,
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
