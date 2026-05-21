<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Sanksi;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
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

    public function acknowledge(Request $request, int $id): RedirectResponse
    {
        $sanksi = Sanksi::where('id_user', $request->user()->id_user)->findOrFail($id);

        if (! $sanksi->acknowledged_at) {
            $sanksi->update(['acknowledged_at' => now()]);
            ActivityLogger::log($request, 'Approve/akui teguran', 'sanksi', $sanksi->id_sanksi, Sanksi::class);
        }

        return back()->with('success', 'Teguran berhasil dikonfirmasi.');
    }
}
