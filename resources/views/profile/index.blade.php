@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<style>
    .profile-page {
        max-width: 980px;
        margin: 0 auto;
    }

    .profile-layout {
        display: grid;
        grid-template-columns: minmax(260px, 340px) minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .profile-card {
        text-align: center;
    }

    .profile-avatar,
    .profile-initial {
        width: 132px;
        height: 132px;
        border-radius: 999px;
        margin: 0 auto 14px;
        border: 2px solid var(--border-color);
    }

    .profile-avatar {
        object-fit: cover;
    }

    .profile-initial {
        display: grid;
        place-items: center;
        background: var(--primary);
        color: #fff;
        font-size: 48px;
        font-weight: 700;
    }

    .profile-name {
        margin: 0 0 4px;
        font-size: 20px;
        font-weight: 700;
    }

    .profile-role {
        color: var(--muted);
        font-size: 13px;
    }

    .profile-upload {
        display: grid;
        gap: 10px;
        margin-top: 18px;
        text-align: left;
    }

    .profile-help {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 12px;
    }

    .profile-section {
        display: grid;
        gap: 14px;
    }

    .profile-section + .profile-section {
        margin-top: 18px;
    }

    .profile-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
    }

    .profile-section-title h2 {
        margin: 0;
    }

    .profile-fields {
        display: grid;
        gap: 12px;
    }

    .readonly-field {
        display: grid;
        gap: 5px;
    }

    .readonly-value {
        min-height: 40px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 10px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        background: var(--soft-bg);
        color: var(--soft-text);
        word-break: break-word;
    }

    .sensitive-label {
        color: var(--danger-soft-text);
        font-size: 11px;
        font-weight: 700;
    }

    .nik-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px;
    }

    .nik-row input {
        letter-spacing: 2px;
    }

    .secondary-button {
        background: var(--soft-bg);
        color: var(--soft-text);
        border: 1px solid var(--border-color);
    }

    .password-actions {
        display: flex;
        justify-content: flex-end;
    }

    .profile-actions {
        display: flex;
        justify-content: flex-end;
    }

    .field-error {
        color: var(--danger-soft-text);
        font-size: 12px;
        margin-top: 5px;
    }

    @media (max-width: 820px) {
        .profile-layout {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-page">
    <h1>Profil Saya</h1>

    <div class="profile-layout">
        <div class="panel profile-card">
            @if($user->foto_profil)
                <img src="{{ Storage::url($user->foto_profil) }}" alt="Foto Profil" class="profile-avatar">
            @else
                <div class="profile-initial">{{ strtoupper(substr($user->nama ?? 'U', 0, 1)) }}</div>
            @endif

            <div class="profile-name">{{ $user->nama }}</div>
            <div class="profile-role">{{ $user->role->nama_role ?? '-' }}</div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-upload">
                @csrf
                <input type="hidden" name="nama" value="{{ $user->nama }}">
                <input type="hidden" name="username" value="{{ $user->username }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                <div>
                    <label for="foto_profil">Foto Profil</label>
                    <input type="file" id="foto_profil" name="foto_profil" accept="image/*">
                    <p class="profile-help">JPG atau PNG, maksimal 2MB.</p>
                    @error('foto_profil')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit">Simpan Foto</button>
            </form>
        </div>

        <div>
            <div class="panel profile-section">
                <div class="profile-section-title">
                    <h2>Informasi Akun</h2>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="profile-fields">
                    @csrf
                    <div>
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required maxlength="150" autocomplete="name">
                        @error('nama')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required maxlength="100" autocomplete="username">
                        @error('username')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="150" autocomplete="email">
                        @error('email')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="readonly-field">
                        <label>Tempat Tugas</label>
                        <div class="readonly-value">{{ $user->tempatTugas->nama_tempat ?? 'Belum ditetapkan' }}</div>
                    </div>

                    <div class="readonly-field">
                        <label>NIK <span class="sensitive-label">Sensitif</span></label>
                        @if($nik && $nik !== 'Gagal mendekripsi NIK')
                            <div class="nik-row">
                                <input type="password" id="nik_input" value="{{ $nik }}" disabled>
                                <button type="button" class="secondary-button" id="nikToggle">Lihat</button>
                            </div>
                        @else
                            <div class="readonly-value muted">{{ $nik ?? 'Data NIK tidak ditemukan' }}</div>
                        @endif
                    </div>

                    <div class="profile-actions">
                        <button type="submit">Simpan Profil</button>
                    </div>
                </form>
            </div>

            <div class="panel profile-section">
                <div class="profile-section-title">
                    <h2>Ubah Password</h2>
                </div>

                <form method="POST" action="{{ route('profile.password') }}" class="profile-fields">
                    @csrf
                    <div>
                        <label>Password Saat Ini</label>
                        <input type="password" name="current_password" required autocomplete="current-password">
                    </div>
                    <div>
                        <label>Password Baru</label>
                        <input type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter, huruf besar/kecil dan angka">
                    </div>
                    <div>
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password">
                    </div>
                    <div class="password-actions">
                        <button type="submit">Simpan Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var nikToggle = document.getElementById('nikToggle');
    var nikInput = document.getElementById('nik_input');

    if (nikToggle && nikInput) {
        nikToggle.addEventListener('click', function () {
            var isHidden = nikInput.type === 'password';
            nikInput.type = isHidden ? 'text' : 'password';
            nikToggle.textContent = isHidden ? 'Sembunyi' : 'Lihat';
        });
    }
</script>
@endsection
