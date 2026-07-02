<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\TempatTugas;
use App\Support\QueryFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Mengelola proses autentikasi: login, registrasi akun awal/petugas,
 * dan logout user dari aplikasi.
 */
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
        $user = null;
        
        // Try to find user by email or username first (fast lookup)
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($field, $loginInput)->first();

        // If not found, try NIK lookup using indexed hash (fast & secure)
        if (!$user && preg_match('/^\d+$/', $loginInput)) {
            $nikHash = hash('sha256', $loginInput);
            $userSensitive = \App\Models\UserSensitive::where('nik_hash', $nikHash)->first();
            if ($userSensitive) {
                $user = $userSensitive->user;
            }
        }

        // Always check password even if user not found (prevent user enumeration)
        $passwordValid = false;
        if ($user) {
            $passwordValid = Hash::check($validated['password'], $user->password);
        } else {
            // Perform dummy hash check to maintain constant time
            Hash::check($validated['password'], '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
        }

        if ($user && $passwordValid) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()
            ->withErrors(['login' => 'Email/NIK atau password tidak sesuai.'])
            ->onlyInput('login');
    }

    public function showRegister(): View
    {
        abort_unless(User::count() === 0, 404);

        return view('auth.register', [
            'tempatTugas' => TempatTugas::orderBy('nama_tempat')->get(),
            'isFirstUser' => true,
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        abort_unless(User::count() === 0, 404);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'id_tempat' => ['nullable', 'exists:tempat_tugas,id_tempat'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $roleId = QueryFilters::whereRoleAlias(Role::query(), ['admin'])->value('id_role') ?? 1;

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

        return redirect('/dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
