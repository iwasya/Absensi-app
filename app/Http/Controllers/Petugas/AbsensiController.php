<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Periode;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $periodes = Periode::orderByDesc('tanggal_mulai')->get();
        $selectedPeriode = $periodes->firstWhere('id_periode', (int) $request->query('id_periode'));
        $items = Absensi::where('id_user', $request->user()->id_user);

        if ($selectedPeriode) {
            $items->where(function ($query) use ($selectedPeriode) {
                $query->where('id_periode', $selectedPeriode->id_periode)
                    ->orWhereBetween('tanggal', [
                        $selectedPeriode->tanggal_mulai->toDateString(),
                        $selectedPeriode->tanggal_selesai->toDateString(),
                    ]);
            });
        }

        return view('petugas.absensi', [
            'today' => Absensi::where('id_user', $request->user()->id_user)->whereDate('tanggal', today())->first(),
            'items' => $items
                ->latest('tanggal')
                ->latest('id_absensi')
                ->paginate(15)
                ->withQueryString(),
            'periodeAktif' => Periode::aktif(),
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
        ]);
    }

    public function masuk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'foto_masuk' => ['nullable', 'image', 'max:2048'],
            'latitude_masuk' => ['nullable', 'numeric'],
            'longitude_masuk' => ['nullable', 'numeric'],
            'lokasi_masuk' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $existing = Absensi::where('id_user', $user->id_user)->whereDate('tanggal', today())->first();

        if ($existing) {
            return back()->with('error', 'Kamu sudah absen masuk hari ini.');
        }

        $foto = $request->file('foto_masuk')?->store('absensi', 'public');
        $status = now()->format('H:i:s') > '08:00:00' ? 'telat' : 'hadir';

        $absensi = Absensi::create([
            'id_user' => $user->id_user,
            'id_periode' => optional(Periode::aktif())->id_periode,
            'tanggal' => today()->toDateString(),
            'jam_masuk' => now()->format('H:i:s'),
            'foto_masuk' => $foto,
            'latitude_masuk' => $validated['latitude_masuk'] ?? null,
            'longitude_masuk' => $validated['longitude_masuk'] ?? null,
            'lokasi_masuk' => $validated['lokasi_masuk'] ?? null,
            'status' => $status,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        ActivityLogger::log($request, 'Absen masuk', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', 'Absen masuk berhasil disimpan.');
    }

    public function pulang(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'foto_pulang' => ['nullable', 'image', 'max:2048'],
            'latitude_pulang' => ['nullable', 'numeric'],
            'longitude_pulang' => ['nullable', 'numeric'],
            'lokasi_pulang' => ['nullable', 'string', 'max:255'],
        ]);

        $absensi = Absensi::where('id_user', $request->user()->id_user)->whereDate('tanggal', today())->first();

        if (! $absensi) {
            return back()->with('error', 'Absen masuk dulu sebelum absen pulang.');
        }

        if ($absensi->jam_pulang) {
            return back()->with('error', 'Kamu sudah absen pulang hari ini.');
        }

        $absensi->update([
            'jam_pulang' => now()->format('H:i:s'),
            'foto_pulang' => $request->file('foto_pulang')?->store('absensi', 'public'),
            'latitude_pulang' => $validated['latitude_pulang'] ?? null,
            'longitude_pulang' => $validated['longitude_pulang'] ?? null,
            'lokasi_pulang' => $validated['lokasi_pulang'] ?? null,
        ]);

        ActivityLogger::log($request, 'Absen pulang', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', 'Absen pulang berhasil disimpan.');
    }
}
