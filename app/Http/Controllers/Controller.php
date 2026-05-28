<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Controller dasar yang dipakai controller lain untuk mewarisi fitur umum Laravel
 * dan aksi bersama seperti pemilihan periode global.
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Menyimpan periode aktif pilihan user ke session agar filter periode konsisten
     * di berbagai halaman.
     */
    public function setPeriode(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'global_periode_id' => 'required|exists:periode,id_periode',
        ]);
        
        session(['global_periode_id' => $request->global_periode_id]);
        
        return back();
    }
}
