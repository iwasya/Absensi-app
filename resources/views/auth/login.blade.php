<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:           #F0F4F8;
            --card:         #FFFFFF;
            --accent:       #0EA5C9;
            --accent2:      #0284A8;
            --accent-soft:  #E0F5FB;
            --accent-border:#BAE8F5;
            --sidebar:      #1A2E3B;
            --green:        #10B981;
            --green-soft:   #D1FAE5;
            --green-dark:   #065F46;
            --red:          #EF4444;
            --red-soft:     #FEE2E2;
            --text:         #1E293B;
            --text2:        #475569;
            --text3:        #94A3B8;
            --border:       #E2E8F0;
            --border2:      #CBD5E1;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 16px;
        }

        /* ── CARD ── */
        .panel {
            width: min(440px, 100%);
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 4px 32px rgba(14, 165, 201, 0.08), 0 1px 4px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        /* ── HEADER STRIP ── */
        .panel-header {
            background: var(--sidebar);
            padding: 28px 32px 24px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .logo-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .logo-box svg {
            width: 22px;
            height: 22px;
            color: #fff;
        }
        .header-text h1 {
            font-size: 15px;
            font-weight: 600;
            color: #ffffff;
            line-height: 1.3;
        }
        .header-text p {
            font-size: 11px;
            color: #64A8C0;
            margin-top: 2px;
        }

        /* ── BODY ── */
        .panel-body {
            padding: 28px 32px 32px;
        }

        .welcome-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 4px;
        }
        .welcome-sub {
            font-size: 13px;
            color: var(--text3);
            margin-bottom: 24px;
        }

        /* ── FORM ── */
        .form-group {
            margin-bottom: 16px;
        }
        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text2);
            margin-bottom: 6px;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: var(--text3);
            pointer-events: none;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px 10px 38px;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            border: 1px solid var(--border2);
            border-radius: 9px;
            background: var(--bg);
            color: var(--text);
            outline: none;
            transition: border-color .18s, box-shadow .18s;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--accent);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(14,165,201,.12);
        }
        input::placeholder { color: var(--text3); }

        /* ── REMEMBER + FORGOT ── */
        .check-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 18px 0;
        }
        .check-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text2);
            cursor: pointer;
        }
        .check-label input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--accent);
            cursor: pointer;
        }
        .forgot-link {
            font-size: 13px;
            font-weight: 500;
            color: var(--accent);
            text-decoration: none;
            transition: color .15s;
        }
        .forgot-link:hover { color: var(--accent2); }

        /* ── SUBMIT BUTTON ── */
        .btn-submit {
            width: 100%;
            border: 0;
            border-radius: 9px;
            background: var(--accent);
            color: #ffffff;
            padding: 12px 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .18s, transform .1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-submit:hover  { background: var(--accent2); }
        .btn-submit:active { transform: scale(.98); }
        .btn-submit svg {
            width: 16px;
            height: 16px;
        }

        /* ── ERROR ── */
        .error-msg {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
            font-size: 12px;
            color: var(--red);
            background: var(--red-soft);
            border-radius: 6px;
            padding: 6px 10px;
        }
        .error-msg svg {
            width: 13px;
            height: 13px;
            flex-shrink: 0;
        }

        /* ── ALERT (session error) ── */
        .alert-error {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 10px 14px;
            background: var(--red-soft);
            border: 1px solid #FECACA;
            border-radius: 9px;
            font-size: 13px;
            color: #B91C1C;
            margin-bottom: 20px;
        }
        .alert-error svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ── DIVIDER ── */
        .divider {
            height: 1px;
            background: var(--border);
            margin: 24px 0;
        }

        /* ── FOOTER INFO ── */
        .panel-foot {
            text-align: center;
            font-size: 12px;
            color: var(--text3);
            padding-top: 4px;
        }
        .panel-foot a {
            color: var(--accent);
            font-weight: 500;
            text-decoration: none;
        }
        .panel-foot a:hover { color: var(--accent2); }

        /* ── INFO BADGE ── */
        .info-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--accent-soft);
            border: 1px solid var(--accent-border);
            border-radius: 9px;
            padding: 10px 14px;
            font-size: 12px;
            color: var(--accent2);
            margin-bottom: 20px;
        }
        .info-badge svg {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

<main class="panel">

    <!-- Header -->
    <div class="panel-header">
        <div class="logo-box">
            <svg fill="none" viewBox="0 0 22 22">
                <path d="M3 11a8 8 0 1016 0A8 8 0 003 11z" stroke="#fff" stroke-width="1.5"/>
                <path d="M11 7v4l3 2.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="header-text">
            <h1>Sistem Presensi & Kinerja Pendamsos</h1>
            <p>Dinas Sosial Provinsi DKI Jakarta</p>
        </div>
    </div>

    <!-- Body -->
    <div class="panel-body">

        <div class="welcome-title">Selamat datang</div>
        <div class="welcome-sub">Masuk untuk mengakses sistem absensi PPSU.</div>

        {{-- Session error --}}
        @if (session('error'))
            <div class="alert-error">
                <svg fill="none" viewBox="0 0 16 16">
                    <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/>
                    <path d="M8 5v3M8 10.5v.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Info --}}
        <div class="info-badge">
            <svg fill="none" viewBox="0 0 16 16">
                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/>
                <path d="M8 7v4M8 5.5v.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            Khusus petugas Pendamsos, Admin, dan Kasie Ekbang
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email / Username --}}
            <div class="form-group">
                <label for="login">Email atau Username</label>
                <div class="input-wrap">
                    <svg fill="none" viewBox="0 0 16 16">
                        <circle cx="8" cy="6" r="3" stroke="currentColor" stroke-width="1.3"/>
                        <path d="M2 14c0-3.314 2.686-5 6-5s6 1.686 6 5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                    </svg>
                    <input
                        id="login"
                        type="text"
                        name="login"
                        value="{{ old('login') }}"
                        placeholder="Masukkan email atau username"
                        required
                        autofocus
                        autocomplete="username"
                    >
                </div>
                @error('login')
                    <div class="error-msg">
                        <svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3M8 10v.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <svg fill="none" viewBox="0 0 16 16">
                        <rect x="3" y="7" width="10" height="7" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
                        <path d="M5 7V5a3 3 0 016 0v2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                    </svg>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                        autocomplete="current-password"
                    >
                </div>
                @error('password')
                    <div class="error-msg">
                        <svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3M8 10v.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Remember + Forgot --}}
            <div class="check-row">
                <label class="check-label">
                    <input type="checkbox" name="remember">
                    Ingat saya
                </label>
                {{-- <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a> --}}
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-submit">
                <svg fill="none" viewBox="0 0 16 16">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Masuk ke Sistem
            </button>

        </form>

        <div class="divider"></div>

        <div class="panel-foot">
            Butuh bantuan? Hubungi Admin Dinas Sosial DKI Jakarta
            {{-- Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a> --}}
        </div>

    </div>
</main>

</body>
</html>