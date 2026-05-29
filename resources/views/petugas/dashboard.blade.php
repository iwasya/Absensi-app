@extends('layouts.app')

@section('title', 'Dashboard Petugas')

@section('content')

<style>
    main { max-width: 100% !important; margin: 0 !important; padding: 24px 28px !important; }

    .dash-wrap { display: flex; flex-direction: column; gap: 18px; }

    /* â”€â”€ Header â”€â”€ */
    .dash-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .dash-header h1 { font-size: 20px; font-weight: 600; color: var(--text-color); margin: 0; }
    .dash-header-sub { font-size: 12px; color: var(--muted); margin-top: 3px; }
    .dash-header-sub strong { color: var(--text-color); }
    .dash-date-pill {
        background: var(--primary-soft); border: 1px solid var(--primary-border);
        border-radius: 99px; padding: 5px 14px;
        font-size: 12px; font-weight: 500; color: var(--primary2);
        display: flex; align-items: center; gap: 6px; white-space: nowrap;
    }
    .dash-date-pill svg { width: 13px; height: 13px; }
    /* â”€â”€ Stat Cards â”€â”€ */
    .stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; }
    .stat-card { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 14px; padding: 16px; transition: box-shadow .2s; }
    .stat-card:hover { box-shadow: 0 4px 16px rgba(14,165,201,.08); }
    .stat-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
    .stat-ico { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-ico svg { width: 17px; height: 17px; }
    .stat-badge { font-size: 10px; font-weight: 600; padding: 3px 8px; border-radius: 99px; }
    .stat-lbl { font-size: 11px; color: var(--muted); margin-bottom: 4px; }
    .stat-val { font-size: 22px; font-weight: 600; line-height: 1; }
    .stat-hint { font-size: 11px; margin-top: 4px; }
    .stat-bar { height: 3px; background: var(--bg-color); border-radius: 99px; margin-top: 8px; overflow: hidden; }
    .stat-bar-fill { height: 100%; border-radius: 99px; }
    .attention-strip { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    .attention-item { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 11px 14px; border: 1px solid var(--border-color); border-radius: 12px; background: var(--panel-bg); }
    .attention-title { color: var(--text-color); font-size: 13px; font-weight: 600; }
    .attention-sub { color: var(--muted); font-size: 11px; margin-top: 2px; }
    .bg-green  { background: var(--green-soft); }
    .bg-amber  { background: var(--amber-soft); }
    .bg-red    { background: var(--red-soft); }
    .bg-accent { background: var(--primary-soft); }

    /* â”€â”€ Mid Row â”€â”€ */
    .mid-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .card-box { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 14px; overflow: hidden; }
    .card-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid var(--border-color); }
    .card-title { font-size: 14px; font-weight: 600; color: var(--text-color); display: flex; align-items: center; gap: 8px; }
    .card-title svg { width: 16px; height: 16px; color: var(--primary); }
    .card-link { font-size: 12px; font-weight: 500; color: var(--primary); text-decoration: none; }
    .card-link:hover { color: var(--primary2); }
    .card-body { padding: 16px 18px; }

    /* â”€â”€ Donut â”€â”€ */
    .donut-section { display: grid; grid-template-columns: 150px minmax(0,1fr); align-items: center; gap: 22px; padding: 4px 0; }
    .donut-wrap { position: relative; width: 142px; height: 142px; border-radius: 50%; flex-shrink: 0; background: var(--bg-color); box-shadow: inset 0 0 0 1px var(--border-color); }
    .donut-wrap::after { content: ""; position: absolute; inset: 18px; border-radius: 50%; background: var(--panel-bg); box-shadow: 0 0 0 1px var(--border-color); }
    .donut-label { position: absolute; inset: 0; z-index: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; }
    .donut-num { font-size: 28px; font-weight: 700; color: var(--text-color); line-height: 1; }
    .donut-sub { font-size: 10px; color: var(--muted); margin-top: 5px; text-transform: uppercase; letter-spacing: .04em; }
    .donut-meta { display: grid; gap: 12px; min-width: 0; }
    .donut-summary { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 8px; }
    .donut-summary-item { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 10px; padding: 9px 10px; min-width: 0; }
    .donut-summary-label { font-size: 10px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .donut-summary-value { font-size: 17px; font-weight: 700; color: var(--text-color); margin-top: 2px; }
    .legend-list { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 8px 12px; }
    .legend-item { display: flex; align-items: center; gap: 7px; min-width: 0; font-size: 12px; color: var(--muted); }
    .legend-text { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }

    /* â”€â”€ Feed â”€â”€ */
    .feed-list { display: flex; flex-direction: column; }
    .feed-item { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--border-color); }
    .feed-item:last-child { border-bottom: none; }
    .feed-ava { width: 32px; height: 32px; border-radius: 8px; background: var(--primary-soft); color: var(--primary2); font-size: 11px; font-weight: 600; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .feed-info { flex: 1; overflow: hidden; }
    .feed-name { font-size: 13px; font-weight: 500; color: var(--text-color); }
    .feed-desc { font-size: 11px; color: var(--muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .feed-badge { font-size: 10px; font-weight: 600; padding: 3px 9px; border-radius: 99px; white-space: nowrap; }
    .fb-ok   { background: var(--green-soft); color: var(--green-dark); }
    .fb-late { background: var(--red-soft);   color: var(--red-dark); }
    .fb-info { background: var(--primary-soft); color: var(--primary2); }

    /* â”€â”€ Bottom Row â”€â”€ */
    .bot-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    /* â”€â”€ Calendar â”€â”€ */
    .cal-nav { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid var(--border-color); }
    .cal-month { font-size: 14px; font-weight: 600; color: var(--text-color); }
    .cal-arrow { width: 28px; height: 28px; border-radius: 7px; border: 1px solid var(--border2); background: var(--panel-bg); color: var(--muted); font-size: 16px; line-height: 1; cursor: pointer; display: flex; align-items: center; justify-content: center; font-family: inherit; transition: background .15s; }
    .cal-arrow:hover { background: var(--bg-color); color: var(--text-color); }
    .cal-body { padding: 12px 16px 14px; }
    .cal-day-labels { display: grid; grid-template-columns: repeat(7,1fr); gap: 2px; margin-bottom: 4px; }
    .cal-day-lbl { text-align: center; font-size: 10px; font-weight: 600; color: var(--muted); text-transform: uppercase; padding: 4px 0; }
    .cal-grid { display: grid; grid-template-columns: repeat(7,1fr); gap: 2px; }
    .cal-cell { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 11px; border-radius: 7px; color: var(--text-color); position: relative; cursor: pointer; border: 1px solid transparent; background: var(--bg-color); font-family: inherit; }
    .cal-cell.other { color: var(--border2); opacity: .65; }
    .cal-cell.today { background: var(--primary-soft); color: var(--primary); font-weight: 700; border: 1px solid var(--primary-border); }
    .cal-cell.selected { border-color: var(--primary); box-shadow: inset 0 0 0 1px var(--primary); }
    .cal-dot-stack { position: absolute; bottom: 3px; left: 50%; transform: translateX(-50%); display: flex; align-items: center; gap: 2px; }
    .cal-cell .cal-dot { width: 4px; height: 4px; border-radius: 50%; }
    .cal-detail { margin: 10px 16px 14px; padding: 12px 14px; border-radius: 10px; background: var(--bg-color); border: 1px solid var(--border-color); }
    .cal-detail-title { font-size: 12px; font-weight: 600; color: var(--text-color); margin-bottom: 8px; }
    .cal-detail-list { display: flex; flex-direction: column; gap: 8px; }
    .cal-detail-row { display: flex; align-items: flex-start; gap: 8px; min-width: 0; }
    .cal-detail-dot { width: 7px; height: 7px; border-radius: 50%; margin-top: 6px; flex: 0 0 auto; }
    .cal-detail-text { min-width: 0; flex: 1; }
    .cal-detail-main { font-size: 12px; color: var(--text-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cal-detail-sub { font-size: 11px; color: var(--muted); margin-top: 2px; line-height: 1.4; }
    .cal-legend { display: flex; flex-wrap: wrap; gap: 12px; padding: 10px 16px 14px; border-top: 1px solid var(--border-color); }
    .cal-leg-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--muted); }
    .cal-leg-dot { width: 7px; height: 7px; border-radius: 50%; }

    /* â”€â”€ Detail Hari Ini â”€â”€ */
    .detail-card-body { padding: 16px 18px; display: flex; flex-direction: column; gap: 10px; }
    .detail-row { background: var(--bg-color); border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; }
    .detail-label { font-size: 11px; color: var(--muted); margin-bottom: 4px; }
    .detail-time { font-size: 22px; font-weight: 700; font-family: 'DM Mono', monospace; line-height: 1; }
    .detail-task-row { background: var(--bg-color); border-radius: 10px; padding: 12px 14px; display: flex; flex-direction: column; gap: 7px; }
    .detail-task-desc { font-size: 12.5px; color: var(--text-color); line-height: 1.6; }

    /* â”€â”€ Empty â”€â”€ */
    .empty-state { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 28px 16px; text-align: center; }
    .empty-ico { width: 44px; height: 44px; border-radius: 12px; background: var(--bg-color); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; }
    .empty-ico svg { width: 20px; height: 20px; color: var(--muted); }
    .empty-title { font-size: 13px; font-weight: 500; color: var(--text-color); }
    .empty-sub   { font-size: 12px; color: var(--muted); }

    @media (max-width: 991px) { .stat-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 860px) { .mid-grid, .bot-grid { grid-template-columns: 1fr; } }
    @media (max-width: 600px) {
        .stat-grid { grid-template-columns: repeat(2,1fr); gap: 8px; }
        .attention-strip { grid-template-columns: 1fr; }
        .donut-section { grid-template-columns: 1fr; justify-items: center; }
        .donut-meta { width: 100%; }
        main { padding: 16px !important; }
    }
</style>

<div class="dash-wrap">

    {{-- â•â•â• HEADER â•â•â• --}}
    <div class="dash-header">
        <div>
            <h1>Halo, {{ auth()->user()->nama ?? 'Petugas' }}</h1>
            <div class="dash-header-sub">
                {{ now()->translatedFormat('l, d F Y') }}
                @if($user?->tempatTugas)
                    - {{ $user->tempatTugas->nama_tempat ?? '' }}
                @endif
            </div>
        </div>
        <div class="dash-date-pill">
            <svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            {{ now()->translatedFormat('F Y') }}
        </div>
    </div>

    {{-- â•â•â• STAT CARDS â•â•â• --}}
    @php
        $totalHadir = $rekapBulan['hadir'] ?? 0;
        $totalTelat = $rekapBulan['telat'] ?? 0;
        $totalAbsen = $rekapBulan['tidak_hadir'] ?? 0;
        $totalCuti = $rekapBulan['cuti'] ?? 0;
        $totalHariKerja = $rekapBulan['hari_kerja'] ?? 1;
        $totalKehadiran = $totalHadir + $totalTelat;
        $totalCatatanAbsensi = $totalHadir + $totalTelat + $totalAbsen + $totalCuti;
    @endphp
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico bg-accent"><svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="var(--primary)" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="var(--primary)" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                <span class="stat-badge" style="background:var(--primary-soft);color:var(--primary2)">Cuti</span>
            </div>
            <div class="stat-lbl">Sisa cuti tahunan</div>
            <div class="stat-val" style="color:var(--primary)">{{ $sisaCutiTahunIni ?? 12 }}</div>
            <div class="stat-hint" style="color:var(--primary2)">hari tersisa</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico bg-amber"><svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="#F59E0B" stroke-width="1.3"/><path d="M8 5v3l2 2" stroke="#F59E0B" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                <span class="stat-badge" style="background:var(--amber-soft);color:var(--amber-dark)">Perhatian</span>
            </div>
            <div class="stat-lbl">Jumlah telat</div>
            <div class="stat-val" style="color:var(--amber)">{{ $totalTelat }}</div>
            <div class="stat-hint" style="color:var(--amber-dark)">kali bulan ini</div>
            @if($totalHariKerja > 0)
            <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ min(($totalTelat/$totalHariKerja)*100,100) }}%;background:var(--amber)"></div></div>
            @endif
        </div>
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico bg-green"><svg fill="none" viewBox="0 0 16 16"><path d="M3 8l3.5 3.5L13 5" stroke="#10B981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                <span class="stat-badge" style="background:var(--green-soft);color:var(--green-dark)">{{ $totalAbsen == 0 ? 'Bagus' : 'Perhatian' }}</span>
            </div>
            <div class="stat-lbl">Tidak absen pagi/sore</div>
            <div class="stat-val" style="color:{{ $totalAbsen == 0 ? 'var(--green)' : 'var(--red)' }}">{{ $totalAbsen }}</div>
            <div class="stat-hint" style="color:var(--green-dark)">bulan ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico bg-amber"><svg fill="none" viewBox="0 0 16 16"><path d="M8 2.5l5.5 10H2.5L8 2.5z" stroke="#F59E0B" stroke-width="1.3" stroke-linejoin="round"/><path d="M8 6v3M8 11.5h.01" stroke="#F59E0B" stroke-width="1.5" stroke-linecap="round"/></svg></div>
                <span class="stat-badge" style="background:var(--amber-soft);color:var(--amber-dark)">{{ ($approvalPending ?? 0) > 0 ? 'Pending' : 'Aman' }}</span>
            </div>
            <div class="stat-lbl">Approval pending</div>
            <div class="stat-val" style="color:var(--amber)">{{ $approvalPending ?? 0 }}</div>
            <div class="stat-hint" style="color:var(--amber-dark)">menunggu atasan/admin</div>
        </div>
    </div>

    @if(($teguranBelumDiakui ?? 0) > 0 || ($tugasLupaInput ?? 0) > 0 || (($totalTugas ?? 0) > 0 && ($tugasDisetujui ?? 0) < ($totalTugas ?? 0)))
        <div class="attention-strip">
            @if(($teguranBelumDiakui ?? 0) > 0)
                <div class="attention-item">
                    <div>
                        <div class="attention-title">Teguran belum diakui</div>
                        <div class="attention-sub">Konfirmasi di menu Sanksi</div>
                    </div>
                    <span class="feed-badge fb-late">{{ $teguranBelumDiakui }}</span>
                </div>
            @endif
            @if(($tugasLupaInput ?? 0) > 0)
                <div class="attention-item">
                    <div>
                        <div class="attention-title">Laporan tugas terlambat input</div>
                        <div class="attention-sub">Cek menu Tugas</div>
                    </div>
                    <span class="feed-badge fb-info">{{ $tugasLupaInput }}</span>
                </div>
            @endif
            @if(($totalTugas ?? 0) > 0 && ($tugasDisetujui ?? 0) < ($totalTugas ?? 0))
                <div class="attention-item">
                    <div>
                        <div class="attention-title">Tugas belum selesai approval</div>
                        <div class="attention-sub">{{ $tugasDisetujui ?? 0 }} dari {{ $totalTugas ?? 0 }} tugas disetujui</div>
                    </div>
                    <span class="feed-badge fb-info">{{ ($totalTugas ?? 0) - ($tugasDisetujui ?? 0) }}</span>
                </div>
            @endif
        </div>
    @endif

    {{-- â•â•â• MID ROW â•â•â• --}}
    <div class="mid-grid">
        {{-- Donut --}}
        <div class="card-box">
            <div class="card-head">
                <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 2v6l4.2 4.2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Absensi {{ now()->translatedFormat('F') }}</span>
                <a href="{{ route('petugas.absensi.index') }}" class="card-link">Lihat detail</a>
            </div>
            <div class="card-body">
                <div class="donut-section">
                    @php
                        $chartTotal = max($totalCatatanAbsensi, 1);
                        $hadirEnd = round(($totalHadir / $chartTotal) * 360, 2);
                        $telatEnd = round((($totalHadir + $totalTelat) / $chartTotal) * 360, 2);
                        $absenEnd = round((($totalHadir + $totalTelat + $totalAbsen) / $chartTotal) * 360, 2);
                        $donutStyle = $totalCatatanAbsensi > 0
                            ? "background: conic-gradient(var(--green) 0 {$hadirEnd}deg, var(--amber) {$hadirEnd}deg {$telatEnd}deg, var(--red) {$telatEnd}deg {$absenEnd}deg, #EC4899 {$absenEnd}deg 360deg);"
                            : 'background: var(--bg-color);';
                    @endphp
                    <div class="donut-wrap" style="{{ $donutStyle }}">
                        <div class="donut-label"><div class="donut-num">{{ $totalCatatanAbsensi }}</div><div class="donut-sub">catatan</div></div>
                    </div>
                    <div class="donut-meta">
                        <div class="donut-summary">
                            <div class="donut-summary-item">
                                <div class="donut-summary-label">Masuk tercatat</div>
                                <div class="donut-summary-value">{{ $totalKehadiran }}</div>
                            </div>
                            <div class="donut-summary-item">
                                <div class="donut-summary-label">Hari kerja</div>
                                <div class="donut-summary-value">{{ $totalHariKerja }}</div>
                            </div>
                        </div>
                        <div class="legend-list">
                            <div class="legend-item"><div class="legend-dot" style="background:var(--green)"></div><span class="legend-text">Tepat waktu ({{ $totalHadir }})</span></div>
                            <div class="legend-item"><div class="legend-dot" style="background:var(--amber)"></div><span class="legend-text">Telat ({{ $totalTelat }})</span></div>
                            <div class="legend-item"><div class="legend-dot" style="background:var(--red)"></div><span class="legend-text">Tidak absen ({{ $totalAbsen }})</span></div>
                            @if($totalCuti > 0)
                                <div class="legend-item"><div class="legend-dot" style="background:#EC4899"></div><span class="legend-text">Cuti ({{ $totalCuti }})</span></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Aktivitas Terbaru --}}
        <div class="card-box">
            <div class="card-head">
                <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Aktivitas terbaru</span>
                <a href="{{ route('petugas.tugas.kalender') }}" class="card-link">Lihat kalender</a>
            </div>
            <div class="card-body">
                <div class="feed-list">
                    @php
                        $aktivitasFeed = collect($aktivitasTerbaru ?? collect())->map(function ($act) {
                            $actStatus = $act->status ?? $act->modul ?? '-';
                            $badgeClass = match($actStatus) {
                                'hadir' => 'fb-ok',
                                'terlambat', 'telat' => 'fb-late',
                                default => 'fb-info',
                            };

                            return [
                                'type' => 'activity',
                                'title' => $act->aktivitas ?? ucfirst($act->modul ?? 'Aktivitas'),
                                'desc' => ($act->created_at?->format('H:i') ?? '--:--') . ' - ' . ($act->created_at?->translatedFormat('l, d M') ?? '-'),
                                'badge' => ucfirst(str_replace('_', ' ', $actStatus)),
                                'badge_class' => $badgeClass,
                            ];
                        });

                        $eventFeed = collect($kalender ?? collect())->map(fn ($event) => [
                            'type' => 'event',
                            'title' => $event->nama_event,
                            'desc' => 'Event - ' . ($event->tanggal?->translatedFormat('l, d M Y') ?? '-'),
                            'badge' => ucfirst(str_replace('_', ' ', $event->jenis_event ?? 'Event')),
                            'badge_class' => 'fb-info',
                        ]);

                        $feedItems = $aktivitasFeed->concat($eventFeed);
                    @endphp

                    @forelse($feedItems as $feed)
                        <div class="feed-item">
                            @if($feed['type'] === 'event')
                                <div class="feed-ava" style="background:var(--primary-soft);color:var(--primary);"><svg fill="none" viewBox="0 0 16 16" width="14" height="14"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                            @else
                                <div class="feed-ava">{{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 2)) }}</div>
                            @endif
                            <div class="feed-info">
                                <div class="feed-name">{{ $feed['title'] }}</div>
                                <div class="feed-desc">{{ $feed['desc'] }}</div>
                            </div>
                            <span class="feed-badge {{ $feed['badge_class'] }}">{{ $feed['badge'] }}</span>
                        </div>
                    @empty
                        <div class="empty-state" style="padding:24px 12px;">
                            <div class="empty-ico"><svg fill="none" viewBox="0 0 16 16"><path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                            <div class="empty-title">Belum ada aktivitas</div>
                            <div class="empty-sub">Aktivitas dan event akan muncul di sini</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- â•â•â• BOTTOM ROW â•â•â• --}}
    <div class="bot-grid">
        {{-- Kalender --}}
        <div class="card-box">
            <div class="cal-nav">
                <button class="cal-arrow" id="prev-btn">&#8249;</button>
                <div class="cal-month" id="cal-title">{{ now()->translatedFormat('F Y') }}</div>
                <button class="cal-arrow" id="next-btn">&#8250;</button>
            </div>
            <div class="cal-body">
                <div class="cal-day-labels">
                    <div class="cal-day-lbl">Min</div><div class="cal-day-lbl">Sen</div><div class="cal-day-lbl">Sel</div>
                    <div class="cal-day-lbl">Rab</div><div class="cal-day-lbl">Kam</div><div class="cal-day-lbl">Jum</div>
                    <div class="cal-day-lbl">Sab</div>
                </div>
                <div class="cal-grid" id="cal-grid"></div>
            </div>
            <div class="cal-detail" id="cal-detail"></div>
            <div class="cal-legend">
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--green)"></div>Hadir</div>
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--amber)"></div>Telat</div>
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--red)"></div>Tidak Absen</div>
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:#EC4899"></div>Cuti</div>
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:#8B5CF6"></div>Tugas</div>
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:#0EA5E9"></div>Event</div>
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:#14B8A6"></div>Libur Mingguan</div>
            </div>
        </div>

        {{-- Detail Hari Ini --}}
        <div class="card-box">
            <div class="card-head">
                <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3l2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Detail hari ini</span>
                <span class="dash-date-pill" style="font-size:11px;padding:3px 10px;">{{ now()->translatedFormat('l, d M') }}</span>
            </div>
            <div class="detail-card-body">
                <div class="detail-row">
                    <div>
                        <div class="detail-label">Jam masuk</div>
                        <div class="detail-time" style="color:var(--green)">{{ $absensiHariIni?->jam_masuk ?? '--:--' }}</div>
                    </div>
                    @if($absensiHariIni?->jam_masuk)
                        <span class="feed-badge {{ in_array($absensiHariIni->status, ['telat','terlambat']) ? 'fb-late' : 'fb-ok' }}">
                            {{ in_array($absensiHariIni->status, ['telat','terlambat']) ? 'Telat' : 'Tepat' }}
                        </span>
                    @else
                        <span class="feed-badge" style="background:var(--bg-color);color:var(--muted);border:1px solid var(--border2)">Belum</span>
                    @endif
                </div>
                <div class="detail-row">
                    <div>
                        <div class="detail-label">Jam pulang</div>
                        <div class="detail-time" style="color:var(--primary)">{{ $absensiHariIni?->jam_pulang ?? '--:--' }}</div>
                    </div>
                    @if($absensiHariIni?->jam_pulang)
                        <span class="feed-badge fb-ok">Tepat</span>
                    @else
                        <span class="feed-badge" style="background:var(--bg-color);color:var(--muted);border:1px solid var(--border2)">Belum</span>
                    @endif
                </div>
                <div class="detail-task-row">
                    <div class="detail-label">Tugas hari ini</div>
                    @if(isset($tugasHariIni) && $tugasHariIni)
                        <div class="detail-task-desc">{{ $tugasHariIni->uraian ?? 'Tidak ada tugas' }}</div>
                        <span class="feed-badge fb-info" style="width:fit-content">{{ ucfirst($tugasHariIni->status ?? 'pending') }}</span>
                    @else
                        <div class="detail-task-desc" style="color:var(--muted)">Belum ada tugas diinput hari ini</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<script>
