@extends('layouts.app')

@section('title', 'Pantau Absensi')

@section('content')
    <h1>Pantau Absensi</h1>
    <div class="panel" style="margin-bottom: 24px;">
        <form action="{{ route('atasan.absensi.index') }}" method="GET" class="filter-bar">
            <div class="filter-control">
                <label>Bulan</label>
                <select name="month">
                    <option value="">-- Semua Bulan --</option>
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="filter-control">
                <label>User</label>
                <select name="id_user">
                    <option value="">-- Semua Petugas --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id_user }}" {{ request('id_user') == $user->id_user ? 'selected' : '' }}>{{ $user->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-control">
                <label>Status</label>
                <select name="status">
                    <option value="">-- Semua Status --</option>
                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="tidak_absen" {{ request('status') == 'tidak_absen' ? 'selected' : '' }}>Tidak Absen</option>
                </select>
            </div>
            <div class="filter-control">
                <label>Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, lokasi, atau keterangan...">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit">Tampilkan</button>
                <a href="{{ route('atasan.absensi.index') }}" class="button" style="background:#f3f4f6; color:#374151; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Reset</a>
                <a href="{{ route('atasan.absensi.print', request()->all()) }}" target="_blank" style="background: #059669; color: white; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Cetak</a>
            </div>
        </form>
    </div>

    <table>
        <thead><tr><th>Petugas</th><th>Tempat</th><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->user->nama ?? '-' }}</td>
                    <td>{{ $item->user->tempatTugas->nama_tempat ?? '-' }}</td>
                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->jam_masuk ?? '-' }}</td>
                    <td>{{ $item->jam_pulang ?? '-' }}</td>
                    <td><span class="badge {{ $item->status }}">{{ $item->status }}</span></td>
                    <td>
                        <a href="{{ route('absensi.detail', $item->id_absensi) }}" class="button" style="padding: 4px 8px; font-size: 12px; background: #6366f1; color: white; border-radius: 4px; text-decoration: none;">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">Belum ada data absensi.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
