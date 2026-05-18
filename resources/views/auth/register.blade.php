@php
    $app_theme = \App\Models\Pengaturan::getNilai('app_theme', 'light');
    $app_name = \App\Models\Pengaturan::getNilai('app_name', 'Absensi PPSU') ?: 'Absensi PPSU';
    $app_logo = \App\Models\Pengaturan::getNilai('app_logo');
    $app_brand_display = \App\Models\Pengaturan::getNilai('app_brand_display', 'logo_name');
    $app_icon = \App\Models\Pengaturan::getNilai('app_icon');
    $app_icon_mode = \App\Models\Pengaturan::getNilai('app_icon_mode', 'upload');
    $app_icon_href = null;

    if (! in_array($app_theme, ['light', 'dark'], true)) {
        $app_theme = 'light';
    }

    if (! in_array($app_brand_display, ['logo_name', 'logo_only', 'name_only'], true)) {
        $app_brand_display = 'logo_name';
    }

    if ($app_icon_mode === 'manual') {
        $iconText = strtoupper(substr(\App\Models\Pengaturan::getNilai('app_icon_text', 'A') ?: 'A', 0, 2));
        $iconBg = \App\Models\Pengaturan::getNilai('app_icon_bg', '#2563eb');
        $iconColor = \App\Models\Pengaturan::getNilai('app_icon_color', '#ffffff');

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
    <title>Register | {{ $app_name }}</title>
    @if($app_icon_href)
        <link rel="icon" href="{{ $app_icon_href }}">
        <link rel="apple-touch-icon" href="{{ $app_icon_href }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f0f4f8;
            --panel: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --border: #e5e7eb;
            --input-border: #d1d5db;
            --input-bg: #ffffff;
            --primary: #0ea5c9;
            --primary2: #0284a8;
            --primary-soft: #e0f5fb;
            --primary-border: #bae8f5;
            --danger: #b91c1c;
            --danger-soft: #fee2e2;
            --sidebar: #1a2e3b;
        }

        [data-theme="dark"] {
            --bg: #0f172a;
            --panel: #1e293b;
            --text: #e2e8f0;
            --muted: #94a3b8;
            --border: #334155;
            --input-border: #475569;
            --input-bg: #0f172a;
            --primary: #38bdf8;
            --primary2: #0ea5c9;
            --primary-soft: #0c2d3f;
            --primary-border: #164e63;
            --danger: #fca5a5;
            --danger-soft: #450a0a;
            --sidebar: #020617;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'DM Sans', Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            display: grid;
            place-items: center;
            padding: 16px;
        }
        .panel {
            width: min(440px, 100%);
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 18px;
            margin-bottom: 22px;
            border-bottom: 1px solid var(--border);
        }
        .brand.logo_only {
            justify-content: center;
        }
        .brand.name_only {
            align-items: flex-start;
        }
        .brand-logo,
        .brand-fallback {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            flex: 0 0 auto;
        }
        .brand-logo {
            width: 132px;
            height: 58px;
            padding: 0;
            box-sizing: border-box;
            display: block;
            object-fit: contain;
            background: transparent;
            border: 0;
            border-radius: 0;
        }
        .brand-fallback {
            display: grid;
            place-items: center;
            background: var(--primary);
            color: #fff;
            font-size: 22px;
            font-weight: 700;
        }
        .brand-title {
            min-width: 0;
        }
        .brand-title strong {
            display: block;
            font-size: 17px;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }
        .brand-title span {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 13px;
        }
        h1 {
            margin: 0 0 8px;
            font-size: 26px;
        }
        p {
            margin: 0 0 24px;
            color: var(--muted);
        }
        .notice {
            background: var(--primary-soft);
            border: 1px solid var(--primary-border);
            color: var(--primary2);
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 18px;
            font-size: 14px;
        }
        label {
            display: block;
            margin: 16px 0 6px;
            font-weight: 700;
            font-size: 14px;
        }
        input,
        select {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid var(--input-border);
            border-radius: 6px;
            padding: 11px 12px;
            font-size: 15px;
            background: var(--input-bg);
            color: var(--text);
        }
        button {
            width: 100%;
            margin-top: 20px;
            border: 0;
            border-radius: 6px;
            background: var(--primary);
            color: #ffffff;
            padding: 12px 16px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
        }
        button:hover {
            background: var(--primary2);
        }
        a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }
        .foot {
            margin-top: 18px;
            text-align: center;
            font-size: 14px;
            color: var(--muted);
        }
        .error {
            margin-top: 6px;
            color: var(--danger);
            font-size: 13px;
        }
    </style>
</head>
<body>
    <main class="panel">
        <div class="brand {{ $app_brand_display }}">
            @if($app_brand_display !== 'name_only')
                @if($app_logo)
                    <img src="{{ Storage::url($app_logo) }}" alt="Logo {{ $app_name }}" class="brand-logo" decoding="async">
                @else
                    <div class="brand-fallback">{{ strtoupper(substr($app_name, 0, 1)) }}</div>
                @endif
            @endif

            @if($app_brand_display !== 'logo_only' || ! $app_logo)
                <div class="brand-title">
                    <strong>{{ $app_name }}</strong>
                    <span>Kelurahan Pisangan Baru</span>
                </div>
            @endif
        </div>

        <h1>Register</h1>
        <p>Buat akun baru untuk {{ $app_name }}.</p>

        @if($isFirstUser)
            <div class="notice">Akun pertama otomatis dibuat sebagai Admin Absensi untuk setup awal.</div>
        @endif

        <form method="POST" action="/register">
            @csrf

            <label for="nama">Nama</label>
            <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required autofocus autocomplete="name">
            @error('nama')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required autocomplete="username">
            @error('username')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="id_tempat">Tempat Tugas</label>
            <select id="id_tempat" name="id_tempat">
                <option value="">Belum dipilih</option>
                @foreach($tempatTugas as $tempat)
                    <option value="{{ $tempat->id_tempat }}" @selected(old('id_tempat') == $tempat->id_tempat)>{{ $tempat->nama_tempat }}</option>
                @endforeach
            </select>
            @error('id_tempat')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password_confirmation">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">

            <button type="submit">Daftar</button>
        </form>

        <div class="foot">
            Sudah punya akun? <a href="{{ route('login', [], false) }}">Login</a>
        </div>
    </main>
</body>
</html>
