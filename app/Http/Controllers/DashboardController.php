<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\Kalender;
use App\Models\Notifikasi;
use App\Models\Periode;
use App\Models\Role;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('role', 'tempatTugas');

        if ($user->isAdmin()) {
            $petugasRoleId = Role::where('nama_role', 'like', '%Petugas%')->value('id_role') ?? 1;

            return view('admin.dashboard', [
                'user' => $user,
                'totalUsers' => User::count(),
                'totalPetugas' => User::where('id_role', $petugasRoleId)->count(),
                'totalAbsensiHariIni' => Absensi::whereDate('tanggal', today())->count(),
                'cutiPending' => Cuti::where('status', 'pending')->count(),
                'tugasPending' => Tugas::where('status', 'pending')->count(),
                'periodeAktif' => Periode::aktif(),
            ]);
        }

        if ($user->isAtasan()) {
            return view('atasan.dashboard', [
                'user' => $user,
                'absensiHariIni' => Absensi::with('user')->whereDate('tanggal', today())->latest('id_absensi')->limit(10)->get(),
                'cutiPending' => Cuti::with('user')->where('status', 'pending')->latest('id_cuti')->limit(10)->get(),
                'tugasPending' => Tugas::with('user')->where('status', 'pending')->latest('id_tugas')->limit(10)->get(),
            ]);
        }

        return view('petugas.dashboard', [
            'user' => $user,
            'absensiHariIni' => Absensi::where('id_user', $user->id_user)->whereDate('tanggal', today())->first(),
            'cutiTerakhir' => Cuti::where('id_user', $user->id_user)->latest('id_cuti')->limit(5)->get(),
            'tugasTerakhir' => Tugas::where('id_user', $user->id_user)->latest('id_tugas')->limit(5)->get(),
            'notifikasiBelumBaca' => Notifikasi::where('id_user', $user->id_user)->where('status_baca', false)->count(),
            'kalender' => Kalender::whereDate('tanggal', '>=', today())->orderBy('tanggal')->limit(5)->get(),
        ]);
    }
}
