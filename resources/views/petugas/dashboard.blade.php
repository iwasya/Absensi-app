@extends('layouts.app')

@section('title', 'Dashboard Petugas')

@section('content')

{{-- ── OCEAN THEME STYLES ── --}}
<style>
    :root {
        --accent:       #0EA5C9;
        --accent2:      #0284A8;
        --accent-soft:  #E0F5FB;
        --accent-border:#BAE8F5;
        --green:        #10B981;
        --green-soft:   #D1FAE5;
        --green-dark:   #065F46;
        --amber:        #F59E0B;
        --amber-soft:   #FEF3C7;
        --amber-dark:   #92400E;
        --red:          #EF4444;
        --red-soft:     #FEE2E2;
        --red-dark:     #7F1D1D;
        --purple:       #8B5CF6;
        --purple-soft:  #EDE9FE;
        --text:         #1E293B;
        --text2:        #475569;
        --text3:        #94A3B8;
        --border:       #E2E8F0;
        --bg:           #F0F4F8;
        --card:         #FFFFFF;
    }

    /* Override layout agar dashboard penuh ke samping */
    main {
        max-width: 100%;
        margin: 0;
        padding: 24px 28px;
    }

    .dash-wrap {
        display: flex;
        flex-direction: column;
        gap: 20px;
        font-family: 'DM Sans', sans-serif;
    }

    /* ── PAGE HEADER ── */
    .dash-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .dash-header h1 {
        font-size: 20px;
        font-weight: 600;
        color: var(--text);
        margin: 0;
    }
    .dash-header-sub {
        font-size: 12px;
        color: var(--text3);
        margin-top: 3px;
    }

    /* ── STAT CARDS ── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }
    .stat-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        transition: box-shadow .2s;
    }
    .stat-card:hover {
        box-shadow: 0 4px 16px rgba(14,165,201,.1);
    }
    .stat-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .stat-ico {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-ico svg {
        width: 17px;
        height: 17px;
    }
    .stat-lbl {
        font-size: 11.5px;
        color: var(--text3);
        margin-bottom: 4px;
    }
    .stat-val {
        font-size: 22px;
        font-weight: 600;
        line-height: 1;
    }
    .stat-hint {
        font-size: 11px;
        margin-top: 3px;
    }

    /* status color helpers */
    .c-green  { color: var(--green); }
    .c-amber  { color: var(--amber); }
    .c-red    { color: var(--red); }
    .c-accent { color: var(--accent); }
    .c-purple { color: var(--purple); }
    .c-muted  { color: var(--text3); }
    .bg-green  { background: var(--green-soft); }
    .bg-amber  { background: var(--amber-soft); }
    .bg-red    { background: var(--red-soft); }
    .bg-accent { background: var(--accent-soft); }
    .bg-purple { background: var(--purple-soft); }

    /* ── PANELS ── */
    .panel-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
    }
    .panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
    }
    .panel-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .panel-title svg {
        width: 16px;
        height: 16px;
        color: var(--accent);
    }
    .panel-body {
        padding: 16px 18px;
    }

    /* ── KALENDER ITEMS ── */
    .kalender-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .kalender-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        background: var(--bg);
        border-radius: 10px;
        border: 1px solid var(--border);
        transition: background .15s;
    }
    .kalender-item:hover {
        background: var(--accent-soft);
        border-color: var(--accent-border);
    }
    .kalender-dot {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        background: var(--accent-soft);
        border: 1px solid var(--accent-border);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .kalender-dot svg {
        width: 16px;
        height: 16px;
        color: var(--accent);
    }
    .kalender-info {
        flex: 1;
    }
    .kalender-nama {
        font-size: 13px;
        font-weight: 500;
        color: var(--text);
    }
    .kalender-tgl {
        font-size: 11px;
        color: var(--text3);
        margin-top: 2px;
    }
    .kalender-badge {
        font-size: 10px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 99px;
        background: var(--accent-soft);
        color: var(--accent2);
        border: 1px solid var(--accent-border);
        white-space: nowrap;
    }
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 28px 16px;
        text-align: center;
    }
    .empty-ico {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--bg);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .empty-ico svg { width: 20px; height: 20px; color: var(--text3); }
    .empty-title { font-size: 13px; font-weight: 500; color: var(--text2); }
    .empty-sub   { font-size: 12px; color: var(--text3); }

    /* ── STATUS ABSEN BADGE ── */
    .absen-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 99px;
        white-space: nowrap;
    }
    .absen-badge svg { width: 11px; height: 11px; }
    .absen-hadir   { background: var(--green-soft); color: var(--green-dark); }
    .absen-telat   { background: var(--amber-soft); color: var(--amber-dark); }
    .absen-absen   { background: var(--red-soft);   color: var(--red-dark); }
    .absen-default { background: var(--bg); color: var(--text3); border: 1px solid var(--border); }

    /* ── NOTIF BADGE ── */
    .notif-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
        height: 24px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 600;
        padding: 0 6px;
    }
    .notif-count.has { background: var(--red); color: #fff; }
    .notif-count.none { background: var(--green-soft); color: var(--green-dark); }

    /* ── QUICK ACTIONS ── */
    .quick-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }
    .quick-btn {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        padding: 14px;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 13px;
        text-decoration: none;
        transition: box-shadow .2s, border-color .2s, background .15s;
        cursor: pointer;
    }
    .quick-btn:hover {
        border-color: var(--accent-border);
        background: var(--accent-soft);
        box-shadow: 0 4px 14px rgba(14,165,201,.12);
    }
    .quick-ico {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .quick-ico svg { width: 18px; height: 18px; }
    .quick-lbl {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text);
    }
    .quick-sub {
        font-size: 11px;
        color: var(--text3);
    }

    /* Responsive */
    @media (max-width: 991px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px)  {
        .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .quick-grid { grid-template-columns: repeat(2, 1fr); }
        .dash-header { flex-direction: column; }
    }
</style>

{{-- Load DM Sans + DM Mono (Ocean fonts) --}}
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&family=DM+Mono:wght@500&display=swap" rel="stylesheet">

<div class="dash-wrap">

    {{-- ── HEADER ROW ── --}}
    <div class="dash-header">
        <div>
            <h1>Dashboard Petugas</h1>
            <div class="dash-header-sub">
                Selamat datang, <strong>{{ auth()->user()->name ?? 'Petugas' }}</strong> — Kelurahan {{ auth()->user()->tempat_tugas ?? '' }}
            </div>
        </div>

    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="stat-grid">

        {{-- Status hari ini --}}
        @php
            $statusRaw   = $absensiHariIni?->status ?? null;
            $statusLabel = $statusRaw ? ucwords(str_replace('_', ' ', $statusRaw)) : 'Tidak Absen';
            $statusClass = match($statusRaw) {
                'hadir'           => ['ico-bg' => 'bg-green',  'val-c' => 'c-green',  'ico' => 'check'],
                'terlambat','telat'=> ['ico-bg' => 'bg-amber', 'val-c' => 'c-amber',  'ico' => 'clock'],
                'tidak_hadir'     => ['ico-bg' => 'bg-red',    'val-c' => 'c-red',    'ico' => 'x'],
                default           => ['ico-bg' => 'bg-accent', 'val-c' => 'c-muted',  'ico' => 'dash'],
            };
        @endphp
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico {{ $statusClass['ico-bg'] }}">
                    @if($statusClass['ico'] === 'check')
                        <svg fill="none" viewBox="0 0 16 16"><path d="M3 8l3.5 3.5L13 5" stroke="#10B981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @elseif($statusClass['ico'] === 'clock')
                        <svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="#F59E0B" stroke-width="1.3"/><path d="M8 5v3l2 2" stroke="#F59E0B" stroke-width="1.3" stroke-linecap="round"/></svg>
                    @elseif($statusClass['ico'] === 'x')
                        <svg fill="none" viewBox="0 0 16 16"><path d="M4 4l8 8M12 4l-8 8" stroke="#EF4444" stroke-width="1.3" stroke-linecap="round"/></svg>
                    @else
                        <svg fill="none" viewBox="0 0 16 16"><path d="M4 8h8" stroke="#0EA5C9" stroke-width="1.5" stroke-linecap="round"/></svg>
                    @endif
                </div>
            </div>
            <div class="stat-lbl">Status hari ini</div>
            <div class="stat-val {{ $statusClass['val-c'] }}" style="font-size:16px">{{ $statusLabel }}</div>
        </div>

        {{-- Jam masuk --}}
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico bg-accent">
                    <svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="#0EA5C9" stroke-width="1.3"/><path d="M8 5v3l2 2" stroke="#0EA5C9" stroke-width="1.3" stroke-linecap="round"/></svg>
                </div>
            </div>
            <div class="stat-lbl">Jam masuk</div>
            <div class="stat-val c-accent" style="font-family:'DM Mono',monospace;font-size:20px">
                {{ $absensiHariIni?->jam_masuk ?? '—' }}
            </div>
            <div class="stat-hint c-muted">hari ini</div>
        </div>

        {{-- Jam pulang --}}
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico bg-green">
                    <svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="#10B981" stroke-width="1.3"/><path d="M8 5v3l2 2" stroke="#10B981" stroke-width="1.3" stroke-linecap="round"/></svg>
                </div>
            </div>
            <div class="stat-lbl">Jam pulang</div>
            <div class="stat-val c-green" style="font-family:'DM Mono',monospace;font-size:20px">
                {{ $absensiHariIni?->jam_pulang ?? '—' }}
            </div>
            <div class="stat-hint c-muted">hari ini</div>
        </div>

        {{-- Notifikasi --}}
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico {{ $notifikasiBelumBaca > 0 ? 'bg-red' : 'bg-green' }}">
                    <svg fill="none" viewBox="0 0 16 16">
                        <path d="M8 2a4.5 4.5 0 00-4.5 4.5v2.5L2 11h12l-1.5-2V6.5A4.5 4.5 0 008 2zM6.5 13a1.5 1.5 0 003 0"
                            stroke="{{ $notifikasiBelumBaca > 0 ? '#EF4444' : '#10B981' }}"
                            stroke-width="1.3" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="notif-count {{ $notifikasiBelumBaca > 0 ? 'has' : 'none' }}">
                    {{ $notifikasiBelumBaca }}
                </span>
            </div>
            <div class="stat-lbl">Notifikasi belum dibaca</div>
            <div class="stat-val {{ $notifikasiBelumBaca > 0 ? 'c-red' : 'c-green' }}">
                {{ $notifikasiBelumBaca }}
            </div>
            <div class="stat-hint c-muted">
                {{ $notifikasiBelumBaca > 0 ? 'perlu dibaca' : 'semua terbaca' }}
            </div>
        </div>

    </div>

    {{-- ── QUICK ACTIONS ── --}}
    <div class="panel-card">
        <div class="panel-head">
            <div class="panel-title">
                <svg fill="none" viewBox="0 0 16 16"><path d="M2 8h12M8 2v12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                Aksi Cepat
            </div>
        </div>
        <div class="panel-body">
            <div class="quick-grid">
                <a href="{{ route('petugas.absensi.index') }}" class="quick-btn">
                    <div class="quick-ico" style="background:var(--accent-soft)">
                        <svg fill="none" viewBox="0 0 18 18"><rect x="2" y="3" width="14" height="13" rx="2" stroke="#0EA5C9" stroke-width="1.4"/><path d="M6 1v3M12 1v3M2 8h14" stroke="#0EA5C9" stroke-width="1.4" stroke-linecap="round"/></svg>
                    </div>
                    <div class="quick-lbl">Input Absensi</div>
                    <div class="quick-sub">Catat kehadiran hari ini</div>
                </a>
                <a href="{{ route('petugas.tugas.index') }}" class="quick-btn">
                    <div class="quick-ico" style="background:var(--green-soft)">
                        <svg fill="none" viewBox="0 0 18 18"><path d="M3 5h12M3 9h9M3 13h6" stroke="#10B981" stroke-width="1.4" stroke-linecap="round"/></svg>
                    </div>
                    <div class="quick-lbl">Input Tugas</div>
                    <div class="quick-sub">Tambah laporan harian</div>
                </a>
                <a href="{{ route('petugas.cuti.index') }}" class="quick-btn">
                    <div class="quick-ico" style="background:var(--amber-soft)">
                        <svg fill="none" viewBox="0 0 18 18"><path d="M9 2v5l3 3" stroke="#F59E0B" stroke-width="1.4" stroke-linecap="round"/><circle cx="9" cy="9" r="7" stroke="#F59E0B" stroke-width="1.4"/></svg>
                    </div>
                    <div class="quick-lbl">Ajukan Cuti</div>
                    <div class="quick-sub">Form cuti + TTD barcode</div>
                </a>
            </div>
        </div>
    </div>

    {{-- ── KALENDER (fungsi asli dipertahankan) ── --}}
    <div class="panel-card">
        <div class="panel-head">
            <div class="panel-title">
                <svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                Kalender Terdekat
            </div>
            <span style="font-size:11px;color:var(--text3)">{{ $kalender->count() }} event</span>
        </div>
        <div class="panel-body">
            <div class="kalender-list">
                @forelse($kalender as $item)
                    <div class="kalender-item">
                        <div class="kalender-dot">
                            <svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                        </div>
                        <div class="kalender-info">
                            <div class="kalender-nama">{{ $item->nama_event }}</div>
                            <div class="kalender-tgl">{{ $item->tanggal->format('d/m/Y') }}</div>
                        </div>
                        <span class="kalender-badge">{{ $item->jenis_event }}</span>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-ico">
                            <svg fill="none" viewBox="0 0 20 20"><rect x="2" y="3" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M6 1v3M14 1v3M2 8h16" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                        </div>
                        <div class="empty-title">Belum ada event kalender</div>
                        <div class="empty-sub">Event dan jadwal akan muncul di sini</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>


@endsection