@extends('layouts.app')

@section('title', 'Manajemen Data Sensitif (NIK)')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
    <h1>Data Sensitif (NIK)</h1>
</div>

<div class="panel">
    <div style="margin-bottom: 16px; font-size: 14px; color: #6b7280;">
        Gunakan halaman ini untuk memasukkan NIK pengguna. NIK akan dienkripsi secara aman sebelum disimpan ke database, dan tidak dapat dilihat secara langsung oleh siapa pun selain pengguna itu sendiri melalui halaman Profil.
    </div>

    <div style="overflow-x: auto;">
        <table style="min-width: 800px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama User</th>
                    <th>Username</th>
                    <th>Status NIK</th>
                    <th style="width: 350px;">Aksi / Update NIK</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id_user }}</td>
                        <td>
                            <strong>{{ $user->nama }}</strong><br>
                            <span class="muted" style="font-size: 12px;">{{ $user->role->nama_role ?? '-' }}</span>
                        </td>
                        <td>{{ $user->username }}</td>
                        <td>
                            @if($user->userSensitive && $user->userSensitive->nik_encrypted)
                                <span class="badge" style="background:#ecfdf5; color:#047857;">Tersimpan (Encrypted)</span>
                            @else
                                <span class="badge" style="background:#fef2f2; color:#b91c1c;">Belum Ada</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.data-sensitif.update') }}" style="display: flex; gap: 8px;">
                                @csrf
                                <input type="hidden" name="id_user" value="{{ $user->id_user }}">
                                <input type="text" name="nik" placeholder="Masukkan NIK baru" style="flex: 1;" maxlength="20">
                                <button type="submit" style="background:#2563eb; padding: 8px 12px; font-size: 13px;">Simpan</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 16px;">
        {{ $users->links('pagination.simple') }}
    </div>
</div>
@endsection
