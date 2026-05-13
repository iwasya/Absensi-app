@extends('layouts.app')

@section('title', 'Kalender')

@section('content')
    <h1>Kalender</h1>
    <div class="panel" style="margin-bottom: 24px;">        <form action="{{ route('admin.kalender.index') }}" method="GET" class="filter-bar">            <div class="filter-control" style="max-width:120px;">                <label>Per Halaman</label>                <select name="per_page" onchange="this.form.submit()" style="width:100%;">                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / hal</option>                    <option value="15" {{ request('per_page') == 15 ? 'selected' : (request('per_page') ? '' : 'selected') }}>15 / hal</option>                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / hal</option>                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / hal</option>                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / hal</option>                </select>            </div>        </form>    </div>    <div class="panel">
        <p class="muted">Tanggal libur, cuti bersama, atau kegiatan yang diisi di sini otomatis tampil di menu Kalender petugas.</p>
        <form method="POST" action="{{ route('admin.kalender.store') }}">
            @csrf
            <div class="form-grid">
                <div><label>Tanggal</label><input type="date" name="tanggal" required></div>
                <div><label>Nama Event</label><input name="nama_event"></div>
                <div><label>Jenis</label><select name="jenis_event"><option value="libur">libur</option><option value="kegiatan">kegiatan</option><option value="cuti_bersama">cuti_bersama</option></select></div>
                <div><label>Keterangan</label><input name="keterangan"></div>
                <button type="submit">Tambah</button>
            </div>
        </form>
    </div>
    <table>
        <thead><tr><th>Tanggal</th><th>Nama</th><th>Jenis</th><th>Keterangan</th><th>Aksi</th></tr></thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->nama_event }}</td>
                    <td>{{ $item->jenis_event }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td><form method="POST" action="{{ route('admin.kalender.delete', $item->id_kalender) }}">@csrf @method('DELETE')<button class="danger">Hapus</button></form></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection

