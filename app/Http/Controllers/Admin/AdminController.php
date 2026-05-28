<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Kalender;
use App\Models\Notifikasi;
use App\Models\Periode;
use App\Models\Role;
use App\Models\Sanksi;
use App\Models\TempatTugas;
use App\Models\User;
use App\Models\UserSensitive;
use App\Support\ActivityLogger;
use App\Support\ImageOptimizer;
use App\Support\QueryFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Mengelola fitur administrasi utama: user, tempat tugas, periode,
 * kalender, cuti, absensi telat, pengaturan aplikasi, log, dan data sensitif.
 */
class AdminController extends Controller
{
    public function users(Request $request): View
    {
        $query = User::with(['role', 'tempatTugas']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                QueryFilters::whereAnyLike($q, ['nama', 'email', 'username'], $search);
            });
        }

        if ($request->filled('role')) {
            $query->where('id_role', $request->role);
        }

        return view('admin.users', [
            'items' => $query->orderBy('created_at', 'asc')->paginate($request->get("per_page", 25))->withQueryString(),
            'roles' => Role::orderBy('id_role')->get(),
            'tempatTugas' => TempatTugas::orderBy('nama_tempat')->get(),
            'filters' => [
                'search'   => $request->get('search', ''),
                'role'     => $request->get('role', ''),
                'per_page' => (int) $request->get('per_page', 25),
            ],
        ]);
    }

    public function createUser(): View
    {
        return view('admin.users-create', [
            'roles' => Role::orderBy('id_role')->get(),
            'tempatTugas' => TempatTugas::orderBy('nama_tempat')->get(),
        ]);
    }

    public function showImportUsers(): View
    {
        return view('admin.users-import');
    }

    public function filterUsers(Request $request): View
    {
        return view('admin.users-filter', [
            'roles' => Role::orderBy('id_role')->get(),
            'filters' => [
                'search' => $request->search,
                'role' => $request->role,
            ],
        ]);
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'id_role' => ['required', 'exists:roles,id_role'],
            'id_tempat' => ['nullable', 'exists:tempat_tugas,id_tempat'],
            'nik' => ['nullable', 'string', 'max:20'],
            'regu' => ['nullable', 'string', 'max:20'],
            'is_ketua_regu' => ['nullable', 'boolean'],
            'shift' => ['nullable', 'string', 'max:30'],
            'status_aktif' => ['nullable', 'in:aktif,nonaktif'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'jabatan' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'id_role' => $validated['id_role'],
            'id_tempat' => $validated['id_tempat'] ?? null,
            'regu' => $validated['regu'] ?? null,
            'is_ketua_regu' => (bool) ($validated['is_ketua_regu'] ?? false),
            'shift' => $validated['shift'] ?? null,
            'status_aktif' => $validated['status_aktif'] ?? 'aktif',
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'jabatan' => $validated['jabatan'] ?? null,
        ]);


        // Simpan NIK ke data sensitif (encrypted)
        if (!empty($validated['nik'])) {
            $sensitive = new UserSensitive();
            $sensitive->id_user = $user->id_user;
            $sensitive->setNik($validated['nik']);
            $sensitive->save();
        }

        ActivityLogger::log($request, 'Membuat user', 'users', $user->id_user, User::class);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat.');
    }

    public function updateUser(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username,' . $user->id_user . ',id_user'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,' . $user->id_user . ',id_user'],
            'password' => ['nullable', 'string', 'min:8'],
            'id_role' => ['required', 'exists:roles,id_role'],
            'id_tempat' => ['nullable', 'exists:tempat_tugas,id_tempat'],
            'nik' => ['nullable', 'string', 'max:20'],
            'regu' => ['nullable', 'string', 'max:20'],
            'is_ketua_regu' => ['nullable', 'boolean'],
            'shift' => ['nullable', 'string', 'max:30'],
            'status_aktif' => ['nullable', 'in:aktif,nonaktif'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'jabatan' => ['nullable', 'string', 'max:100'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        ActivityLogger::log($request, 'Mengubah user', 'users', $user->id_user, User::class);

        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function deleteUser(Request $request, int $id): RedirectResponse
    {
        if ($request->user()->id_user === $id) {
            return back()->with('error', 'Akun sendiri tidak bisa dihapus dari halaman ini.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        ActivityLogger::log($request, 'Menghapus user', 'users', $id, User::class);

        return back()->with('success', 'User berhasil dihapus.');
    }

    public function tempat(Request $request): View
    {
        return view('admin.tempat', [
            'items' => TempatTugas::orderBy('id_tempat', 'asc')->paginate($request->get('per_page', 15)),
        ]);
    }

    public function storeTempat(Request $request): RedirectResponse
    {
        $tempat = TempatTugas::create($this->validateTempat($request));

        ActivityLogger::log($request, 'Membuat tempat tugas', 'tempat_tugas', $tempat->id_tempat, TempatTugas::class);

        return back()->with('success', 'Tempat tugas berhasil dibuat.');
    }

    public function updateTempat(Request $request, int $id): RedirectResponse
    {
        $tempat = TempatTugas::findOrFail($id);
        $tempat->update($this->validateTempat($request));

        ActivityLogger::log($request, 'Mengubah tempat tugas', 'tempat_tugas', $tempat->id_tempat, TempatTugas::class);

        return back()->with('success', 'Tempat tugas berhasil diperbarui.');
    }

    private function validateTempat(Request $request): array
    {
        return $request->validate([
            'nama_tempat' => ['required', 'string', 'max:150'],
            'alamat' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ], [
            'latitude.between' => 'Latitude harus di antara -90 sampai 90. Contoh Jakarta: -6.209286.',
            'longitude.between' => 'Longitude harus di antara -180 sampai 180. Gunakan titik desimal, contoh: 106.871253.',
        ]);
    }

    public function deleteTempat(Request $request, int $id): RedirectResponse
    {
        TempatTugas::findOrFail($id)->delete();
        ActivityLogger::log($request, 'Menghapus tempat tugas', 'tempat_tugas', $id, TempatTugas::class);

        return back()->with('success', 'Tempat tugas berhasil dihapus.');
    }

    public function periode(Request $request): View
    {
        return view('admin.periode', [
            'items' => Periode::orderBy('id_periode', 'asc')->paginate($request->get('per_page', 15)),
            'users' => User::with('role')->orderBy('nama')->get(),
        ]);
    }

    public function storePeriode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100', Rule::unique('periode', 'nama_periode')->where(fn ($query) => $query->where('nama_periode', 'Periode ' . $request->tahun))],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $periode = Periode::create([
            'nama_periode' => 'Periode ' . $validated['tahun'],
            'tanggal_mulai' => $validated['tahun'] . '-01-01',
            'tanggal_selesai' => $validated['tahun'] . '-12-31',
            'status' => $validated['status'],
        ]);

        ActivityLogger::log($request, 'Membuat periode', 'periode', $periode->id_periode, Periode::class);

        return back()->with('success', 'Periode berhasil dibuat.');
    }

    public function updatePeriode(Request $request, int $id): RedirectResponse
    {
        $periode = Periode::findOrFail($id);
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        $periode->update([
            'nama_periode' => 'Periode ' . $validated['tahun'],
            'tanggal_mulai' => $validated['tahun'] . '-01-01',
            'tanggal_selesai' => $validated['tahun'] . '-12-31',
            'status' => $validated['status'],
        ]);

        ActivityLogger::log($request, 'Mengubah periode', 'periode', $periode->id_periode, Periode::class);

        return back()->with('success', 'Periode berhasil diperbarui.');
    }

    public function deletePeriode(Request $request, int $id): RedirectResponse
    {
        Periode::findOrFail($id)->delete();
        ActivityLogger::log($request, 'Menghapus periode', 'periode', $id, Periode::class);

        return back()->with('success', 'Periode berhasil dihapus.');
    }

    public function exportPeriode(Request $request)
    {
        $year = (int) $request->query('tahun', now()->year);
        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=absensi_{$year}.csv",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($year) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Nama', 'Regu', 'Shift', 'Status User', 'Masuk', 'Pulang', 'Status Absensi', 'Approval Pulang', 'Keterangan']);

            Absensi::with('user')
                ->whereYear('tanggal', $year)
                ->orderBy('tanggal')
                ->orderBy('id_user')
                ->chunk(500, function ($items) use ($file) {
                    foreach ($items as $item) {
                        fputcsv($file, [
                            optional($item->tanggal)->format('Y-m-d'),
                            $item->user->nama ?? '-',
                            $item->user->regu ?? '-',
                            $item->shift ?? $item->user->shift ?? '-',
                            $item->user->status_aktif ?? '-',
                            $item->jam_masuk,
                            $item->jam_pulang,
                            $item->status,
                            $item->approval_pulang_status,
                            $item->keterangan,
                        ]);
                    }
                });

            fclose($file);
        };

        ActivityLogger::log($request, "Export absensi tahun {$year}", 'absensi', null, Absensi::class);

        return response()->stream($callback, 200, $headers);
    }

    public function kalender(Request $request): View
    {
        return view('admin.kalender', [
            'items' => Kalender::orderBy('id_kalender', 'asc')->paginate($request->get('per_page', 20)),
        ]);
    }

    public function storeKalender(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'nama_event' => ['nullable', 'string', 'max:150'],
            'jenis_event' => ['required', 'in:libur,kegiatan,cuti_bersama'],
            'keterangan' => ['nullable', 'string'],
        ]);

        // Validate uniqueness AFTER nama_event is validated (not using raw request)
        $namaEvent = $validated['nama_event'];
        $tanggal = $validated['tanggal'];

        $exists = Kalender::where('tanggal', $tanggal)
            ->where(function ($query) use ($namaEvent) {
                // If nama_event is empty/null, only match other empty/null events
                if (empty($namaEvent)) {
                    $query->whereNull('nama_event')->orWhere('nama_event', '');
                } else {
                    $query->where('nama_event', $namaEvent);
                }
            })
            ->exists();

        if ($exists) {
            return back()->with('error', 'Jadwal dengan tanggal dan nama event ini sudah ada di kalender.')->withInput();
        }

        $kalender = Kalender::create($validated);

        if (in_array($kalender->jenis_event, ['libur', 'cuti_bersama'], true)) {
            \App\Models\Absensi::whereDate('tanggal', $kalender->tanggal)
                ->where('status', 'tidak_absen')
                ->where('keterangan', 'Tidak hadir (otomatis sistem)')
                ->delete();
        }

        ActivityLogger::log($request, 'Membuat kalender', 'kalender', $kalender->id_kalender, Kalender::class);

        $waktu = now();
        $judul = "Info Jadwal Kalender";
        $jenisStr = ucfirst(str_replace('_', ' ', $kalender->jenis_event));
        $namaEvent = $kalender->nama_event ? " ({$kalender->nama_event})" : "";
        $tglStr = \Carbon\Carbon::parse($kalender->tanggal)->translatedFormat('d F Y');
        $pesan = "Terdapat jadwal baru: {$jenisStr}{$namaEvent} pada tanggal {$tglStr}.";

        User::select('id_user')->chunk(200, function($users) use ($pesan, $judul, $waktu, $kalender) {
            $notifikasis = [];
            foreach ($users as $u) {
                $notifikasis[] = [
                    'id_user' => $u->id_user,
                    'judul' => $judul,
                    'pesan' => $pesan,
                    'tipe' => 'system',
                    'status_baca' => false,
                    'created_at' => $waktu,
                    'reference_id' => $kalender->id_kalender,
                    'reference_type' => Kalender::class,
                ];
            }
            \App\Models\Notifikasi::insert($notifikasis);
        });

        return back()->with('success', 'Kalender berhasil dibuat dan notifikasi telah dikirim.');
    }

    public function deleteKalender(Request $request, int $id): RedirectResponse
    {
        Kalender::findOrFail($id)->delete();
        ActivityLogger::log($request, 'Menghapus kalender', 'kalender', $id, Kalender::class);

        return back()->with('success', 'Kalender berhasil dihapus.');
    }

    public function sanksi(Request $request): View
    {
        return view('admin.sanksi', [
            'items' => Sanksi::with('user')->orderBy('id_sanksi', 'asc')->paginate($request->get('per_page', 20)),
        ]);
    }

    public function cuti(Request $request): View
    {
        return view('admin.cuti', [
            'items' => Cuti::with(['user', 'pengganti', 'adminApprover', 'approver'])
                ->latest('id_cuti')
                ->paginate($request->get('per_page', 20))
                ->withQueryString(),
        ]);
    }

    public function approvePulangAbsensi(Request $request, int $id): RedirectResponse
    {
        $absensi = Absensi::with('user')->findOrFail($id);

        if (! in_array($absensi->approval_pulang_status, ['pending_ketua', 'pending_atasan'], true)) {
            return back()->with('error', 'Request absen pulang ini tidak sedang pending.');
        }

        $absensi->update([
            'approval_pulang_status' => 'approved',
            'approval_pulang_approved_by' => $request->user()->id_user,
        ]);

        Notifikasi::create([
            'id_user' => $absensi->id_user,
            'judul' => 'Absen Pulang Dibuka Admin',
            'pesan' => 'Admin membuka absen pulang untuk tanggal ' . $absensi->tanggal->format('d/m/Y') . '. Silakan upload foto pulang.',
            'tipe' => 'absensi',
            'status_baca' => false,
            'reference_id' => $absensi->id_absensi,
            'reference_type' => Absensi::class,
        ]);

        ActivityLogger::log($request, 'Approve request absen pulang oleh admin', 'absensi', $absensi->id_absensi, Absensi::class);

        return back()->with('success', 'Absen pulang berhasil dibuka untuk petugas.');
    }

    public function pengaturan(): View
    {
        return view('admin.pengaturan', [
            'app_name' => \App\Models\Pengaturan::getNilai('app_name', 'Absensi PPSU'),
            'app_logo' => \App\Models\Pengaturan::getNilai('app_logo'),
            'app_brand_display' => \App\Models\Pengaturan::getNilai('app_brand_display', 'logo_name'),
            'app_icon' => \App\Models\Pengaturan::getNilai('app_icon'),
            'app_icon_mode' => \App\Models\Pengaturan::getNilai('app_icon_mode', 'upload'),
            'app_icon_text' => \App\Models\Pengaturan::getNilai('app_icon_text', 'A'),
            'app_icon_bg' => \App\Models\Pengaturan::getNilai('app_icon_bg', '#2563eb'),
            'app_icon_color' => \App\Models\Pengaturan::getNilai('app_icon_color', '#ffffff'),
            'app_theme' => \App\Models\Pengaturan::getNilai('app_theme', 'light'),
        ]);
    }

    public function storePengaturan(Request $request): RedirectResponse
    {
        $request->validate([
            'app_name' => ['required', 'string', 'max:80'],
            'app_logo' => ['nullable', 'image', 'max:2048'],
            'app_brand_display' => ['required', 'in:logo_name,logo_only,name_only'],
            'app_icon' => ['nullable', 'image', 'max:1024'],
            'app_icon_mode' => ['required', 'in:upload,manual'],
            'app_icon_text' => ['nullable', 'string', 'max:2'],
            'app_icon_bg' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'app_icon_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'app_theme' => ['required', 'in:light,dark'],
        ]);

        if ($request->hasFile('app_logo')) {
            $oldLogo = \App\Models\Pengaturan::getNilai('app_logo');
            if ($oldLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldLogo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldLogo);
            }
            $logoPath = ImageOptimizer::storeUploaded($request->file('app_logo'), 'pengaturan', 900, 420, 82);
            if (! $logoPath) {
                return back()->with('error', 'Logo gagal diproses.');
            }
            \App\Models\Pengaturan::updateOrCreate(
                ['kunci' => 'app_logo'],
                ['nilai' => $logoPath]
            );
        }

        if ($request->hasFile('app_icon')) {
            $oldIcon = \App\Models\Pengaturan::getNilai('app_icon');
            if ($oldIcon && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldIcon)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldIcon);
            }
            $iconPath = ImageOptimizer::storeUploaded($request->file('app_icon'), 'pengaturan', 256, 256, 82);
            if (! $iconPath) {
                return back()->with('error', 'Ikon gagal diproses.');
            }
            \App\Models\Pengaturan::updateOrCreate(
                ['kunci' => 'app_icon'],
                ['nilai' => $iconPath]
            );
        }

        \App\Models\Pengaturan::updateOrCreate(
            ['kunci' => 'app_theme'],
            ['nilai' => $request->app_theme]
        );

        foreach ([
            'app_name' => $request->app_name,
            'app_brand_display' => $request->app_brand_display,
            'app_icon_mode' => $request->app_icon_mode,
            'app_icon_text' => strtoupper(substr($request->app_icon_text ?: 'A', 0, 2)),
            'app_icon_bg' => $request->app_icon_bg,
            'app_icon_color' => $request->app_icon_color,
        ] as $kunci => $nilai) {
            \App\Models\Pengaturan::updateOrCreate(
                ['kunci' => $kunci],
                ['nilai' => $nilai]
            );
        }

        ActivityLogger::log($request, 'Memperbarui pengaturan aplikasi', 'pengaturan', null, \App\Models\Pengaturan::class);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function exportCuti(Request $request)
    {
        $items = Cuti::with(['user', 'adminApprover', 'approver', 'pengganti']);

        if ($request->filled('status')) {
            $items->where('status', $request->status);
        }

        if ($request->filled('admin_status')) {
            $items->where('admin_status', $request->admin_status);
        }

        if ($request->filled('jenis_cuti')) {
            $items->where('jenis_cuti', $request->jenis_cuti);
        }

        $items = $items->orderBy('id_cuti', 'desc')->get();

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=cuti_' . date('Ymd_His') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Nama Petugas',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Jenis Cuti',
                'Alasan',
                'Alamat Cuti',
                'Pengganti',
                'Status Admin',
                'Approved Oleh Admin',
                'Tanggal Proses Admin',
                'Status Atasan',
                'Approved Oleh Atasan',
                'Tanggal Proses Atasan',
                'Created At',
            ]);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->id_cuti,
                    $item->user->nama ?? '-',
                    $item->tanggal_mulai?->format('Y-m-d') ?? '-',
                    $item->tanggal_selesai?->format('Y-m-d') ?? '-',
                    $item->jenis_cuti,
                    $item->alasan,
                    $item->alamat_cuti ?? '-',
                    $item->pengganti->nama ?? '-',
                    $item->admin_status ?? 'pending',
                    $item->adminApprover->nama ?? '-',
                    $item->admin_processed_at?->format('Y-m-d H:i:s') ?? '-',
                    $item->status,
                    $item->approver->nama ?? '-',
                    $item->approved_at?->format('Y-m-d H:i:s') ?? '-',
                    $item->created_at?->format('Y-m-d H:i:s') ?? '-',
                ]);
            }

            fclose($file);
        };

        ActivityLogger::log($request, 'Export cuti ke CSV', 'cuti', null, Cuti::class);

        return response()->stream($callback, 200, $headers);
    }

    public function logs(Request $request): View
    {
        return view('admin.logs', [
            'items' => ActivityLog::with('user')->orderBy('id_log', 'asc')->paginate($request->get('per_page', 15)),
        ]);
    }

    public function exportLogs(Request $request)
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=activity_log.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Waktu', 'User', 'Aktivitas', 'Modul', 'Status', 'IP', 'Device']);

            ActivityLog::with('user')->orderBy('created_at', 'asc')->chunk(500, function ($logs) use ($file) {
                foreach ($logs as $log) {
                    fputcsv($file, [
                        $log->created_at,
                        $log->user->nama ?? '-',
                        $log->aktivitas,
                        $log->modul,
                        $log->status,
                        $log->ip_address,
                        $log->device,
                    ]);
                }
            });
            fclose($file);
        };

        ActivityLogger::log($request, 'Export activity log ke CSV', 'activity_logs', null, ActivityLog::class);

        return response()->stream($callback, 200, $headers);
    }

    public function bukaAksesAbsen(Request $request): View
    {
        return view('admin.buka_absen', [
            'users' => User::whereHas('role', function ($q) {
                QueryFilters::whereRoleAlias($q, ['petugas', 'karyawan']);
            })->orderBy('nama')->get(),
            'items' => \App\Models\Absensi::with('user')
                ->whereDate('tanggal', today())
                ->where('status', 'akses_dibuka')
                ->orderBy('id_absensi', 'asc')
                ->paginate($request->get('per_page', 15)),
        ]);
    }

    public function storeAksesAbsen(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_user' => ['required', 'exists:users,id_user'],
        ]);

        $user = User::findOrFail($validated['id_user']);
        if (! $user->isPetugas()) {
            return back()->with('error', 'Akses absen telat hanya bisa diberikan kepada petugas.');
        }

        $absensi = \App\Models\Absensi::firstOrNew([
            'id_user' => $validated['id_user'],
            'tanggal' => today()->toDateString(),
        ]);

        if ($absensi->exists && $absensi->jam_masuk) {
            return back()->with('error', 'Petugas ini sudah absen masuk hari ini.');
        }

        if ($absensi->exists && $absensi->status === 'cuti') {
            return back()->with('error', 'Petugas ini sedang cuti hari ini, akses absen tidak bisa dibuka.');
        }

        $absensi->id_periode = optional(Periode::aktif())->id_periode;
        $absensi->status = 'akses_dibuka';
        $absensi->keterangan = 'Akses absen telat diberikan oleh Admin.';
        $absensi->save();

        ActivityLogger::log($request, 'Membuka akses absen telat', 'absensi', $absensi->id_absensi, \App\Models\Absensi::class);

        return back()->with('success', 'Akses absen telat berhasil diberikan untuk user tersebut hari ini.');
    }

    public function dataSensitif(Request $request): View
    {
        $query = User::with('userSensitive');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                QueryFilters::whereAnyLike($q, ['nama', 'email', 'username'], $search);
            });
        }

        return view('admin.data_sensitif', [
            'users' => $query->orderBy('id_user', 'asc')->paginate($request->get("per_page", 50))->withQueryString(),
        ]);
    }

    public function updateDataSensitif(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_user' => ['required', 'exists:users,id_user'],
            'nik' => ['nullable', 'string', 'max:20'],
        ]);

        $userSensitive = UserSensitive::firstOrNew(['id_user' => $validated['id_user']]);

        if (!empty($validated['nik'])) {
            $userSensitive->setNik($validated['nik']);
            $userSensitive->save();
        } else {
            if ($userSensitive->exists) {
                $userSensitive->delete();
            }
        }

        ActivityLogger::log($request, 'Mengubah NIK sensitif', 'user_sensitive', $validated['id_user'], UserSensitive::class);

        return back()->with('success', 'Data NIK sensitif berhasil disimpan.');
    }

    public function downloadUsersTemplate(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UsersTemplateExport, 'template_import_users.xlsx');
    }

    public function importUsers(Request $request): RedirectResponse
    {
        // Set timeout lebih lama untuk import besar
        set_time_limit(300); // 5 menit
        ini_set('memory_limit', '512M');
        
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        try {
            $import = new \App\Imports\UsersImport;
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));


            ActivityLogger::log($request, 'Import users dari Excel', 'user', null, User::class);

            return back()->with('success', 'Data user berhasil diimport dari Excel.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function bulkDeleteUsers(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id_user',
            'delete_nik_data' => 'nullable|boolean',
        ]);

        $userIds = $request->user_ids;
        $deleteNikData = $request->has('delete_nik_data');
        $currentUserId = $request->user()->id_user;

        // Prevent deleting yourself
        if (in_array($currentUserId, $userIds)) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri dalam bulk delete.');
        }

        // Hapus data NIK sensitif jika diminta
        if ($deleteNikData) {
            UserSensitive::whereIn('id_user', $userIds)->delete();
        }

        // Hapus users
        User::whereIn('id_user', $userIds)->delete();

        ActivityLogger::log($request, 'Bulk delete ' . count($userIds) . ' users', 'user', null, User::class);

        $message = count($userIds) . ' user berhasil dihapus';
        if ($deleteNikData) {
            $message .= ' beserta data NIK sensitif';
        }

        return back()->with('success', $message);
    }
}
