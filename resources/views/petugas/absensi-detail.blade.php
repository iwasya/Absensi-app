@extends('layouts.app')

@section('title', 'Detail Absensi')

@section('content')
@php
    $statusText = ucfirst(str_replace('_', ' ', $item->status ?? 'belum absen'));
    $initials = collect(explode(' ', trim($item->user->nama ?? $item->user->username ?? 'P')))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->implode('');
    $approvalMasukText = $item->approval_masuk_status
        ? ucfirst(str_replace('_', ' ', $item->approval_masuk_status))
        : 'Tidak ada';
    $approvalPulangText = $item->approval_pulang_status
        ? ucfirst(str_replace('_', ' ', $item->approval_pulang_status))
        : 'Tidak ada';
@endphp

<style>
    main {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 24px 28px !important;
    }

    .attendance-detail {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .detail-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .detail-title {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .detail-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border: 1px solid var(--primary-border);
        border-radius: 12px;
        background: var(--primary-soft);
        color: var(--primary);
        flex: 0 0 auto;
    }

    .detail-icon svg,
    .detail-section-title svg,
    .detail-action svg,
    .detail-back svg {
        width: 18px;
        height: 18px;
    }

    .detail-title h1 {
        margin: 0;
        color: var(--text-color);
        font-size: 22px;
        line-height: 1.2;
    }

    .detail-subtitle {
        margin-top: 5px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.45;
    }

    .detail-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 38px;
        padding: 9px 13px;
        border: 1px solid var(--border2);
        border-radius: 8px;
        background: var(--bg-color);
        color: var(--text-color);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .detail-back:hover {
        border-color: var(--primary-border);
        color: var(--primary);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: minmax(260px, 1.25fr) repeat(3, minmax(160px, 1fr));
        gap: 14px;
    }

    .summary-card,
    .detail-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
    }

    .summary-card {
        min-height: 92px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .person-avatar {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        background: var(--primary-soft);
        color: var(--primary);
        font-size: 14px;
        font-weight: 800;
        flex: 0 0 auto;
        text-transform: uppercase;
    }

    .summary-label {
        color: var(--muted);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .summary-value {
        margin-top: 6px;
        color: var(--text-color);
        font-size: 18px;
        font-weight: 800;
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    .summary-note {
        margin-top: 4px;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .content-grid {
        display: grid;
        grid-template-columns: minmax(280px, .9fr) minmax(0, 1.4fr);
        gap: 16px;
        align-items: start;
    }

    .stack {
        display: grid;
        gap: 16px;
    }

    .detail-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-color);
    }

    .detail-section-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        color: var(--text-color);
        font-size: 15px;
        font-weight: 700;
    }

    .detail-section-title svg {
        color: var(--primary);
    }

    .detail-card-body {
        padding: 16px;
    }

    .info-list {
        display: grid;
        gap: 12px;
    }

    .info-row {
        display: grid;
        grid-template-columns: 130px minmax(0, 1fr);
        gap: 12px;
        align-items: start;
    }

    .info-label {
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
    }

    .info-value {
        color: var(--text-color);
        font-size: 13px;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .timeline-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .check-card {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background: var(--panel-bg);
        overflow: hidden;
    }

    .check-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 13px 14px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-color);
    }

    .check-title {
        color: var(--text-color);
        font-size: 14px;
        font-weight: 800;
    }

    .check-time {
        color: var(--primary);
        font-size: 18px;
        font-weight: 800;
        white-space: nowrap;
    }

    .photo-frame {
        width: 100%;
        aspect-ratio: 4 / 3;
        display: grid;
        place-items: center;
        background: var(--input-bg);
        border-bottom: 1px solid var(--border-color);
        overflow: hidden;
    }

    .photo-frame img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    .photo-empty {
        display: grid;
        gap: 8px;
        place-items: center;
        padding: 24px;
        color: var(--muted);
        font-size: 13px;
        text-align: center;
    }

    .photo-empty svg {
        width: 32px;
        height: 32px;
        color: var(--primary);
    }

    .check-body {
        display: grid;
        gap: 12px;
        padding: 14px;
    }

    .detail-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 36px;
        width: fit-content;
        padding: 8px 12px;
        border: 1px solid var(--primary-border);
        border-radius: 8px;
        background: var(--primary-soft);
        color: var(--primary2);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .detail-action:hover {
        color: var(--primary);
        filter: brightness(1.02);
    }

    .approval-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .approval-item {
        display: grid;
        gap: 8px;
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background: var(--bg-color);
    }

    .approval-meta {
        color: var(--muted);
        font-size: 12px;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .muted-text {
        color: var(--muted);
    }

    .attendance-detail .badge.approved,
    .attendance-detail .badge.approve {
        background: var(--green-soft);
        color: var(--green-dark);
        border-color: #A7F3D0;
    }

    .attendance-detail .badge.pending,
    .attendance-detail .badge.pending_ketua,
    .attendance-detail .badge.pending_atasan {
        background: var(--amber-soft);
        color: var(--amber-dark);
        border-color: #FDE68A;
    }

    .attendance-detail .badge.rejected,
    .attendance-detail .badge.reject,
    .attendance-detail .badge.tidak_absen,
    .attendance-detail .badge.alpa {
        background: var(--red-soft);
        color: var(--red-dark);
        border-color: #FCA5A5;
    }

    @media (max-width: 1100px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        main {
            padding: 18px !important;
        }

        .summary-grid,
        .timeline-grid,
        .approval-grid {
            grid-template-columns: 1fr;
        }

        .detail-title h1 {
            font-size: 19px;
        }

        .info-row {
            grid-template-columns: 1fr;
            gap: 4px;
        }
    }
</style>

<div class="attendance-detail">
    <div class="detail-header">
        <div class="detail-title">
            <div class="detail-icon">
                <svg fill="none" viewBox="0 0 16 16"><rect x="2.5" y="2.5" width="11" height="12" rx="2" stroke="currentColor" stroke-width="1.3"/><path d="M5 1.5v3M11 1.5v3M2.5 6.5h11M5.5 9h3.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            </div>
            <div>
                <h1>Detail Absensi</h1>
                <div class="detail-subtitle">{{ $item->tanggal->translatedFormat('l, d F Y') }}</div>
            </div>
        </div>
        <a href="{{ url()->previous() }}" class="detail-back">
            <svg fill="none" viewBox="0 0 16 16"><path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Kembali
        </a>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="person-avatar">{{ $initials ?: 'P' }}</div>
            <div style="min-width:0;">
                <div class="summary-label">Petugas</div>
                <div class="summary-value">{{ $item->user->nama ?? '-' }}</div>
                <div class="summary-note">{{ $item->user->username ?? '-' }}</div>
            </div>
        </div>
        <div class="summary-card">
            <div style="min-width:0;">
                <div class="summary-label">Status</div>
                <div class="summary-value"><span class="badge {{ $item->status }}">{{ $statusText }}</span></div>
                <div class="summary-note">Tanggal data {{ $item->tanggal->format('d/m/Y') }}</div>
            </div>
        </div>
        <div class="summary-card">
            <div style="min-width:0;">
                <div class="summary-label">Jam Masuk</div>
                <div class="summary-value">{{ $item->jam_masuk ?? '--:--' }}</div>
                <div class="summary-note">Shift {{ $item->shift ?: '-' }}</div>
            </div>
        </div>
        <div class="summary-card">
            <div style="min-width:0;">
                <div class="summary-label">Jam Pulang</div>
                <div class="summary-value">{{ $item->jam_pulang ?? '--:--' }}</div>
                <div class="summary-note">Istirahat {{ $item->jam_istirahat_mulai ?? '--:--' }} - {{ $item->jam_istirahat_selesai ?? '--:--' }}</div>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <div class="stack">
            <div class="detail-card">
                <div class="detail-card-head">
                    <h2 class="detail-section-title">
                        <svg fill="none" viewBox="0 0 16 16"><path d="M8 8a3 3 0 100-6 3 3 0 000 6zM2.5 14a5.5 5.5 0 0111 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                        Informasi Petugas
                    </h2>
                </div>
                <div class="detail-card-body">
                    <div class="info-list">
                        <div class="info-row">
                            <div class="info-label">Nama</div>
                            <div class="info-value">{{ $item->user->nama ?? '-' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Username</div>
                            <div class="info-value">{{ $item->user->username ?? '-' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tempat Tugas</div>
                            <div class="info-value">{{ $item->user->tempatTugas->nama_tempat ?? '-' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Shift</div>
                            <div class="info-value">{{ $item->shift ?: '-' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Keterangan</div>
                            <div class="info-value">{{ $item->keterangan ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <div class="detail-card-head">
                    <h2 class="detail-section-title">
                        <svg fill="none" viewBox="0 0 16 16"><path d="M3 8h10M8 3v10" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/></svg>
                        Approval
                    </h2>
                </div>
                <div class="detail-card-body">
                    <div class="approval-grid">
                        <div class="approval-item">
                            <div class="summary-label">Absen Masuk</div>
                            @if($item->approval_masuk_status)
                                <span class="badge {{ $item->approval_masuk_status }}">{{ $approvalMasukText }}</span>
                            @else
                                <span class="muted-text">{{ $approvalMasukText }}</span>
                            @endif
                            <div class="approval-meta">
                                {{ $item->approval_masuk_reason ?: 'Tidak ada alasan approval.' }}
                                @if($item->approval_masuk_requested_at)
                                    <br>Diajukan {{ $item->approval_masuk_requested_at->translatedFormat('d F Y H:i') }}
                                @endif
                            </div>
                        </div>
                        <div class="approval-item">
                            <div class="summary-label">Absen Pulang</div>
                            @if($item->approval_pulang_status)
                                <span class="badge {{ $item->approval_pulang_status }}">{{ $approvalPulangText }}</span>
                            @else
                                <span class="muted-text">{{ $approvalPulangText }}</span>
                            @endif
                            <div class="approval-meta">
                                {{ $item->approval_pulang_reason ?: 'Tidak ada alasan approval.' }}
                                @if($item->approval_pulang_requested_at)
                                    <br>Diajukan {{ $item->approval_pulang_requested_at->translatedFormat('d F Y H:i') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-card-head">
                <h2 class="detail-section-title">
                    <svg fill="none" viewBox="0 0 16 16"><path d="M3 8l3.5 3.5L13 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Data Absensi
                </h2>
            </div>
            <div class="detail-card-body">
                <div class="timeline-grid">
                    <div class="check-card">
                        <div class="check-head">
                            <div class="check-title">Masuk</div>
                            <div class="check-time">{{ $item->jam_masuk ?? '--:--' }}</div>
                        </div>
                        <div class="photo-frame">
                            @if($item->foto_masuk)
                                <img src="{{ route('absensi.photo', [$item, 'masuk']) }}" alt="Foto masuk {{ $item->user->nama ?? 'petugas' }}" loading="lazy" decoding="async">
                            @else
                                <div class="photo-empty">
                                    <svg fill="none" viewBox="0 0 16 16"><path d="M2.5 5.5h2l1-2h3l1 2h2a2 2 0 012 2v5a2 2 0 01-2 2h-9a2 2 0 01-2-2v-5a2 2 0 012-2z" stroke="currentColor" stroke-width="1.3"/><path d="M5.5 10a2.5 2.5 0 115 0 2.5 2.5 0 01-5 0z" stroke="currentColor" stroke-width="1.3"/></svg>
                                    Tidak ada foto masuk
                                </div>
                            @endif
                        </div>
                        <div class="check-body">
                            <div class="info-list">
                                <div class="info-row">
                                    <div class="info-label">Lokasi</div>
                                    <div class="info-value">{{ $item->lokasi_masuk ?: '-' }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Koordinat</div>
                                    <div class="info-value">{{ $item->latitude_masuk ?? '-' }}, {{ $item->longitude_masuk ?? '-' }}</div>
                                </div>
                            </div>
                            @if($item->latitude_masuk && $item->longitude_masuk)
                                <a href="https://www.google.com/maps?q={{ $item->latitude_masuk }},{{ $item->longitude_masuk }}" target="_blank" rel="noopener" class="detail-action">
                                    <svg fill="none" viewBox="0 0 16 16"><path d="M8 14s4.5-4 4.5-8A4.5 4.5 0 103.5 6c0 4 4.5 8 4.5 8z" stroke="currentColor" stroke-width="1.3"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                                    Lihat Maps
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="check-card">
                        <div class="check-head">
                            <div class="check-title">Pulang</div>
                            <div class="check-time">{{ $item->jam_pulang ?? '--:--' }}</div>
                        </div>
                        <div class="photo-frame">
                            @if($item->foto_pulang)
                                <img src="{{ route('absensi.photo', [$item, 'pulang']) }}" alt="Foto pulang {{ $item->user->nama ?? 'petugas' }}" loading="lazy" decoding="async">
                            @else
                                <div class="photo-empty">
                                    <svg fill="none" viewBox="0 0 16 16"><path d="M2.5 5.5h2l1-2h3l1 2h2a2 2 0 012 2v5a2 2 0 01-2 2h-9a2 2 0 01-2-2v-5a2 2 0 012-2z" stroke="currentColor" stroke-width="1.3"/><path d="M5.5 10a2.5 2.5 0 115 0 2.5 2.5 0 01-5 0z" stroke="currentColor" stroke-width="1.3"/></svg>
                                    Tidak ada foto pulang
                                </div>
                            @endif
                        </div>
                        <div class="check-body">
                            <div class="info-list">
                                <div class="info-row">
                                    <div class="info-label">Lokasi</div>
                                    <div class="info-value">{{ $item->lokasi_pulang ?: '-' }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Koordinat</div>
                                    <div class="info-value">{{ $item->latitude_pulang ?? '-' }}, {{ $item->longitude_pulang ?? '-' }}</div>
                                </div>
                            </div>
                            @if($item->latitude_pulang && $item->longitude_pulang)
                                <a href="https://www.google.com/maps?q={{ $item->latitude_pulang }},{{ $item->longitude_pulang }}" target="_blank" rel="noopener" class="detail-action">
                                    <svg fill="none" viewBox="0 0 16 16"><path d="M8 14s4.5-4 4.5-8A4.5 4.5 0 103.5 6c0 4 4.5 8 4.5 8z" stroke="currentColor" stroke-width="1.3"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                                    Lihat Maps
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
