@extends('layouts.app')

@section('title', 'Sanksi')

@section('content')
    <h1>Sanksi</h1>
    <div class="panel">
        <form method="POST" action="{{ route('admin.sanksi.store') }}">
            @csrf
            <div class="form-grid">
                <div><label>User</label><select name="id_user" required>@foreach($users as $user)<option value="{{ $user->id_user }}">{{ $user->nama }}</option>@endforeach</select></div>
                <div><label>Jenis</label><input name="jenis_sanksi"></div>
                <div><label>Tanggal</label><input type="date" name="tanggal"></div>
                <div><label>Keterangan</label><input name="keterangan"></div>
                <button type="submit">Tambah</button>
            </div>
        </form>
    </div>
    <table>
        <thead><tr><th>User</th><th>Jenis</th><th>Tanggal</th><th>Keterangan</th><th>Aksi</th></tr></thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->user->nama ?? '-' }}</td>
                    <td>{{ $item->jenis_sanksi }}</td>
                    <td>{{ $item->tanggal?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td><form method="POST" action="{{ route('admin.sanksi.delete', $item->id_sanksi) }}">@csrf @method('DELETE')<button class="danger">Hapus</button></form></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
