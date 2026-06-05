@php
    $appName = config('app.name', 'Absensi PPSU') ?: 'Absensi PPSU';
    $user = auth()->user();
    $isAtasanArea = request()->is('atasan') || request()->is('atasan/*');
    $isAdminArea = request()->is('admin') || request()->is('admin/*');
    $targetArea = $isAtasanArea ? 'atasan' : ($isAdminArea ? 'admin' : 'halaman ini');

    $homeUrl = url('/dashboard');
    $homeLabel = 'Kembali ke Dashboard';

    if ($user?->isPetugas() && Route::has('petugas.absensi.index')) {
        $homeUrl = route('petugas.absensi.index');
        $homeLabel = 'Buka Halaman Petugas';
    } elseif ($user?->isAtasan() && Route::has('atasan.absensi.index')) {
        $homeUrl = route('atasan.absensi.index');
        $homeLabel = 'Buka Halaman Atasan';
    } elseif ($user?->isAdmin() && Route::has('admin.users.index')) {
        $homeUrl = route('admin.users.index');
        $homeLabel = 'Buka Halaman Admin';
    }

    $title = 'Akses tidak sesuai peran akun';
    $message = 'Halaman ini hanya tersedia untuk akun dengan peran yang sesuai. Data tetap aman dan sesi kamu masih aktif.';

    if ($user?->isPetugas() && $isAtasanArea) {
        $title = 'Area atasan tidak tersedia untuk akun petugas';
        $message = 'Akun petugas dipakai untuk absensi, tugas harian, cuti, dan informasi regu. Untuk membuka area atasan, silakan gunakan akun atasan yang sudah diberi izin.';
    } elseif ($user?->isPetugas() && $isAdminArea) {
        $title = 'Area admin tidak tersedia untuk akun petugas';
        $message = 'Akun petugas tidak punya izin mengelola data master atau pengaturan aplikasi. Gunakan akun admin jika memang perlu membuka area ini.';
    } elseif (! $user) {
        $title = 'Sesi belum dikenali';
        $message = 'Silakan login ulang agar sistem bisa memastikan peran akun kamu sebelum membuka halaman ini.';
        $homeUrl = url('/login');
        $homeLabel = 'Login Ulang';
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 | {{ $appName }}</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f0f4f8;
            --panel: #ffffff;
            --text: #172033;
            --muted: #64748b;
            --line: #dbe5ef;
            --primary: #0e7490;
            --primary-dark: #155e75;
            --primary-soft: #cffafe;
            --amber: #f59e0b;
            --amber-soft: #fff7d6;
            --green: #059669;
            --green-soft: #dcfce7;
            --red-soft: #fee2e2;
            --red: #dc2626;
            --shadow: 0 24px 80px rgba(15, 23, 42, .12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'DM Sans', Arial, sans-serif;
            color: var(--text);
            background:
                linear-gradient(180deg, rgba(14, 116, 144, .10), rgba(5, 150, 105, .07) 48%, rgba(245, 158, 11, .08)),
                var(--bg);
            display: grid;
            place-items: center;
            padding: 22px;
        }

        .notice {
            width: min(920px, 100%);
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .notice-head {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 24px;
            padding: 34px;
            background:
                linear-gradient(135deg, rgba(207, 250, 254, .92), rgba(255, 247, 214, .76)),
                #ffffff;
            border-bottom: 1px solid var(--line);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 7px 11px;
            border-radius: 999px;
            color: var(--primary-dark);
            background: rgba(255, 255, 255, .7);
            border: 1px solid rgba(14, 116, 144, .18);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .eyebrow svg { width: 15px; height: 15px; }

        h1 {
            margin: 18px 0 12px;
            max-width: 680px;
            font-size: clamp(30px, 5vw, 54px);
            line-height: 1;
            letter-spacing: 0;
        }

        p {
            margin: 0;
            max-width: 670px;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.65;
        }

        .code {
            align-self: start;
            min-width: 118px;
            padding: 16px 18px;
            border-radius: 14px;
            background: var(--primary-dark);
            color: #ffffff;
            text-align: center;
            box-shadow: 0 14px 40px rgba(21, 94, 117, .22);
        }

        .code strong {
            display: block;
            font-family: 'DM Mono', monospace;
            font-size: 38px;
            line-height: 1;
        }

        .code span {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: rgba(255, 255, 255, .76);
        }

        .notice-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .info {
            padding: 28px 34px 32px;
            border-right: 1px solid var(--line);
        }

        .info h2,
        .steps h2 {
            margin: 0 0 14px;
            font-size: 15px;
            line-height: 1.3;
        }

        .facts {
            display: grid;
            gap: 10px;
        }

        .fact {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fbfdff;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.45;
        }

        .fact svg {
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
            color: var(--green);
        }

        .steps {
            padding: 28px 34px 32px;
        }

        .role-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            max-width: 100%;
            margin-bottom: 16px;
            padding: 9px 12px;
            border-radius: 10px;
            background: var(--amber-soft);
            border: 1px solid #fde68a;
            color: #8a5200;
            font-size: 13px;
            font-weight: 600;
        }

        .role-pill svg { width: 17px; height: 17px; flex: 0 0 auto; }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 22px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid transparent;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .button svg { width: 17px; height: 17px; }
        .button.primary { background: var(--primary); color: #ffffff; }
        .button.primary:hover { background: var(--primary-dark); }
        .button.secondary { background: #ffffff; color: var(--text); border-color: var(--line); }
        .button.secondary:hover { background: #f8fafc; }

        .logout-form { display: inline; }

        .footnote {
            margin-top: 18px;
            color: var(--muted);
            font-size: 12.5px;
            line-height: 1.5;
        }

        @media (max-width: 760px) {
            body { padding: 14px; }
            .notice-head { grid-template-columns: 1fr; padding: 26px; }
            .code { width: fit-content; }
            .notice-body { grid-template-columns: 1fr; }
            .info { border-right: 0; border-bottom: 1px solid var(--line); }
            .info, .steps { padding: 24px 26px; }
            .actions { flex-direction: column; }
            .button { width: 100%; }
        }
    </style>
</head>
<body>
    <main class="notice" role="main">
        <section class="notice-head" aria-labelledby="error-title">
            <div>
                <div class="eyebrow">
                    <svg fill="none" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l8 4v5c0 4.5-3.2 7.5-8 9-4.8-1.5-8-4.5-8-9V7l8-4z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M9.5 12l1.8 1.8 3.7-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Akses dibatasi
                </div>
                <h1 id="error-title">{{ $title }}</h1>
                <p>{{ $message }}</p>
            </div>
            <div class="code" aria-label="Kode error 403">
                <strong>403</strong>
                <span>Forbidden</span>
            </div>
        </section>

        <section class="notice-body" aria-label="Detail akses">
            <div class="info">
                <h2>Yang terjadi</h2>
                <div class="facts">
                    <div class="fact">
                        <svg fill="none" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L10 17l-5-5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Sistem berhasil mengenali akun kamu, tetapi perannya tidak cocok untuk membuka area {{ $targetArea }}.
                    </div>
                    <div class="fact">
                        <svg fill="none" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7L10 17l-5-5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Halaman lain yang sesuai dengan peran akun tetap bisa digunakan seperti biasa.
                    </div>
                </div>
            </div>

            <div class="steps">
                <h2>Langkah berikutnya</h2>
                <div class="role-pill">
                    <svg fill="none" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v4m0 4h.01M10.3 4.3L2.6 18a2 2 0 001.7 3h15.4a2 2 0 001.7-3L13.7 4.3a2 2 0 00-3.4 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $user ? 'Login sebagai: ' . (optional($user->role)->nama_role ?? 'Pengguna') : 'Akun belum login' }}
                </div>
                <p>Gunakan menu yang tersedia untuk peran akun kamu. Jika akses ini memang dibutuhkan, hubungi admin agar role akun diperiksa.</p>
                <div class="actions">
                    <a class="button primary" href="{{ $homeUrl }}">
                        <svg fill="none" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12l9-8 9 8M5 10.5V20h14v-9.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ $homeLabel }}
                    </a>
                    @auth
                        <form class="logout-form" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="button secondary" type="submit">
                                <svg fill="none" viewBox="0 0 24 24" aria-hidden="true"><path d="M15 17l5-5-5-5M20 12H9m3 8H6a2 2 0 01-2-2V6a2 2 0 012-2h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Ganti Akun
                            </button>
                        </form>
                    @endauth
                </div>
                <div class="footnote">Kode 403 berarti halaman sengaja dilindungi, bukan error pada data absensi.</div>
            </div>
        </section>
    </main>
</body>
</html>
