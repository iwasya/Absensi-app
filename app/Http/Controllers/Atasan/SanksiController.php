<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Sanksi;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SanksiController extends Controller
{
    public function index(): View
    {
        return view('atasan.sanksi', [
            'items' => Sanksi::with('user')->orderByDesc('tanggal')->orderByDesc('id_sanksi')->paginate(20),
            'users' => User::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $sanksi = Sanksi::create($request->validate([
            'id_user' => ['required', 'exists:users,id_user'],
            'jenis_sanksi' => ['nullable', 'string', 'max:100'],
            'tanggal' => ['nullable', 'date'],
            'keterangan' => ['nullable', 'string'],
        ]));

        ActivityLogger::log($request, 'Membuat sanksi', 'sanksi', $sanksi->id_sanksi, Sanksi::class);

        return back()->with('success', 'Sanksi berhasil dibuat.');
    }

    public function delete(Request $request, int $id): RedirectResponse
    {
        Sanksi::findOrFail($id)->delete();
        ActivityLogger::log($request, 'Menghapus sanksi', 'sanksi', $id, Sanksi::class);

        return back()->with('success', 'Sanksi berhasil dihapus.');
    }
}
