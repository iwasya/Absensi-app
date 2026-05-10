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
    public function index(Request $request): View
    {
        $items = Sanksi::with('user');

        if ($request->filled('month')) {
            $items->whereMonth('tanggal', $request->month);
        }

        if ($request->filled('id_user')) {
            $items->where('id_user', $request->id_user);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $items->where(function($q) use ($search) {
                $q->where('jenis_sanksi', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        return view('atasan.sanksi', [
            'items' => $items->orderByDesc('tanggal')->orderByDesc('id_sanksi')->paginate(20)->withQueryString(),
            'users' => User::orderBy('nama')->get(),
        ]);
    }

    public function print(Request $request): View
    {
        $items = Sanksi::with('user');

        if ($request->filled('month')) {
            $items->whereMonth('tanggal', $request->month);
        }

        if ($request->filled('id_user')) {
            $items->where('id_user', $request->id_user);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $items->where(function($q) use ($search) {
                $q->where('jenis_sanksi', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $items = $items->orderByDesc('tanggal')->get();

        return view('atasan.sanksi_print', [
            'items' => $items,
            'month' => $request->month,
            'selectedUser' => $request->id_user ? User::find($request->id_user) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $sanksi = Sanksi::create($request->validate([
            'id_user' => ['required', 'exists:users,id_user'],
            'jenis_sanksi' => ['required', 'string', 'max:100'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
        ]));

        \App\Models\Notifikasi::create([
            'id_user' => $sanksi->id_user,
            'judul' => 'Sanksi Baru Diterima',
            'pesan' => "Anda telah menerima sanksi: {$sanksi->jenis_sanksi}. Silakan cek riwayat sanksi Anda.",
            'tipe' => 'system',
            'status_baca' => false,
            'reference_id' => $sanksi->id_sanksi,
            'reference_type' => Sanksi::class,
        ]);

        ActivityLogger::log($request, 'Membuat sanksi', 'sanksi', $sanksi->id_sanksi, Sanksi::class);

        return back()->with('success', 'Sanksi berhasil dibuat dan notifikasi telah dikirim.');
    }

    public function delete(Request $request, int $id): RedirectResponse
    {
        Sanksi::findOrFail($id)->delete();
        ActivityLogger::log($request, 'Menghapus sanksi', 'sanksi', $id, Sanksi::class);

        return back()->with('success', 'Sanksi berhasil dihapus.');
    }
}
