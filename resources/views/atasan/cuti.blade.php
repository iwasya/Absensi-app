@extends('layouts.app')

@section('title', 'Approve Cuti')

@section('content')
<style>
    main {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 24px 28px !important;
    }

    .leave-page {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .leave-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .leave-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .leave-title h1 {
        margin: 0;
        font-size: 22px;
        line-height: 1.2;
    }

    .leave-title-icon {
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

    .leave-title-icon svg,
    .leave-card-title svg,
    .leave-date-icon svg {
        width: 18px;
        height: 18px;
    }

    .leave-count {
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

    .leave-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
    }

    .leave-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
        flex-wrap: wrap;
    }

    .leave-card-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        color: var(--text-color);
        font-size: 15px;
        font-weight: 600;
    }

    .leave-card-title svg {
        color: var(--primary);
    }

    .leave-filter {
        display: flex;
        align-items: end;
        gap: 12px;
        padding: 16px;
        flex-wrap: wrap;
    }

    .leave-filter-control {
        min-width: 220px;
    }

    .leave-filter-note {
        margin: 0;
        padding: 0 16px 16px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .leave-link,
    .leave-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .leave-link {
        background: var(--bg-color);
        color: var(--text-color);
        border: 1px solid var(--border2);
    }

    .leave-table-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .leave-table {
        width: 100%;
        min-width: 1120px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }

    .leave-table th {
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

    .leave-table td {
        padding: 13px 14px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: top;
    }

    .leave-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .leave-table tbody tr:hover {
        background: var(--bg-color);
    }

    .person-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        width: max-content;
        min-width: 0;
    }

    .person-avatar {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: var(--green-soft);
        color: var(--green-dark);
        font-size: 12px;
        font-weight: 800;
        flex: 0 0 auto;
    }

    .person-name,
    .leave-reason {
        color: var(--text-color);
        font-weight: 700;
        line-height: 1.35;
    }

    .person-name {
        white-space: nowrap;
    }

    .leave-date {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        max-width: 150px;
    }

    .leave-date-icon {
        width: 34px;
        height: 34px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: var(--primary-soft);
        color: var(--primary);
        border: 1px solid var(--primary-border);
        flex: 0 0 auto;
    }

    .leave-date-value {
        display: grid;
        gap: 3px;
        color: var(--text-color);
        font-weight: 600;
        line-height: 1.25;
    }

    .leave-date-label {
        color: var(--muted);
        font-size: 11px;
        font-weight: 600;
    }

    .leave-date-line {
        display: block;
        white-space: nowrap;
    }

    .leave-kind {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 6px 10px;
        border-radius: 99px;
        background: var(--primary-soft);
        color: var(--primary2);
        border: 1px solid var(--primary-border);
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .leave-detail {
        max-width: 360px;
        color: var(--muted);
        line-height: 1.5;
    }

    .leave-detail small {
        display: block;
        margin-top: 3px;
        color: var(--muted);
        font-size: 12px;
    }

    .leave-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .leave-btn-approve {
        background: var(--green);
        color: #fff;
    }

    .leave-btn-reject {
        background: var(--red);
        color: #fff;
    }

    .leave-muted {
        color: var(--muted);
        font-size: 13px;
    }

    .leave-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 14px 15px;
        border-top: 1px solid var(--border-color);
    }

    .leave-result {
        color: var(--muted);
        font-size: 13px;
    }

    .leave-empty {
        padding: 38px 18px;
        text-align: center;
        color: var(--muted);
    }

    .leave-empty-icon {
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

    .leave-empty-icon svg {
        width: 20px;
        height: 20px;
    }

    .leave-empty strong {
        display: block;
        margin-bottom: 4px;
        color: var(--text-color);
        font-size: 14px;
    }

    @media (max-width: 760px) {
        main {
            padding: 16px !important;
        }

        .leave-title h1 {
            font-size: 20px;
        }

        .leave-filter-control,
        .leave-filter button,
        .leave-link {
            width: 100%;
        }
    }
</style>

<div class="leave-page">
    <div class="leave-header">
        <div class="leave-title">
            <span class="leave-title-icon" aria-hiden="true">
                <svg fill="none" viewBox="0 0 20 20">
                    <rect x="3" y="3" width="14" height="14" rx="3" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M6.5 10.2 9 12.7 14 7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <h1>Approve Cuti</h1>
        </div>
        <div class="leave-count">{{ $items->total() }} pengajuan</div>
    </div>

    <div class="leave-card">
        <div class="leave-card-head">
            <h2 class="leave-card-title">
                <svg fill="none" viewBox="0 0 16 16">
                    <path d="M3 5h10M5 8h6M7 11h2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Filter Periode
            </h2>
        </div>
        <form method="GET" action="{{ url()->current() }}" class="leave-filter">
            <div class="leave-filter-control">
                <label for="id_periode">Periode Data</label>
                <select id="id_periode" name="id_periode">
                    <option value="">Semua tahun</option>
                    @foreach($periodes ?? [] as $periode)
                        <option value="{{ $periode->id_periode }}" @selected(optional($selectedPeriode ?? null)->id_periode === $periode->id_periode)>
                            {{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('Y') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit">Tampilkan</button>
            @if(isset($selectedPeriode) && $selectedPeriode)
                <a href="{{ url()->current() }}" class="leave-link">Reset</a>
            @endif
        </form>
        <p class="leave-filter-note">
            @if(isset($selectedPeriode) && $selectedPeriode)
                Menampilkan arsip tahun {{ \Carbon\Carbon::parse($selectedPeriode->tanggal_mulai)->format('Y') }}.
            @else
                Menampilkan semua data. Pilih tahun untuk melihat arsip tahun sebelumnya.
            @endif
        </p>
    </div>

    <div class="leave-card">
        <div class="leave-card-head">
            <h2 class="leave-card-title">
                <svg fill="none" viewBox="0 0 16 16">
                    <path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Daftar Pengajuan Cuti
            </h2>
        </div>

        <div class="leave-table-scroll">
            <table class="leave-table">
                <thead>
                    <tr>
                        <th style="min-width:220px;">Petugas</th>
                        <th style="width:170px;">Tanggal</th>
                        <th style="width:160px;">Jenis</th>
                        <th>Alasan & Alamat</th>
                        <th style="width:170px;">Pengganti</th>
                        <th style="width:130px;">Konfirmasi</th>
                        <th style="width:120px;">Dokumen</th>
                        <th style="width:130px;">Admin</th>
                        <th style="width:130px;">Status</th>
                        <th style="width:170px;">Disetujui Oleh</th>
                        <th style="width:180px;">Aksi</th>
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
                                <div class="leave-date">
                                    <span class="leave-date-icon" aria-hidden="true">
                                        <svg fill="none" viewBox="0 0 16 16">
                                            <rect x="2.5" y="3.5" width="11" height="10" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
                                            <path d="M5 2v3M11 2v3M2.5 7h11" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                                        </svg>
                                    </span>
                                    <span class="leave-date-value">
                                        <span>
                                            <span class="leave-date-label">Mulai</span>
                                            <span class="leave-date-line">{{ $item->tanggal_mulai->format('d/m/y') }}</span>
                                        </span>
                                        <span>
                                            <span class="leave-date-label">Selesai</span>
                                            <span class="leave-date-line">{{ $item->tanggal_selesai->format('d/m/y') }}</span>
                                        </span>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="leave-kind">{{ str_replace('_', ' ', $item->jenis_cuti) }}</span>
                            </td>
                            <td>
                                <div class="leave-detail">
                                    <div class="leave-reason">{{ $item->alasan }}</div>
                                    @if($item->alasan == 'Alasan Lainnya')
                                        <small>{{ $item->alasan_lainnya }}</small>
                                    @endif
                                    <small>Alamat: {{ $item->alamat_cuti ?: '-' }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="leave-muted">{{ $item->pengganti->nama ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $item->replacement_status ?? 'pending' }}">
                                    {{ $item->replacement_status === 'accepted' ? 'Diterima' : ($item->replacement_status === 'rejected' ? 'Ditolak' : 'Pending') }}
                                </span>
                            </td>
                            <td>
                                @if($item->dokumen_path)
                                    <a class="leave-link" href="{{ asset('storage/' . $item->dokumen_path) }}" target="_blank">Lihat</a>
                                @else
                                    <span class="leave-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $item->admin_status }}">{{ ($item->admin_status ?? 'pending') === 'notified' ? 'Diberitahu' : ucfirst($item->admin_status ?? 'pending') }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $item->status }}">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
                            </td>
                            <td>
                                <span class="leave-muted">{{ $item->approver->nama ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="leave-actions">
                                    @if($item->status === 'pending')
                                        <form method="POST" action="{{ route('atasan.cuti.approve', $item->id_cuti) }}" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="leave-btn leave-btn-approve">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('atasan.cuti.reject', $item->id_cuti) }}" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="leave-btn leave-btn-reject">Reject</button>
                                        </form>
                                    @else
                                        <span class="leave-muted">Selesai</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <div class="leave-empty">
                                    <div class="leave-empty-icon" aria-hidden="true">
                                        <svg fill="none" viewBox="0 0 20 20">
                                            <rect x="3" y="3" width="14" height="14" rx="3" stroke="currentColor" stroke-width="1.5"/>
                                            <path d="M6.5 10.2 9 12.7 14 7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <strong>Belum ada pengajuan cuti</strong>
                                    Pengajuan cuti petugas akan tampil di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="leave-footer">
            {{ $items->links('pagination.simple') }}
        </div>
    </div>
</div>
@endsection
