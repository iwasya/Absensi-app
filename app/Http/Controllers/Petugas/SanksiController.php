<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Sanksi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SanksiController extends Controller
{
    public function index(Request $request): View
    {
        $items = Sanksi::where('id_user', $request->user()->id_user)
            ->orderByDesc('tanggal')
            ->orderByDesc('id_sanksi')
            ->paginate($request->get("per_page", 20));

        return view('petugas.sanksi', [
            'items' => $items,
        ]);
    }
}
