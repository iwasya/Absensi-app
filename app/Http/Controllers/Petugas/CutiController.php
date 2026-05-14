<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Periode;
use App\Support\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CutiController extends Controller
{
    public function index(Request $request): View
    {
        $periodes = Periode::orderByDesc('tanggal_mulai')->get();
        $selectedPeriode = $periodes->firstWhere('id_periode', (int) $request->query('id_periode'));
        $items = Cuti::with('pengganti')->where('id_user', $request->user()->id_user);

        if ($selectedPeriode) {
            $items->where(function ($query) use ($selectedPeriode) {
                $query->where('id_periode', $selectedPeriode->id_periode)
                    ->orWhereBetween('tanggal_mulai', [
                        $selectedPeriode->tanggal_mulai->toDateString(),
                        $selectedPeriode->tanggal_selesai->toDateString(),
                    ]);
            });
        }

        $petugasRole = \App\Models\Role::where('nama_role', 'like', '%petugas%')->first();
        $petugasList = \App\Models\User::where('id_role', $petugasRole->id_role)
            ->where('id_user', '!=', $request->user()->id_user)
            ->orderBy('nama')
            ->get();

        return view('petugas.cuti', [
            'items' => $items->latest('id_cuti')->paginate($request->get("per_page", 15))->withQueryString(),
            'periodeAktif' => Periode::aktif(),
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
            'petugasList' => $petugasList,
            'cutiTerpakaiTahunIni' => $this->jumlahCutiTahunan($request->user()->id_user, now()->year),
            'batasCutiTahunan' => 12,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jenis_cuti' => ['required', 'in:Tahunan,Besar'],
            'alasan' => ['required', 'string'],
            'alasan_lainnya' => ['nullable', 'string'],
            'alamat_cuti' => ['required', 'string'],
            'id_pengganti' => ['required', 'exists:users,id_user'],
        ]);

        $tahunCuti = Carbon::parse($validated['tanggal_mulai'])->year;
        $jumlahCutiTahunan = $this->jumlahCutiTahunan($request->user()->id_user, $tahunCuti);

        if ($jumlahCutiTahunan >= 12) {
            return back()
                ->withInput()
                ->with('error', "Kuota cuti tahun {$tahunCuti} sudah habis. Maksimal 12 kali pengajuan cuti per tahun.");
        }

        $cuti = Cuti::create([
            'id_user' => $request->user()->id_user,
            'id_periode' => optional(Periode::aktif())->id_periode,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jenis_cuti' => $validated['jenis_cuti'],
            'alasan' => $validated['alasan'],
            'alasan_lainnya' => $validated['alasan_lainnya'] ?? null,
            'alamat_cuti' => $validated['alamat_cuti'],
            'id_pengganti' => $validated['id_pengganti'],
            'status' => 'pending',
        ]);

        $atasans = \App\Models\User::whereHas('role', function($q) {
            $q->where('nama_role', 'like', '%atasan%')
              ->orWhere('nama_role', 'like', '%manager%')
              ->orWhere('nama_role', 'like', '%menejer%');
        })->get();

        foreach ($atasans as $atasan) {
            \App\Models\Notifikasi::create([
                'id_user' => $atasan->id_user,
                'judul' => 'Pengajuan Cuti Baru',
                'pesan' => 'Petugas ' . $request->user()->nama . ' mengajukan cuti.',
                'tipe' => 'cuti',
                'status_baca' => false,
                'reference_id' => $cuti->id_cuti,
                'reference_type' => Cuti::class,
            ]);
        }

        ActivityLogger::log($request, 'Mengajukan cuti', 'cuti', $cuti->id_cuti, Cuti::class);

        return back()->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    public function print(int $id): View
    {
        $cuti = Cuti::with(['user.tempatTugas', 'pengganti', 'approver'])->findOrFail($id);
        $user = auth()->user();

        // Authorization: only owner, atasan, or admin can view the print
        if ($cuti->id_user !== $user->id_user && !$user->isAtasan() && !$user->isAdmin()) {
            abort(403);
        }

        return view('petugas.cuti_print', compact('cuti'));
    }

    private function jumlahCutiTahunan(int $userId, int $year): int
    {
        return Cuti::where('id_user', $userId)
            ->whereYear('tanggal_mulai', $year)
            ->whereIn('status', ['pending', 'approve'])
            ->count();
    }
}
