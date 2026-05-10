<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
}
