@php
    $appName = config('app.name', 'Absensi PPSU') ?: 'Absensi PPSU';
    $retryAfter = isset($exception) && method_exists($exception, 'getHeaders')
        ? ($exception->getHeaders()['Retry-After'] ?? null)
        : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance | {{ $appName }}</title>
    <meta name="robots" content="noindex">
    @if($retryAfter)
        <meta http-equiv="refresh" content="{{ max((int) $retryAfter, 30) }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #eef4f7;
            --panel: #ffffff;
            --text: #172033;
            --muted: #64748b;
            --line: #dbe5ef;
            --primary: #0e7490;
            --primary-dark: #155e75;
            --primary-soft: #cffafe;
            --green: #059669;
            --green-soft: #dcfce7;
            --amber: #f59e0b;
            --amber-soft: #fff7d6;
            --red: #dc2626;
            --red-soft: #fee2e2;
            --shadow: 0 24px 80px rgba(15, 23, 42, .13);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'DM Sans', Arial, sans-serif;
            color: var(--text);
            background:
                linear-gradient(145deg, rgba(207, 250, 254, .85), rgba(220, 252, 231, .76) 45%, rgba(255, 247, 214, .8)),
                var(--bg);
            display: grid;
            place-items: center;
            padding: 22px;
        }

        .maintenance {
            width: min(920px, 100%);
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .top {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 24px;
            align-items: center;
            padding: 34px;
            border-bottom: 1px solid var(--line);
        }

        .mark {
            width: 86px;
            height: 86px;
            border-radius: 22px;
            display: grid;
            place-items: center;
            background: var(--primary-soft);
            color: var(--primary-dark);
            border: 1px solid rgba(14, 116, 144, .18);
        }

        .mark svg {
            width: 42px;
            height: 42px;
        }

        .eyebrow {
            margin-bottom: 10px;
            color: var(--primary-dark);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            max-width: 720px;
            font-size: clamp(30px, 5vw, 54px);
            line-height: 1;
            letter-spacing: 0;
        }

        .lead {
            margin: 14px 0 0;
            max-width: 680px;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.65;
        }

        .body {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .panel {
            padding: 28px 34px 32px;
        }

        .panel:first-child {
            border-right: 1px solid var(--line);
        }

        h2 {
            margin: 0 0 14px;
            font-size: 15px;
            line-height: 1.3;
        }

        .status {
            display: grid;
            gap: 10px;
        }

        .row {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fbfdff;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.45;
        }

        .dot {
            width: 11px;
            height: 11px;
            border-radius: 999px;
            flex: 0 0 auto;
            background: var(--amber);
            box-shadow: 0 0 0 5px var(--amber-soft);
        }

        .dot.green {
            background: var(--green);
            box-shadow: 0 0 0 5px var(--green-soft);
        }

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

        .note {
            margin-top: 18px;
            color: var(--muted);
            font-size: 12.5px;
            line-height: 1.5;
        }

        @media (max-width: 760px) {
            body { padding: 14px; }
            .top { grid-template-columns: 1fr; padding: 26px; }
            .mark { width: 74px; height: 74px; border-radius: 18px; }
            .body { grid-template-columns: 1fr; }
            .panel { padding: 24px 26px; }
            .panel:first-child { border-right: 0; border-bottom: 1px solid var(--line); }
            .actions { flex-direction: column; }
            .button { width: 100%; }
        }
    </style>
</head>
<body>
    <main class="maintenance" role="main">
        <section class="top" aria-labelledby="maintenance-title">
            <div class="mark" aria-hidden="true">
                <svg fill="none" viewBox="0 0 24 24"><path d="M14.7 6.3a4 4 0 01-5 5l-4.4 4.4a2 2 0 102.8 2.8l4.4-4.4a4 4 0 005-5l-2.8 2.8-2.8-2.8 2.8-2.8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div>
                <div class="eyebrow">Maintenance sistem</div>
                <h1 id="maintenance-title">Aplikasi sedang dirapikan sebentar</h1>
                <p class="lead">Kami sedang melakukan perbaikan atau pengecekan layanan. Data absensi tetap aman, hanya akses web yang sementara dibatasi.</p>
            </div>
        </section>

        <section class="body" aria-label="Informasi maintenance">
            <div class="panel">
                <h2>Status layanan</h2>
                <div class="status">
                    <div class="row">
                        <span class="dot"></span>
                        Halaman web sedang tidak bisa digunakan untuk sementara.
                    </div>
                    <div class="row">
                        <span class="dot green"></span>
                        Tim sedang mengecek agar aplikasi kembali stabil.
                    </div>
                </div>
            </div>

            <div class="panel">
                <h2>Yang bisa dilakukan</h2>
                <p class="lead">Coba muat ulang halaman beberapa saat lagi. Jika absensi mendesak, hubungi admin atau atasan agar pencatatan bisa dibantu sesuai prosedur.</p>
                <div class="actions">
                    <a class="button primary" href="{{ url()->current() }}">
                        <svg fill="none" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a9 9 0 11-2.6-6.4M21 4v6h-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Muat Ulang
                    </a>
                    <a class="button secondary" href="{{ url('/login') }}">
                        <svg fill="none" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12h13m0 0l-5-5m5 5l-5 5M21 4v16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Ke Login
                    </a>
                </div>
                <div class="note">Kode 503 berarti layanan sementara belum tersedia. Halaman ini juga muncul otomatis saat maintenance mode Laravel aktif.</div>
            </div>
        </section>
    </main>
</body>
</html>
