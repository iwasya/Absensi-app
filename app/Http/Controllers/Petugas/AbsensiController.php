<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Notifikasi;
use App\Models\Periode;
use App\Models\Shift;
use App\Models\User;
use App\Services\AbsensiTidakAbsenService;
use App\Services\FaceVerificationService;
use App\Support\ActivityLogger;
use App\Support\ImageOptimizer;
use App\Support\QueryFilters;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Mengelola absensi petugas: absen masuk/pulang, validasi lokasi,
 * riwayat/print absensi, dan alur pengajuan absensi terlewat lewat ketua regu.
 */
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
                QueryFilters::whereAnyLike($q, ['status', 'keterangan', 'lokasi_masuk'], $search);
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

        $absensiTidakAbsen = app(AbsensiTidakAbsenService::class);
        $absensiTidakAbsen->backfillForUserUntilYesterday($user);
        $todayHolidayInfo = $absensiTidakAbsen->holidayInfo(today());
        $todayWeeklyOffInfo = $absensiTidakAbsen->weeklyOffInfo($user, today());
        if (! $todayHolidayInfo['is_holiday'] && $todayWeeklyOffInfo['is_holiday']) {
            $todayHolidayInfo = $todayWeeklyOffInfo + ['event' => null];
        }
        if (! $todayHolidayInfo['is_holiday']) {
            if ($absensiTidakAbsen->leaveInfo($user, today())['is_leave']) {
                $absensiTidakAbsen->generateForDate(today(), $user);
            }
        }

        $today = $this->activeAbsensiForUser($user, $request->integer('open_absensi') ?: null);
        $activeDate = $today?->tanggal ?? today();
        $shiftWindow = $this->shiftWindow($user, $activeDate);
        $holidayInfo = $absensiTidakAbsen->holidayInfo($activeDate);
        $weeklyOffInfo = $absensiTidakAbsen->weeklyOffInfo($user, $activeDate);
        if (! $holidayInfo['is_holiday'] && $weeklyOffInfo['is_holiday']) {
            $holidayInfo = $weeklyOffInfo + ['event' => null];
        }
        $leaveInfo = $absensiTidakAbsen->leaveInfo($user, $activeDate);

        return view('petugas.absensi', [
            'today' => $today,
            'items' => $items
                ->latest('tanggal')
                ->latest('id_absensi')
                ->paginate(5)
                ->withQueryString(),
            'periodeAktif' => Periode::aktif(),
            'tempatTugas' => $user->tempatTugas,
            'jamMasukBuka' => $shiftWindow['jam_masuk_buka'],
            'jamMasukTutup' => $shiftWindow['jam_masuk_tutup'],
            'jamPulangBuka' => $shiftWindow['jam_pulang_buka'],
            'jamPulangTutup' => $shiftWindow['jam_pulang_tutup'],
            'jamPulangBukaAt' => $shiftWindow['pulang_buka_at'],
            'jamPulangTutupAt' => $shiftWindow['pulang_tutup_at'],
            'isOvernightShift' => $shiftWindow['is_overnight'],
            'jarakMaksMeter' => config('absensi.jarak_maks_meter', 100),
            'holidayInfo' => $holidayInfo,
            'leaveInfo' => $leaveInfo,
            'shifts' => Shift::aktif()->orderBy('urutan')->orderBy('nama_shift')->get(),
            'hasAssignedShift' => (bool) $this->shiftForUser($user),
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
                QueryFilters::whereAnyLike($q, ['status', 'keterangan', 'lokasi_masuk'], $search);
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

    public function photo(Absensi $absensi, string $type)
    {
        $user = auth()->user();
        if ($user->isPetugas() && $absensi->id_user !== $user->id_user) {
            abort(403);
        }

        $field = match ($type) {
            'masuk' => 'foto_masuk',
            'pulang' => 'foto_pulang',
            default => null,
        };

        if (! $field || ! $absensi->{$field} || ! Storage::disk('public')->exists($absensi->{$field})) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($absensi->{$field}));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
            return null;
        }

        $earthRadius = 6371000; // in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function decodeBase64Image(string $base64String): ?string
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

        return $imageBase64;
    }

    private function storeDecodedImage(string $imageBinary, string $folder): ?string
    {
        return ImageOptimizer::storeBinary($imageBinary, $folder, 720, 720, 72);
    }

    private function validateFaceMatchesProfile(User $user, string $imageBinary, string $jenisAbsen): ?string
    {
        $faceVerification = app(FaceVerificationService::class);
        $result = $faceVerification->verify($user, $imageBinary);

        if ($result['status'] === 'mismatched') {
            $this->notifyFaceMismatch($user, $jenisAbsen, $result['confidence']);

            return 'Foto tidak sesuai dengan foto profil. Gunakan wajah sendiri untuk absen.';
        }

        if ($faceVerification->shouldFailClosed($result)) {
            return $result['reason'] ?: 'Verifikasi wajah belum bisa diproses. Coba lagi.';
        }

        return null;
    }

    private function notifyFaceMismatch(User $user, string $jenisAbsen, ?float $confidence = null): void
    {
        $confidenceText = $confidence !== null
            ? ' Skor kecocokan: ' . number_format($confidence * 100, 1) . '%.'
            : '';

        Notifikasi::create([
            'id_user' => $user->id_user,
            'judul' => 'Foto Absensi Tidak Sesuai',
            'pesan' => 'Foto absen ' . $jenisAbsen . ' tidak sesuai dengan foto profil kamu.' . $confidenceText,
            'tipe' => 'absensi',
            'status_baca' => false,
            'reference_id' => $user->id_user,
            'reference_type' => User::class,
        ]);

        Cache::forget("notifikasi:unread-count:{$user->id_user}");
        Cache::forget("notifikasi:header:{$user->id_user}");
    }

    private function validateAssignedArea($user, ?float $latitude, ?float $longitude, ?float $accuracy = null): ?string
    {
        $tempat = $user->tempatTugas;

        if (! $tempat || $tempat->latitude === null || $tempat->longitude === null) {
            return null;
        }

        if ($latitude === null || $longitude === null) {
            return 'Lokasi GPS belum terbaca. Izinkan akses lokasi lalu coba lagi.';
        }

        $maxAccuracy = max((float) config('absensi.jarak_maks_meter', 100), 100.0);
        if ($accuracy !== null && $accuracy > $maxAccuracy) {
            return 'Akurasi GPS masih terlalu rendah. Aktifkan lokasi presisi, tunggu beberapa saat, lalu coba lagi.';
        }

        $dist = $this->calculateDistance(
            $latitude,
            $longitude,
            (float) $tempat->latitude,
            (float) $tempat->longitude
        );

        $jarakMaks = config('absensi.jarak_maks_meter', 100);
        if ($dist === null || $dist > $jarakMaks) {
            $distanceText = $dist === null ? '' : ' Jarak terbaca sekitar ' . round($dist) . ' meter dari titik kantor.';

            return 'Anda berada di luar area kantor.' . $distanceText;
        }

        return null;
    }

    private function shiftForUser(User $user): ?Shift
    {
        if (! $user->shift) {
            return null;
        }

        return Shift::aktif()->where('nama_shift', $user->shift)->first();
    }

    private function shiftWindow(User $user, Carbon|string $date): array
    {
        $targetDate = $date instanceof Carbon
            ? $date->copy()->startOfDay()
            : Carbon::parse($date)->startOfDay();

        $shift = $this->shiftForUser($user);
        if (! $shift) {
            $masukBuka = $targetDate->copy()->setTimeFromTimeString(config('absensi.jam_masuk_buka', '07:50:00'));
            $masukTutup = $targetDate->copy()->setTimeFromTimeString(config('absensi.jam_masuk_tutup', '08:05:00'));
            $jamMasukBatasTelat = $targetDate->copy()->setTimeFromTimeString(config('absensi.jam_masuk_batas_telat', '07:50:00'));
            $pulangBuka = $masukBuka->copy()->addHours((int) config('absensi.durasi_kerja_default_jam', 8));
            $pulangTutup = $pulangBuka->copy()->setTimeFromTimeString(config('absensi.jam_pulang_tutup', '23:59:59'));
        } else {
            $masukBuka = $targetDate->copy()->setTimeFromTimeString(Carbon::parse($shift->jam_masuk)->format('H:i:s'));
            $masukTutup = $masukBuka->copy()->addMinutes(15);
            $jamMasukBatasTelat = $masukBuka;
            $pulangBuka = $masukBuka->copy()->addHours($shift->durasi_jam ?: 8);

            if ($pulangBuka->lessThanOrEqualTo($masukBuka)) {
                $pulangBuka->addDay();
            }

            $pulangTutup = $pulangBuka->copy()->setTimeFromTimeString(config('absensi.jam_pulang_tutup', '23:59:59'));
        }

        return [
            'jam_masuk_buka' => $masukBuka->format('H:i:s'),
            'jam_masuk_tutup' => $masukTutup->format('H:i:s'),
            'jam_masuk_batas_telat' => $jamMasukBatasTelat->format('H:i:s'),
            'jam_pulang_buka' => $pulangBuka->format('H:i:s'),
            'jam_pulang_tutup' => $pulangTutup->format('H:i:s'),
            'masuk_buka_at' => $masukBuka,
            'masuk_tutup_at' => $masukTutup,
            'masuk_batas_telat_at' => $jamMasukBatasTelat,
            'pulang_buka_at' => $pulangBuka,
            'pulang_tutup_at' => $pulangTutup,
            'is_overnight' => $pulangBuka->toDateString() !== $targetDate->toDateString(),
        ];
    }

    private function activeAbsensiForUser(User $user, ?int $preferredAbsensiId = null): ?Absensi
    {
        if ($preferredAbsensiId) {
            $preferredAbsensi = Absensi::where('id_user', $user->id_user)
                ->where('id_absensi', $preferredAbsensiId)
                ->first();

            if ($preferredAbsensi && $this->isFillableApprovedAbsensi($preferredAbsensi)) {
                return $preferredAbsensi;
            }
        }

        $todayAbsensi = Absensi::where('id_user', $user->id_user)
            ->where('tanggal', today()->toDateString())
            ->first();

        if ($todayAbsensi?->jam_masuk) {
            return $todayAbsensi;
        }

        $yesterday = today()->subDay();
        $yesterdayWindow = $this->shiftWindow($user, $yesterday);

        if ($yesterdayWindow['is_overnight'] && now()->lessThanOrEqualTo($yesterdayWindow['pulang_tutup_at'])) {
            $overnightAbsensi = Absensi::where('id_user', $user->id_user)
                ->where('tanggal', $yesterday->toDateString())
                ->whereNotNull('jam_masuk')
                ->whereNull('jam_pulang')
                ->first();

            if ($overnightAbsensi) {
                return $overnightAbsensi;
            }
        }

        if ($todayAbsensi?->status === 'akses_dibuka') {
            return $todayAbsensi;
        }

        $openedAbsensi = Absensi::where('id_user', $user->id_user)
            ->where(function ($query) {
                $query->where(function ($masukQuery) {
                    $masukQuery->where('status', 'akses_dibuka')
                        ->where('approval_masuk_status', 'approved')
                        ->whereNull('jam_masuk');
                })->orWhere(function ($pulangQuery) {
                    $pulangQuery->where('approval_pulang_status', 'approved')
                        ->whereNotNull('jam_masuk')
                        ->whereNull('jam_pulang');
                })->orWhere(function ($approvedMasukQuery) {
                    $approvedMasukQuery->where('approval_masuk_status', 'approved')
                        ->whereNotNull('jam_masuk')
                        ->whereNull('jam_pulang');
                });
            })
            ->latest('tanggal')
            ->first();

        if ($openedAbsensi) {
            return $openedAbsensi;
        }

        return $todayAbsensi;
    }

    private function isFillableApprovedAbsensi(Absensi $absensi): bool
    {
        if ($absensi->status === 'akses_dibuka'
            && $absensi->approval_masuk_status === 'approved'
            && ! $absensi->jam_masuk) {
            return true;
        }

        if ($absensi->approval_masuk_status === 'approved'
            && (bool) $absensi->jam_masuk
            && ! $absensi->jam_pulang) {
            return true;
        }

        return $absensi->approval_pulang_status === 'approved'
            && (bool) $absensi->jam_masuk
            && ! $absensi->jam_pulang;
    }

    public function masuk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'foto_masuk' => ['required', 'string'],
            'latitude_masuk' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude_masuk' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy_masuk' => ['nullable', 'numeric', 'min:0'],
            'lokasi_masuk' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'shift' => ['nullable', 'string', 'max:30'],
            'jam_istirahat_mulai' => ['nullable', 'date_format:H:i'],
            'jam_istirahat_selesai' => ['nullable', 'date_format:H:i', 'after:jam_istirahat_mulai'],
            'id_absensi' => ['nullable', 'exists:absensi,id_absensi'],
        ]);

        $user = $request->user();
        $assignedShift = $user->shift;
        $targetAbsensi = null;
        $targetDate = today();

        if (! empty($validated['id_absensi'])) {
            $targetAbsensi = Absensi::where('id_user', $user->id_user)
                ->where('id_absensi', $validated['id_absensi'])
                ->firstOrFail();
            $targetDate = $targetAbsensi->tanggal;
        }

        $existing = $targetAbsensi ?: Absensi::where('id_user', $user->id_user)->where('tanggal', $targetDate->toDateString())->first();
        $isLateAccess = $existing && $existing->status === 'akses_dibuka' && ! $existing->jam_masuk;

        $absensiTidakAbsen = app(AbsensiTidakAbsenService::class);
        $holidayInfo = $absensiTidakAbsen->holidayInfo($targetDate);
        $weeklyOffInfo = $absensiTidakAbsen->weeklyOffInfo($user, $targetDate);
        if (! $holidayInfo['is_holiday'] && $weeklyOffInfo['is_holiday']) {
            $holidayInfo = $weeklyOffInfo + ['event' => null];
        }
        if ($holidayInfo['is_holiday'] && ! $isLateAccess) {
            return back()->with('error', 'Tanggal ini libur (' . $holidayInfo['reason'] . '), absensi tidak dibuka.');
        }
        $leaveInfo = $absensiTidakAbsen->leaveInfo($user, $targetDate);
        if ($leaveInfo['is_leave'] && ! $isLateAccess) {
            $absensiTidakAbsen->generateForDate($targetDate, $user);

            return back()->with('error', 'Tanggal ini kamu sedang cuti (' . $leaveInfo['reason'] . '), absensi tidak dibuka.');
        }

        $now = now();
        $shiftWindow = $this->shiftWindow($user, $targetDate);

        if ($existing && $existing->jam_masuk) {
            return back()->with('error', 'Kamu sudah absen masuk hari ini.');
        }

        if ($now->lt($shiftWindow['masuk_buka_at']) && ! $isLateAccess) {
            return back()->with('error', 'Absen masuk belum dibuka untuk shift kamu.');
        }

        if ($now->gt($shiftWindow['masuk_tutup_at']) && ! $isLateAccess) {
            return back()->with('error', 'Absen masuk sudah terkunci karena melewati batas waktu. Hubungi admin untuk membuka akses absen telat.');
        }

        if (! empty($validated['jam_istirahat_mulai']) && ! empty($validated['jam_istirahat_selesai'])) {
            $mulaiIstirahat = \Carbon\Carbon::createFromFormat('H:i', $validated['jam_istirahat_mulai']);
            $selesaiIstirahat = \Carbon\Carbon::createFromFormat('H:i', $validated['jam_istirahat_selesai']);
            if ($mulaiIstirahat->diffInMinutes($selesaiIstirahat) < 60) {
                return back()->withInput()->with('error', 'Durasi istirahat minimal 1 jam.');
            }
        }

        $areaError = $this->validateAssignedArea(
            $user,
            isset($validated['latitude_masuk']) ? (float) $validated['latitude_masuk'] : null,
            isset($validated['longitude_masuk']) ? (float) $validated['longitude_masuk'] : null,
            isset($validated['accuracy_masuk']) ? (float) $validated['accuracy_masuk'] : null
        );
        if ($areaError) {
            return back()->with('error', $areaError);
        }

        $foto = null;
        if ($request->filled('foto_masuk')) {
            $fotoBinary = $this->decodeBase64Image($request->input('foto_masuk'));
            if (! $fotoBinary) {
                return back()->with('error', 'Format foto tidak valid. Hanya menerima JPEG, PNG, atau WebP.');
            }

            $faceError = $this->validateFaceMatchesProfile($user, $fotoBinary, 'masuk');
            if ($faceError) {
                return back()->with('error', $faceError);
            }

            $foto = $this->storeDecodedImage($fotoBinary, 'absensi');
            if (! $foto) {
                return back()->with('error', 'Foto gagal diproses.');
            }
        }

        // Tentukan status berdasarkan waktu absen masuk
        $isTelat = $now->gt($shiftWindow['masuk_batas_telat_at']);
        
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
        if (! empty($validated['keterangan'])) {
            $keteranganOtomatis .= ' | ' . $validated['keterangan'];
        }

        if ($existing && $existing->status === 'akses_dibuka' && ! $existing->jam_masuk) {
            $existing->update([
                'jam_masuk' => now()->format('H:i:s'),
                'shift' => $assignedShift,
                'jam_istirahat_mulai' => $validated['jam_istirahat_mulai'] ?? '12:00',
                'jam_istirahat_selesai' => $validated['jam_istirahat_selesai'] ?? '14:00',
                'foto_masuk' => $foto,
                'latitude_masuk' => $validated['latitude_masuk'] ?? null,
                'longitude_masuk' => $validated['longitude_masuk'] ?? null,
                'lokasi_masuk' => $validated['lokasi_masuk'] ?? null,
                'status' => $status,
                'keterangan' => $existing->status === 'tidak_absen'
                    ? $keteranganOtomatis . ' | Mengubah catatan tidak absen otomatis'
                    : $keteranganOtomatis,
            ]);
            $absensi = $existing;
        } else {
            $absensi = Absensi::create([
                'id_user' => $user->id_user,
                'id_periode' => optional(Periode::aktif())->id_periode,
                'tanggal' => $targetDate->toDateString(),
                'shift' => $assignedShift,
                'jam_masuk' => now()->format('H:i:s'),
                'jam_istirahat_mulai' => $validated['jam_istirahat_mulai'] ?? '12:00',
                'jam_istirahat_selesai' => $validated['jam_istirahat_selesai'] ?? '14:00',
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
            'latitude_pulang' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude_pulang' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy_pulang' => ['nullable', 'numeric', 'min:0'],
            'lokasi_pulang' => ['nullable', 'string', 'max:255'],
            'id_absensi' => ['nullable', 'exists:absensi,id_absensi'],
        ]);

        $user = $request->user();
        $absensiTidakAbsen = app(AbsensiTidakAbsenService::class);
        $targetAbsensi = null;
        $targetDate = today();
        if (! empty($validated['id_absensi'])) {
            $targetAbsensi = Absensi::where('id_user', $user->id_user)
                ->where('id_absensi', $validated['id_absensi'])
                ->firstOrFail();
            $targetDate = $targetAbsensi->tanggal;
        } else {
            $targetAbsensi = $this->activeAbsensiForUser($user);
            if ($targetAbsensi) {
                $targetDate = $targetAbsensi->tanggal;
            }
        }

        $absensi = $targetAbsensi ?? Absensi::where('id_user', $user->id_user)->where('tanggal', today()->toDateString())->first();
        $isApprovedForgottenCheckout = $absensi?->approval_pulang_status === 'approved' && ! $absensi?->jam_pulang;

        $holidayInfo = $absensiTidakAbsen->holidayInfo($targetDate);
        $weeklyOffInfo = $absensiTidakAbsen->weeklyOffInfo($user, $targetDate);
        if (! $holidayInfo['is_holiday'] && $weeklyOffInfo['is_holiday']) {
            $holidayInfo = $weeklyOffInfo + ['event' => null];
        }
        if ($holidayInfo['is_holiday'] && ! $isApprovedForgottenCheckout) {
            return back()->with('error', 'Hari ini libur (' . $holidayInfo['reason'] . '), absensi pulang tidak dibuka.');
        }
        $leaveInfo = $absensiTidakAbsen->leaveInfo($user, $targetDate);
        if ($leaveInfo['is_leave'] && ! $isApprovedForgottenCheckout) {
            $absensiTidakAbsen->generateForDate($targetDate, $user);

            return back()->with('error', 'Hari ini kamu sedang cuti (' . $leaveInfo['reason'] . '), absensi pulang tidak dibuka.');
        }

        if (! $absensi) {
            return back()->with('error', 'Absen masuk dulu sebelum absen pulang.');
        }

        if (! $absensi->jam_masuk) {
            return back()->with('error', 'Absen masuk dulu sebelum absen pulang.');
        }

        $now = now();
        $shiftWindow = $this->shiftWindow($user, $targetDate);
        if ($now->lt($shiftWindow['pulang_buka_at']) && ! $isApprovedForgottenCheckout) {
            return back()->with('error', 'Absen pulang belum dibuka. Jam pulang dibuka pukul ' . substr($shiftWindow['jam_pulang_buka'], 0, 5) . '.');
        }

        if ($now->gt($shiftWindow['pulang_tutup_at']) && ! $isApprovedForgottenCheckout) {
            return back()->with('error', 'Absen pulang sudah ditutup. Ajukan approval absen pulang terlewat.');
        }

        $areaError = $this->validateAssignedArea(
            $user,
            isset($validated['latitude_pulang']) ? (float) $validated['latitude_pulang'] : null,
            isset($validated['longitude_pulang']) ? (float) $validated['longitude_pulang'] : null,
            isset($validated['accuracy_pulang']) ? (float) $validated['accuracy_pulang'] : null
        );
        if ($areaError) {
            return back()->with('error', $areaError);
        }

        if ($absensi->jam_pulang) {
            return back()->with('error', 'Kamu sudah absen pulang hari ini.');
        }

        $fotoPath = $absensi->foto_pulang;
        if ($request->filled('foto_pulang')) {
            $fotoBinary = $this->decodeBase64Image($request->input('foto_pulang'));
            if (! $fotoBinary) {
                return back()->with('error', 'Format foto tidak valid. Hanya menerima JPEG, PNG, atau WebP.');
            }

            $faceError = $this->validateFaceMatchesProfile($user, $fotoBinary, 'pulang');
            if ($faceError) {
                return back()->with('error', $faceError);
            }

            $fotoPath = $this->storeDecodedImage($fotoBinary, 'absensi');
            if (! $fotoPath) {
                return back()->with('error', 'Foto gagal diproses.');
            }
        }

        $absensi->update([
            'jam_pulang' => now()->format('H:i:s'),
            'foto_pulang' => $fotoPath,
            'latitude_pulang' => $validated['latitude_pulang'] ?? null,
            'longitude_pulang' => $validated['longitude_pulang'] ?? null,
            'lokasi_pulang' => $validated['lokasi_pulang'] ?? null,
            'approval_pulang_status' => $isApprovedForgottenCheckout ? 'used' : $absensi->approval_pulang_status,
            'keterangan' => $absensi->keterangan ? $absensi->keterangan . ' | Selesai' : 'Selesai',
        ]);

        ActivityLogger::log($request, 'Absen pulang', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', 'Absen pulang berhasil disimpan.');
    }

    public function requestPulangApproval(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'approval_pulang_reason' => ['required', 'string', 'max:500'],
        ]);

        $absensi = Absensi::where('id_user', $request->user()->id_user)->findOrFail($id);

        if (! $absensi->jam_masuk || $absensi->jam_pulang) {
            return back()->with('error', 'Request hanya untuk absensi yang sudah masuk tetapi belum pulang.');
        }

        if (in_array($absensi->approval_pulang_status, ['pending_ketua', 'pending_atasan', 'approved'], true)) {
            return back()->with('error', 'Request lupa absen pulang untuk tanggal ini sudah diproses atau masih menunggu.');
        }

        $requester = $request->user();
        $directToAtasan = $requester->isKetuaRegu();

        $absensi->update([
            'approval_pulang_status' => $directToAtasan ? 'pending_atasan' : 'pending_ketua',
            'approval_pulang_requested_at' => now(),
            'approval_pulang_forwarded_by' => $directToAtasan ? $requester->id_user : null,
            'approval_pulang_forwarded_at' => $directToAtasan ? now() : null,
            'approval_pulang_reason' => $validated['approval_pulang_reason'],
        ]);

        $ketuaRegu = $directToAtasan
            ? collect()
            : User::where('is_ketua_regu', true)
                ->where('regu', $requester->regu)
                ->where('id_user', '!=', $requester->id_user)
                ->get();

        if ($ketuaRegu->isEmpty()) {
            $ketuaRegu = User::whereHas('role', function ($query) {
                QueryFilters::whereRoleAlias($query, ['atasan', 'manager', 'menejer']);
            })->get();

            if (! $directToAtasan) {
                $absensi->update([
                    'approval_pulang_status' => 'pending_atasan',
                    'approval_pulang_forwarded_at' => now(),
                ]);
            }
        }

        foreach ($ketuaRegu as $ketua) {
            Notifikasi::create([
                'id_user' => $ketua->id_user,
                'judul' => 'Request Lupa Absen Pulang',
                'pesan' => $requester->nama . ' dari ' . ($requester->regu ?: 'regu belum diisi') . ' meminta pembukaan absen pulang tanggal ' . $absensi->tanggal->format('d/m/Y') . '.',
                'tipe' => 'absensi',
                'status_baca' => false,
                'reference_id' => $absensi->id_absensi,
                'reference_type' => Absensi::class,
            ]);
        }

        ActivityLogger::log($request, 'Request lupa absen pulang', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', $directToAtasan
            ? 'Request dikirim langsung ke atasan untuk approval.'
            : 'Request dikirim ke ketua regu/atasan untuk approval.');
    }

    public function requestMasukApproval(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'approval_masuk_reason' => ['required', 'string', 'max:500'],
        ]);

        $absensi = Absensi::where('id_user', $request->user()->id_user)->findOrFail($id);

        if ($absensi->jam_masuk || ! $absensi->tanggal->lt(today())) {
            return back()->with('error', 'Pengajuan hanya untuk absen masuk yang terlewat pada tanggal sebelumnya.');
        }

        if ($absensi->status === 'cuti') {
            return back()->with('error', 'Tanggal ini tercatat cuti, pengajuan absen masuk tidak dibuka.');
        }

        $absensiTidakAbsen = app(AbsensiTidakAbsenService::class);
        $holidayInfo = $absensiTidakAbsen->holidayInfo($absensi->tanggal);
        $weeklyOffInfo = $absensiTidakAbsen->weeklyOffInfo($request->user(), $absensi->tanggal);
        if (! $holidayInfo['is_holiday'] && $weeklyOffInfo['is_holiday']) {
            $holidayInfo = $weeklyOffInfo + ['event' => null];
        }
        if ($holidayInfo['is_holiday']) {
            return back()->with('error', 'Tanggal ini libur (' . $holidayInfo['reason'] . '), pengajuan absen masuk tidak dibuka.');
        }

        $leaveInfo = $absensiTidakAbsen->leaveInfo($request->user(), $absensi->tanggal);
        if ($leaveInfo['is_leave']) {
            return back()->with('error', 'Tanggal ini tercatat cuti (' . $leaveInfo['reason'] . '), pengajuan absen masuk tidak dibuka.');
        }

        if (in_array($absensi->approval_masuk_status, ['pending_ketua', 'pending_atasan', 'approved'], true)) {
            return back()->with('error', 'Pengajuan absen masuk untuk tanggal ini sudah diproses atau masih menunggu.');
        }

        $requester = $request->user();
        $directToAtasan = $requester->isKetuaRegu();

        $absensi->update([
            'approval_masuk_status' => $directToAtasan ? 'pending_atasan' : 'pending_ketua',
            'approval_masuk_requested_at' => now(),
            'approval_masuk_forwarded_by' => $directToAtasan ? $requester->id_user : null,
            'approval_masuk_forwarded_at' => $directToAtasan ? now() : null,
            'approval_masuk_reason' => $validated['approval_masuk_reason'],
        ]);

        $recipients = $directToAtasan
            ? collect()
            : User::where('is_ketua_regu', true)
                ->where('regu', $requester->regu)
                ->where('id_user', '!=', $requester->id_user)
                ->get();

        if ($recipients->isEmpty()) {
            $recipients = User::whereHas('role', function ($query) {
                QueryFilters::whereRoleAlias($query, ['atasan', 'manager', 'menejer']);
            })->get();

            if (! $directToAtasan) {
                $absensi->update([
                    'approval_masuk_status' => 'pending_atasan',
                    'approval_masuk_forwarded_at' => now(),
                ]);
            }
        }

        foreach ($recipients as $recipient) {
            Notifikasi::create([
                'id_user' => $recipient->id_user,
                'judul' => 'Pengajuan Absen Masuk Terlewat',
                'pesan' => $requester->nama . ' dari ' . ($requester->regu ?: 'regu belum diisi') . ' mengajukan absen masuk tanggal ' . $absensi->tanggal->format('d/m/Y') . '.',
                'tipe' => 'absensi',
                'status_baca' => false,
                'reference_id' => $absensi->id_absensi,
                'reference_type' => Absensi::class,
            ]);
        }

        ActivityLogger::log($request, 'Pengajuan absen masuk terlewat', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', $directToAtasan
            ? 'Pengajuan absen masuk dikirim langsung ke atasan.'
            : 'Pengajuan absen masuk dikirim ke ketua regu/atasan.');
    }

    public function requestMasukApprovalHariIni(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'approval_masuk_reason' => ['required', 'string', 'max:500'],
        ]);

        $user = $request->user();
        $absensiTidakAbsen = app(AbsensiTidakAbsenService::class);
        $holidayInfo = $absensiTidakAbsen->holidayInfo(today());
        $weeklyOffInfo = $absensiTidakAbsen->weeklyOffInfo($user, today());
        if (! $holidayInfo['is_holiday'] && $weeklyOffInfo['is_holiday']) {
            $holidayInfo = $weeklyOffInfo + ['event' => null];
        }
        if ($holidayInfo['is_holiday']) {
            return back()->with('error', 'Hari ini libur (' . $holidayInfo['reason'] . '), pengajuan absen masuk tidak dibuka.');
        }

        $leaveInfo = $absensiTidakAbsen->leaveInfo($user, today());
        if ($leaveInfo['is_leave']) {
            return back()->with('error', 'Hari ini kamu sedang cuti (' . $leaveInfo['reason'] . '), pengajuan absen masuk tidak dibuka.');
        }

        $shiftWindow = $this->shiftWindow($user, today());
        if (now()->lessThanOrEqualTo($shiftWindow['masuk_tutup_at'])) {
            return back()->with('error', 'Absen masuk masih bisa dilakukan. Gunakan form absen masuk biasa.');
        }

        $absensi = Absensi::firstOrCreate(
            [
                'id_user' => $user->id_user,
                'tanggal' => today()->toDateString(),
            ],
            [
                'id_periode' => optional(Periode::aktif())->id_periode,
                'shift' => $user->shift,
                'status' => 'tidak_absen',
                'keterangan' => 'Menunggu pengajuan absen masuk terlewat',
            ]
        );

        if ($absensi->jam_masuk) {
            return back()->with('error', 'Kamu sudah absen masuk hari ini.');
        }

        if (in_array($absensi->approval_masuk_status, ['pending_ketua', 'pending_atasan', 'approved'], true)) {
            return back()->with('error', 'Pengajuan absen masuk untuk tanggal ini sudah diproses atau masih menunggu.');
        }

        $directToAtasan = $user->isKetuaRegu();

        $absensi->update([
            'approval_masuk_status' => $directToAtasan ? 'pending_atasan' : 'pending_ketua',
            'approval_masuk_requested_at' => now(),
            'approval_masuk_forwarded_by' => $directToAtasan ? $user->id_user : null,
            'approval_masuk_forwarded_at' => $directToAtasan ? now() : null,
            'approval_masuk_reason' => $validated['approval_masuk_reason'],
        ]);

        $recipients = $directToAtasan
            ? collect()
            : User::where('is_ketua_regu', true)
                ->where('regu', $user->regu)
                ->where('id_user', '!=', $user->id_user)
                ->get();

        if ($recipients->isEmpty()) {
            $recipients = User::whereHas('role', function ($query) {
                QueryFilters::whereRoleAlias($query, ['atasan', 'manager', 'menejer']);
            })->get();

            if (! $directToAtasan) {
                $absensi->update([
                    'approval_masuk_status' => 'pending_atasan',
                    'approval_masuk_forwarded_at' => now(),
                ]);
            }
        }

        foreach ($recipients as $recipient) {
            Notifikasi::create([
                'id_user' => $recipient->id_user,
                'judul' => 'Pengajuan Absen Masuk Terlewat',
                'pesan' => $user->nama . ' dari ' . ($user->regu ?: 'regu belum diisi') . ' mengajukan absen masuk tanggal ' . $absensi->tanggal->format('d/m/Y') . '.',
                'tipe' => 'absensi',
                'status_baca' => false,
                'reference_id' => $absensi->id_absensi,
                'reference_type' => Absensi::class,
            ]);
        }

        ActivityLogger::log($request, 'Pengajuan absen masuk terlewat hari ini', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', $directToAtasan
            ? 'Pengajuan absen masuk dikirim langsung ke atasan.'
            : 'Pengajuan absen masuk dikirim ke ketua regu/atasan.');
    }

    public function approvalRegu(Request $request): View
    {
        $user = $request->user();
        if (! $user->isKetuaRegu()) {
            abort(403, 'Hanya ketua regu yang dapat membuka halaman ini.');
        }

        $items = Absensi::with('user')
            ->where(function ($query) {
                $query->where('approval_masuk_status', 'pending_ketua')
                    ->orWhere('approval_pulang_status', 'pending_ketua');
            })
            ->whereHas('user', function ($query) use ($user) {
                $query->where('regu', $user->regu);
            })
            ->latest('approval_masuk_requested_at')
            ->latest('approval_pulang_requested_at')
            ->paginate($request->get('per_page', 20))
            ->withQueryString();

        return view('petugas.approval-regu', compact('items'));
    }

    public function forwardPulangApproval(Request $request, int $id): RedirectResponse
    {
        $absensi = $this->ketuaReguAbsensi($request, $id);

        $absensi->update([
            'approval_pulang_status' => 'pending_atasan',
            'approval_pulang_forwarded_by' => $request->user()->id_user,
            'approval_pulang_forwarded_at' => now(),
        ]);

        $atasans = User::whereHas('role', function ($query) {
            QueryFilters::whereRoleAlias($query, ['atasan', 'manager', 'menejer']);
        })->get();

        foreach ($atasans as $atasan) {
            Notifikasi::create([
                'id_user' => $atasan->id_user,
                'judul' => 'Request Absen Pulang Diteruskan Ketua Regu',
                'pesan' => $request->user()->nama . ' meneruskan request lupa absen pulang ' . ($absensi->user->nama ?? '-') . ' tanggal ' . $absensi->tanggal->format('d/m/Y') . '.',
                'tipe' => 'absensi',
                'status_baca' => false,
                'reference_id' => $absensi->id_absensi,
                'reference_type' => Absensi::class,
            ]);
        }

        ActivityLogger::log($request, 'Ketua regu meneruskan request absen pulang', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', 'Request diteruskan ke atasan.');
    }

    public function forwardMasukApproval(Request $request, int $id): RedirectResponse
    {
        $absensi = $this->ketuaReguAbsensi($request, $id, 'masuk');

        $absensi->update([
            'approval_masuk_status' => 'pending_atasan',
            'approval_masuk_forwarded_by' => $request->user()->id_user,
            'approval_masuk_forwarded_at' => now(),
        ]);

        $atasans = User::whereHas('role', function ($query) {
            QueryFilters::whereRoleAlias($query, ['atasan', 'manager', 'menejer']);
        })->get();

        foreach ($atasans as $atasan) {
            Notifikasi::create([
                'id_user' => $atasan->id_user,
                'judul' => 'Pengajuan Absen Masuk Diteruskan',
                'pesan' => $request->user()->nama . ' meneruskan pengajuan absen masuk ' . ($absensi->user->nama ?? '-') . ' tanggal ' . $absensi->tanggal->format('d/m/Y') . '.',
                'tipe' => 'absensi',
                'status_baca' => false,
                'reference_id' => $absensi->id_absensi,
                'reference_type' => Absensi::class,
            ]);
        }

        ActivityLogger::log($request, 'Ketua regu meneruskan pengajuan absen masuk', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', 'Pengajuan absen masuk diteruskan ke atasan.');
    }

    public function rejectPulangApprovalByKetua(Request $request, int $id): RedirectResponse
    {
        $absensi = $this->ketuaReguAbsensi($request, $id);

        $absensi->update([
            'approval_pulang_status' => 'rejected_ketua',
            'approval_pulang_forwarded_by' => $request->user()->id_user,
            'approval_pulang_forwarded_at' => now(),
        ]);

        Notifikasi::create([
            'id_user' => $absensi->id_user,
            'judul' => 'Request Absen Pulang Ditolak Ketua Regu',
            'pesan' => 'Request lupa absen pulang tanggal ' . $absensi->tanggal->format('d/m/Y') . ' ditolak ketua regu.',
            'tipe' => 'absensi',
            'status_baca' => false,
            'reference_id' => $absensi->id_absensi,
            'reference_type' => Absensi::class,
        ]);

        ActivityLogger::log($request, 'Ketua regu menolak request absen pulang', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', 'Request ditolak.');
    }

    public function rejectMasukApprovalByKetua(Request $request, int $id): RedirectResponse
    {
        $absensi = $this->ketuaReguAbsensi($request, $id, 'masuk');

        $absensi->update([
            'approval_masuk_status' => 'rejected_ketua',
            'approval_masuk_forwarded_by' => $request->user()->id_user,
            'approval_masuk_forwarded_at' => now(),
        ]);

        Notifikasi::create([
            'id_user' => $absensi->id_user,
            'judul' => 'Pengajuan Absen Masuk Ditolak Ketua Regu',
            'pesan' => 'Pengajuan absen masuk tanggal ' . $absensi->tanggal->format('d/m/Y') . ' ditolak ketua regu.',
            'tipe' => 'absensi',
            'status_baca' => false,
            'reference_id' => $absensi->id_absensi,
            'reference_type' => Absensi::class,
        ]);

        ActivityLogger::log($request, 'Ketua regu menolak pengajuan absen masuk', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', 'Pengajuan absen masuk ditolak.');
    }

    private function ketuaReguAbsensi(Request $request, int $id, string $jenis = 'pulang'): Absensi
    {
        $user = $request->user();
        if (! $user->isKetuaRegu()) {
            abort(403);
        }

        $statusColumn = $jenis === 'masuk' ? 'approval_masuk_status' : 'approval_pulang_status';

        return Absensi::with('user')
            ->where($statusColumn, 'pending_ketua')
            ->whereHas('user', function ($query) use ($user) {
                $query->where('regu', $user->regu);
            })
            ->findOrFail($id);
    }
}
