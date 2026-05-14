<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Periode;
use App\Services\AbsensiTidakAbsenService;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $items = Absensi::where('id_user', $user->id_user);

        if ($request->filled('month')) {
            $items->whereMonth('tanggal', $request->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $items->where(function($q) use ($search) {
                $q->where('status', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('lokasi_masuk', 'like', "%{$search}%");
            });
        }

        if (!$request->filled('month') && !$request->filled('search')) {
            $activePeriodeId = session('global_periode_id') ?? optional(Periode::aktif())->id_periode;
            $selectedPeriode = Periode::find($activePeriodeId);
            if ($selectedPeriode) {
                $items->where(function ($query) use ($selectedPeriode) {
                    $query->where('id_periode', $selectedPeriode->id_periode)
                        ->orWhereBetween('tanggal', [
                            $selectedPeriode->tanggal_mulai->toDateString(),
                            $selectedPeriode->tanggal_selesai->toDateString(),
                        ]);
                });
            }
        }

        app(AbsensiTidakAbsenService::class)->backfillForUserUntilYesterday($user);

        return view('petugas.absensi', [
            'today' => Absensi::where('id_user', $user->id_user)->whereDate('tanggal', today())->first(),
            'items' => $items
                ->latest('tanggal')
                ->latest('id_absensi')
                ->paginate($request->get("per_page", 15))
                ->withQueryString(),
            'periodeAktif' => Periode::aktif(),
            'tempatTugas' => $user->tempatTugas,
        ]);
    }

    public function print(Request $request): View
    {
        $user = $request->user();
        $items = Absensi::where('id_user', $user->id_user);

        if ($request->filled('month')) {
            $items->whereMonth('tanggal', $request->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $items->where(function($q) use ($search) {
                $q->where('status', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('lokasi_masuk', 'like', "%{$search}%");
            });
        }

        $items = $items->latest('tanggal')->get();

        return view('petugas.absensi_print', [
            'items' => $items,
            'user' => $user,
            'month' => $request->month,
        ]);
    }

    public function show($id): View
    {
        $item = Absensi::with('user.tempatTugas')->findOrFail($id);
        
        $user = auth()->user();
        // Authorization: petugas only see their own, atasan/admin see all
        if ($user->isPetugas() && $item->id_user !== $user->id_user) {
            abort(403);
        }

        return view('petugas.absensi-detail', compact('item'));
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

    private function processBase64Image(string $base64String, string $folder): ?string
    {
        // Validate base64 format
        if (!preg_match('/^data:image\\/(jpeg|jpg|png|webp);base64,/', $base64String, $matches)) {
            return null;
        }

        $allowedTypes = ['jpeg', 'jpg', 'png', 'webp'];
        $imageType = $matches[1];

        if (!in_array($imageType, $allowedTypes)) {
            return null;
        }

        // Extract base64 data
        $imageParts = explode(';base64,', $base64String);
        if (count($imageParts) !== 2) {
            return null;
        }

        $imageBase64 = base64_decode($imageParts[1], true);
        if ($imageBase64 === false) {
            return null;
        }

        // Validate file size (max 5MB)
        if (strlen($imageBase64) > 5 * 1024 * 1024) {
            return null;
        }

        // Verify it is actually an image using getimagesizefromstring
        $imageInfo = @getimagesizefromstring($imageBase64);
        if ($imageInfo === false) {
            return null;
        }

        // Verify MIME type matches
        $mimeType = $imageInfo['mime'];
        $validMimes = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($validMimes[$mimeType])) {
            return null;
        }

        // Use extension from actual MIME type, not from user input
        $extension = $validMimes[$mimeType];
        $fileName = $folder . '/' . uniqid() . '_' . time() . '.' . $extension;

        \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageBase64);

        return $fileName;
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
        $now = now()->format('H:i:s');
        $jamMasukBuka = config('absensi.jam_masuk_buka', '06:00:00');
        $jamMasukTutup = config('absensi.jam_masuk_tutup', '07:15:00');

        $existing = Absensi::where('id_user', $user->id_user)->whereDate('tanggal', today())->first();
        $isLateAccess = $existing && $existing->status === 'akses_dibuka';

        if (!$isLateAccess && ($now < $jamMasukBuka || $now > $jamMasukTutup)) {
            return back()->with('error', "Absen masuk hanya dibuka dari jam " . substr($jamMasukBuka, 0, 5) . " sampai " . substr($jamMasukTutup, 0, 5) . ".");
        }

        if ($existing && $existing->jam_masuk && $existing->status !== 'akses_dibuka') {
            return back()->with('error', 'Kamu sudah absen masuk hari ini.');
        }

        $tempat = $user->tempatTugas;
        if ($tempat && $tempat->latitude && $tempat->longitude) {
            $dist = $this->calculateDistance(
                $validated['latitude_masuk'] ?? 0,
                $validated['longitude_masuk'] ?? 0,
                $tempat->latitude,
                $tempat->longitude
            );
            $jarakMaks = config('absensi.jarak_maks_meter', 100);
            if ($dist === null || $dist > $jarakMaks) {
                return back()->with('error', 'Anda berada di luar area kantor.');
            }
        }

        $foto = null;
        if ($request->filled('foto_masuk')) {
            $foto = $this->processBase64Image($request->input('foto_masuk'), 'absensi');
            if (!$foto) {
                return back()->with('error', 'Format foto tidak valid. Hanya menerima JPEG, PNG, atau WebP.');
            }
        }

        // Tentukan status berdasarkan waktu absen masuk
        $jamBatasTelat = config('absensi.jam_masuk_batas_telat', '07:00:00');
        $isTelat = $now > $jamBatasTelat;
        
        if ($isLateAccess) {
            $status = 'telat';
            $keteranganOtomatis = 'Hadir terlambat (Akses Telat Admin)';
        } elseif ($isTelat) {
            $status = 'telat';
            $keteranganOtomatis = 'Hadir terlambat';
        } else {
            $status = 'hadir';
            $keteranganOtomatis = 'Hadir tepat waktu';
        }

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
        $jamPulangBuka = config('absensi.jam_pulang_buka', '16:00:00');
        $jamPulangTutup = config('absensi.jam_pulang_tutup', '23:59:59');
        if ($now < $jamPulangBuka || $now > $jamPulangTutup) {
            return back()->with('error', "Absen pulang hanya dibuka dari jam " . substr($jamPulangBuka, 0, 5) . " sampai " . substr($jamPulangTutup, 0, 5) . ".");
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
            $jarakMaks = config('absensi.jarak_maks_meter', 100);
            if ($dist === null || $dist > $jarakMaks) {
                return back()->with('error', 'Anda berada di luar area kantor.');
            }
        }

        if ($absensi->jam_pulang) {
            return back()->with('error', 'Kamu sudah absen pulang hari ini.');
        }

        $fotoPath = $absensi->foto_pulang;
        if ($request->filled('foto_pulang')) {
            $fotoPath = $this->processBase64Image($request->input('foto_pulang'), 'absensi');
            if (!$fotoPath) {
                return back()->with('error', 'Format foto tidak valid. Hanya menerima JPEG, PNG, atau WebP.');
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