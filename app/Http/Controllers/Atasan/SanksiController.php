<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Sanksi;
use App\Models\User;
use App\Support\ActivityLogger;
use App\Support\QueryFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Mengelola sanksi dari sisi atasan: melihat, mencetak,
 * membuat sanksi untuk petugas, dan menghapus sanksi yang masih boleh dihapus.
 */
class SanksiController extends Controller
{
    /**
     * Menampilkan daftar sanksi dengan filter bulan, petugas, dan pencarian.
     */
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
                QueryFilters::whereLike($q, 'jenis_sanksi', $search);
                QueryFilters::orWhereLike($q, 'keterangan', $search);
                $q->orWhereHas('user', function($qu) use ($search) {
                      QueryFilters::whereLike($qu, 'nama', $search);
                  });
            });
        }

        return view('atasan.sanksi', [
            'items' => $items->orderByDesc('tanggal')->orderByDesc('id_sanksi')->paginate($request->get("per_page", 20))->withQueryString(),
            'users' => User::orderBy('nama')->get(),
        ]);
    }

    /**
     * Menyiapkan data sanksi untuk halaman cetak sesuai filter yang dipilih.
     */
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
                QueryFilters::whereLike($q, 'jenis_sanksi', $search);
                QueryFilters::orWhereLike($q, 'keterangan', $search);
                $q->orWhereHas('user', function($qu) use ($search) {
                      QueryFilters::whereLike($qu, 'nama', $search);
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

    /**
     * Membuat sanksi baru untuk petugas dan mengirim notifikasi ke petugas terkait.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_user' => ['required', 'exists:users,id_user'],
            'jenis_sanksi' => ['required', 'string', 'max:100'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
        ]);

        // Verify user is petugas (atasan should only give sanksi to petugas)
        $targetUser = User::findOrFail($validated['id_user']);
        if (!$targetUser->isPetugas()) {
            return back()->with('error', 'Sanksi hanya dapat diberikan kepada petugas.');
        }

        $sanksi = Sanksi::create($validated);

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

    /**
     * Menghapus sanksi yang masih berada dalam batas waktu penghapusan.
     */
    public function delete(Request $request, int $id): RedirectResponse
    {
        $sanksi = Sanksi::findOrFail($id);
        
        // Authorization: Only allow deletion if sanksi was created recently (within 24 hours)
        // This prevents manipulation of historical data
        if ($sanksi->created_at && $sanksi->created_at->diffInHours(now()) > 24) {
            return back()->with('error', 'Sanksi yang sudah lebih dari 24 jam tidak dapat dihapus.');
        }

        $sanksi->delete();
        ActivityLogger::log($request, 'Menghapus sanksi', 'sanksi', $id, Sanksi::class);

        return back()->with('success', 'Sanksi berhasil dihapus.');
    }
}
