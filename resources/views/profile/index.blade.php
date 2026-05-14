@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<style>
    .profile-page {
        --profile-soft: var(--primary-soft);
        max-width: 1080px;
        margin: 0 auto;
    }

    .profile-page h1,
    .profile-page h2 {
        color: var(--text-color);
    }

    .profile-hero {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        align-items: center;
        padding: 18px;
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        margin-bottom: 18px;
    }

    .profile-kicker {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        min-height: 26px;
        padding: 4px 10px;
        margin-bottom: 8px;
        border-radius: 99px;
        background: var(--primary-soft);
        border: 1px solid var(--primary-border);
        color: var(--primary2);
        font-size: 11px;
        font-weight: 600;
    }

    .profile-title {
        margin: 0;
        font-size: 20px;
        line-height: 1.25;
        font-weight: 600;
        letter-spacing: 0;
    }

    .profile-subtitle {
        max-width: 620px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.7;
        margin-top: 10px;
    }

    .profile-hero-meta {
        position: relative;
        z-index: 1;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .profile-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 34px;
        padding: 5px 14px;
        border-radius: 999px;
        background: var(--primary-soft);
        border: 1px solid var(--primary-border);
        color: var(--primary2);
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
    }

    .profile-pill-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary);
        flex: 0 0 auto;
    }

    .profile-layout {
        display: grid;
        grid-template-columns: minmax(280px, 350px) minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .profile-panel {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 18px;
        box-shadow: none;
        margin-bottom: 18px;
    }

    .profile-card {
        position: sticky;
        top: 88px;
        overflow: hidden;
        text-align: center;
    }

    .profile-card::before {
        content: "";
        display: block;
        height: 82px;
        margin: -18px -18px 0;
        background: var(--primary-soft);
        border-bottom: 1px solid var(--primary-border);
    }

    .profile-avatar-wrap {
        position: relative;
        width: 142px;
        height: 142px;
        margin: -62px auto 16px;
        border-radius: 50%;
        padding: 5px;
        background: var(--panel-bg);
        border: 1px solid var(--primary-border);
        box-shadow: none;
    }

    .profile-avatar,
    .profile-initial {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 1px solid var(--border-color);
    }

    .profile-avatar {
        display: block;
        object-fit: cover;
    }

    .profile-initial {
        display: grid;
        place-items: center;
        background: var(--primary);
        color: #fff;
        font-size: 52px;
        font-weight: 800;
        letter-spacing: 0;
    }

    .profile-name {
        margin: 0;
        font-size: 21px;
        line-height: 1.25;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .profile-role {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        margin-top: 9px;
        padding: 5px 12px;
        border-radius: 999px;
        background: var(--primary-soft);
        color: var(--primary2);
        border: 1px solid var(--primary-border);
        font-size: 12px;
        font-weight: 700;
    }

    .profile-upload {
        display: grid;
        gap: 12px;
        margin-top: 20px;
        text-align: left;
        padding-top: 18px;
        border-top: 1px solid var(--border-color);
    }

    .profile-upload-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
    }

    .profile-upload-title strong {
        font-size: 13px;
        font-weight: 700;
    }

    .profile-help {
        margin: 7px 0 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .profile-section {
        display: grid;
        gap: 16px;
    }

    .profile-section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--border-color);
    }

    .profile-section-head h2 {
        margin: 0;
        font-size: 17px;
    }

    .profile-section-head p {
        margin: 5px 0 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.55;
    }

    .profile-section-badge {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 999px;
        border: 1px solid var(--border-color);
        background: var(--profile-soft);
        color: var(--primary2);
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .profile-fields {
        display: grid;
        gap: 12px;
    }

    .profile-info-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .readonly-field {
        display: grid;
        gap: 6px;
        min-width: 0;
    }

    .readonly-value {
        min-height: 44px;
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 11px;
        background: var(--bg-color);
        color: var(--text-color);
        word-break: break-word;
        line-height: 1.45;
    }

    .readonly-value::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary);
        opacity: .45;
        flex: 0 0 auto;
    }

    .sensitive-label {
        display: inline-flex;
        vertical-align: middle;
        margin-left: 6px;
        padding: 2px 7px;
        border-radius: 999px;
        background: var(--red-soft);
        color: var(--red-dark);
        border: 1px solid #FCA5A5;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .nik-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px;
    }

    .nik-row input {
        min-height: 44px;
        letter-spacing: 2px;
        font-family: 'DM Mono', Consolas, monospace;
        font-size: 12.5px;
    }

    .secondary-button {
        min-width: 86px;
        background: var(--panel-bg);
        color: var(--primary2);
        border: 1px solid var(--primary-border);
    }

    .secondary-button:hover {
        background: var(--primary-soft);
        color: var(--primary2);
    }

    .password-fields {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .password-fields .full-row {
        grid-column: 1 / -1;
    }

    .password-actions {
        display: flex;
        justify-content: flex-end;
        padding-top: 2px;
    }

    .profile-primary-button {
        min-height: 40px;
    }

    .profile-upload input[type="file"] {
        padding: 8px;
        cursor: pointer;
    }

    .profile-upload input[type="file"]::file-selector-button {
        border: 0;
        border-radius: 7px;
        background: var(--primary-soft);
        color: var(--primary2);
        padding: 7px 10px;
        margin-right: 10px;
        font: inherit;
        font-weight: 700;
        cursor: pointer;
    }

    @media (max-width: 920px) {
        .profile-hero {
            grid-template-columns: 1fr;
            align-items: start;
        }

        .profile-hero-meta {
            justify-content: flex-start;
        }

        .profile-layout {
            grid-template-columns: 1fr;
        }

        .profile-card {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .profile-page {
            margin-top: -4px;
        }

        .profile-hero,
        .profile-panel {
            border-radius: 14px;
            padding: 16px;
        }

        .profile-card::before {
            margin: -16px -16px 0;
        }

        .profile-info-grid,
        .password-fields {
            grid-template-columns: 1fr;
        }

        .profile-section-head {
            display: grid;
        }

        .nik-row {
            grid-template-columns: 1fr;
        }

        .secondary-button,
        .profile-primary-button {
            width: 100%;
        }

        .password-actions {
            justify-content: stretch;
        }
    }
</style>

<div class="profile-page">
    <section class="profile-hero" aria-labelledby="profileTitle">
        <div>
            <div class="profile-kicker">Akun Pengguna</div>
            <h1 class="profile-title" id="profileTitle">Profil Saya</h1>
            <div class="profile-subtitle">
                Kelola foto profil, lihat informasi akun, dan perbarui password agar akses absensi tetap aman.
            </div>
        </div>
        <div class="profile-hero-meta">
            <div class="profile-pill">
                <span class="profile-pill-dot"></span>
                {{ $user->role->nama_role ?? 'Role belum diatur' }}
            </div>
            <div class="profile-pill">{{ $user->tempatTugas->nama_tempat ?? 'Tempat tugas belum diatur' }}</div>
        </div>
    </section>

    <div class="profile-layout">
        <aside class="profile-panel profile-card">
            <div class="profile-avatar-wrap">
                @if($user->foto_profil)
                    <img src="{{ Storage::url($user->foto_profil) }}" alt="Foto Profil" class="profile-avatar">
                @else
                    <div class="profile-initial">{{ strtoupper(substr($user->nama ?? 'U', 0, 1)) }}</div>
                @endif
            </div>

            <h2 class="profile-name">{{ $user->nama }}</h2>
            <div class="profile-role">{{ $user->role->nama_role ?? '-' }}</div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-upload">
                @csrf
                <div>
                    <div class="profile-upload-title">
                        <strong>Foto Profil</strong>
                    </div>
                    <label for="foto_profil">Unggah foto baru</label>
                    <input type="file" id="foto_profil" name="foto_profil" accept="image/*">
                    <p class="profile-help">Gunakan foto JPG atau PNG dengan ukuran maksimal 2MB.</p>
                </div>
                <button type="submit" class="profile-primary-button">Simpan Foto</button>
            </form>
        </aside>

        <div>
            <section class="profile-panel profile-section">
                <div class="profile-section-head">
                    <div>
                        <h2>Informasi Akun</h2>
                        <p>Data utama akun ditampilkan sebagai referensi identitas pengguna.</p>
                    </div>
                    <span class="profile-section-badge">Read Only</span>
                </div>

                <div class="profile-fields profile-info-grid">
                    <div class="readonly-field">
                        <label>Nama Lengkap</label>
                        <div class="readonly-value">{{ $user->nama }}</div>
                    </div>

                    <div class="readonly-field">
                        <label>Username</label>
                        <div class="readonly-value">{{ $user->username }}</div>
                    </div>

                    <div class="readonly-field">
                        <label>Email</label>
                        <div class="readonly-value">{{ $user->email }}</div>
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
                </div>
            </section>

            <section class="profile-panel profile-section">
                <div class="profile-section-head">
                    <div>
                        <h2>Ubah Password</h2>
                        <p>Gunakan password yang kuat dan tidak sama dengan akun lain.</p>
                    </div>
                    <span class="profile-section-badge">Keamanan</span>
                </div>

                <form method="POST" action="{{ route('profile.password') }}" class="profile-fields password-fields">
                    @csrf
                    <div class="full-row">
                        <label>Password Saat Ini</label>
                        <input type="password" name="current_password" required autocomplete="current-password">
                    </div>
                    <div>
                        <label>Password Baru</label>
                        <input type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                    </div>
                    <div>
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password">
                    </div>
                    <div class="password-actions full-row">
                        <button type="submit" class="profile-primary-button">Simpan Password</button>
                    </div>
                </form>
            </section>
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
