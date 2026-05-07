<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Notifikasi;
use App\Models\Periode;
use App\Models\Tugas;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function absensi(Request $request): View
    {
        $periodes = Periode::orderByDesc('tanggal_mulai')->get();
        $selectedPeriode = $periodes->firstWhere('id_periode', (int) $request->query('id_periode'));
        $items = Absensi::with(['user.tempatTugas', 'periode']);

        if ($selectedPeriode) {
            $items->where(function ($query) use ($selectedPeriode) {
                $query->where('id_periode', $selectedPeriode->id_periode)
                    ->orWhereBetween('tanggal', [
                        $selectedPeriode->tanggal_mulai->toDateString(),
                        $selectedPeriode->tanggal_selesai->toDateString(),
                    ]);
            });
        }

        return view('atasan.absensi', [
            'items' => $items->latest('tanggal')->latest('id_absensi')->paginate(25)->withQueryString(),
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
        ]);
    }

    public function cuti(Request $request): View
    {
        $periodes = Periode::orderByDesc('tanggal_mulai')->get();
        $selectedPeriode = $periodes->firstWhere('id_periode', (int) $request->query('id_periode'));
        $items = Cuti::with(['user', 'approver', 'periode']);

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
            'items' => $items->latest('id_cuti')->paginate(25)->withQueryString(),
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
                        $selectedPeriode->tanggal_mulai->startOfDay(),
                        $selectedPeriode->tanggal_selesai->endOfDay(),
                    ]);
            });
        }

        return view('atasan.tugas', [
            'items' => $items->latest('id_tugas')->paginate(25)->withQueryString(),
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
        $cuti = Cuti::findOrFail($id);
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
        $tugas = Tugas::findOrFail($id);
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
}
