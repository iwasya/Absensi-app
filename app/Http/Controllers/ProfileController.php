<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $user->load(['tempatTugas', 'userSensitive']);

        $nikAsli = null;
        if ($user->userSensitive && $user->userSensitive->nik_encrypted) {
            try {
                $nikAsli = Crypt::decryptString($user->userSensitive->nik_encrypted);
            } catch (\Exception $e) {
                $nikAsli = 'Gagal mendekripsi NIK';
            }
        }

        return view('profile.index', [
            'user' => $user,
            'nik' => $nikAsli,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'foto_profil' => ['nullable', 'image', 'max:2048'], // max 2MB
        ]);

        $user = $request->user();

        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $path = $request->file('foto_profil')->store('profil', 'public');
            $user->foto_profil = $path;
            $user->save();
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
