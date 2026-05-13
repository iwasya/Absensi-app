<?php $__env->startSection('title', 'Pengaturan Aplikasi'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .settings-page {
        max-width: 760px;
        margin: 0 auto;
    }

    .settings-panel {
        display: grid;
        gap: 24px;
    }

    .settings-section {
        display: grid;
        gap: 10px;
        padding-bottom: 22px;
        border-bottom: 1px solid var(--border-color);
    }

    .settings-section:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .settings-title {
        margin-bottom: 4px;
    }

    .settings-help {
        margin: 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.45;
    }

    .asset-preview {
        width: fit-content;
        max-width: 100%;
        min-width: 160px;
        min-height: 96px;
        display: grid;
        place-items: center;
        padding: 14px;
        background: var(--soft-bg);
        color: var(--soft-text);
        border: 1px solid var(--border-color);
        border-radius: 8px;
    }

    .asset-preview img {
        max-height: 82px;
        max-width: 100%;
        object-fit: contain;
    }

    .icon-preview {
        min-width: 76px;
        min-height: 76px;
        padding: 12px;
    }

    .icon-preview img {
        width: 48px;
        height: 48px;
        object-fit: contain;
    }

    .manual-icon-preview {
        width: 54px;
        height: 54px;
        display: grid;
        place-items: center;
        border-radius: 14px;
        font-size: 24px;
        font-weight: 700;
    }

    .icon-manual-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .theme-options {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .theme-option {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 58px;
        padding: 12px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--soft-bg);
        color: var(--soft-text);
        cursor: pointer;
        margin: 0;
    }

    .theme-option input {
        width: auto;
        margin: 0;
    }

    .theme-option:has(input:checked) {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.16);
    }

    .theme-swatch {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        border: 1px solid var(--border-color);
        flex: 0 0 auto;
    }

    .theme-swatch.light {
        background: linear-gradient(135deg, #ffffff 0 50%, #dbeafe 50% 100%);
    }

    .theme-swatch.dark {
        background: linear-gradient(135deg, #020617 0 50%, #334155 50% 100%);
    }

    .settings-submit {
        width: 100%;
        padding: 12px 18px;
        font-size: 16px;
    }

    @media (max-width: 640px) {
        .theme-options {
            grid-template-columns: 1fr;
        }

        .icon-manual-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="settings-page">
    <h1>Pengaturan Aplikasi</h1>

    <div class="panel settings-panel">
        <form method="POST" action="<?php echo e(route('admin.pengaturan.store')); ?>" enctype="multipart/form-data" class="settings-panel">
            <?php echo csrf_field(); ?>

            <div class="settings-section">
                <div>
                    <h2 class="settings-title">Logo Aplikasi</h2>
                    <p class="settings-help">Logo ini akan tampil di sidebar. Kosongkan input file jika tidak ingin mengubah logo.</p>
                </div>

                <?php if($app_logo): ?>
                    <div class="asset-preview">
                        <img src="<?php echo e(Storage::url($app_logo)); ?>" alt="Logo Aplikasi">
                    </div>
                <?php else: ?>
                    <div class="asset-preview muted">Belum ada logo</div>
                <?php endif; ?>

                <div>
                    <label for="app_logo">Upload Logo Baru</label>
                    <input id="app_logo" type="file" name="app_logo" accept="image/*">
                    <p class="settings-help">Format: JPG atau PNG. Maksimal 2MB.</p>
                </div>

                <?php $__errorArgs = ['app_logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="settings-section">
                <div>
                    <h2 class="settings-title">Ikon Web</h2>
                    <p class="settings-help">Ikon ini akan tampil di tab browser dan shortcut perangkat. Pilih upload gambar atau buat ikon manual dari teks dan warna.</p>
                </div>

                <?php if($app_icon_mode === 'manual'): ?>
                    <div class="asset-preview icon-preview">
                        <div class="manual-icon-preview" style="background: <?php echo e($app_icon_bg); ?>; color: <?php echo e($app_icon_color); ?>;">
                            <?php echo e(strtoupper(substr($app_icon_text ?: 'A', 0, 2))); ?>

                        </div>
                    </div>
                <?php elseif($app_icon): ?>
                    <div class="asset-preview icon-preview">
                        <img src="<?php echo e(Storage::url($app_icon)); ?>" alt="Ikon Web">
                    </div>
                <?php else: ?>
                    <div class="asset-preview icon-preview muted">Belum ada ikon</div>
                <?php endif; ?>

                <div class="theme-options">
                    <label class="theme-option">
                        <input type="radio" name="app_icon_mode" value="upload" <?php echo e($app_icon_mode === 'upload' ? 'checked' : ''); ?>>
                        <span>Upload Gambar</span>
                    </label>

                    <label class="theme-option">
                        <input type="radio" name="app_icon_mode" value="manual" <?php echo e($app_icon_mode === 'manual' ? 'checked' : ''); ?>>
                        <span>Manual</span>
                    </label>
                </div>

                <div>
                    <label for="app_icon">Upload Ikon Baru</label>
                    <input id="app_icon" type="file" name="app_icon" accept="image/*">
                    <p class="settings-help">Rekomendasi: PNG persegi 512x512. Maksimal 1MB.</p>
                </div>

                <div class="icon-manual-grid">
                    <div>
                        <label for="app_icon_text">Teks Ikon</label>
                        <input id="app_icon_text" type="text" name="app_icon_text" maxlength="2" value="<?php echo e($app_icon_text); ?>">
                    </div>
                    <div>
                        <label for="app_icon_bg">Warna Background</label>
                        <input id="app_icon_bg" type="color" name="app_icon_bg" value="<?php echo e($app_icon_bg); ?>">
                    </div>
                    <div>
                        <label for="app_icon_color">Warna Teks</label>
                        <input id="app_icon_color" type="color" name="app_icon_color" value="<?php echo e($app_icon_color); ?>">
                    </div>
                </div>

                <?php $__errorArgs = ['app_icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['app_icon_mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['app_icon_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['app_icon_bg'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['app_icon_color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="settings-section">
                <div>
                    <h2 class="settings-title">Tema Tampilan</h2>
                    <p class="settings-help">Pilih tema yang akan digunakan di seluruh aplikasi.</p>
                </div>

                <div class="theme-options">
                    <label class="theme-option">
                        <input type="radio" name="app_theme" value="light" <?php echo e($app_theme === 'light' ? 'checked' : ''); ?>>
                        <span class="theme-swatch light" aria-hidden="true"></span>
                        <span>Terang</span>
                    </label>

                    <label class="theme-option">
                        <input type="radio" name="app_theme" value="dark" <?php echo e($app_theme === 'dark' ? 'checked' : ''); ?>>
                        <span class="theme-swatch dark" aria-hidden="true"></span>
                        <span>Gelap</span>
                    </label>
                </div>

                <?php $__errorArgs = ['app_theme'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <button type="submit" class="settings-submit">Simpan Pengaturan</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/admin/pengaturan.blade.php ENDPATH**/ ?>