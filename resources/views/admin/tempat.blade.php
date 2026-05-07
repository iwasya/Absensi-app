@extends('layouts.app')

@section('title', 'Tempat Tugas')

@section('content')
    <h1>Tempat Tugas</h1>
    <div class="panel">
        <form method="POST" action="{{ route('admin.tempat.store') }}">
            @csrf
            <div class="form-grid">
                <div><label>Nama Tempat</label><input name="nama_tempat" required></div>
                <div><label>Alamat</label><input name="alamat"></div>
                <div><label>Latitude</label><input name="latitude"></div>
                <div><label>Longitude</label><input name="longitude"></div>
                <button type="submit">Tambah</button>
            </div>
        </form>
    </div>
    <table>
        <thead><tr><th>Nama</th><th>Alamat</th><th>Latitude</th><th>Longitude</th><th>Aksi</th><th>Hapus</th></tr></thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <form method="POST" action="{{ route('admin.tempat.update', $item->id_tempat) }}">
                        @csrf
                        @method('PUT')
                        <td><input name="nama_tempat" value="{{ $item->nama_tempat }}" required></td>
                        <td><input name="alamat" value="{{ $item->alamat }}"></td>
                        <td><input name="latitude" value="{{ $item->latitude }}"></td>
                        <td><input name="longitude" value="{{ $item->longitude }}"></td>
                        <td><button type="submit">Simpan</button></td>
                    </form>
                    <td><form method="POST" action="{{ route('admin.tempat.delete', $item->id_tempat) }}">@csrf @method('DELETE')<button class="danger">Hapus</button></form></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
