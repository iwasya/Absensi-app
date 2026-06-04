@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<style>
    main { max-width: 100% !important; margin: 0 !important; padding: 24px 28px !important; }

    .admin-dash { display: flex; flex-direction: column; gap: 18px; }

    .dash-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
    .dash-title h1 { margin: 0; font-size: 20px; font-weight: 700; color: var(--text-color); }
    .dash-sub { margin-top: 4px; font-size: 12px; color: var(--muted); }
    .dash-pill {
        display: inline-flex; align-items: center; gap: 7px; white-space: nowrap;
        padding: 6px 14px; border-radius: 99px; border: 1px solid var(--primary-border);
        color: var(--primary2); background: var(--primary-soft); font-size: 12px; font-weight: 600;
    }
    .dash-pill svg { width: 14px; height: 14px; }

    .stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .stat-card {
        min-width: 0; background: var(--panel-bg); border: 1px solid var(--border-color);
        border-radius: 14px; padding: 16px; transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { box-shadow: 0 6px 18px rgba(14, 165, 201, .08); transform: translateY(-1px); }
    .stat-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 10px; }
    .stat-ico { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex: 0 0 auto; }
    .stat-ico svg { width: 18px; height: 18px; }
    .stat-badge { font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 99px; white-space: nowrap; }
    .stat-label { font-size: 11px; color: var(--muted); margin-bottom: 4px; }
    .stat-value { font-size: 24px; font-weight: 750; color: var(--text-color); line-height: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .stat-hint { margin-top: 6px; font-size: 11px; color: var(--muted); }

    .bg-primary { background: var(--primary-soft); color: var(--primary); }
    .bg-green { background: var(--green-soft); color: var(--green); }
    .bg-amber { background: var(--amber-soft); color: var(--amber); }
    .bg-red { background: var(--red-soft); color: var(--red); }
    .bg-violet { background: rgba(99, 102, 241, .1); color: #6366F1; }

    .content-grid { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(320px, .9fr); gap: 14px; }
    .card-box { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 14px; overflow: hidden; }
    .card-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--border-color); }
    .card-title { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 700; color: var(--text-color); }
    .card-title svg { width: 16px; height: 16px; color: var(--primary); }
    .card-link { color: var(--primary); text-decoration: none; font-size: 12px; font-weight: 600; white-space: nowrap; }
    .card-link:hover { color: var(--primary2); }
    .card-body { padding: 16px 18px; }

    .queue-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    .queue-item { background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; padding: 13px 14px; min-width: 0; }
    .queue-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
    .queue-name { font-size: 12px; font-weight: 700; color: var(--text-color); }
    .queue-count { font-size: 20px; line-height: 1; font-weight: 800; }
    .queue-note { margin-top: 6px; font-size: 11px; color: var(--muted); }

    .period-box { display: grid; gap: 12px; }
    .period-main { padding: 14px; border-radius: 12px; background: var(--bg-color); border: 1px solid var(--border-color); }
    .period-label { font-size: 11px; color: var(--muted); margin-bottom: 5px; }
    .period-name { font-size: 18px; font-weight: 800; color: var(--text-color); line-height: 1.25; }
    .period-meta { margin-top: 6px; font-size: 11px; color: var(--muted); }

    .quick-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }
    .quick-link {
        min-width: 0; display: flex; align-items: center; gap: 10px; padding: 12px;
        color: var(--text-color); text-decoration: none; background: var(--bg-color);
        border: 1px solid var(--border-color); border-radius: 12px;
    }
    .quick-link:hover { border-color: var(--primary-border); background: var(--primary-soft); }
    .quick-ico { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex: 0 0 auto; }
    .quick-ico svg { width: 16px; height: 16px; }
    .quick-text { min-width: 0; }
    .quick-title { display: block; font-size: 12px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .quick-sub { display: block; margin-top: 2px; font-size: 10.5px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    @media (max-width: 1100px) { .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .content-grid { grid-template-columns: 1fr; } }
    @media (max-width: 720px) { main { padding: 16px !important; } .quick-grid, .queue-grid { grid-template-columns: 1fr; } }
    @media (max-width: 520px) { .stat-grid { grid-template-columns: 1fr; } }
</style>

<div class="admin-dash">
    <div class="dash-header">
        <div class="dash-title">
            <h1>Dashboard Admin</h1>
            <div class="dash-sub">{{ now()->translatedFormat('l, d F Y') }} - Ringkasan operasional aplikasi absensi</div>
        </div>
        <div class="dash-pill">
            <svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            {{ now()->translatedFormat('F Y') }}
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-ico bg-primary"><svg fill="none" viewBox="0 0 16 16"><path d="M5.5 7a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM1.5 14c.4-2.5 1.9-4 4-4s3.6 1.5 4 4M11 7.5a2 2 0 1 0 0-4M10.5 10c1.8.1 3.1 1.5 3.5 4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                <span class="stat-badge" style="background:var(--primary-soft);color:var(--primary2)">User</span>
            </div>
            <div class="stat-label">Total user</div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-hint">seluruh akun terdaftar</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-ico bg-green"><svg fill="none" viewBox="0 0 16 16"><path d="M3 8l3.5 3.5L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                <span class="stat-badge" style="background:var(--green-soft);color:var(--green-dark)">Petugas</span>
            </div>
            <div class="stat-label">Petugas PPSU</div>
            <div class="stat-value">{{ $totalPetugas }}</div>
            <div class="stat-hint">akun petugas aktif di sistem</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-ico bg-violet"><svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                <span class="stat-badge" style="background:rgba(99,102,241,.1);color:#4338CA">Hari ini</span>
            </div>
            <div class="stat-label">Absensi hari ini</div>
            <div class="stat-value">{{ $totalAbsensiHariIni }}</div>
            <div class="stat-hint">catatan masuk hari ini</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-ico bg-amber"><svg fill="none" viewBox="0 0 16 16"><path d="M8 2v7M8 12.5h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/></svg></div>
                <span class="stat-badge" style="background:var(--amber-soft);color:var(--amber-dark)">Pending</span>
            </div>
            <div class="stat-label">Antrean admin</div>
            <div class="stat-value">{{ ($cutiPendingAdmin ?? 0) + ($approvalPulangPending ?? 0) + ($tugasPending ?? 0) }}</div>
            <div class="stat-hint">cuti, approval pulang, dan tugas</div>
        </div>
    </div>

    <div class="content-grid">
        <div class="card-box">
            <div class="card-head">
                <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Antrean Persetujuan</span>
                <a href="{{ route('admin.cuti.index') }}" class="card-link">Cek cuti</a>
            </div>
            <div class="card-body">
                <div class="queue-grid">
                    <div class="queue-item">
                        <div class="queue-row">
                            <div class="queue-name">Cuti pending</div>
                            <div class="queue-count" style="color:var(--red)">{{ $cutiPending }}</div>
                        </div>
                        <div class="queue-note">pengajuan menunggu keputusan awal</div>
                    </div>
                    <div class="queue-item">
                        <div class="queue-row">
                            <div class="queue-name">Cuti pending admin</div>
                            <div class="queue-count" style="color:var(--amber)">{{ $cutiPendingAdmin ?? 0 }}</div>
                        </div>
                        <div class="queue-note">perlu verifikasi dari admin</div>
                    </div>
                    <div class="queue-item">
                        <div class="queue-row">
                            <div class="queue-name">Approval pulang</div>
                            <div class="queue-count" style="color:var(--primary)">{{ $approvalPulangPending ?? 0 }}</div>
                        </div>
                        <div class="queue-note">request lupa absen pulang</div>
                    </div>
                    <div class="queue-item">
                        <div class="queue-row">
                            <div class="queue-name">Tugas pending</div>
                            <div class="queue-count" style="color:#6366F1">{{ $tugasPending }}</div>
                        </div>
                        <div class="queue-note">laporan tugas belum selesai ditinjau</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-box">
            <div class="card-head">
                <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Periode Aktif</span>
                <a href="{{ route('admin.periode.index') }}" class="card-link">Kelola</a>
            </div>
            <div class="card-body">
                <div class="period-box">
                    <div class="period-main">
                        <div class="period-label">Periode berjalan</div>
                        <div class="period-name">{{ $periodeAktif?->nama_periode ?? 'Belum ada periode aktif' }}</div>
                        <div class="period-meta">Digunakan sebagai acuan absensi, cuti, dan laporan tugas.</div>
                    </div>
                    <a class="quick-link" href="{{ route('admin.buka-absen.index') }}">
                        <span class="quick-ico bg-amber"><svg fill="none" viewBox="0 0 16 16"><path d="M8 5v3l2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/></svg></span>
                        <span class="quick-text">
                            <span class="quick-title">Buka akses absen</span>
                            <span class="quick-sub">beri akses telat sesuai kebutuhan</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-box">
        <div class="card-head">
            <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><path d="M2 4h12M2 8h12M2 12h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Akses Cepat</span>
            <a href="{{ route('admin.logs.index') }}" class="card-link">Log aktivitas</a>
        </div>
        <div class="card-body">
            <div class="quick-grid">
                <a class="quick-link" href="{{ route('admin.users.index') }}">
                    <span class="quick-ico bg-primary"><svg fill="none" viewBox="0 0 16 16"><path d="M5.5 7a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM1.5 14c.4-2.5 1.9-4 4-4s3.6 1.5 4 4M11 7.5a2 2 0 1 0 0-4M10.5 10c1.8.1 3.1 1.5 3.5 4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></span>
                    <span class="quick-text"><span class="quick-title">Kelola user</span><span class="quick-sub">akun, role, regu, dan tempat</span></span>
                </a>
                <a class="quick-link" href="{{ route('admin.tempat.index') }}">
                    <span class="quick-ico bg-green"><svg fill="none" viewBox="0 0 16 16"><path d="M8 14s5-4.1 5-8A5 5 0 0 0 3 6c0 3.9 5 8 5 8Z" stroke="currentColor" stroke-width="1.3"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg></span>
                    <span class="quick-text"><span class="quick-title">Tempat tugas</span><span class="quick-sub">lokasi dan area kerja</span></span>
                </a>
                <a class="quick-link" href="{{ route('admin.kalender.index') }}">
                    <span class="quick-ico bg-violet"><svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></span>
                    <span class="quick-text"><span class="quick-title">Kalender</span><span class="quick-sub">event dan hari libur</span></span>
                </a>
                <a class="quick-link" href="{{ route('admin.cuti.index') }}">
                    <span class="quick-ico bg-red"><svg fill="none" viewBox="0 0 16 16"><path d="M4 3h8M4 7h8M4 11h5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></span>
                    <span class="quick-text"><span class="quick-title">Pengajuan cuti</span><span class="quick-sub">review dan export cuti</span></span>
                </a>
                <a class="quick-link" href="{{ route('admin.sift.index') }}">
                    <span class="quick-ico bg-amber"><svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3l2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></span>
                    <span class="quick-text"><span class="quick-title">Shift kerja</span><span class="quick-sub">jadwal dan penugasan shift</span></span>
                </a>
                <a class="quick-link" href="{{ route('admin.pengaturan.index') }}">
                    <span class="quick-ico bg-primary"><svg fill="none" viewBox="0 0 16 16"><path d="M8 5.5A2.5 2.5 0 1 0 8 10.5 2.5 2.5 0 0 0 8 5.5Z" stroke="currentColor" stroke-width="1.3"/><path d="M8 2v1.2M8 12.8V14M2 8h1.2M12.8 8H14M3.8 3.8l.9.9M11.3 11.3l.9.9M12.2 3.8l-.9.9M4.7 11.3l-.9.9" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></span>
                    <span class="quick-text"><span class="quick-title">Pengaturan</span><span class="quick-sub">konfigurasi sistem</span></span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
