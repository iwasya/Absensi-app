@extends('layouts.app')

@section('title', 'Regu & Penugasan')

@section('content')
<style>
    main { max-width: 100% !important; margin: 0 !important; padding: 24px !important; }
    .regu-page { display: flex; flex-direction: column; gap: 16px; }
    .regu-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .regu-header h1 { margin: 0 0 4px; font-size: 22px; }
    .regu-muted { color: var(--muted); font-size: 13px; }
    .regu-badge { display: inline-flex; align-items: center; gap: 7px; border: 1px solid var(--primary-border); background: var(--primary-soft); color: var(--primary2); border-radius: 999px; padding: 7px 12px; font-size: 12px; font-weight: 600; }
    .regu-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .regu-card { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 14px; padding: 16px; min-width: 0; }
    .regu-card-icon { width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; margin-bottom: 12px; }
    .regu-card-icon svg { width: 18px; height: 18px; }
    .regu-label { color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 5px; }
    .regu-value { color: var(--text-color); font-size: 17px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .regu-sub { color: var(--muted); font-size: 12px; margin-top: 6px; line-height: 1.45; }
    .regu-section { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 14px; overflow: hidden; }
    .regu-section-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 14px 16px; border-bottom: 1px solid var(--border-color); background: var(--bg-color); }
    .regu-section-title { font-size: 15px; font-weight: 700; color: var(--text-color); }
    .regu-table-wrap { overflow-x: auto; }
    .regu-table { width: 100%; min-width: 760px; border-collapse: collapse; font-size: 13px; }
    .regu-table th { padding: 11px 14px; text-align: left; background: var(--bg-color); border-bottom: 1px solid var(--border-color); color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: .04em; }
    .regu-table td { padding: 13px 14px; border-bottom: 1px solid var(--border-color); color: var(--text-color); vertical-align: middle; }
    .regu-table tr:last-child td { border-bottom: 0; }
    .regu-role { display: inline-flex; align-items: center; border-radius: 999px; padding: 5px 10px; font-size: 12px; font-weight: 700; background: var(--bg-color); color: var(--muted); border: 1px solid var(--border2); white-space: nowrap; }
    .regu-role.lead { background: var(--amber-soft); color: var(--amber-dark); border-color: #fde68a; }
    @media (max-width: 980px) { .regu-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 560px) { .regu-grid { grid-template-columns: 1fr; } main { padding: 16px !important; } }
</style>

<div class="regu-page">
    <div class="regu-header">
        <div>
            <h1>Regu & Penugasan</h1>
            <div class="regu-muted">Informasi regu, shift, tempat kerja, dan anggota regu kamu.</div>
        </div>
        @if($user->regu)
            <div class="regu-badge">
                <svg fill="none" viewBox="0 0 16 16" width="14" height="14"><path d="M5 6.5a3 3 0 116 0M2.5 13a5.5 5.5 0 0111 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                {{ $anggotaRegu->count() }} anggota
            </div>
        @else
            <div class="regu-badge" style="background:var(--amber-soft);border-color:#fde68a;color:var(--amber-dark);">
                Belum masuk regu
            </div>
        @endif
    </div>

    <div class="regu-grid">
        <div class="regu-card">
            <div class="regu-card-icon" style="background:var(--green-soft);color:var(--green);">
                <svg fill="none" viewBox="0 0 16 16"><path d="M5 6.5a3 3 0 116 0M2.5 13a5.5 5.5 0 0111 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            </div>
            <div class="regu-label">Regu</div>
            <div class="regu-value">{{ $user->regu ?? '-' }}</div>
            <div class="regu-sub">{{ $user->isKetuaRegu() ? 'Kamu ketua regu' : 'Anggota regu' }}</div>
        </div>
        <div class="regu-card">
            <div class="regu-card-icon" style="background:var(--primary-soft);color:var(--primary);">
                <svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 4.5V8l2.5 1.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            </div>
            <div class="regu-label">Shift</div>
            <div class="regu-value">{{ $user->shift ?? '-' }}</div>
            <div class="regu-sub">{{ $shift ? $shift->jam_kerja . ' (' . $shift->durasi_jam . ' jam)' : 'Belum ada shift aktif' }}</div>
        </div>
        <div class="regu-card">
            <div class="regu-card-icon" style="background:var(--amber-soft);color:var(--amber-dark);">
                <svg fill="none" viewBox="0 0 16 16"><path d="M8 14s5-4.2 5-8A5 5 0 003 6c0 3.8 5 8 5 8z" stroke="currentColor" stroke-width="1.3"/><circle cx="8" cy="6" r="1.7" stroke="currentColor" stroke-width="1.3"/></svg>
            </div>
            <div class="regu-label">Tempat Kerja</div>
            <div class="regu-value" title="{{ $user->tempatTugas->nama_tempat ?? '-' }}">{{ $user->tempatTugas->nama_tempat ?? '-' }}</div>
            <div class="regu-sub">{{ $user->tempatTugas->alamat ?? 'Alamat belum diisi' }}</div>
        </div>
        <div class="regu-card">
            <div class="regu-card-icon" style="background:var(--red-soft);color:var(--red);">
                <svg fill="none" viewBox="0 0 16 16"><path d="M8 2l2 4 4 .6-3 3 .7 4.4L8 12l-3.7 2 .7-4.4-3-3L6 6l2-4z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
            </div>
            <div class="regu-label">Ketua Regu</div>
            <div class="regu-value">{{ $ketuaRegu->nama ?? '-' }}</div>
            <div class="regu-sub">{{ $ketuaRegu?->no_hp ? 'HP: ' . $ketuaRegu->no_hp : 'Kontak belum diisi' }}</div>
        </div>
    </div>

    <div class="regu-section">
        <div class="regu-section-head">
            <div class="regu-section-title">Anggota Regu</div>
            <div class="regu-muted">{{ $user->regu ? 'Regu ' . $user->regu : 'Belum masuk regu' }}</div>
        </div>
        <div class="regu-table-wrap">
            <table class="regu-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Shift</th>
                        <th>Tempat Kerja</th>
                        <th>No HP</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anggotaRegu as $anggota)
                        <tr>
                            <td>
                                <strong>{{ $anggota->nama }}</strong>
                                @if($anggota->id_user === $user->id_user)
                                    <div class="regu-sub">Kamu</div>
                                @endif
                            </td>
                            <td>
                                <span class="regu-role {{ $anggota->is_ketua_regu ? 'lead' : '' }}">
                                    {{ $anggota->is_ketua_regu ? 'Ketua Regu' : ($anggota->jabatan ?: 'Anggota') }}
                                </span>
                            </td>
                            <td>{{ $anggota->shift ?? '-' }}</td>
                            <td>{{ $anggota->tempatTugas->nama_tempat ?? '-' }}</td>
                            <td>{{ $anggota->no_hp ?? '-' }}</td>
                            <td>{{ ucfirst($anggota->status_aktif ?? 'aktif') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="regu-muted">
                                {{ $user->regu ? 'Belum ada anggota regu yang tercatat.' : 'Kamu belum dimasukkan ke regu, jadi daftar anggota belum ditampilkan.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
