<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Mengelola daftar notifikasi user serta aksi menandai notifikasi
 * sebagai sudah dibaca, baik satuan maupun massal.
 */
class NotifikasiController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifikasi', [
            'items' => Notifikasi::where('id_user', $request->user()->id_user)->latest('id_notifikasi')->paginate(20),
        ]);
    }

    public function read(Request $request, int $id): RedirectResponse|JsonResponse
    {
        Notifikasi::where('id_user', $request->user()->id_user)
            ->where('id_notifikasi', $id)
            ->update(['status_baca' => true]);
        $this->clearNotificationCache($request->user()->id_user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => Notifikasi::where('id_user', $request->user()->id_user)
                    ->where('status_baca', false)
                    ->count(),
            ]);
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function readAll(Request $request): RedirectResponse|JsonResponse
    {
        Notifikasi::where('id_user', $request->user()->id_user)
            ->where('status_baca', false)
            ->update(['status_baca' => true]);
        $this->clearNotificationCache($request->user()->id_user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    private function clearNotificationCache(int $userId): void
    {
        Cache::forget("notifikasi:unread-count:{$userId}");
        Cache::forget("notifikasi:header:{$userId}");
    }
}
