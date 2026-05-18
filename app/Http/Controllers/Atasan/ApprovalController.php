<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Notifikasi;
use App\Models\Periode;
use App\Models\Tugas;
use App\Support\ActivityLogger;
use App\Support\QueryFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function absensi(Request $request): View
    {
        $items = Absensi::with(['user.tempatTugas', 'periode']);

        if ($request->filled('month')) {
            $items->whereMonth('tanggal', $request->month);
        }

        if ($request->filled('id_user')) {
            $items->where('id_user', $request->id_user);
        }

        if ($request->filled('status')) {
            $items->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $items->where(function($q) use ($search) {
                $q->whereHas('user', function($qu) use ($search) {
                    QueryFilters::whereLike($qu, 'nama', $search);
                });
                QueryFilters::orWhereLike($q, 'lokasi_masuk', $search);
                QueryFilters::orWhereLike($q, 'keterangan', $search);
            });
        }

        if (!$request->filled('month') && !$request->filled('id_user') && !$request->filled('status') && !$request->filled('search')) {
            $periodes = Periode::orderByDesc('tanggal_mulai')->get();
            $selectedPeriode = $periodes->firstWhere('id_periode', (int) $request->query('id_periode'));
            if ($selectedPeriode) {
                $items->where(function ($query) use ($selectedPeriode) {
                    $query->where('id_periode', $selectedPeriode->id_periode)
                        ->orWhereBetween('tanggal', [
                            $selectedPeriode->tanggal_mulai->toDateString(),
                            $selectedPeriode->tanggal_selesai->toDateString(),
                        ]);
                });
            }
        } else {
            $periodes = Periode::orderByDesc('tanggal_mulai')->get();
            $selectedPeriode = null;
        }

        return view('atasan.absensi', [
            'items' => $items->latest('tanggal')->latest('id_absensi')->paginate($request->get("per_page", 25))->withQueryString(),
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
            'users' => \App\Models\User::orderBy('nama')->get(),
        ]);
    }

    public function printAbsensi(Request $request): View
    {
        $items = Absensi::with(['user.tempatTugas', 'periode']);

        if ($request->filled('month')) {
            $items->whereMonth('tanggal', $request->month);
        }

        if ($request->filled('id_user')) {
            $items->where('id_user', $request->id_user);
        }

        if ($request->filled('status')) {
            $items->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $items->where(function($q) use ($search) {
                $q->whereHas('user', function($qu) use ($search) {
                    QueryFilters::whereLike($qu, 'nama', $search);
                });
                QueryFilters::orWhereLike($q, 'lokasi_masuk', $search);
                QueryFilters::orWhereLike($q, 'keterangan', $search);
            });
        }

        $items = $items->latest('tanggal')->get();

        return view('atasan.absensi_print', [
            'items' => $items,
            'month' => $request->month,
            'selectedUser' => $request->id_user ? \App\Models\User::find($request->id_user) : null,
        ]);
    }

    public function cuti(Request $request): View
    {
        $periodes = Periode::orderByDesc('tanggal_mulai')->get();
        $selectedPeriode = $periodes->firstWhere('id_periode', (int) $request->query('id_periode'));
        $items = Cuti::with(['user', 'approver', 'periode', 'pengganti']);

        if ($selectedPeriode) {
            $items->where(function ($query) use ($selectedPeriode) {
                $query->where('id_periode', $selectedPeriode->id_periode)
                    ->orWhereBetween('tanggal_mulai', [
                        $selectedPeriode->tanggal_mulai->toDateString(),
                        $selectedPeriode->tanggal_selesai->toDateString(),
                    ]);
            });
        }

        return view('atasan.cuti', [
            'items' => $items->latest('id_cuti')->paginate($request->get("per_page", 25))->withQueryString(),
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
        ]);
    }

    public function tugas(Request $request): View
    {
        $periodes = Periode::orderByDesc('tanggal_mulai')->get();
        $selectedPeriode = $periodes->firstWhere('id_periode', (int) $request->query('id_periode'));
        $items = Tugas::with(['user', 'periode']);

        if ($selectedPeriode) {
            $items->where(function ($query) use ($selectedPeriode) {
                $query->where('id_periode', $selectedPeriode->id_periode)
                    ->orWhereBetween('tanggal_mulai', [
                        $selectedPeriode->tanggal_mulai->toDateString(),
                        $selectedPeriode->tanggal_selesai->toDateString(),
                    ]);
            });
        }

        return view('atasan.tugas', [
            'items' => $items->latest('id_tugas')->paginate($request->get("per_page", 25))->withQueryString(),
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
        ]);
    }

    public function approveCuti(Request $request, int $id): RedirectResponse
    {
        return $this->updateCuti($request, $id, 'approve');
    }

    public function rejectCuti(Request $request, int $id): RedirectResponse
    {
        return $this->updateCuti($request, $id, 'reject');
    }

    public function approveTugas(Request $request, int $id): RedirectResponse
    {
        return $this->updateTugas($request, $id, 'approve');
    }

    public function rejectTugas(Request $request, int $id): RedirectResponse
    {
        return $this->updateTugas($request, $id, 'reject');
    }

    private function updateCuti(Request $request, int $id, string $status): RedirectResponse
    {
        $cuti = Cuti::with('user')->findOrFail($id);
        
        // Authorization: Verify the user is petugas (atasan should only approve petugas cuti)
        if (!$cuti->user->isPetugas()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah cuti ini.');
        }
        
        // Prevent re-approval of already processed cuti
        if ($cuti->status !== 'pending') {
            return back()->with('error', 'Cuti ini sudah diproses sebelumnya.');
        }
        
        $cuti->update([
            'status' => $status,
            'approver_id' => $request->user()->id_user,
        ]);

        Notifikasi::create([
            'id_user' => $cuti->id_user,
            'judul' => 'Status cuti diperbarui',
            'pesan' => 'Pengajuan cuti kamu ' . ($status === 'approve' ? 'disetujui.' : 'ditolak.'),
            'tipe' => 'cuti',
            'status_baca' => false,
            'reference_id' => $cuti->id_cuti,
            'reference_type' => Cuti::class,
        ]);

        ActivityLogger::log($request, ucfirst($status) . ' cuti', 'cuti', $cuti->id_cuti, Cuti::class);

        return back()->with('success', 'Status cuti berhasil diperbarui.');
    }

    private function updateTugas(Request $request, int $id, string $status): RedirectResponse
    {
        $tugas = Tugas::with('user')->findOrFail($id);
        
        // Authorization: Verify the user is petugas (atasan should only approve petugas tugas)
        if (!$tugas->user->isPetugas()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah tugas ini.');
        }
        
        // Prevent re-approval of already processed tugas
        if ($tugas->status !== 'pending') {
            return back()->with('error', 'Tugas ini sudah diproses sebelumnya.');
        }
        
        $tugas->update(['status' => $status]);

        Notifikasi::create([
            'id_user' => $tugas->id_user,
            'judul' => 'Status tugas diperbarui',
            'pesan' => 'Laporan tugas kamu ' . ($status === 'approve' ? 'disetujui.' : 'ditolak.'),
            'tipe' => 'tugas',
            'status_baca' => false,
            'reference_id' => $tugas->id_tugas,
            'reference_type' => Tugas::class,
        ]);

        ActivityLogger::log($request, ucfirst($status) . ' tugas', 'tugas', $tugas->id_tugas, Tugas::class);

        return back()->with('success', 'Status tugas berhasil diperbarui.');
    }
    
    public function kalender(Request $request): View
    {
        $user = $request->user();
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        if ($month < 1 || $month > 12) $month = now()->month;
        if ($year < 2000 || $year > 2100) $year = now()->year;

        $currentMonth = \Carbon\Carbon::create($year, $month, 1)->startOfDay();
        $calendarStart = $currentMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $calendarEnd = $currentMonth->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);

        $events = \App\Models\Kalender::whereBetween('tanggal', [
                $calendarStart->toDateString(),
                $calendarEnd->toDateString(),
            ])
            ->orderBy('tanggal')
            ->get()
            ->groupBy(fn ($item) => $item->tanggal->format('Y-m-d'));

        $absensiByDate = Absensi::whereBetween('tanggal', [
                $calendarStart->toDateString(),
                $calendarEnd->toDateString(),
            ])
            ->whereHas('user', function ($query) use ($user) {
                $query->whereHas('role', function ($roleQuery) {
                    QueryFilters::whereRoleAlias($roleQuery, ['petugas', 'karyawan']);
                });

                if ($user->id_tempat) {
                    $query->where('id_tempat', $user->id_tempat);
                }
            })
            ->get()
            ->groupBy(fn ($item) => $item->tanggal->format('Y-m-d'))
            ->map(fn ($items) => $items->count());

        $allTugas = Tugas::with('user')
            ->whereHas('user', function ($query) use ($user) {
                $query->whereHas('role', function ($roleQuery) {
                    QueryFilters::whereRoleAlias($roleQuery, ['petugas', 'karyawan']);
                });

                if ($user->id_tempat) {
                    $query->where('id_tempat', $user->id_tempat);
                }
            })
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

        $tugasByDate = [];
        foreach ($allTugas as $tugas) {
            $start = $tugas->tanggal_mulai->copy()->startOfDay();
            $end = $tugas->tanggal_selesai ? $tugas->tanggal_selesai->copy()->startOfDay() : $start->copy();
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $dateKey = $d->format('Y-m-d');
                $tugasByDate[$dateKey] = ($tugasByDate[$dateKey] ?? 0) + 1;
            }
        }

        $days = collect();
        for ($day = $calendarStart->copy(); $day->lte($calendarEnd); $day->addDay()) {
            $days->push($day->copy());
        }

        return view('atasan.kalender', [
            'currentMonth' => $currentMonth,
            'previousMonth' => $currentMonth->copy()->subMonth(),
            'nextMonth' => $currentMonth->copy()->addMonth(),
            'days' => $days,
            'events' => $events,
            'absensiByDate' => $absensiByDate,
            'tugasByDate' => collect($tugasByDate),
        ]);
    }
}
