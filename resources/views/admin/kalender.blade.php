@extends('layouts.app')

@section('title', 'Kalender')

@section('content')
    <h1>Kalender</h1>
    <div class="panel">
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