(function() {
    var curDate = new Date(), today = new Date();
    var months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    var hadirDays = {!! json_encode($kalenderHadir ?? []) !!};
    var telatDays = {!! json_encode($kalenderTelat ?? []) !!};
    var absenDays = {!! json_encode($kalenderAbsen ?? []) !!};
    var cutiDays = {!! json_encode($kalenderCuti ?? []) !!};
    var weeklyOffDays = {!! json_encode($kalenderLiburMingguan ?? []) !!};
    var absensiDetails = @json($absensiCalendarDetails ?? []);
    var tugasDetails = @json($tugasCalendarDetails ?? []);
    var eventDetails = @json($eventCalendarDetails ?? []);
    var weeklyOffDetails = @json($weeklyOffCalendarDetails ?? []);
    var selectedKey = keyFromDate(today);

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function keyFromDate(date) {
        return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate());
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char];
        });
    }

    function absensiColor(status) {
        var normalized = String(status || '').toLowerCase();
        if (normalized.indexOf('telat') !== -1 || normalized.indexOf('terlambat') !== -1) return 'var(--amber)';
        if (normalized.indexOf('tidak') !== -1 || normalized.indexOf('absen') !== -1) return 'var(--red)';
        if (normalized.indexOf('cuti') !== -1) return '#EC4899';
        return 'var(--green)';
    }

    function detailRows(key) {
        var rows = [];
        (absensiDetails[key] || []).forEach(function(item) {
            rows.push({ color: absensiColor(item.nama), title: item.nama, meta: item.waktu + ' - ' + item.status });
        });
        (tugasDetails[key] || []).forEach(function(item) {
            rows.push({ color: '#8B5CF6', title: item.nama, meta: item.waktu + ' - ' + item.status });
        });
        (eventDetails[key] || []).forEach(function(item) {
            rows.push({ color: '#0EA5E9', title: item.nama, meta: item.status });
        });
        (weeklyOffDetails[key] || []).forEach(function(item) {
            rows.push({ color: '#14B8A6', title: item.nama, meta: item.status + ' - ' + item.waktu });
        });
        return rows;
    }

    function colorsForDate(key, dayNumber) {
        var colors = [];
        if (hadirDays.indexOf(dayNumber) !== -1) colors.push('var(--green)');
        if (telatDays.indexOf(dayNumber) !== -1) colors.push('var(--amber)');
        if (absenDays.indexOf(dayNumber) !== -1) colors.push('var(--red)');
        if (cutiDays.indexOf(dayNumber) !== -1) colors.push('#EC4899');
        if ((tugasDetails[key] || []).length) colors.push('#8B5CF6');
        if ((eventDetails[key] || []).length) colors.push('#0EA5E9');
        if (weeklyOffDays.indexOf(dayNumber) !== -1) colors.push('#14B8A6');
        return colors.slice(0, 5);
    }

    function appendDots(cell, colors) {
        if (!colors.length) return;
        var stack = document.createElement('div');
        stack.className = 'cal-dot-stack';
        colors.forEach(function(color) {
            var dot = document.createElement('div');
            dot.className = 'cal-dot';
            dot.style.background = color;
            stack.appendChild(dot);
        });
        cell.appendChild(stack);
    }

    function renderDetail(key, date) {
        var box = document.getElementById('cal-detail');
        if (!box) return;
        var rows = detailRows(key);
        var title = date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();

        if (!rows.length) {
            box.innerHTML = '<div class="cal-detail-title">' + escapeHtml(title) + '</div>'
                + '<div class="cal-detail-sub">Belum ada keterangan pada tanggal ini.</div>';
            return;
        }

        box.innerHTML = '<div class="cal-detail-title">' + escapeHtml(title) + '</div>'
            + '<div class="cal-detail-list">'
            + rows.map(function(row) {
                return '<div class="cal-detail-row">'
                    + '<span class="cal-detail-dot" style="background:' + row.color + '"></span>'
                    + '<div class="cal-detail-text">'
                    + '<div class="cal-detail-main">' + escapeHtml(row.title) + '</div>'
                    + '<div class="cal-detail-sub">' + escapeHtml(row.meta) + '</div>'
                    + '</div>'
                    + '</div>';
            }).join('')
            + '</div>';
    }

    function markSelected() {
        document.querySelectorAll('#cal-grid .cal-cell').forEach(function(cell) {
            cell.classList.toggle('selected', cell.dataset.date === selectedKey);
        });
    }

    function makeCell(text, className, date) {
        var cell = document.createElement('button');
        cell.type = 'button';
        cell.className = className;
        cell.textContent = text;
        cell.dataset.date = keyFromDate(date);

        cell.addEventListener('click', function() {
            selectedKey = cell.dataset.date;
            renderDetail(selectedKey, date);
            markSelected();
        });

        return cell;
    }

    function buildCal() {
        var g = document.getElementById('cal-grid'), t = document.getElementById('cal-title');
        if (!g || !t) return;
        t.textContent = months[curDate.getMonth()] + ' ' + curDate.getFullYear();
        g.innerHTML = '';
        var first = new Date(curDate.getFullYear(), curDate.getMonth(), 1).getDay();
        var daysInM = new Date(curDate.getFullYear(), curDate.getMonth()+1, 0).getDate();
        var prevDays = new Date(curDate.getFullYear(), curDate.getMonth(), 0).getDate();
        var isCurM = curDate.getFullYear()===today.getFullYear() && curDate.getMonth()===today.getMonth();
        for (var i=0;i<first;i++){
            var prevDate = new Date(curDate.getFullYear(), curDate.getMonth() - 1, prevDays - first + 1 + i);
            g.appendChild(makeCell(prevDays-first+1+i, 'cal-cell other', prevDate));
        }
        for (var n=1;n<=daysInM;n++){
            var date = new Date(curDate.getFullYear(), curDate.getMonth(), n);
            var c=makeCell(n, 'cal-cell', date);
            if(isCurM&&n===today.getDate())c.classList.add('today');
            if(isCurM) appendDots(c, colorsForDate(keyFromDate(date), n));
            g.appendChild(c);
        }
        var remain=7-((first+daysInM)%7);if(remain<7)for(var j=1;j<=remain;j++){
            var nextDate = new Date(curDate.getFullYear(), curDate.getMonth() + 1, j);
            g.appendChild(makeCell(j, 'cal-cell other', nextDate));
        }
        markSelected();
        renderDetail(selectedKey, new Date(selectedKey + 'T00:00:00'));
    }
    buildCal();
    var pb=document.getElementById('prev-btn'),nb=document.getElementById('next-btn');
    if(pb)pb.addEventListener('click',function(){curDate=new Date(curDate.getFullYear(),curDate.getMonth()-1,1);buildCal();});
    if(nb)nb.addEventListener('click',function(){curDate=new Date(curDate.getFullYear(),curDate.getMonth()+1,1);buildCal();});
})();
</script>
@endsection
