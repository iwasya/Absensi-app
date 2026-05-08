<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function setPeriode(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'global_periode_id' => 'required|exists:periode,id_periode',
        ]);
        
        session(['global_periode_id' => $request->global_periode_id]);
        
        return back();
    }
}
