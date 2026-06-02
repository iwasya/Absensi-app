@extends('layouts.app')

@section('title', 'Tempat Tugas')

@section('content')
    <h1>Tempat Tugas</h1>
    <div class="panel" style="margin-bottom: 24px;">        <form action="{{ route('admin.tempat.index') }}" method="GET" class="filter-bar">            <div class="filter-control" style="max-width:120px;">                <label>Per Halaman</label>                <select name="per_page" onchange="this.form.submit()" style="width:100%;">                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / hal</option>                    <option value="15" {{ request('per_page') == 15 ? 'selected' : (request('per_page') ? '' : 'selected') }}>15 / hal</option>                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / hal</option>                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / hal</option>                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / hal</option>                </select>            </div>        </form>    </div>    <div class="panel">
        <form method="POST" action="{{ route('admin.tempat.store') }}">
            @csrf
            <div class="form-grid">
                <div><label>Nama Tempat</label><input name="nama_tempat" required></div>
                <div><label>Alamat</label><input name="alamat"></div>
                <div><label>Latitude</label><input type="number" step="0.0000001" min="-90" max="90" name="latitude" placeholder="-6.2092860"></div>
                <div><label>Longitude</label><input type="number" step="0.0000001" min="-180" max="180" name="longitude" placeholder="106.8712530"></div>
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
                        <td><input type="number" step="0.0000001" min="-90" max="90" name="latitude" value="{{ $item->latitude }}"></td>
                        <td><input type="number" step="0.0000001" min="-180" max="180" name="longitude" value="{{ $item->longitude }}"></td>
                        <td><button type="submit">Simpan</button></td>
                    </form>
                    <td><form method="POST" action="{{ route('admin.tempat.delete', $item->id_tempat) }}">@csrf @method('DELETE')<button class="danger">Hapus</button></form></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
