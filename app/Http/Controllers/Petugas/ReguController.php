<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\User;
use App\Support\QueryFilters;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReguController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load(['role', 'tempatTugas']);
        $shift = $user->shift
            ? Shift::where('nama_shift', $user->shift)->first()
            : null;

        $anggotaRegu = collect();
        if ($user->regu) {
            $anggotaRegu = User::with(['role', 'tempatTugas'])
                ->whereHas('role', function ($query) {
                    QueryFilters::whereRoleAlias($query, ['petugas', 'karyawan']);
                })
                ->where('regu', $user->regu)
                ->orderByDesc('is_ketua_regu')
                ->orderBy('nama')
                ->get();
        }

        return view('petugas.regu', [
            'user' => $user,
            'shift' => $shift,
            'anggotaRegu' => $anggotaRegu,
            'ketuaRegu' => $anggotaRegu->firstWhere('is_ketua_regu', true),
        ]);
    }
}
