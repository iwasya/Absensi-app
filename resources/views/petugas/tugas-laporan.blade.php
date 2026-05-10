@extends('layouts.app')

@section('title', 'Lap. Tugas Harian')

@section('content')
    <h1>Lap. Tugas Harian</h1>
    <div class="panel">
        <p class="muted">Riwayat laporan tugas harian kamu. Laporan akan muncul di akun atasan untuk approve/reject.</p>
        <a href="{{ route('petugas.tugas.input') }}">Input tugas baru</a>
    </div>

    <div class="panel" style="margin-bottom: 24px;">
        <form action="{{ route('petugas.tugas.laporan') }}" method="GET" class="filter-bar">
            <div class="filter-control">
                <label>Bulan</label>
                <select name="month">
                    <option value="">-- Pilih Bulan --</option>
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="filter-control">
                <label>Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Uraian atau status...">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit">Tampilkan</button>
                <a href="{{ route('petugas.tugas.laporan') }}" class="button" style="background:#f3f4f6; color:#374151; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Reset</a>
                <a href="{{ route('petugas.tugas.laporan.print', request()->all()) }}" target="_blank" style="background: #059669; color: white; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Cetak</a>
            </div>
        </form>
    </div>

    <table>
        <thead><tr><th>Mulai</th><th>Selesai</th><th>Uraian</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->tanggal_mulai->format('d/m/Y H:i') }}</td>
                    <td>{{ $item->tanggal_selesai?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>{{ $item->uraian }}</td>
                    <td><span class="badge {{ $item->status }}">{{ $item->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Belum ada laporan tugas.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
