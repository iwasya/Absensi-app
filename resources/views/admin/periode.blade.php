@extends('layouts.app')

@section('title', 'Periode')

@section('content')
<style>
    .period-page {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .period-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .period-header h1 {
        margin: 0;
        font-size: 22px;
        line-height: 1.2;
    }

    .period-count {
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

    .period-top-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 12px;
        align-items: start;
    }

    .period-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
    }

    .period-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .period-card-title {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: var(--text-color);
    }

    .period-card-body {
        padding: 16px;
    }

    .period-note {
        margin: 0 0 14px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .period-filter-card .filter-bar {
        margin: 0;
        padding: 16px;
        border: 0;
        border-radius: 0;
        background: transparent;
    }

    .period-form-grid {
        display: grid;
        grid-template-columns: minmax(160px, 1fr) minmax(160px, 1fr) auto;
        gap: 12px;
        align-items: end;
    }

    .period-table-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .period-table {
        width: 100%;
        min-width: 760px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }

    .period-table th {
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

    .period-table td {
        padding: 13px 14px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .period-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .period-table tbody tr:hover {
        background: var(--bg-color);
    }

    .period-year-input {
        max-width: 140px;
    }

    .period-range {
        color: var(--text-color);
        font-weight: 500;
        white-space: nowrap;
    }

    .period-status-toggle {
        display: inline-grid;
        grid-template-columns: repeat(2, minmax(86px, 1fr));
        gap: 4px;
        padding: 4px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        background: var(--bg-color);
    }

    .period-status-toggle input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .period-status-toggle label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        margin: 0;
        padding: 7px 10px;
        border-radius: 7px;
        color: var(--muted);
        cursor: pointer;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        transition: background .15s, color .15s, box-shadow .15s;
        white-space: nowrap;
    }

    .period-status-toggle input:checked + label {
        box-shadow: 0 1px 3px rgba(15, 23, 42, .08);
    }

    .period-status-toggle input[value="aktif"]:checked + label {
        background: var(--green-soft);
        color: var(--green-dark);
    }

    .period-status-toggle input[value="nonaktif"]:checked + label {
        background: var(--panel-bg);
        color: var(--text-color);
        border: 1px solid var(--border-color);
    }

    .period-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .period-btn {
        min-height: 34px;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 12.5px;
    }

    .period-btn-light {
        background: var(--bg-color);
        color: var(--text-color);
        border: 1px solid var(--border2);
    }

    .period-btn-light:hover {
        background: var(--primary-soft);
        color: var(--primary);
        border-color: var(--primary-border);
    }

    .period-btn-danger {
        background: var(--red);
    }

    .period-empty {
        padding: 36px 18px;
        text-align: center;
        color: var(--muted);
    }

    .period-empty strong {
        display: block;
        margin-bottom: 4px;
        color: var(--text-color);
        font-size: 14px;
    }

    .period-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 14px 15px;
        border-top: 1px solid var(--border-color);
    }

    .period-result {
        color: var(--muted);
        font-size: 13px;
    }

    @media (max-width: 880px) {
        .period-top-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .period-header h1 {
            font-size: 20px;
        }

        .period-form-grid {
            grid-template-columns: 1fr;
        }

        .period-form-grid button {
            width: 100%;
        }
    }
</style>

<div class="period-page">
    <div class="period-header">
        <h1>Periode</h1>
        <div class="period-count">{{ $items->total() }} total periode</div>
    </div>

    <div class="period-top-grid">
        <div class="period-card">
            <div class="period-card-head">
                <h2 class="period-card-title">Tambah Periode</h2>
            </div>
            <div class="period-card-body">
                <p class="period-note">Periode dibuat per tahun. Contoh tahun 2025 otomatis menjadi 01/01/2025 - 31/12/2025.</p>
                <form method="POST" action="{{ route('admin.periode.store') }}">
                    @csrf
                    <div class="period-form-grid">
                        <div>
                            <label for="tahun">Tahun</label>
                            <input id="tahun" type="number" name="tahun" min="2000" max="2100" value="{{ date('Y') }}" required>
                        </div>
                        <div>
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <button type="submit">Tambah</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="period-card period-filter-card">
            <div class="period-card-head">
                <h2 class="period-card-title">Tampilan</h2>
            </div>
            <form action="{{ route('admin.periode.index') }}" method="GET" class="filter-bar">
                <div class="filter-control" style="width:100%;">
                    <label for="per_page">Per Halaman</label>
                    <select id="per_page" name="per_page" onchange="this.form.submit()" style="width:100%;">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / hal</option>
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : (request('per_page') ? '' : 'selected') }}>15 / hal</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / hal</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / hal</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / hal</option>
                    </select>
                </div>
            </form>
            <form action="{{ route('admin.periode.export') }}" method="GET" class="filter-bar" style="padding-top:10px;">
                <div class="filter-control" style="width:100%;">
                    <label for="tahun_export">Export Tahun</label>
                    <input id="tahun_export" type="number" name="tahun" min="2000" max="2100" value="{{ request('tahun', date('Y')) }}">
                </div>
                <button type="submit">Export CSV</button>
            </form>
        </div>
    </div>

    <div class="period-card" style="margin-bottom:16px;">
        <div class="period-card-head">
            <h2 class="period-card-title">Status Semua User</h2>
        </div>
        <div class="period-table-scroll">
            <table class="period-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Regu</th>
                        <th>Shift</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users ?? [] as $user)
                        <tr>
                            <td>{{ $user->nama }}</td>
                            <td>{{ $user->role->nama_role ?? '-' }}</td>
                            <td>{{ $user->regu ?? '-' }}</td>
                            <td>{{ $user->shift ?? '-' }}</td>
                            <td><span class="badge {{ $user->status_aktif ?? 'aktif' }}">{{ $user->status_aktif ?? 'aktif' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="period-empty">Belum ada user.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="period-card">
        <div class="period-table-scroll">
            <table class="period-table">
                <thead>
                    <tr>
                        <th style="width:170px;">Tahun</th>
                        <th>Rentang</th>
                        <th style="width:210px;">Status</th>
                        <th style="width:190px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <input class="period-year-input" type="number" name="tahun" min="2000" max="2100" value="{{ $item->tanggal_mulai->format('Y') }}" required form="periodUpdate{{ $item->id_periode }}">
                            </td>
                            <td>
                                <div class="period-range">
                                    {{ $item->tanggal_mulai->format('d/m/Y') }} - {{ $item->tanggal_selesai->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                <div class="period-status-toggle">
                                    <input id="status_aktif_{{ $item->id_periode }}" type="radio" name="status" value="aktif" form="periodUpdate{{ $item->id_periode }}" @checked($item->status === 'aktif')>
                                    <label for="status_aktif_{{ $item->id_periode }}">Aktif</label>

                                    <input id="status_nonaktif_{{ $item->id_periode }}" type="radio" name="status" value="nonaktif" form="periodUpdate{{ $item->id_periode }}" @checked($item->status === 'nonaktif')>
                                    <label for="status_nonaktif_{{ $item->id_periode }}">Nonaktif</label>
                                </div>
                            </td>
                            <td>
                                <div class="period-actions">
                                    <form id="periodUpdate{{ $item->id_periode }}" method="POST" action="{{ route('admin.periode.update', $item->id_periode) }}" style="margin:0;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="period-btn period-btn-light">Simpan</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.periode.delete', $item->id_periode) }}" style="margin:0;" onsubmit="return confirm('Hapus periode ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="period-btn period-btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="period-empty">
                                    <strong>Belum ada periode</strong>
                                    Data periode akan tampil di sini setelah ditambahkan.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="period-footer">
            {{ $items->links('pagination.simple') }}
        </div>
    </div>
</div>
@endsection
