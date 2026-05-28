<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\User;
use App\Support\QueryFilters;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Mengelola master shift dan penempatan shift petugas,
 * termasuk tambah/edit shift, aktivasi, assignment, bulk assignment, dan export.
 */
class AdminSiftController extends Controller
{
    /**
     * Halaman utama SIFT - Kelola jam shift kerja
     */
    public function index(Request $request): View
    {
        $activeTab = $request->query('tab', 'shifts');

        // Get shifts (master data)
        $shifts = Shift::orderBy('urutan')->orderBy('id')->get();

        // Get users grouped by shift
        $users = User::with(['role', 'tempatTugas'])
            ->whereHas('role', function ($q) {
                QueryFilters::whereRoleAlias($q, ['petugas', 'karyawan']);
            })
            ->orderBy('regu')
            ->orderBy('nama')
            ->get();

        return view('admin.sift', [
            'shifts' => $shifts,
            'users' => $users,
            'activeTab' => $activeTab,
        ]);
    }

    /**
     * Simpan shift baru
     */
    public function storeShift(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_shift' => ['required', 'string', 'max:50', 'unique:shifts,nama_shift'],
            'jam_masuk' => ['required', 'date_format:H:i'],
            'durasi_jam' => ['required', 'integer', 'min:1', 'max:24'],
            'warna' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ], [
            'nama_shift.unique' => 'Nama shift sudah ada.',
            'jam_masuk.date_format' => 'Format jam harus HH:MM (contoh: 07:00)',
        ]);

        // Hitung jam pulang otomatis
        $jamPulang = \Carbon\Carbon::parse($validated['jam_masuk'])
            ->addHours($validated['durasi_jam'])
            ->format('H:i');

        $shift = Shift::create([
            'nama_shift' => $validated['nama_shift'],
            'jam_masuk' => $validated['jam_masuk'],
            'jam_pulang' => $jamPulang,
            'durasi_jam' => $validated['durasi_jam'],
            'warna' => $validated['warna'] ?? '#3B82F6',
            'status' => true,
            'urutan' => Shift::max('urutan') + 1,
        ]);

        \App\Support\ActivityLogger::log($request, "Membuat shift {$shift->nama_shift}", 'shifts', $shift->id, Shift::class);

        return back()->with('success', "Shift {$shift->nama_shift} berhasil dibuat ({$validated['jam_masuk']} - {$jamPulang})");
    }

    /**
     * Update shift
     */
    public function updateShift(Request $request, int $id): RedirectResponse
    {
        $shift = Shift::findOrFail($id);

        $validated = $request->validate([
            'nama_shift' => ['required', 'string', 'max:50', 'unique:shifts,nama_shift,' . $shift->id],
            'jam_masuk' => ['required', 'date_format:H:i'],
            'durasi_jam' => ['required', 'integer', 'min:1', 'max:24'],
            'warna' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        // Hitung jam pulang otomatis
        $jamPulang = \Carbon\Carbon::parse($validated['jam_masuk'])
            ->addHours($validated['durasi_jam'])
            ->format('H:i');

        $oldName = $shift->nama_shift;
        $shift->update([
            'nama_shift' => $validated['nama_shift'],
            'jam_masuk' => $validated['jam_masuk'],
            'jam_pulang' => $jamPulang,
            'durasi_jam' => $validated['durasi_jam'],
            'warna' => $validated['warna'] ?? $shift->warna,
        ]);

        // Update semua user yang pakai shift lama ke shift baru
        if ($oldName !== $validated['nama_shift']) {
            User::where('shift', $oldName)->update(['shift' => $validated['nama_shift']]);
        }

        \App\Support\ActivityLogger::log($request, "Update shift {$shift->nama_shift}", 'shifts', $shift->id, Shift::class);

        return back()->with('success', "Shift berhasil diupdate ({$validated['jam_masuk']} - {$jamPulang})");
    }

    /**
     * Toggle status shift (aktif/nonaktif)
     */
    public function toggleShift(Request $request, int $id): RedirectResponse
    {
        $shift = Shift::findOrFail($id);
        $shift->update(['status' => !$shift->status]);

        \App\Support\ActivityLogger::log($request, $shift->status ? "Aktifkan shift {$shift->nama_shift}" : "Nonaktifkan shift {$shift->nama_shift}", 'shifts', $shift->id, Shift::class);

        return back()->with('success', "Shift {$shift->nama_shift} " . ($shift->status ? 'diaktifkan' : 'dinonaktifkan'));
    }

    /**
     * Hapus shift
     */
    public function destroyShift(Request $request, int $id): RedirectResponse
    {
        $shift = Shift::findOrFail($id);

        // Cek apakah ada user yang pakai shift ini
        $userCount = User::where('shift', $shift->nama_shift)->count();
        if ($userCount > 0) {
            return back()->with('error', "Tidak bisa hapus shift. Ada {$userCount} petugas yang masih menggunakan shift ini.");
        }

        $nama = $shift->nama_shift;
        $shift->delete();

        \App\Support\ActivityLogger::log($request, "Hapus shift {$nama}", 'shifts', $id, Shift::class);

        return back()->with('success', "Shift {$nama} berhasil dihapus");
    }

    /**
     * Assign shift ke petugas (individual)
     */
    public function assignShift(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id_user'],
            'shift' => ['nullable', 'string', 'max:50'],
        ]);

        $user = User::findOrFail($validated['user_id']);
        $newShift = $validated['shift'] ?? null;
        $oldShift = $user->shift;

        $user->update(['shift' => $newShift]);

        \App\Support\ActivityLogger::log(
            $request,
            $newShift ? "Assign shift {$newShift} ke {$user->nama}" : "Hapus shift dari {$user->nama}",
            'users',
            $user->id_user,
            User::class
        );

        $message = $newShift
            ? "Shift {$newShift} berhasil ditetapkan ke {$user->nama}"
            : "Shift berhasil dihapus dari {$user->nama}";

        return back()->with('success', $message);
    }

    /**
     * Bulk assign shift ke beberapa petugas
     */
    public function bulkAssignShift(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', 'exists:users,id_user'],
            'shift' => ['nullable', 'string', 'max:50'],
        ]);

        $count = User::whereIn('id_user', $validated['user_ids'])
            ->update(['shift' => $validated['shift'] ?? null]);

        \App\Support\ActivityLogger::log(
            $request,
            $validated['shift']
                ? "Bulk assign shift {$validated['shift']} ke {$count} petugas"
                : "Bulk hapus shift dari {$count} petugas",
            'users',
            null,
            User::class
        );

        return back()->with('success', "Shift berhasil diupdate untuk {$count} petugas");
    }

    /**
     * Export data shift ke CSV
     */
    public function export(Request $request)
    {
        $shifts = Shift::orderBy('urutan')->orderBy('id')->get();
        $users = User::with(['role', 'tempatTugas'])
            ->whereHas('role', function ($q) {
                QueryFilters::whereRoleAlias($q, ['petugas', 'karyawan']);
            })
            ->orderBy('shift')
            ->orderBy('nama')
            ->get();

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=sift_' . date('Ymd_His') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($shifts, $users) {
            $file = fopen('php://output', 'w');

            // Section: Master Shift
            fputcsv($file, ['=== MASTER SHIFT ===']);
            fputcsv($file, ['Nama Shift', 'Jam Masuk', 'Jam Pulang', 'Durasi (Jam)', 'Warna', 'Status']);
            foreach ($shifts as $shift) {
                fputcsv($file, [
                    $shift->nama_shift,
                    $shift->jam_masuk ? \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') : '-',
                    $shift->jam_pulang ? \Carbon\Carbon::parse($shift->jam_pulang)->format('H:i') : '-',
                    $shift->durasi_jam,
                    $shift->warna,
                    $shift->status ? 'Aktif' : 'Nonaktif',
                ]);
            }

            // Empty row
            fputcsv($file, []);

            // Section: Petugas & Shift
            fputcsv($file, ['=== DAFTAR PETUGAS & SHIFT ===']);
            fputcsv($file, ['ID', 'Nama', 'Username', 'Regu', 'Tempat Tugas', 'Shift', 'Status']);

            $usersByShift = $users->groupBy(fn ($u) => $u->shift ?: '(Tanpa Shift)');
            foreach ($usersByShift as $shiftName => $group) {
                foreach ($group as $item) {
                    fputcsv($file, [
                        $item->id_user,
                        $item->nama,
                        $item->username,
                        $item->regu ?? '-',
                        $item->tempatTugas->nama_tempat ?? '-',
                        $item->shift ?? '-',
                        $item->status_aktif ?? 'aktif',
                    ]);
                }
            }

            fclose($file);
        };

        \App\Support\ActivityLogger::log($request, 'Export SIFT ke CSV', 'shifts', null, Shift::class);

        return response()->stream($callback, 200, $headers);
    }
}
