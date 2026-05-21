@extends('layouts.app')

@section('title', 'Sanksi')

@section('content')
    <h1>Sanksi</h1>
    <div class="panel">
        <form method="POST" action="{{ route('atasan.sanksi.store') }}">
            @csrf
            <div class="form-grid">
                <div>
                    <label>User</label>
                    <select name="id_user" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id_user }}">{{ $user->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Jenis Sanksi</label>
                    <select name="jenis_sanksi" required>
                        <option value="">-- Pilih Jenis Sanksi --</option>
                        <option value="Teguran Lisan">Teguran Lisan</option>
                        <option value="Teguran Tertulis">Teguran Tertulis</option>
                        <option value="SP1">SP1</option>
                        <option value="SP2">SP2</option>
                        <option value="SP3">SP3</option>
                        <option value="Pemotongan TPP">Pemotongan TPP</option>
                        <option value="Pembinaan">Pembinaan</option>
                        <option value="Hukuman Disiplin Ringan">Hukuman Disiplin Ringan</option>
                        <option value="Hukuman Disiplin Sedang">Hukuman Disiplin Sedang</option>
                        <option value="Hukuman Disiplin Berat">Hukuman Disiplin Berat</option>
                    </select>
                </div>
                <div><label>Tanggal</label><input type="date" name="tanggal"></div>
                <div><label>Keterangan</label><input name="keterangan"></div>
                <button type="submit">Tambah</button>
            </div>
        </form>
    </div>
    <div class="panel" style="margin-bottom: 24px;">
        <form action="{{ route('atasan.sanksi.index') }}" method="GET" class="filter-bar">
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
                    <option value="">-- Semua User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id_user }}" {{ request('id_user') == $user->id_user ? 'selected' : '' }}>{{ $user->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-control">
                <label>Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Jenis sanksi atau keterangan...">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit">Tampilkan</button>
                <a href="{{ route('atasan.sanksi.index') }}" class="button" style="background:#f3f4f6; color:#374151; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Reset</a>
                <a href="{{ route('atasan.sanksi.print', request()->all()) }}" target="_blank" style="background: #059669; color: white; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Cetak</a>
            </div>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Jenis</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Konfirmasi Petugas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->user->nama ?? '-' }}</td>
                    <td>{{ $item->jenis_sanksi }}</td>
                    <td>{{ $item->tanggal?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>{{ $item->acknowledged_at ? $item->acknowledged_at->format('d/m/Y H:i') : 'Belum diakui' }}</td>
                    <td>
                        <form method="POST" action="{{ route('atasan.sanksi.delete', $item->id_sanksi) }}">
                            @csrf 
                            @method('DELETE')
                            <button class="danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
