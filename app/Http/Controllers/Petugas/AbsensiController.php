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
        $activePeriodeId = session('global_periode_id') ?? optional(Periode::aktif())->id_periode;
        $selectedPeriode = Periode::find($activePeriodeId);
        
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

        // Lazy evaluation for 'tidak_absen'
        $now = now();
        $user = $request->user();
        if ($now->format('H:i:s') > '07:15:00') {
            $existingToday = Absensi::where('id_user', $user->id_user)
                ->whereDate('tanggal', today())
                ->first();
            
            if (!$existingToday) {
                Absensi::create([
                    'id_user' => $user->id_user,
                    'id_periode' => optional(Periode::aktif())->id_periode,
                    'tanggal' => today()->toDateString(),
                    'status' => 'tidak_absen',
                    'keterangan' => 'Terlambat absen masuk (otomatis sistem)'
                ]);
            }
        }

        return view('petugas.absensi', [
            'today' => Absensi::where('id_user', $request->user()->id_user)->whereDate('tanggal', today())->first(),
            'items' => $items
                ->latest('tanggal')
                ->latest('id_absensi')
                ->paginate(15)
                ->withQueryString(),
            'periodeAktif' => Periode::aktif(),
            'tempatTugas' => $user->tempatTugas,
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) return null;
        $earthRadius = 6371000; // in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function masuk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'foto_masuk' => ['required', 'string'],
            'latitude_masuk' => ['nullable', 'numeric'],
            'longitude_masuk' => ['nullable', 'numeric'],
            'lokasi_masuk' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $existing = Absensi::where('id_user', $user->id_user)->whereDate('tanggal', today())->first();

        if ($existing && $existing->status !== 'akses_dibuka') {
            return back()->with('error', 'Kamu sudah absen masuk hari ini atau waktu absen telah berakhir.');
        }

        $now = now()->format('H:i:s');
        $isLateAccess = ($existing && $existing->status === 'akses_dibuka');
        
        if (!$isLateAccess && ($now < '07:00:00' || $now > '07:15:00')) {
            return back()->with('error', 'Absen masuk hanya dibuka dari jam 07:00 sampai 07:15.');
        }

        $tempat = $user->tempatTugas;
        if ($tempat && $tempat->latitude && $tempat->longitude) {
            $dist = $this->calculateDistance(
                $validated['latitude_masuk'] ?? 0,
                $validated['longitude_masuk'] ?? 0,
                $tempat->latitude,
                $tempat->longitude
            );
            if ($dist === null || $dist > 100) {
                return back()->with('error', 'Anda berada di luar area kantor.');
            }
        }

        $foto = null;
        if ($request->filled('foto_masuk')) {
            $imageParts = explode(";base64,", $request->input('foto_masuk'));
            if (count($imageParts) === 2) {
                $imageTypeAux = explode("image/", $imageParts[0]);
                $imageType = $imageTypeAux[1] ?? 'png';
                $imageBase64 = base64_decode($imageParts[1]);
                $fileName = 'absensi/' . uniqid() . '.' . $imageType;
                \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageBase64);
                $foto = $fileName;
            }
        }
        $status = $isLateAccess ? 'telat' : 'hadir';
        $keteranganOtomatis = $isLateAccess ? 'Hadir terlambat (Akses Telat Admin)' : 'Hadir tepat waktu';

        if ($existing && $existing->status === 'akses_dibuka') {
            $existing->update([
                'jam_masuk' => now()->format('H:i:s'),
                'foto_masuk' => $foto,
                'latitude_masuk' => $validated['latitude_masuk'] ?? null,
                'longitude_masuk' => $validated['longitude_masuk'] ?? null,
                'lokasi_masuk' => $validated['lokasi_masuk'] ?? null,
                'status' => $status,
                'keterangan' => $keteranganOtomatis,
            ]);
            $absensi = $existing;
        } else {
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
                'keterangan' => $keteranganOtomatis,
            ]);
        }

        ActivityLogger::log($request, 'Absen masuk', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', 'Absen masuk berhasil disimpan.');
    }

    public function pulang(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'foto_pulang' => ['required', 'string'],
            'latitude_pulang' => ['nullable', 'numeric'],
            'longitude_pulang' => ['nullable', 'numeric'],
            'lokasi_pulang' => ['nullable', 'string', 'max:255'],
        ]);

        $absensi = Absensi::where('id_user', $request->user()->id_user)->whereDate('tanggal', today())->first();

        if (! $absensi) {
            return back()->with('error', 'Absen masuk dulu sebelum absen pulang.');
        }

        $now = now()->format('H:i:s');
        if ($now < '16:00:00' || $now > '23:59:59') {
            return back()->with('error', 'Absen pulang hanya dibuka dari jam 16:00 sampai 23:59.');
        }

        $user = $request->user();
        $tempat = $user->tempatTugas;
        if ($tempat && $tempat->latitude && $tempat->longitude) {
            $dist = $this->calculateDistance(
                $validated['latitude_pulang'] ?? 0,
                $validated['longitude_pulang'] ?? 0,
                $tempat->latitude,
                $tempat->longitude
            );
            if ($dist === null || $dist > 100) {
                return back()->with('error', 'Anda berada di luar area kantor.');
            }
        }

        if ($absensi->jam_pulang) {
            return back()->with('error', 'Kamu sudah absen pulang hari ini.');
        }

        $fotoPath = $absensi->foto_pulang;
        if ($request->filled('foto_pulang')) {
            $imageParts = explode(";base64,", $request->input('foto_pulang'));
            if (count($imageParts) === 2) {
                $imageTypeAux = explode("image/", $imageParts[0]);
                $imageType = $imageTypeAux[1] ?? 'png';
                $imageBase64 = base64_decode($imageParts[1]);
                $fileName = 'absensi/' . uniqid() . '.' . $imageType;
                \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageBase64);
                $fotoPath = $fileName;
            }
        }

        $absensi->update([
            'jam_pulang' => now()->format('H:i:s'),
            'foto_pulang' => $fotoPath,
            'latitude_pulang' => $validated['latitude_pulang'] ?? null,
            'longitude_pulang' => $validated['longitude_pulang'] ?? null,
            'lokasi_pulang' => $validated['lokasi_pulang'] ?? null,
            'keterangan' => $absensi->keterangan ? $absensi->keterangan . ' | Selesai' : 'Selesai',
        ]);

        ActivityLogger::log($request, 'Absen pulang', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', 'Absen pulang berhasil disimpan.');
    }
}
