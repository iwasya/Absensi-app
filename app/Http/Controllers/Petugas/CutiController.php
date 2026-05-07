<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Periode;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CutiController extends Controller
{
    public function index(Request $request): View
    {
        $periodes = Periode::orderByDesc('tanggal_mulai')->get();
        $selectedPeriode = $periodes->firstWhere('id_periode', (int) $request->query('id_periode'));
        $items = Cuti::where('id_user', $request->user()->id_user);

        if ($selectedPeriode) {
            $items->where(function ($query) use ($selectedPeriode) {
                $query->where('id_periode', $selectedPeriode->id_periode)
                    ->orWhereBetween('tanggal_mulai', [
                        $selectedPeriode->tanggal_mulai->toDateString(),
                        $selectedPeriode->tanggal_selesai->toDateString(),
                    ]);
            });
        }

        return view('petugas.cuti', [
            'items' => $items->latest('id_cuti')->paginate(15)->withQueryString(),
            'periodeAktif' => Periode::aktif(),
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jenis_cuti' => ['nullable', 'string', 'max:100'],
            'alasan' => ['nullable', 'string'],
        ]);

        $cuti = Cuti::create([
            'id_user' => $request->user()->id_user,
            'id_periode' => optional(Periode::aktif())->id_periode,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jenis_cuti' => $validated['jenis_cuti'] ?? null,
            'alasan' => $validated['alasan'] ?? null,
            'status' => 'pending',
        ]);

        ActivityLogger::log($request, 'Mengajukan cuti', 'cuti', $cuti->id_cuti, Cuti::class);

        return back()->with('success', 'Pengajuan cuti berhasil dikirim.');
    }
}
