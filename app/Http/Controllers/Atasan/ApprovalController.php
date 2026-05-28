<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Notifikasi;
use App\Models\Periode;
use App\Models\Shift;
use App\Models\TempatTugas;
use App\Models\Tugas;
use App\Models\User;
use App\Services\AbsensiTidakAbsenService;
use App\Support\ActivityLogger;
use App\Support\QueryFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Mengelola workflow atasan: pemantauan absensi, approval cuti/tugas,
 * approval pengajuan absen masuk/pulang, pengaturan regu, dan kalender atasan.
 */
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
        $items = $this->queryTugasApproval($selectedPeriode);

        return view('atasan.tugas', [
            'items' => $items->latest('id_tugas')->paginate($request->get("per_page", 25))->withQueryString(),
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
        ]);
    }

    /**
     * Mengekspor data approval tugas ke CSV sesuai periode yang sedang difilter.
     */
    public function exportTugas(Request $request)
    {
        $selectedPeriode = Periode::find((int) $request->query('id_periode'));
        $items = $this->queryTugasApproval($selectedPeriode)
            ->latest('id_tugas')
            ->get();

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=tugas_atasan_' . date('Ymd_His') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Nama Petugas',
                'Periode',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Uraian',
                'Status',
                'Status Input',
                'Waktu Submit',
            ]);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->id_tugas,
                    $item->user->nama ?? '-',
                    $item->periode->nama_periode ?? '-',
                    $item->tanggal_mulai?->format('Y-m-d H:i:s') ?? '-',
                    $item->tanggal_selesai?->format('Y-m-d H:i:s') ?? '-',
                    $item->uraian,
                    $item->status,
                    $item->is_late_input ? 'Telat input' : 'Tepat waktu',
                    $item->submitted_at?->format('Y-m-d H:i:s') ?? $item->created_at?->format('Y-m-d H:i:s') ?? '-',
                ]);
            }

            fclose($file);
        };

        ActivityLogger::log($request, 'Export tugas atasan ke CSV', 'tugas', null, Tugas::class);

        return response()->stream($callback, 200, $headers);
    }

    public function regu(Request $request): View
    {
        $atasan = $request->user();

        // Ambil hanya petugas yang satu tempat tugas dengan atasan
        $query = User::with(['role', 'tempatTugas'])
            ->whereHas('role', function ($query) {
                QueryFilters::whereRoleAlias($query, ['petugas', 'karyawan']);
            });

        // Filter: hanya tampilkan petugas yang satu tempat tugas dengan atasan
        if ($atasan->id_tempat) {
            $query->where('id_tempat', $atasan->id_tempat);
        }

        $petugas = $query->orderBy('regu')->orderBy('nama')->get();

        return view('atasan.regu', [
            'petugasByRegu' => $petugas->groupBy(fn (User $user) => $user->regu ?: 'Belum Ada Regu'),
            'petugasList' => $petugas->filter(fn (User $user) => blank($user->regu))->values(),
            'tempatTugas' => TempatTugas::orderBy('nama_tempat')->get(),
            'atasanTempat' => $atasan->tempatTugas,
        ]);
    }

    public function storeRegu(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_regu' => ['required', 'string', 'max:20'],
            'anggota_ids' => ['required', 'array', 'size:5'],
            'anggota_ids.*' => ['required', 'distinct', 'exists:users,id_user'],
            'ketua_id' => ['required', 'exists:users,id_user'],
        ], [
            'anggota_ids.size' => 'Satu regu harus terdiri dari tepat 5 orang.',
        ]);

        if (! in_array((int) $validated['ketua_id'], array_map('intval', $validated['anggota_ids']), true)) {
            return back()->withInput()->with('error', 'Ketua regu harus termasuk dalam 5 anggota yang dipilih.');
        }

        $atasan = $request->user();
        $anggota = User::with('role')
            ->whereIn('id_user', $validated['anggota_ids'])
            ->get();

        if ($anggota->count() !== 5 || $anggota->contains(fn (User $user) => ! $user->isPetugas())) {
            return back()->withInput()->with('error', 'Pilih tepat 5 petugas dari area tugas kamu.');
        }

        User::where('regu', $validated['nama_regu'])
            ->update(['is_ketua_regu' => false]);

        User::whereIn('id_user', $anggota->pluck('id_user'))->update([
            'regu' => $validated['nama_regu'],
            'is_ketua_regu' => false,
        ]);

        $ketua = User::findOrFail($validated['ketua_id']);
        $ketua->update([
            'regu' => $validated['nama_regu'],
            'is_ketua_regu' => true,
        ]);

        Notifikasi::create([
            'id_user' => $ketua->id_user,
            'judul' => 'Ditunjuk Sebagai Ketua Regu',
            'pesan' => 'Atasan menunjuk kamu sebagai ketua ' . $validated['nama_regu'] . '.',
            'tipe' => 'system',
            'status_baca' => false,
            'reference_id' => $ketua->id_user,
            'reference_type' => User::class,
        ]);

        ActivityLogger::log($request, 'Membuat regu petugas', 'users', $ketua->id_user, User::class);

        return back()->with('success', $validated['nama_regu'] . ' berhasil dibuat dengan 5 anggota.');
    }

    public function updateReguOperasional(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_regu' => ['required', 'string', 'max:20'],
            'id_tempat' => ['nullable', 'exists:tempat_tugas,id_tempat'],
            'shifts' => ['nullable', 'array'],
            'shifts.*' => ['nullable', 'in:Shift 1,Shift 2,Shift 3'],
        ]);

        $anggota = User::where('regu', $validated['nama_regu'])->get();
        if ($anggota->isEmpty()) {
            return back()->with('error', 'Regu tidak ditemukan.');
        }

        User::where('regu', $validated['nama_regu'])->update([
            'id_tempat' => $validated['id_tempat'] ?? null,
        ]);

        foreach (($validated['shifts'] ?? []) as $userId => $shift) {
            User::where('regu', $validated['nama_regu'])
                ->where('id_user', $userId)
                ->update(['shift' => $shift ?: null]);
        }

        ActivityLogger::log($request, 'Mengubah tempat kerja dan shift regu', 'users', null, User::class);

        return back()->with('success', 'Tempat kerja dan shift ' . $validated['nama_regu'] . ' berhasil diperbarui.');
    }

    public function setKetuaRegu(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_user' => ['required', 'exists:users,id_user'],
        ]);

        $atasan = $request->user();
        $ketua = User::with('role')->findOrFail($validated['id_user']);

        if (! $ketua->isPetugas()) {
            return back()->with('error', 'Ketua regu harus dipilih dari petugas.');
        }

        if (! $ketua->regu) {
            return back()->with('error', 'Petugas ini belum punya regu. Isi regu di menu Admin Users terlebih dahulu.');
        }

        User::where('regu', $ketua->regu)
            ->update(['is_ketua_regu' => false]);

        $ketua->update(['is_ketua_regu' => true]);

        Notifikasi::create([
            'id_user' => $ketua->id_user,
            'judul' => 'Ditunjuk Sebagai Ketua Regu',
            'pesan' => 'Atasan menunjuk kamu sebagai ketua ' . $ketua->regu . '. Kamu dapat mengelola request approval regu.',
            'tipe' => 'system',
            'status_baca' => false,
            'reference_id' => $ketua->id_user,
            'reference_type' => User::class,
        ]);

        ActivityLogger::log($request, 'Menentukan ketua regu', 'users', $ketua->id_user, User::class);

        return back()->with('success', $ketua->nama . ' berhasil ditetapkan sebagai ketua ' . $ketua->regu . '.');
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

    public function remindTugas(Request $request, int $id): RedirectResponse
    {
        $tugas = Tugas::with('user')->findOrFail($id);

        Notifikasi::create([
            'id_user' => $tugas->id_user,
            'judul' => 'Pengingat Laporan Tugas',
            'pesan' => 'Atasan mengingatkan agar laporan tugas diisi lengkap dan diperbarui bila ada kekurangan.',
            'tipe' => 'tugas',
            'status_baca' => false,
            'reference_id' => $tugas->id_tugas,
            'reference_type' => Tugas::class,
        ]);

        ActivityLogger::log($request, 'Mengirim pengingat laporan tugas', 'tugas', $tugas->id_tugas, Tugas::class);

        return back()->with('success', 'Pengingat laporan tugas berhasil dikirim.');
    }

    public function approvePulang(Request $request, int $id): RedirectResponse
    {
        return $this->updatePulangApproval($request, $id, 'approved');
    }

    /**
     * Query dasar laporan tugas untuk halaman approval dan export CSV.
     */
    private function queryTugasApproval(?Periode $selectedPeriode)
    {
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

        return $items;
    }

    public function rejectPulang(Request $request, int $id): RedirectResponse
    {
        return $this->updatePulangApproval($request, $id, 'rejected');
    }

    public function approveMasuk(Request $request, int $id): RedirectResponse
    {
        return $this->updateMasukApproval($request, $id, 'approved');
    }

    public function rejectMasuk(Request $request, int $id): RedirectResponse
    {
        return $this->updateMasukApproval($request, $id, 'rejected');
    }

    private function updateMasukApproval(Request $request, int $id, string $status): RedirectResponse
    {
        $absensi = Absensi::with('user')->findOrFail($id);

        if (! $absensi->user?->isPetugas()) {
            abort(403, 'Approval absen hanya untuk petugas.');
        }

        if ($absensi->approval_masuk_status !== 'pending_atasan') {
            return back()->with('error', 'Pengajuan absen masuk ini belum diteruskan ketua regu atau sudah diproses.');
        }

        $updates = [
            'approval_masuk_status' => $status,
            'approval_masuk_approved_by' => $request->user()->id_user,
        ];

        if ($status === 'approved') {
            $jamMasuk = $this->jamMasukUntukAbsensi($absensi);
            $updates = array_merge($updates, [
                'jam_masuk' => $jamMasuk,
                'status' => 'telat',
                'shift' => $absensi->shift ?: $absensi->user->shift,
                'keterangan' => trim(($absensi->keterangan ? $absensi->keterangan . ' | ' : '') . 'Absen masuk terlewat disetujui atasan. Alasan: ' . $absensi->approval_masuk_reason),
            ]);
        }

        $absensi->update($updates);

        Notifikasi::create([
            'id_user' => $absensi->id_user,
            'judul' => $status === 'approved' ? 'Pengajuan Absen Masuk Disetujui' : 'Pengajuan Absen Masuk Ditolak',
            'pesan' => $status === 'approved'
                ? 'Atasan menyetujui pengajuan absen masuk tanggal ' . $absensi->tanggal->format('d/m/Y') . '.'
                : 'Pengajuan absen masuk tanggal ' . $absensi->tanggal->format('d/m/Y') . ' ditolak.',
            'tipe' => 'absensi',
            'status_baca' => false,
            'reference_id' => $absensi->id_absensi,
            'reference_type' => Absensi::class,
        ]);

        ActivityLogger::log($request, ucfirst($status) . ' pengajuan absen masuk', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', 'Status pengajuan absen masuk berhasil diperbarui.');
    }

    private function updatePulangApproval(Request $request, int $id, string $status): RedirectResponse
    {
        $absensi = Absensi::with('user')->findOrFail($id);

        if (! $absensi->user?->isPetugas()) {
            abort(403, 'Approval absen hanya untuk petugas.');
        }

        if ($absensi->approval_pulang_status !== 'pending_atasan') {
            return back()->with('error', 'Request absen pulang ini belum diteruskan ketua regu atau sudah diproses.');
        }

        $absensi->update([
            'approval_pulang_status' => $status,
            'approval_pulang_approved_by' => $request->user()->id_user,
        ]);

        Notifikasi::create([
            'id_user' => $absensi->id_user,
            'judul' => $status === 'approved' ? 'Absen Pulang Dibuka' : 'Request Absen Pulang Ditolak',
            'pesan' => $status === 'approved'
                ? 'Atasan membuka absen pulang untuk tanggal ' . $absensi->tanggal->format('d/m/Y') . '. Silakan upload foto pulang.'
                : 'Request lupa absen pulang tanggal ' . $absensi->tanggal->format('d/m/Y') . ' ditolak.',
            'tipe' => 'absensi',
            'status_baca' => false,
            'reference_id' => $absensi->id_absensi,
            'reference_type' => Absensi::class,
        ]);

        ActivityLogger::log($request, ucfirst($status) . ' request absen pulang', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', 'Status request absen pulang berhasil diperbarui.');
    }

    private function jamMasukUntukAbsensi(Absensi $absensi): string
    {
        $shiftName = $absensi->shift ?: $absensi->user?->shift;

        if ($shiftName) {
            $shift = Shift::where('nama_shift', $shiftName)->first();
            if ($shift?->jam_masuk) {
                return \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i:s');
            }
        }

        return config('absensi.jam_masuk_buka', '06:00:00');
    }

    private function updateCuti(Request $request, int $id, string $status): RedirectResponse
    {
        $cuti = Cuti::with('user')->findOrFail($id);
        
        // Authorization: Verify the user is petugas (atasan should only approve petugas cuti)
        if (!$cuti->user->isPetugas()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah cuti ini.');
        }
        
        // Prevent re-approval of already processed cuti
        if ($cuti->admin_status !== 'approve') {
            return back()->with('error', 'Cuti harus disetujui admin terlebih dahulu.');
        }

        if ($cuti->status !== 'pending') {
            return back()->with('error', 'Cuti ini sudah diproses sebelumnya.');
        }
        
        $cuti->update([
            'status' => $status,
            'approver_id' => $request->user()->id_user,
        ]);

        if ($status === 'approve') {
            $cuti->refresh()->load('user');
            app(AbsensiTidakAbsenService::class)->syncApprovedLeave($cuti);
        }

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
