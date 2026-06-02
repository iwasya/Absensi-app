@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<style>
    main {
        max-width: 100% !important;
        padding: 20px 28px !important;
        background: var(--bg-color);
    }

    .users-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        max-width: 1200px;
    }

    .users-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .users-header > div {
        flex: 1;
        min-width: 0;
    }

    .users-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: var(--text-color);
        letter-spacing: -0.5px;
    }

    .users-header p {
        margin: 8px 0 0 0 !important;
        font-size: 14px;
        color: var(--muted);
    }

    .users-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: box-shadow 0.3s ease;
    }

    .users-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .users-card-body {
        padding: 32px;
    }

    .users-form-grid {
        display: grid;
        gap: 20px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .users-form-grid .form-control {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .users-form-grid label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-color);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .required-indicator {
        color: var(--danger);
        font-weight: 700;
        font-size: 16px;
    }

    .users-form-grid input,
    .users-form-grid select {
        width: 100%;
        min-height: 48px;
        padding: 12px 14px;
        border: 1.5px solid var(--border-color);
        border-radius: 12px;
        background: var(--bg-color);
        color: var(--text-color);
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s ease;
    }

    .users-form-grid input::placeholder,
    .users-form-grid select::placeholder {
        color: var(--muted);
    }

    .users-form-grid input:hover,
    .users-form-grid select:hover {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.05);
    }

    .users-form-grid input:focus,
    .users-form-grid select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1);
    }

    .help-text {
        font-size: 13px;
        color: var(--muted);
        margin-top: 2px;
    }

    .field-error {
        font-size: 13px;
        color: var(--danger);
        font-weight: 500;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .error-alert {
        margin-bottom: 20px;
        padding: 16px 20px;
        border: 1.5px solid var(--danger-border);
        border-radius: 12px;
        background: var(--danger-soft);
        color: var(--danger);
    }

    .error-alert strong {
        font-weight: 700;
        display: block;
        margin-bottom: 8px;
    }

    .error-alert ul {
        margin: 0;
        padding-left: 20px;
        list-style: none;
    }

    .error-alert li {
        margin: 6px 0;
        padding-left: 20px;
        position: relative;
    }

    .error-alert li:before {
        content: "•";
        position: absolute;
        left: 0;
        font-weight: bold;
    }

    .password-row {
        display: flex;
        gap: 10px;
        align-items: stretch;
    }

    .password-row input {
        flex: 1;
        min-height: 48px;
    }

    .show-password-btn {
        padding: 12px 18px;
        border-radius: 12px;
        border: 1.5px solid var(--border-color);
        background: var(--bg-color);
        color: var(--text-color);
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .show-password-btn:hover {
        border-color: var(--primary);
        background: var(--primary);
        color: #fff;
    }

    .pw-strength {
        display: none;
    }

    .pw-strength > i {
        display: none;
    }

    .users-form-actions {
        margin-top: 32px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
        padding-top: 24px;
        border-top: 1px solid var(--border-color);
    }

    .users-btn-primary,
    .users-btn-secondary {
        padding: 14px 28px;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        font-weight: 700;
        font-size: 15px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        letter-spacing: 0.3px;
    }

    .users-btn-primary {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 4px 14px rgba(var(--primary-rgb), 0.3);
        min-width: 140px;
    }

    .users-btn-primary:hover {
        opacity: 0.95;
        box-shadow: 0 6px 20px rgba(var(--primary-rgb), 0.4);
        transform: translateY(-2px);
    }

    .users-btn-primary:active {
        opacity: 0.9;
        transform: translateY(0);
    }

    .users-btn-secondary {
        background: transparent;
        border: 2px solid var(--border-color);
        color: var(--text-color);
        min-width: 120px;
    }

    .users-btn-secondary:hover {
        border-color: var(--primary);
        background: var(--primary);
        color: #fff;
        transform: translateY(-2px);
    }

    .section-divider {
        margin-top: 32px;
        margin-bottom: 24px;
        padding: 0;
        border: none;
        border-top: 1px solid var(--border-color);
    }

    .form-section-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-color);
        margin: 28px 0 20px 0;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.08) 0%, rgba(var(--primary-rgb), 0.04) 100%);
        border-left: 4px solid var(--primary);
        border-radius: 8px;
    }

    @media (max-width: 760px) {
        main {
            padding: 16px 16px !important;
        }

        .users-header {
            flex-direction: column;
            align-items: stretch;
        }

        .users-header a {
            width: 100%;
        }

        .users-card-body {
            padding: 20px;
        }

        .users-form-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .users-header h1 {
            font-size: 24px;
        }

        .password-row {
            flex-direction: column;
            align-items: stretch;
        }

        .form-section-title {
            font-size: 16px;
            margin: 20px 0 16px 0;
            padding: 10px 12px;
        }

        .users-form-actions {
            flex-direction: column;
            gap: 10px;
        }

        .users-btn-primary,
        .users-btn-secondary {
            width: 100%;
        }
    }
