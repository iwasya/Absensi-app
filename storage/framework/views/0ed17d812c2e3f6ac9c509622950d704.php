<?php $__env->startSection('title', 'Profil Pengguna'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .profile-page {
        max-width: 1080px;
        margin: 0 auto;
    }

    .profile-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        align-items: center;
        padding: 18px;
        margin-bottom: 18px;
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
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
        font-weight: 600;
        line-height: 1.25;
    }

    .profile-subtitle {
        max-width: 620px;
        margin-top: 7px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .profile-hero-meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 9px;
    }

    .profile-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 34px;
        padding: 5px 13px;
        border-radius: 99px;
        background: var(--primary-soft);
        border: 1px solid var(--primary-border);
        color: var(--primary2);
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .profile-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary);
        flex: 0 0 auto;
    }

    .profile-layout {
        display: grid;
        grid-template-columns: minmax(280px, 340px) minmax(0, 1fr);
        gap: 18px;
        align-items: start;
    }

    .profile-panel {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 18px;
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
        height: 84px;
        margin: -18px -18px 0;
        background: var(--primary-soft);
        border-bottom: 1px solid var(--primary-border);
    }

    .profile-avatar-wrap {
        width: 140px;
        height: 140px;
        margin: -62px auto 16px;
        padding: 5px;
        border-radius: 50%;
        background: var(--panel-bg);
        border: 1px solid var(--primary-border);
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
        font-size: 50px;
        font-weight: 800;
    }

    .profile-name {
        margin: 0;
        font-size: 21px;
        font-weight: 700;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .profile-role {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        margin-top: 9px;
        padding: 5px 12px;
        border-radius: 99px;
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
        padding-top: 18px;
        border-top: 1px solid var(--border-color);
        text-align: left;
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
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 99px;
        background: var(--primary-soft);
        border: 1px solid var(--primary-border);
        color: var(--primary2);
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .profile-fields {
        display: grid;
        gap: 12px;
    }

    .profile-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .full-row {
        grid-column: 1 / -1;
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
        line-height: 1.45;
        word-break: break-word;
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
        border-radius: 99px;
        background: var(--red-soft);
        color: var(--red-dark);
        border: 1px solid #FCA5A5;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .nik-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px;
    }

    .nik-row input {
        min-height: 44px;
        font-family: 'DM Mono', Consolas, monospace;
        font-size: 12.5px;
        letter-spacing: 2px;
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

    .profile-actions {
        display: flex;
        justify-content: flex-end;
        padding-top: 2px;
    }

    .profile-primary-button {
        min-height: 40px;
    }

    .field-error {
        margin-top: 5px;
        color: var(--red-dark);
        font-size: 12px;
        line-height: 1.4;
    }

    @media (max-width: 920px) {
        .profile-hero,
        .profile-layout {
            grid-template-columns: 1fr;
        }

        .profile-hero-meta {
            justify-content: flex-start;
        }

        .profile-card {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .profile-hero,
        .profile-panel {
            padding: 16px;
        }

        .profile-card::before {
            margin: -16px -16px 0;
        }

        .profile-grid {
            grid-template-columns: 1fr;
        }

        .full-row {
            grid-column: auto;
        }

        .profile-section-head,
        .profile-actions {
            display: grid;
        }

        .nik-row {
            grid-template-columns: 1fr;
        }

        .secondary-button,
        .profile-primary-button,
        .profile-actions button {
            width: 100%;
        }
    }
</style>

<div class="profile-page">
    <section class="profile-hero" aria-labelledby="profileTitle">
        <div>
            <div class="profile-kicker">Akun Pengguna</div>
            <h1 class="profile-title" id="profileTitle">Profil Saya</h1>
            <div class="profile-subtitle">
                Kelola foto profil, data akun, dan password agar akses absensi tetap aman.
            </div>
        </div>
        <div class="profile-hero-meta">
            <div class="profile-pill">
                <span class="profile-dot"></span>
                <?php echo e($user->role->nama_role ?? 'Role belum diatur'); ?>

            </div>
            <div class="profile-pill"><?php echo e($user->tempatTugas->nama_tempat ?? 'Tempat tugas belum diatur'); ?></div>
        </div>
    </section>

    <div class="profile-layout">
        <aside class="profile-panel profile-card">
            <div class="profile-avatar-wrap">
                <?php if($user->foto_profil): ?>
                    <img src="<?php echo e(Storage::url($user->foto_profil)); ?>" alt="Foto Profil" class="profile-avatar">
                <?php else: ?>
                    <div class="profile-initial"><?php echo e(strtoupper(substr($user->nama ?? 'U', 0, 1))); ?></div>
                <?php endif; ?>
            </div>

            <h2 class="profile-name"><?php echo e($user->nama); ?></h2>
            <div class="profile-role"><?php echo e($user->role->nama_role ?? '-'); ?></div>

            <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data" class="profile-upload">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="nama" value="<?php echo e(old('nama', $user->nama)); ?>">
                <input type="hidden" name="username" value="<?php echo e(old('username', $user->username)); ?>">
                <input type="hidden" name="email" value="<?php echo e(old('email', $user->email)); ?>">

                <div>
                    <label for="foto_profil">Foto Profil</label>
                    <input type="file" id="foto_profil" name="foto_profil" accept="image/*">
                    <p class="profile-help">Gunakan foto JPG atau PNG dengan ukuran maksimal 2MB.</p>
                    <?php $__errorArgs = ['foto_profil'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="field-error"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="submit" class="profile-primary-button">Simpan Foto</button>
            </form>
        </aside>

        <div>
            <section class="profile-panel profile-section">
                <div class="profile-section-head">
                    <div>
                        <h2>Informasi Akun</h2>
                        <p>Perbarui data dasar akun yang digunakan untuk masuk ke aplikasi.</p>
                    </div>
                    <span class="profile-section-badge">Profil</span>
                </div>

                <form method="POST" action="<?php echo e(route('profile.update')); ?>" class="profile-fields profile-grid">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" value="<?php echo e(old('nama', $user->nama)); ?>" required maxlength="150" autocomplete="name">
                        <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="field-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo e(old('username', $user->username)); ?>" required maxlength="100" autocomplete="username">
                        <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="field-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required maxlength="150" autocomplete="email">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="field-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="readonly-field">
                        <label>Tempat Tugas</label>
                        <div class="readonly-value"><?php echo e($user->tempatTugas->nama_tempat ?? 'Belum ditetapkan'); ?></div>
                    </div>

                    <div class="readonly-field full-row">
                        <label>NIK <span class="sensitive-label">Sensitif</span></label>
                        <?php if($nik && $nik !== 'Gagal mendekripsi NIK'): ?>
                            <div class="nik-row">
                                <input type="password" id="nik_input" value="<?php echo e($nik); ?>" disabled>
                                <button type="button" class="secondary-button" id="nikToggle">Lihat</button>
                            </div>
                        <?php else: ?>
                            <div class="readonly-value muted"><?php echo e($nik ?? 'Data NIK tidak ditemukan'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="profile-actions full-row">
                        <button type="submit">Simpan Profil</button>
                    </div>
                </form>
            </section>

            <section class="profile-panel profile-section">
                <div class="profile-section-head">
                    <div>
                        <h2>Ubah Password</h2>
                        <p>Gunakan password yang kuat dan tidak sama dengan akun lain.</p>
                    </div>
                    <span class="profile-section-badge">Keamanan</span>
                </div>

                <form method="POST" action="<?php echo e(route('profile.password')); ?>" class="profile-fields profile-grid">
                    <?php echo csrf_field(); ?>
                    <div class="full-row">
                        <label for="current_password">Password Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                    </div>

                    <div>
                        <label for="password">Password Baru</label>
                        <input type="password" id="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                    </div>

                    <div>
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <div class="profile-actions full-row">
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/profile/index.blade.php ENDPATH**/ ?>