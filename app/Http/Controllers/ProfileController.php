<?php

namespace App\Http\Controllers;

use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Mengelola halaman profil user, pembaruan data dasar akun,
 * foto profil, dan penggantian password.
 */
class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $user->load(['role', 'tempatTugas', 'userSensitive']);

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
        $user = $request->user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($user->id_user, 'id_user'),
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id_user, 'id_user'),
            ],
            'foto_profil' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048', 'dimensions:max_width=2000,max_height=2000'],
            'foto_profil_live' => ['nullable', 'string'],
        ]);

        $user->nama = $validated['nama'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];

        $livePhotoInput = $request->input('foto_profil_live');
        $livePhoto = $this->decodeLivePhoto($livePhotoInput);

        if ($livePhotoInput && $livePhoto === null) {
            return back()
                ->withErrors(['foto_profil' => 'Foto live tidak valid atau terlalu besar.'])
                ->withInput();
        }

        if ($request->hasFile('foto_profil') || $livePhoto !== null) {
            // Hapus foto lama jika ada
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $path = $livePhoto !== null
                ? ImageOptimizer::storeBinary($livePhoto, 'profil', 512, 512, 78)
                : ImageOptimizer::storeUploaded($request->file('foto_profil'), 'profil', 512, 512, 78);

            if (! $path) {
                return back()->with('error', 'Foto profil gagal diproses.');
            }
            $user->foto_profil = $path;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = $request->user();
        $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    private function decodeLivePhoto(?string $dataUrl): ?string
    {
        if (! $dataUrl) {
            return null;
        }

        if (! preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $dataUrl)) {
            return null;
        }

        $encoded = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $decoded = base64_decode($encoded, true);

        if ($decoded === false || strlen($decoded) > 2 * 1024 * 1024) {
            return null;
        }

        return $decoded;
    }
}
