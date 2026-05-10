@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <h1>Profil Saya</h1>
    
    <div class="panel">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            
            <div style="text-align: center; margin-bottom: 24px;">
                @if($user->foto_profil)
                    <img src="{{ Storage::url($user->foto_profil) }}" alt="Foto Profil" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb; margin-bottom: 12px;">
                @else
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: #2563eb; color: #fff; display: grid; place-items: center; font-weight: bold; font-size: 48px; margin: 0 auto 12px;">
                        {{ strtoupper(substr($user->nama ?? 'U', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <label for="foto_profil" style="display:inline-block; background:#f3f4f6; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:13px;">Ganti Foto Profil</label>
                    <input type="file" id="foto_profil" name="foto_profil" accept="image/*" style="display:none;" onchange="this.form.submit()">
                </div>
                <div style="font-size:12px; color:#6b7280; margin-top:8px;">Format: JPG, PNG. Maks: 2MB. (Akan langsung tersimpan saat dipilih)</div>
            </div>

            <div style="margin-bottom: 16px;">
                <label>Nama Lengkap</label>
                <input type="text" value="{{ $user->nama }}" disabled style="background: #f9fafb; color: #6b7280;">
            </div>

            <div style="margin-bottom: 16px;">
                <label>Username</label>
                <input type="text" value="{{ $user->username }}" disabled style="background: #f9fafb; color: #6b7280;">
            </div>

            <div style="margin-bottom: 16px;">
                <label>Email</label>
                <input type="text" value="{{ $user->email }}" disabled style="background: #f9fafb; color: #6b7280;">
            </div>

            <div style="margin-bottom: 16px;">
                <label>Peran / Role</label>
                <input type="text" value="{{ $user->role->nama_role ?? '-' }}" disabled style="background: #f9fafb; color: #6b7280;">
            </div>

            <div style="margin-bottom: 16px;">
                <label>Tempat Tugas</label>
                <input type="text" value="{{ $user->tempatTugas->nama_tempat ?? 'Belum ditetapkan' }}" disabled style="background: #f9fafb; color: #6b7280;">
            </div>

            <div style="margin-bottom: 16px;">
                <label>NIK (Nomor Induk Kependudukan) <span style="font-size:11px; color:#ef4444; font-weight:normal; margin-left:4px;">*Sensitif</span></label>
                @if($nik && $nik !== 'Gagal mendekripsi NIK')
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <input type="password" id="nik_input" value="{{ $nik }}" disabled style="background: #f9fafb; color: #111827; letter-spacing: 2px; flex: 1;">
                        <button type="button" onclick="var el=document.getElementById('nik_input'); el.type = el.type === 'password' ? 'text' : 'password';" style="background: #e5e7eb; color: #374151; padding: 10px 14px; border-radius: 6px; font-weight: bold;">Lihat</button>
                    </div>
                @else
                    <input type="text" value="{{ $nik ?? 'Data NIK tidak ditemukan' }}" disabled style="background: #f9fafb; color: #6b7280; font-style: italic;">
                @endif
            </div>

        </form>
    </div>

    <div class="panel" style="margin-top: 24px;">
        <h2 style="font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid #e5e7eb; padding-bottom: 12px;">Ubah Password</h2>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            <div style="margin-bottom: 16px;">
                <label>Password Baru</label>
                <input type="password" name="password" required placeholder="Masukkan password baru">
            </div>
            <div style="margin-bottom: 16px;">
                <label>Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required placeholder="Ulangi password baru">
            </div>
            <button type="submit" style="background: #2563eb; color: #fff; padding: 10px 16px; border-radius: 6px; font-weight: bold; border: none; cursor: pointer;">Simpan Password</button>
        </form>
    </div>
</div>
@endsection