</style>

<div class="users-page">
    <div class="users-header">
        <div>
            <h1>👤 Tambah User Baru</h1>
            <p>Form untuk membuat dan mengatur akun user baru dalam sistem.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="users-btn-secondary">← Kembali</a>
    </div>

    <div class="users-card">
        <div class="users-card-body">
            @if($errors->any())
                <div class="error-alert">
                    <strong>⚠️ Periksa kembali data Anda</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                
                <!-- Identitas Dasar -->
                <div class="form-section-title">📋 Identitas Dasar</div>
                <div class="users-form-grid">
                    <div class="form-control">
                        <label for="nama">Nama <span class="required-indicator">*</span></label>
                        <input id="nama" name="nama" type="text" required value="{{ old('nama') }}" placeholder="Masukkan nama lengkap" aria-required="true" aria-invalid="{{ $errors->has('nama') ? 'true' : 'false' }}" aria-describedby="namaHelp namaError">
                        <div id="namaHelp" class="help-text">✓ Nama lengkap sesuai dokumen resmi</div>
                        @error('nama')<div id="namaError" class="field-error">❌ {{ $message }}</div>@enderror
                    </div>

                    <div class="form-control">
                        <label for="nik">NIK <span class="required-indicator">*</span></label>
                        <input id="nik" name="nik" type="text" required pattern="[0-9]+" maxlength="20" placeholder="Nomor Induk Kependudukan" value="{{ old('nik') }}" aria-describedby="nikHelp nikError">
                        <div id="nikHelp" class="help-text">✓ Hanya angka, tanpa spasi atau karakter lain</div>
                        @error('nik')<div id="nikError" class="field-error">❌ {{ $message }}</div>@enderror
                    </div>

                    <div class="form-control">
                        <label for="username">Username / ID Akun <span class="required-indicator">*</span></label>
                        <input id="username" name="username" type="text" required placeholder="NIK atau username unik" value="{{ old('username') }}" aria-describedby="usernameHelp usernameError">
                        <div id="usernameHelp" class="help-text">✓ ID login unik (bisa NIK atau custom username)</div>
                        @error('username')<div id="usernameError" class="field-error">❌ {{ $message }}</div>@enderror
                    </div>

                    <div class="form-control">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" placeholder="nama@perusahaan.com" value="{{ old('email') }}" aria-describedby="emailHelp emailError">
                        <div id="emailHelp" class="help-text">✓ Alamat email yang aktif dan valid</div>
                        @error('email')<div id="emailError" class="field-error">❌ {{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Informasi Kontak & Pekerjaan -->
                <div class="form-section-title">📞 Kontak & Pekerjaan</div>
                <div class="users-form-grid">
                    <div class="form-control">
                        <label for="no_hp">No Telepon</label>
                        <input id="no_hp" name="no_hp" type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="20" placeholder="6281234567890" value="{{ preg_replace('/[^0-9]/', '', (string) old('no_hp')) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" aria-describedby="hpHelp hpError">
                        <div id="hpHelp" class="help-text">✓ Hanya angka, disimpan terenkripsi sebagai data sensitif</div>
                        @error('no_hp')<div id="hpError" class="field-error">❌ {{ $message }}</div>@enderror
                    </div>

                    <div class="form-control">
                        <label for="jabatan">Jabatan</label>
                        <input id="jabatan" name="jabatan" type="text" placeholder="Contoh: Manager, Staff" value="{{ old('jabatan') }}" aria-describedby="jabatanHelp">
                        <div id="jabatanHelp" class="help-text">✓ Posisi/jabatan di perusahaan</div>
                    </div>

                    <div class="form-control">
                        <label for="id_tempat">Tempat Tugas</label>
                        <select id="id_tempat" name="id_tempat" aria-describedby="tempatError">
                            <option value="">- Pilih Tempat Tugas -</option>
                            @foreach($tempatTugas as $tempat)
                                <option value="{{ $tempat->id_tempat }}" {{ old('id_tempat') == $tempat->id_tempat ? 'selected' : '' }}>{{ $tempat->nama_tempat }}</option>
                            @endforeach
                        </select>
                        @error('id_tempat')<div id="tempatError" class="field-error">❌ {{ $message }}</div>@enderror
                    </div>

                    <div class="form-control">
                        <label for="regu">Regu / Tim</label>
                        <input id="regu" name="regu" type="text" placeholder="Contoh: Regu A, Tim IT" value="{{ old('regu') }}" aria-describedby="reguHelp">
                        <div id="reguHelp" class="help-text">✓ Opsional - regu/tim penugasan</div>
                    </div>
                </div>

                <!-- Jadwal & Status -->
                <div class="form-section-title">⏰ Jadwal & Status</div>
                <div class="users-form-grid">
                    <div class="form-control">
                        <label for="shift">Shift Kerja</label>
                        <select id="shift" name="shift" aria-describedby="shiftHelp">
                            <option value="">- Pilih Shift -</option>
                            <option value="Shift 1" {{ old('shift') == 'Shift 1' ? 'selected' : '' }}>Shift 1 (Pagi)</option>
                            <option value="Shift 2" {{ old('shift') == 'Shift 2' ? 'selected' : '' }}>Shift 2 (Siang)</option>
                            <option value="Shift 3" {{ old('shift') == 'Shift 3' ? 'selected' : '' }}>Shift 3 (Malam)</option>
                        </select>
                        <div id="shiftHelp" class="help-text">✓ Pilih jadwal kerja yang berlaku</div>
                    </div>

                    <div class="form-control">
                        <label for="status_aktif">Status Akun <span class="required-indicator">*</span></label>
                        <select id="status_aktif" name="status_aktif" required aria-describedby="statusHelp">
                            <option value="aktif" {{ old('status_aktif', 'aktif') == 'aktif' ? 'selected' : '' }}>✓ Aktif</option>
                            <option value="nonaktif" {{ old('status_aktif') == 'nonaktif' ? 'selected' : '' }}>✗ Nonaktif</option>
                        </select>
                        <div id="statusHelp" class="help-text">✓ Aktifkan/nonaktifkan akun user</div>
                    </div>

                    <div class="form-control">
                        <label for="id_role">Role / Peran <span class="required-indicator">*</span></label>
                        <select id="id_role" name="id_role" required aria-describedby="roleError">
                            @foreach($roles as $role)
                                <option value="{{ $role->id_role }}" {{ old('id_role') == $role->id_role ? 'selected' : '' }}>{{ $role->nama_role }}</option>
                            @endforeach
                        </select>
                        @error('id_role')<div id="roleError" class="field-error">❌ {{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Password & Alamat -->
                <div class="form-section-title">🔐 Keamanan & Alamat</div>
                <div class="users-form-grid">
                    <div class="form-control" style="grid-column: span 2;">
                        <label for="password">Password <span class="required-indicator">*</span></label>
                        <div class="password-row">
                            <input id="password" name="password" type="password" required placeholder="Minimal 8 karakter dengan campuran" aria-describedby="passwordHelp passwordError">
                            <button type="button" class="show-password-btn" id="togglePassword">👁️ Tampil</button>
                        </div>
                        <div id="passwordHelp" class="help-text">✓ Gunakan huruf besar, angka, dan simbol untuk keamanan maksimal</div>
                        @error('password')<div id="passwordError" class="field-error">❌ {{ $message }}</div>@enderror
                    </div>

                    <div class="form-control" style="grid-column: span 2;">
                        <label for="alamat">Alamat Lengkap</label>
                        <input id="alamat" name="alamat" type="text" placeholder="Jalan, No., Kecamatan, Kabupaten..." value="{{ old('alamat') }}" aria-describedby="alamatHelp">
                        <div id="alamatHelp" class="help-text">✓ Opsional - alamat tempat tinggal</div>
                    </div>
                </div>

                <script>
                    (function(){
                        const toggle = document.getElementById('togglePassword');
                        const pw = document.getElementById('password');
                        const bar = document.getElementById('pwBar');
                        if(toggle && pw){
                            toggle.addEventListener('click', function(){
                                if(pw.type === 'password'){
                                    pw.type = 'text';
                                    toggle.textContent = '🔒 Sembunyi';
                                } else {
                                    pw.type = 'password';
                                    toggle.textContent = '👁️ Tampil';
                                }
                            });

                            function strengthScore(val){
                                let score = 0;
                                if(!val) return 0;
                                if(val.length >= 8) score += 1;
                                if(/[A-Z]/.test(val)) score += 1;
                                if(/[0-9]/.test(val)) score += 1;
                                if(/[^A-Za-z0-9]/.test(val)) score += 1;
                                return score; // 0..4
                            }

                            pw.addEventListener('input', function(e){
                                const s = strengthScore(e.target.value);
                                const pct = (s / 4) * 100;
                                bar.style.width = pct + '%';
                                if(s <= 1) bar.style.background = '#e74c3c';
                                else if(s === 2) bar.style.background = '#f39c12';
                                else if(s === 3) bar.style.background = '#27ae60';
                                else bar.style.background = '#0b7a5f';
                            });
                        }
                    })();
                </script>
                <div class="users-form-actions">
                    <button type="submit" class="users-btn-primary">💾 Tambah User</button>
                    <a href="{{ route('admin.users.index') }}" class="users-btn-secondary">✕ Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
