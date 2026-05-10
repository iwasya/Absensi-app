<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\TempatTugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = $validated['login'];
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($field, $loginInput)->first();

        // Coba cari dari tabel sensitif (NIK) jika tidak ketemu dari users
        if (!$user) {
            $sensitives = \App\Models\UserSensitive::all();
            foreach ($sensitives as $sensitive) {
                if ($sensitive->nik_encrypted) {
                    try {
                        $decrypted = \Illuminate\Support\Facades\Crypt::decryptString($sensitive->nik_encrypted);
                        if ($decrypted === $loginInput) {
                            $user = User::find($sensitive->id_user);
                            break;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        if ($user && Hash::check($validated['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['login' => 'Email/Username/NIK atau password tidak sesuai.'])
            ->onlyInput('login');
    }

    public function showRegister(): View
    {
        return view('auth.register', [
            'tempatTugas' => TempatTugas::orderBy('nama_tempat')->get(),
            'isFirstUser' => User::count() === 0,
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'id_tempat' => ['nullable', 'exists:tempat_tugas,id_tempat'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $roleId = User::count() === 0
            ? (Role::where('nama_role', 'like', '%Admin%')->value('id_role') ?? 2)
            : (Role::where('nama_role', 'like', '%Petugas%')->value('id_role') ?? 1);

        $user = User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'id_role' => $roleId,
            'id_tempat' => $validated['id_tempat'] ?? null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
