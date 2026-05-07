@extends('layouts.app')

@section('title', 'Kelola Users')

@section('content')
    <h1>Kelola Users</h1>
    <div class="panel">
        <h2>Tambah User</h2>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-grid">
                <div><label>Nama</label><input name="nama" required></div>
                <div><label>Username</label><input name="username" required></div>
                <div><label>Email</label><input type="email" name="email" required></div>
                <div><label>Password</label><input type="password" name="password" required></div>
                <div><label>Role</label><select name="id_role" required>@foreach($roles as $role)<option value="{{ $role->id_role }}">{{ $role->nama_role }}</option>@endforeach</select></div>
                <div><label>Tempat Tugas</label><select name="id_tempat"><option value="">-</option>@foreach($tempatTugas as $tempat)<option value="{{ $tempat->id_tempat }}">{{ $tempat->nama_tempat }}</option>@endforeach</select></div>
                <button type="submit">Tambah</button>
            </div>
        </form>
    </div>

    <table>
        <thead><tr><th>Nama</th><th>Login</th><th>Role</th><th>Tempat</th><th>Update</th><th>Hapus</th></tr></thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <form method="POST" action="{{ route('admin.users.update', $item->id_user) }}">
                        @csrf
                        @method('PUT')
                        <td><input name="nama" value="{{ $item->nama }}" required></td>
                        <td>
                            <input name="username" value="{{ $item->username }}" required>
                            <input type="email" name="email" value="{{ $item->email }}" required style="margin-top:6px">
                            <input type="password" name="password" placeholder="Password baru opsional" style="margin-top:6px">
                        </td>
                        <td><select name="id_role">@foreach($roles as $role)<option value="{{ $role->id_role }}" @selected($item->id_role == $role->id_role)>{{ $role->nama_role }}</option>@endforeach</select></td>
                        <td><select name="id_tempat"><option value="">-</option>@foreach($tempatTugas as $tempat)<option value="{{ $tempat->id_tempat }}" @selected($item->id_tempat == $tempat->id_tempat)>{{ $tempat->nama_tempat }}</option>@endforeach</select></td>
                        <td><button type="submit">Simpan</button></td>
                    </form>
                    <td>
                        <form method="POST" action="{{ route('admin.users.delete', $item->id_user) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
