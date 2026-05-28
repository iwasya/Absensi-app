@php
    $app_theme = $app_theme ?? 'light';
    $app_name = ($app_name ?? 'Absensi PPSU') ?: 'Absensi PPSU';
    $app_logo = $app_logo ?? null;
    $app_brand_display = $app_brand_display ?? 'logo_name';
    $app_icon = $app_icon ?? null;
    $app_icon_mode = $app_icon_mode ?? 'upload';
    $app_icon_href = null;

    if (! in_array($app_theme, ['light', 'dark'], true)) {
        $app_theme = 'light';
    }

    if (! in_array($app_brand_display, ['logo_name', 'logo_only', 'name_only'], true)) {
        $app_brand_display = 'logo_name';
    }

    if ($app_icon_mode === 'manual') {
        $iconText = strtoupper(substr(($app_icon_text ?? 'A') ?: 'A', 0, 2));
        $iconBg = $app_icon_bg ?? '#2563eb';
        $iconColor = $app_icon_color ?? '#ffffff';

        if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $iconBg)) {
            $iconBg = '#2563eb';
        }

        if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $iconColor)) {
            $iconColor = '#ffffff';
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" rx="14" fill="' . $iconBg . '"/><text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle" font-family="Arial,sans-serif" font-size="' . (strlen($iconText) > 1 ? '24' : '32') . '" font-weight="700" fill="' . $iconColor . '">' . htmlspecialchars($iconText, ENT_QUOTES, 'UTF-8') . '</text></svg>';
        $app_icon_href = 'data:image/svg+xml,' . rawurlencode($svg);
    } elseif ($app_icon) {
        $app_icon_href = Storage::url($app_icon);
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $app_theme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | {{ $app_name }}</title>
    @if($app_icon_href)
        <link rel="icon" href="{{ $app_icon_href }}">
        <link rel="apple-touch-icon" href="{{ $app_icon_href }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:           #F0F4F8;
            --card:         #FFFFFF;
            --accent:       #0E7490;
            --accent2:      #155E75;
            --accent-soft:  #CFFAFE;
            --accent-border:#A5F3FC;
            --sidebar:      #1A2E3B;
            --green:        #10B981;
            --green-soft:   #D1FAE5;
            --green-dark:   #065F46;
            --red:          #EF4444;
            --red-soft:     #FEE2E2;
            --red-border:   #FECACA;
            --text:         #1E293B;
            --text2:        #475569;
            --text3:        #94A3B8;
            --border:       #E2E8F0;
            --border2:      #CBD5E1;
        }

        [data-theme="dark"] {
            --bg:           #0F172A;
            --card:         #1E293B;
            --accent:       #22D3EE;
            --accent2:      #06B6D4;
            --accent-soft:  #083344;
            --accent-border:#155E75;
            --sidebar:      #020617;
            --green-soft:   #052E24;
            --green-dark:   #86EFAC;
            --red:          #FCA5A5;
            --red-soft:     #450A0A;
            --red-border:   #7F1D1D;
            --text:         #E2E8F0;
            --text2:        #CBD5E1;
            --text3:        #94A3B8;
            --border:       #334155;
            --border2:      #475569;
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
            box-shadow: 0 4px 32px rgba(14, 116, 144, 0.08), 0 1px 4px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        /* ── HEADER STRIP ── */
        .panel-header {
            background:
                linear-gradient(135deg, rgba(255,255,255,.12), rgba(255,255,255,0) 42%),
                var(--accent);
            padding: 30px 32px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            position: relative;
            isolation: isolate;
        }
        .panel-header::after {
            content: "";
            position: absolute;
            inset: auto 32px 0;
            height: 1px;
            background: rgba(255,255,255,.18);
        }
        .logo-box {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .logo-box.logo-image {
            width: min(190px, 78%);
            height: 74px;
            background: transparent;
            border: 0;
            border-radius: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .logo-box img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: contain;
        }
        .logo-box svg {
            width: 28px;
            height: 28px;
            color: #fff;
        }
        .header-text {
            text-align: center;
            display: grid;
            gap: 5px;
        }
        .header-text h1 {
            font-size: 19px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.25;
            letter-spacing: .01em;
        }
        .header-text p {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.78);
            margin-top: 0;
        }
        .header-text .header-subtitle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            margin: 5px auto 0;
            padding: 5px 10px;
            border: 1px solid rgba(255,255,255,.22);
            border-radius: 99px;
            background: rgba(255,255,255,.10);
            color: rgba(255,255,255,.9);
            font-size: 11px;
            font-weight: 600;
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
            background: var(--card);
            box-shadow: 0 0 0 3px rgba(14,116,144,.12);
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
            border: 1px solid var(--red-border);
            border-radius: 9px;
            font-size: 13px;
            color: var(--red);
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
        @if($app_brand_display !== 'name_only')
            @if($app_logo)
                <div class="logo-box logo-image">
                    <img src="{{ Storage::url($app_logo) }}" alt="Logo {{ $app_name }}" decoding="async">
                </div>
            @else
                <div class="logo-box">
                    <svg fill="none" viewBox="0 0 22 22">
                        <path d="M3 11a8 8 0 1016 0A8 8 0 003 11z" stroke="#fff" stroke-width="1.5"/>
                        <path d="M11 7v4l3 2.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
            @endif
        @endif
        <div class="header-text">
            @if($app_brand_display !== 'logo_only' || ! $app_logo)
                <h1>Kelurahan Pisangan Baru</h1>
            @endif
            <p>{{ $app_name }}</p>
            <div class="header-subtitle">Sistem Absensi & Laporan Petugas</div>
        </div>
    </div>

    <!-- Body -->
    <div class="panel-body">

        <div class="welcome-title">Selamat datang</div>
        <div class="welcome-sub">Masuk untuk mengakses {{ $app_name }}.</div>

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

        <form method="POST" action="{{ route('login', [], false) }}">
            @csrf

            <div class="form-group">
                <label for="login">Email atau Username</label>
                <div class="input-wrap">
                    <svg fill="none" viewBox="0 0 16 16">
                        <path d="M2 4.5A1.5 1.5 0 013.5 3h9A1.5 1.5 0 0114 4.5v7A1.5 1.5 0 0112.5 13h-9A1.5 1.5 0 012 11.5v-7z" stroke="currentColor" stroke-width="1.3"/>
                        <path d="M2 5l6 4.5L14 5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <input id="login" type="text" name="login" value="{{ old('login') }}" placeholder="Email atau username" required autofocus autocomplete="username">
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
        </div>

    </div>
</main>

</body>
</html>
