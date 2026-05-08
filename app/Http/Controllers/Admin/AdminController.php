<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kalender;
use App\Models\Periode;
use App\Models\Role;
use App\Models\Sanksi;
use App\Models\TempatTugas;
use App\Models\User;
use App\Models\UserSensitive;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function users(): View
    {
        return view('admin.users', [
            'items' => User::with(['role', 'tempatTugas'])->orderBy('nama')->paginate(25),
            'roles' => Role::orderBy('id_role')->get(),
            'tempatTugas' => TempatTugas::orderBy('nama_tempat')->get(),
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
        ]);

        $user = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLogger::log($request, 'Membuat user', 'users', $user->id_user, User::class);

        return back()->with('success', 'User berhasil dibuat.');
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

    public function tempat(): View
    {
        return view('admin.tempat', [
            'items' => TempatTugas::orderBy('nama_tempat')->paginate(15),
        ]);
    }

    public function storeTempat(Request $request): RedirectResponse
    {
        $tempat = TempatTugas::create($request->validate([
            'nama_tempat' => ['required', 'string', 'max:150'],
            'alamat' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]));

        ActivityLogger::log($request, 'Membuat tempat tugas', 'tempat_tugas', $tempat->id_tempat, TempatTugas::class);

        return back()->with('success', 'Tempat tugas berhasil dibuat.');
    }

    public function updateTempat(Request $request, int $id): RedirectResponse
    {
        $tempat = TempatTugas::findOrFail($id);
        $tempat->update($request->validate([
            'nama_tempat' => ['required', 'string', 'max:150'],
            'alamat' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]));

        ActivityLogger::log($request, 'Mengubah tempat tugas', 'tempat_tugas', $tempat->id_tempat, TempatTugas::class);

        return back()->with('success', 'Tempat tugas berhasil diperbarui.');
    }

    public function deleteTempat(Request $request, int $id): RedirectResponse
    {
        TempatTugas::findOrFail($id)->delete();
        ActivityLogger::log($request, 'Menghapus tempat tugas', 'tempat_tugas', $id, TempatTugas::class);

        return back()->with('success', 'Tempat tugas berhasil dihapus.');
    }

    public function periode(): View
    {
        return view('admin.periode', [
            'items' => Periode::orderByDesc('tanggal_mulai')->paginate(15),
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

    public function kalender(): View
    {
        return view('admin.kalender', [
            'items' => Kalender::orderByDesc('tanggal')->orderByDesc('id_kalender')->paginate(20),
        ]);
    }

    public function storeKalender(Request $request): RedirectResponse
    {
        $kalender = Kalender::create($request->validate([
            'tanggal' => ['required', 'date'],
            'nama_event' => ['nullable', 'string', 'max:150'],
            'jenis_event' => ['required', 'in:libur,kegiatan,cuti_bersama'],
            'keterangan' => ['nullable', 'string'],
        ]));

        ActivityLogger::log($request, 'Membuat kalender', 'kalender', $kalender->id_kalender, Kalender::class);

        return back()->with('success', 'Kalender berhasil dibuat.');
    }

    public function deleteKalender(Request $request, int $id): RedirectResponse
    {
        Kalender::findOrFail($id)->delete();
        ActivityLogger::log($request, 'Menghapus kalender', 'kalender', $id, Kalender::class);

        return back()->with('success', 'Kalender berhasil dihapus.');
    }

    public function sanksi(): View
    {
        return view('admin.sanksi', [
            'items' => Sanksi::with('user')->orderByDesc('tanggal')->orderByDesc('id_sanksi')->paginate(20),
        ]);
    }

    public function logs(): View
    {
        return view('admin.logs', [
            'items' => ActivityLog::with('user')->latest('id_log')->paginate(50),
        ]);
    }

    public function bukaAksesAbsen(): View
    {
        return view('admin.buka_absen', [
            'users' => User::whereHas('role', function ($q) {
                $q->where('nama_role', 'like', '%petugas%')
                  ->orWhere('nama_role', 'like', '%karyawan%');
            })->orderBy('nama')->get(),
            'items' => \App\Models\Absensi::with('user')
                ->whereDate('tanggal', today())
                ->where('status', 'akses_dibuka')
                ->latest('id_absensi')
                ->get(),
        ]);
    }

    public function storeAksesAbsen(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_user' => ['required', 'exists:users,id_user'],
        ]);

        $absensi = \App\Models\Absensi::firstOrNew([
            'id_user' => $validated['id_user'],
            'tanggal' => today()->toDateString(),
        ]);

        $absensi->id_periode = optional(Periode::aktif())->id_periode;
        $absensi->status = 'akses_dibuka';
        $absensi->keterangan = 'Akses absen telat diberikan oleh Admin.';
        $absensi->save();

        ActivityLogger::log($request, 'Membuka akses absen telat', 'absensi', $absensi->id_absensi, \App\Models\Absensi::class);

        return back()->with('success', 'Akses absen telat berhasil diberikan untuk user tersebut hari ini.');
    }

    public function dataSensitif(): View
    {
        return view('admin.data_sensitif', [
            'users' => User::with('userSensitive')->orderBy('nama')->paginate(50),
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
            $userSensitive->nik_encrypted = Crypt::encryptString($validated['nik']);
            $userSensitive->save();
        } else {
            if ($userSensitive->exists) {
                $userSensitive->delete();
            }
        }

        ActivityLogger::log($request, 'Mengubah NIK sensitif', 'user_sensitive', $validated['id_user'], UserSensitive::class);

        return back()->with('success', 'Data NIK sensitif berhasil disimpan.');
    }
}
