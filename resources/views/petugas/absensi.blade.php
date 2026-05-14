@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
<style>
    main { max-width: 100% !important; margin: 0 !important; padding: 20px !important; }

    /* ── Status Cards ── */
    .abs-status-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }
    @media (max-width: 900px) { .abs-status-bar { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 500px) { .abs-status-bar { grid-template-columns: 1fr; } }

    .abs-stat-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: border-color .2s;
    }
    .abs-stat-card:hover { border-color: var(--primary-border); }
    .abs-stat-icon {
        width: 46px; height: 46px;
        border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .abs-stat-icon svg { width: 22px; height: 22px; }
    .abs-stat-label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 5px; font-weight: 500; }
    .abs-stat-value { font-size: 17px; font-weight: 700; color: var(--text-color); line-height: 1; }

    /* ── Absensi Grid ── */
    .abs-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    @media (max-width: 860px) { .abs-grid { grid-template-columns: 1fr; } }

    .abs-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        transition: box-shadow .2s;
    }
    .abs-card:hover { box-shadow: 0 4px 20px rgba(14,165,201,.08); }
    .abs-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        background: var(--bg-color);
    }
    .abs-card-title {
        font-size: 14px; font-weight: 600; color: var(--text-color);
        display: flex; align-items: center; gap: 8px;
    }
    .abs-card-title svg { width: 16px; height: 16px; color: var(--primary); }
    .abs-card-time {
        font-size: 11px; color: var(--muted);
        background: var(--panel-bg);
        border: 1px solid var(--border2);
        border-radius: 99px;
        padding: 4px 12px;
        font-weight: 500;
        white-space: nowrap;
    }
    .abs-card-body { padding: 20px; }

    /* ── Alert States ── */
    .abs-info {
        padding: 14px 16px;
        background: var(--bg-color);
        border: 1px solid var(--border2);
        border-radius: 10px;
        color: var(--muted);
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 9px;
    }
    .abs-info svg { width: 16px; height: 16px; flex-shrink: 0; }

    /* ── Camera ── */
    .camera-container {
        background: var(--bg-color);
        border: 2px dashed var(--border2);
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        transition: border-color .2s;
    }
    .camera-container:focus-within { border-color: var(--primary); border-style: solid; }
    .cam-placeholder {
        text-align: center;
        padding: 32px;
        color: var(--muted);
        pointer-events: none;
    }
    .cam-placeholder svg { width: 38px; height: 38px; opacity: .35; display: block; margin: 0 auto 10px; }
    .cam-placeholder p { font-size: 13px; margin: 0; }
    .camera-container video { width: 100%; max-height: 340px; object-fit: cover; display: block; }
    .camera-container img  { width: 100%; max-height: 340px; object-fit: cover; display: block; }

    .cam-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
    .btn-cam {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 15px; border-radius: 9px;
        font-size: 12.5px; font-weight: 500;
        cursor: pointer; border: none; font-family: inherit;
        transition: all .15s; line-height: 1;
    }
    .btn-cam svg { width: 14px; height: 14px; flex-shrink: 0; }
    .btn-cam-open    { background: #1F2937; color: #fff; }
    .btn-cam-open:hover    { background: #111827; }
    .btn-cam-capture { background: #059669; color: #fff; }
    .btn-cam-capture:hover { background: #047857; }
    .btn-cam-retake  { background: var(--red); color: #fff; }
    .btn-cam-retake:hover  { background: #DC2626; }

    /* ── Coord Row ── */
    .coord-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1.3fr;
        gap: 10px;
        margin-top: 0;
    }
    @media (max-width: 540px) { .coord-row { grid-template-columns: 1fr; } }

    /* ── History Section ── */
    .abs-section {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .abs-section-header {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .abs-section-title {
        font-size: 14px; font-weight: 600; color: var(--text-color);
        display: flex; align-items: center; gap: 8px;
    }
    .abs-section-title svg { width: 16px; height: 16px; color: var(--primary); }

    /* ── Filter Row ── */
    .abs-filter {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
        background: none;
        border-radius: 0;
        margin: 0;
    }
    .abs-filter .filter-control { min-width: 180px; flex: 1; }
    .abs-filter-actions { display: flex; gap: 8px; align-items: flex-end; padding-bottom: 1px; }

    .btn-f {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 16px; border-radius: 9px;
        font-size: 12.5px; font-weight: 500;
        cursor: pointer; border: none; font-family: inherit;
        text-decoration: none; transition: all .15s; line-height: 1;
        white-space: nowrap;
    }
    .btn-f svg { width: 13px; height: 13px; }
    .btn-f-primary { background: var(--primary); color: #fff; }
    .btn-f-primary:hover { background: var(--primary2); color: #fff; }
    .btn-f-reset { background: var(--bg-color); color: var(--text-color); border: 1px solid var(--border2); }
    .btn-f-reset:hover { background: var(--border-color); color: var(--text-color); }
    .btn-f-print { background: #059669; color: #fff; }
    .btn-f-print:hover { background: #047857; color: #fff; }

    /* ── Table ── */
    .abs-table-wrap { overflow-x: auto; }
    .abs-table { width: 100%; border-collapse: collapse; font-size: 13px; background: var(--panel-bg); border: none; border-radius: 0; }
    .abs-table th {
        padding: 11px 18px;
        background: var(--bg-color);
        font-size: 11px; font-weight: 600; color: var(--muted);
        text-transform: uppercase; letter-spacing: .05em;
        border-bottom: 1px solid var(--border-color);
        text-align: left; white-space: nowrap;
    }
    .abs-table td {
        padding: 13px 18px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-color);
        vertical-align: middle;
    }
    .abs-table tr:last-child td { border-bottom: 0; }
    .abs-table tbody tr:hover { background: var(--bg-color); }
    .abs-date-main { font-weight: 500; font-size: 13px; }
    .abs-date-sub  { font-size: 11px; color: var(--muted); margin-top: 2px; }
    .abs-time-masuk  { font-weight: 600; color: var(--green); }
    .abs-time-pulang { font-weight: 600; color: #F59E0B; }
    .abs-time-nil    { color: var(--muted); }

    .btn-detail {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 7px;
        font-size: 12px; font-weight: 500;
        background: var(--primary-soft); color: var(--primary2);
        border: 1px solid var(--primary-border);
        text-decoration: none; transition: all .15s;
    }
    .btn-detail:hover { background: var(--primary-border); color: var(--primary2); }
    .btn-detail svg { width: 12px; height: 12px; }

    .abs-empty {
        padding: 48px 20px; text-align: center; color: var(--muted); font-size: 13px;
    }
    .abs-empty svg { width: 38px; height: 38px; opacity: .3; display: block; margin: 0 auto 10px; }
    .abs-pagination { padding: 12px 20px; border-top: 1px solid var(--border-color); }
</style>

{{-- ── Page Header ── --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="margin-bottom:4px;">Absensi</h1>
        <p style="font-size:13px;color:var(--muted);margin:0;">
            Periode aktif: <strong style="color:var(--text-color);">{{ $periodeAktif?->nama_periode ?? '-' }}</strong>
        </p>
    </div>
</div>

{{-- ── Status Bar ── --}}
<div class="abs-status-bar">
    <div class="abs-stat-card">
        <div class="abs-stat-icon" style="background:var(--primary-soft);">
            <svg fill="none" viewBox="0 0 24 24" style="color:var(--primary)">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                <path d="M12 7v5l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>
        <div>
            <div class="abs-stat-label">Status Hari Ini</div>
            <span class="badge {{ $today?->status ?? '' }}">{{ $today?->status ?? 'belum absen' }}</span>
        </div>
    </div>
    <div class="abs-stat-card">
        <div class="abs-stat-icon" style="background:var(--green-soft);">
            <svg fill="none" viewBox="0 0 24 24" style="color:var(--green)">
                <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
            </svg>
        </div>
        <div>
            <div class="abs-stat-label">Jam Masuk</div>
            <div class="abs-stat-value">{{ $today?->jam_masuk ?? '--:--' }}</div>
        </div>
    </div>
    <div class="abs-stat-card">
        <div class="abs-stat-icon" style="background:var(--amber-soft);">
            <svg fill="none" viewBox="0 0 24 24" style="color:var(--amber)">
                <path d="M17 8l4 4-4 4M3 12h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div>
            <div class="abs-stat-label">Jam Pulang</div>
            <div class="abs-stat-value">{{ $today?->jam_pulang ?? '--:--' }}</div>
        </div>
    </div>
    <div class="abs-stat-card">
        <div class="abs-stat-icon" style="background:rgba(99,102,241,.12);">
            <svg fill="none" viewBox="0 0 24 24" style="color:#6366f1">
                <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                <path d="M8 2v3M16 2v3M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>
        <div>
            <div class="abs-stat-label">Hari Ini</div>
            <div class="abs-stat-value" style="font-size:14px;">{{ now()->translatedFormat('d M Y') }}</div>
        </div>
    </div>
</div>

{{-- ── Form Absensi ── --}}
<div class="abs-grid">

    {{-- ABSEN MASUK --}}
    <div class="abs-card">
        <div class="abs-card-header">
            <div class="abs-card-title">
                <svg fill="none" viewBox="0 0 16 16"><path d="M3 8l3.5 3.5L13 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Absen Masuk
            </div>
            <span class="abs-card-time">07:00 – 07:15</span>
        </div>
        <div class="abs-card-body">
            @if($today?->jam_masuk)
                <div class="success" style="margin:0;">
                    <strong>Sudah absen masuk</strong> — pukul {{ $today->jam_masuk }}
                </div>
            @elseif(now()->format('H:i:s') > '07:15:00' && $today?->status !== 'akses_dibuka')
                <div class="error" style="margin:0;">Waktu absen masuk telah habis. Jika tidak ada akses admin, hari ini akan tercatat Tidak Absen setelah hari berganti.</div>
            @elseif(now()->format('H:i:s') < '07:00:00' && $today?->status !== 'akses_dibuka')
                <div class="abs-info">
                    <svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3l2 1.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Absen masuk belum dibuka. Silakan kembali pukul <strong>07:00</strong>.
                </div>
            @else
                @if($today?->status === 'akses_dibuka')
                    <div style="padding:12px 16px;background:var(--green-soft);border:1px solid #A7F3D0;border-radius:10px;color:var(--green-dark);font-size:13px;font-weight:600;margin-bottom:16px;">
                        Akses khusus diberikan oleh Admin. Anda dapat melakukan absen telat.
                    </div>
                @endif
                <form id="form_masuk" method="POST" action="{{ route('petugas.absensi.masuk') }}" enctype="multipart/form-data">
                    @csrf
                    <label style="margin-bottom:8px;">Foto Masuk</label>
                    <div class="camera-container" id="camera_wrap_masuk">
                        <div class="cam-placeholder" id="cam_placeholder_masuk">
                            <svg fill="none" viewBox="0 0 24 24"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="13" r="4" stroke="currentColor" stroke-width="1.5"/></svg>
                            <p>Kamera belum aktif</p>
                        </div>
                        <video id="video_masuk" width="100%" style="display:none;" autoplay playsinline></video>
                        <canvas id="canvas_masuk" style="display:none;"></canvas>
                        <img id="photo_masuk" style="width:100%;display:none;" />
                    </div>
                    <input type="hidden" name="foto_masuk" id="foto_masuk_input">
                    <div class="cam-actions">
                        <button type="button" id="btn_open_cam_masuk" class="btn-cam btn-cam-open">
                            <svg fill="none" viewBox="0 0 16 16"><path d="M15 5l-4 3 4 3V5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/><rect x="1" y="4" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
                            Buka Kamera
                        </button>
                        <button type="button" id="btn_capture_masuk" class="btn-cam btn-cam-capture" style="display:none;">
                            <svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="9" r="3" stroke="currentColor" stroke-width="1.2"/><path d="M2 5h2l1-2h6l1 2h2v8H2V5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
                            Ambil Foto
                        </button>
                        <button type="button" id="btn_retake_masuk" class="btn-cam btn-cam-retake" style="display:none;">
                            <svg fill="none" viewBox="0 0 16 16"><path d="M2 8a6 6 0 1011.5-2.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/><path d="M13.5 2v3.5H10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Ulangi Foto
                        </button>
                    </div>
                    <div class="coord-row">
                        <div><label>Latitude</label><input name="latitude_masuk" id="lat_masuk"></div>
                        <div><label>Longitude</label><input name="longitude_masuk" id="lng_masuk"></div>
                        <div><label>Lokasi</label><input name="lokasi_masuk"></div>
                    </div>
                    <label style="margin-top:14px;">Keterangan</label>
                    <textarea name="keterangan" style="margin-top:6px;"></textarea>
                    <button type="submit" style="margin-top:14px;width:100%;padding:11px;">Simpan Absen Masuk</button>
                </form>
            @endif
        </div>
    </div>

    {{-- ABSEN PULANG --}}
    <div class="abs-card">
        <div class="abs-card-header">
            <div class="abs-card-title">
                <svg fill="none" viewBox="0 0 16 16"><path d="M10 3l5 5-5 5M3 8h12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Absen Pulang
            </div>
            <span class="abs-card-time">16:00 – 23:59</span>
        </div>
        <div class="abs-card-body">
            @if(!$today?->jam_masuk && $today?->status !== 'tidak_absen')
                <div class="abs-info">
                    <svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3l2 1.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Silakan absen masuk terlebih dahulu.
                </div>
            @elseif($today?->jam_pulang)
                <div class="success" style="margin:0;">
                    <strong>Sudah absen pulang</strong> — pukul {{ $today->jam_pulang }}
                </div>
            @elseif($today?->status === 'tidak_absen' || (now()->format('H:i:s') > '07:15:00' && !$today?->jam_masuk && $today?->status !== 'akses_dibuka'))
                <div class="error" style="margin:0;">Tidak ada absen masuk untuk hari ini sehingga tidak bisa absen pulang.</div>
            @elseif(now()->format('H:i:s') < '16:00:00')
                <div class="abs-info">
                    <svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3l2 1.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Absen pulang belum dibuka. Silakan kembali pukul <strong>16:00</strong>.
                </div>
            @else
                <form id="form_pulang" method="POST" action="{{ route('petugas.absensi.pulang') }}" enctype="multipart/form-data">
                    @csrf
                    <label style="margin-bottom:8px;">Foto Pulang</label>
                    <div class="camera-container" id="camera_wrap_pulang">
                        <div class="cam-placeholder" id="cam_placeholder_pulang">
                            <svg fill="none" viewBox="0 0 24 24"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="13" r="4" stroke="currentColor" stroke-width="1.5"/></svg>
                            <p>Kamera belum aktif</p>
                        </div>
                        <video id="video_pulang" width="100%" style="display:none;" autoplay playsinline></video>
                        <canvas id="canvas_pulang" style="display:none;"></canvas>
                        <img id="photo_pulang" style="width:100%;display:none;" />
                    </div>
                    <input type="hidden" name="foto_pulang" id="foto_pulang_input">
                    <div class="cam-actions">
                        <button type="button" id="btn_open_cam_pulang" class="btn-cam btn-cam-open">
                            <svg fill="none" viewBox="0 0 16 16"><path d="M15 5l-4 3 4 3V5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/><rect x="1" y="4" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
                            Buka Kamera
                        </button>
                        <button type="button" id="btn_capture_pulang" class="btn-cam btn-cam-capture" style="display:none;">
                            <svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="9" r="3" stroke="currentColor" stroke-width="1.2"/><path d="M2 5h2l1-2h6l1 2h2v8H2V5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
                            Ambil Foto
                        </button>
                        <button type="button" id="btn_retake_pulang" class="btn-cam btn-cam-retake" style="display:none;">
                            <svg fill="none" viewBox="0 0 16 16"><path d="M2 8a6 6 0 1011.5-2.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/><path d="M13.5 2v3.5H10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Ulangi Foto
                        </button>
                    </div>
                    <div class="coord-row">
                        <div><label>Latitude</label><input name="latitude_pulang" id="lat_pulang"></div>
                        <div><label>Longitude</label><input name="longitude_pulang" id="lng_pulang"></div>
                        <div><label>Lokasi</label><input name="lokasi_pulang"></div>
                    </div>
                    <button type="submit" style="margin-top:14px;width:100%;padding:11px;">Simpan Absen Pulang</button>
                </form>
            @endif
        </div>
    </div>
</div>

{{-- ── Riwayat Absensi ── --}}
<div class="abs-section">
    <div class="abs-section-header">
        <div class="abs-section-title">
            <svg fill="none" viewBox="0 0 16 16"><path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            Riwayat Absensi
        </div>
    </div>

    {{-- Filter --}}
    <form action="{{ route('petugas.absensi.index') }}" method="GET" class="abs-filter">
        <div class="filter-control">
            <label>Bulan</label>
            <select name="month">
                <option value="">-- Pilih Bulan --</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="filter-control">
            <label>Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Status atau keterangan...">
        </div>
        <div class="abs-filter-actions">
            <button type="submit" class="btn-f btn-f-primary">
                <svg fill="none" viewBox="0 0 16 16"><circle cx="7" cy="7" r="5" stroke="currentColor" stroke-width="1.3"/><path d="M11 11l3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                Tampilkan
            </button>
            <a href="{{ route('petugas.absensi.index') }}" class="btn-f btn-f-reset">Reset</a>
            <a href="{{ route('petugas.absensi.print', request()->all()) }}" target="_blank" class="btn-f btn-f-print">
                <svg fill="none" viewBox="0 0 16 16"><path d="M4 5V2h8v3M3 5h10a1 1 0 011 1v5H2V6a1 1 0 011-1z" stroke="currentColor" stroke-width="1.2"/><path d="M4 12v2h8v-2" stroke="currentColor" stroke-width="1.2"/></svg>
                Cetak
            </a>
        </div>
    </form>

    {{-- Table --}}
    <div class="abs-table-wrap">
        <table class="abs-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="abs-date-main">{{ $item->tanggal->format('d/m/Y') }}</div>
                            <div class="abs-date-sub">{{ $item->tanggal->translatedFormat('l') }}</div>
                        </td>
                        <td>
                            @if($item->jam_masuk)
                                <span class="abs-time-masuk">{{ $item->jam_masuk }}</span>
                            @else
                                <span class="abs-time-nil">—</span>
                            @endif
                        </td>
                        <td>
                            @if($item->jam_pulang)
                                <span class="abs-time-pulang">{{ $item->jam_pulang }}</span>
                            @else
                                <span class="abs-time-nil">—</span>
                            @endif
                        </td>
                        <td><span class="badge {{ $item->status }}">{{ $item->status }}</span></td>
                        <td>
                            <a href="{{ route('absensi.detail', $item->id_absensi) }}" class="btn-detail">
                                <svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.2"/><path d="M8 6v2l1 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="abs-empty">
                                <svg fill="none" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="1.5"/></svg>
                                Belum ada riwayat absensi.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
        <div class="abs-pagination">
            {{ $items->links('pagination.simple') }}
        </div>
    @endif
</div>

<script>
    function calculateDistance(lat1, lon1, lat2, lon2) {
        var R    = 6371e3;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a    = Math.sin(dLat/2) * Math.sin(dLat/2)
                 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180)
                 * Math.sin(dLon/2) * Math.sin(dLon/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    var tempatLat  = {{ isset($tempatTugas) && $tempatTugas->latitude  ? $tempatTugas->latitude  : 'null' }};
    var tempatLng  = {{ isset($tempatTugas) && $tempatTugas->longitude ? $tempatTugas->longitude : 'null' }};
    var namaTempat = "{{ isset($tempatTugas) ? $tempatTugas->nama_tempat : '' }}";

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var userLat = position.coords.latitude;
            var userLng = position.coords.longitude;

            var inRange = true;
            if (tempatLat !== null && tempatLng !== null) {
                if (calculateDistance(userLat, userLng, tempatLat, tempatLng) > 100) inRange = false;
            }

            ['masuk', 'pulang'].forEach(function (type) {
                var lat = document.getElementById('lat_' + type);
                var lng = document.getElementById('lng_' + type);
                var loc = document.querySelector('input[name="lokasi_' + type + '"]');

                if (lat && lng) {
                    lat.value = userLat.toFixed(8);
                    lng.value = userLng.toFixed(8);
                }
                if (loc) {
                    loc.readOnly = true;
                    if (!inRange) {
                        loc.value          = 'Di luar area kantor';
                        loc.style.color    = 'red';
                        loc.style.fontWeight = 'bold';
                    } else {
                        loc.value          = namaTempat || 'Area Kantor';
                        loc.style.color    = 'green';
                        loc.style.fontWeight = 'bold';
                    }
                }
            });

            if (!inRange) {
                alert('Anda berada di luar area kantor! Jarak Anda terlalu jauh dari lokasi yang diizinkan.');
                document.querySelectorAll('button[type="submit"]').forEach(function (btn) {
                    btn.disabled       = true;
                    btn.style.opacity  = '0.5';
                    btn.style.cursor   = 'not-allowed';
                });
            }
        });
    }

    function setupCamera(type) {
        var video       = document.getElementById('video_' + type);
        var canvas      = document.getElementById('canvas_' + type);
        var photo       = document.getElementById('photo_' + type);
        var input       = document.getElementById('foto_' + type + '_input');
        var placeholder = document.getElementById('cam_placeholder_' + type);
        var btnOpen     = document.getElementById('btn_open_cam_' + type);
        var btnCapture  = document.getElementById('btn_capture_' + type);
        var btnRetake   = document.getElementById('btn_retake_' + type);
        var stream      = null;

        if (!btnOpen) return;

        btnOpen.addEventListener('click', async function () {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                video.srcObject = stream;
                video.style.display = 'block';
                photo.style.display = 'none';
                if (placeholder) placeholder.style.display = 'none';
                btnOpen.style.display    = 'none';
                btnCapture.style.display = 'inline-flex';
                btnRetake.style.display  = 'none';
                input.value = '';
            } catch (err) {
                alert('Akses kamera ditolak atau perangkat kamera tidak ditemukan.');
                console.error(err);
            }
        });

        btnCapture.addEventListener('click', function () {
            if (!stream) return;
            var maxW = 640, maxH = 480;
            var w = video.videoWidth, h = video.videoHeight;
            if (w > h) { if (w > maxW) { h *= maxW / w; w = maxW; } }
            else        { if (h > maxH) { w *= maxH / h; h = maxH; } }
            canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(video, 0, 0, w, h);
            var data    = canvas.toDataURL('image/jpeg', 0.7);
            photo.src   = data;
            input.value = data;
            video.style.display    = 'none';
            photo.style.display    = 'block';
            btnCapture.style.display = 'none';
            btnRetake.style.display  = 'inline-flex';
            stream.getTracks().forEach(function (t) { t.stop(); });
            stream = null;
        });

        btnRetake.addEventListener('click', function () { btnOpen.click(); });

        var form = document.getElementById('form_' + type);
        if (form) {
            form.addEventListener('submit', function (e) {
                if (!input.value) {
                    e.preventDefault();
                    alert('Silakan ambil foto terlebih dahulu sebelum menyimpan absensi.');
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        setupCamera('masuk');
        setupCamera('pulang');
    });
</script>
@endsection
