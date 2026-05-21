@extends('layouts.app')

@section('title', 'Pantau Absensi')

@section('content')
<style>
    main {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 24px 28px !important;
    }

    .monitor-page {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .monitor-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .monitor-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .monitor-title h1 {
        margin: 0;
        font-size: 22px;
        line-height: 1.2;
    }

    .monitor-icon {
        width: 40px;
        height: 40px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: var(--primary-soft);
        color: var(--primary);
        border: 1px solid var(--primary-border);
        flex: 0 0 auto;
    }

    .monitor-icon svg,
    .monitor-card-title svg,
    .date-icon svg {
        width: 18px;
        height: 18px;
    }

    .monitor-count {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 7px 13px;
        border: 1px solid var(--primary-border);
        border-radius: 99px;
        background: var(--primary-soft);
        color: var(--primary2);
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .monitor-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
    }

    .monitor-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
        flex-wrap: wrap;
    }

    .monitor-card-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        color: var(--text-color);
        font-size: 15px;
        font-weight: 600;
    }

    .monitor-card-title svg {
        color: var(--primary);
    }

    .monitor-filter {
        display: grid;
        grid-template-columns: 170px minmax(190px, 1fr) 180px minmax(220px, 1.2fr) auto;
        gap: 12px;
        align-items: end;
        padding: 16px;
    }

    .monitor-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .monitor-btn,
    .monitor-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 38px;
        padding: 9px 13px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .monitor-link-secondary {
        background: var(--bg-color);
        color: var(--text-color);
        border: 1px solid var(--border2);
    }

    .monitor-link-print {
        background: var(--green);
        color: #fff;
        border: 1px solid var(--green);
    }

    .monitor-link-detail {
        min-height: 32px;
        padding: 7px 10px;
        background: var(--primary-soft);
        color: var(--primary2);
        border: 1px solid var(--primary-border);
        font-size: 12px;
    }

    .monitor-table-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .monitor-table {
        width: 100%;
        min-width: 980px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }

    .monitor-table th {
        padding: 12px 14px;
        background: var(--bg-color);
        color: var(--muted);
        border-bottom: 1px solid var(--border-color);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .monitor-table td {
        padding: 13px 14px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .monitor-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .monitor-table tbody tr:hover {
        background: var(--bg-color);
    }

    .person-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .person-avatar {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: var(--primary-soft);
        color: var(--primary);
        font-size: 12px;
        font-weight: 800;
        flex: 0 0 auto;
    }

    .person-name {
        color: var(--text-color);
        font-weight: 700;
        line-height: 1.3;
    }

    .place-text {
        max-width: 240px;
        color: var(--muted);
        line-height: 1.45;
    }

    .date-cell {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
    }

    .date-icon {
        width: 34px;
        height: 34px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: var(--bg-color);
        color: var(--primary);
        border: 1px solid var(--border-color);
        flex: 0 0 auto;
    }

    .date-value {
        color: var(--text-color);
        font-weight: 600;
    }

    .time-value {
        font-family: 'DM Mono', monospace;
        color: var(--text-color);
        font-size: 13px;
        white-space: nowrap;
    }

    .time-empty {
        color: var(--muted);
    }

    .status-badge {
        text-transform: capitalize;
    }

    .monitor-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 14px 15px;
        border-top: 1px solid var(--border-color);
    }

    .monitor-result {
        color: var(--muted);
        font-size: 13px;
    }

    .monitor-empty {
        padding: 38px 18px;
        text-align: center;
        color: var(--muted);
    }

    .monitor-empty-icon {
        width: 44px;
        height: 44px;
        display: grid;
        place-items: center;
        margin: 0 auto 10px;
        border-radius: 12px;
        background: var(--bg-color);
        color: var(--muted);
        border: 1px solid var(--border-color);
    }

    .monitor-empty-icon svg {
        width: 20px;
        height: 20px;
    }

    .monitor-empty strong {
        display: block;
        margin-bottom: 4px;
        color: var(--text-color);
        font-size: 14px;
    }

    @media (max-width: 1180px) {
        .monitor-filter {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        main {
            padding: 16px !important;
        }

        .monitor-title h1 {
            font-size: 20px;
        }

        .monitor-filter {
            grid-template-columns: 1fr;
        }

        .monitor-actions,
        .monitor-btn,
        .monitor-link {
            width: 100%;
        }
    }
</style>

<div class="monitor-page">
    <div class="monitor-header">
        <div class="monitor-title">
            <span class="monitor-icon" aria-hidden="true">
                <svg fill="none" viewBox="0 0 20 20">
                    <path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </span>
            <h1>Pantau Absensi</h1>
        </div>
        <div class="monitor-count">{{ $items->total() }} data absensi</div>
    </div>

    <div class="monitor-card">
        <div class="monitor-card-head">
            <h2 class="monitor-card-title">
                <svg fill="none" viewBox="0 0 16 16">
                    <path d="M3 5h10M5 8h6M7 11h2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Filter Data
            </h2>
        </div>

        <form action="{{ route('atasan.absensi.index') }}" method="GET" class="monitor-filter">
            <div>
                <label for="month">Bulan</label>
                <select id="month" name="month">
                    <option value="">Semua Bulan</option>
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="id_user">Petugas</label>
                <select id="id_user" name="id_user">
                    <option value="">Semua Petugas</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id_user }}" {{ request('id_user') == $user->id_user ? 'selected' : '' }}>{{ $user->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="telat" {{ request('status') == 'telat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="tidak_absen" {{ request('status') == 'tidak_absen' ? 'selected' : '' }}>Tidak Absen</option>
                    <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                </select>
            </div>

            <div>
                <label for="search">Cari</label>
                <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Nama, lokasi, atau keterangan...">
            </div>

            <div class="monitor-actions">
                <button type="submit" class="monitor-btn">Tampilkan</button>
                <a href="{{ route('atasan.absensi.index') }}" class="monitor-link monitor-link-secondary">Reset</a>
                <a href="{{ route('atasan.absensi.print', request()->all()) }}" target="_blank" class="monitor-link monitor-link-print">Cetak</a>
            </div>
        </form>
    </div>

    <div class="monitor-card">
        <div class="monitor-card-head">
            <h2 class="monitor-card-title">
                <svg fill="none" viewBox="0 0 16 16">
                    <path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Data Absensi
            </h2>
        </div>

        <div class="monitor-table-scroll">
            <table class="monitor-table">
                <thead>
                    <tr>
                        <th>Petugas</th>
                        <th>Tempat</th>
                        <th style="width:180px;">Tanggal</th>
                        <th style="width:110px;">Masuk</th>
                        <th style="width:110px;">Pulang</th>
                        <th style="width:150px;">Status</th>
                        <th style="width:170px;">Approval Pulang</th>
                        <th style="width:110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <div class="person-cell">
                                    <div class="person-avatar">{{ strtoupper(substr($item->user->nama ?? 'U', 0, 1)) }}</div>
                                    <div class="person-name">{{ $item->user->nama ?? '-' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="place-text">{{ $item->user->tempatTugas->nama_tempat ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="date-cell">
                                    <span class="date-icon" aria-hidden="true">
                                        <svg fill="none" viewBox="0 0 16 16">
                                            <rect x="2.5" y="3.5" width="11" height="10" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
                                            <path d="M5 2v3M11 2v3M2.5 7h11" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                                        </svg>
                                    </span>
                                    <span class="date-value">{{ $item->tanggal->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="{{ $item->jam_masuk ? 'time-value' : 'time-empty' }}">{{ $item->jam_masuk ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="{{ $item->jam_pulang ? 'time-value' : 'time-empty' }}">{{ $item->jam_pulang ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="badge status-badge {{ $item->status }}">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                            </td>
                            <td>
                                @if($item->approval_pulang_status === 'pending_atasan')
                                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                        <form method="POST" action="{{ route('atasan.absensi.approve-pulang', $item->id_absensi) }}" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="monitor-link monitor-link-print" style="border:0;">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('atasan.absensi.reject-pulang', $item->id_absensi) }}" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="monitor-link monitor-link-secondary" style="border-color:var(--red);color:var(--red);">Reject</button>
                                        </form>
                                    </div>
                                    <small class="place-text">{{ $item->approval_pulang_reason }}</small>
                                @elseif($item->approval_pulang_status)
                                    <span class="badge {{ $item->approval_pulang_status }}">{{ ucfirst($item->approval_pulang_status) }}</span>
                                @else
                                    <span class="time-empty">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('absensi.detail', $item->id_absensi) }}" class="monitor-link monitor-link-detail">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="monitor-empty">
                                    <div class="monitor-empty-icon" aria-hidden="true">
                                        <svg fill="none" viewBox="0 0 20 20">
                                            <path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                    </div>
                                    <strong>Belum ada data absensi</strong>
                                    Data absensi akan tampil di sini setelah petugas melakukan absensi.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="monitor-footer">
            <div class="monitor-result">Menampilkan {{ $items->count() }} dari {{ $items->total() }} data</div>
            {{ $items->links('pagination.simple') }}
        </div>
    </div>
</div>
@endsection
