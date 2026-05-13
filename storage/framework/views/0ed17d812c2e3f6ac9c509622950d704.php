<?php $__env->startSection('title', 'Profil Pengguna'); ?>

<?php $__env->startSection('content'); ?>
<div style="max-width: 600px; margin: 0 auto;">
    <h1>Profil Saya</h1>
    
    <div class="panel">
        <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            
            <div style="text-align: center; margin-bottom: 24px;">
                <?php if($user->foto_profil): ?>
                    <img src="<?php echo e(Storage::url($user->foto_profil)); ?>" alt="Foto Profil" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb; margin-bottom: 12px;">
                <?php else: ?>
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: #2563eb; color: #fff; display: grid; place-items: center; font-weight: bold; font-size: 48px; margin: 0 auto 12px;">
                        <?php echo e(strtoupper(substr($user->nama ?? 'U', 0, 1))); ?>

                    </div>
                <?php endif; ?>
                <div>
                    <label for="foto_profil" style="display:inline-block; background:#f3f4f6; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:13px;">Ganti Foto Profil</label>
                    <input type="file" id="foto_profil" name="foto_profil" accept="image/*" style="display:none;" onchange="this.form.submit()">
                </div>
                <div style="font-size:12px; color:#6b7280; margin-top:8px;">Format: JPG, PNG. Maks: 2MB. (Akan langsung tersimpan saat dipilih)</div>
            </div>

            <div style="margin-bottom: 16px;">
                <label>Nama Lengkap</label>
                <input type="text" value="<?php echo e($user->nama); ?>" disabled style="background: #f9fafb; color: #6b7280;">
            </div>

            <div style="margin-bottom: 16px;">
                <label>Username</label>
                <input type="text" value="<?php echo e($user->username); ?>" disabled style="background: #f9fafb; color: #6b7280;">
            </div>

            <div style="margin-bottom: 16px;">
                <label>Email</label>
                <input type="text" value="<?php echo e($user->email); ?>" disabled style="background: #f9fafb; color: #6b7280;">
            </div>

            <div style="margin-bottom: 16px;">
                <label>Peran / Role</label>
                <input type="text" value="<?php echo e($user->role->nama_role ?? '-'); ?>" disabled style="background: #f9fafb; color: #6b7280;">
            </div>

            <div style="margin-bottom: 16px;">
                <label>Tempat Tugas</label>
                <input type="text" value="<?php echo e($user->tempatTugas->nama_tempat ?? 'Belum ditetapkan'); ?>" disabled style="background: #f9fafb; color: #6b7280;">
            </div>

            <div style="margin-bottom: 16px;">
                <label>NIK (Nomor Induk Kependudukan) <span style="font-size:11px; color:#ef4444; font-weight:normal; margin-left:4px;">*Sensitif</span></label>
                <?php if($nik && $nik !== 'Gagal mendekripsi NIK'): ?>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <input type="password" id="nik_input" value="<?php echo e($nik); ?>" disabled style="background: #f9fafb; color: #111827; letter-spacing: 2px; flex: 1;">
                        <button type="button" onclick="var el=document.getElementById('nik_input'); el.type = el.type === 'password' ? 'text' : 'password';" style="background: #e5e7eb; color: #374151; padding: 10px 14px; border-radius: 6px; font-weight: bold;">Lihat</button>
                    </div>
                <?php else: ?>
                    <input type="text" value="<?php echo e($nik ?? 'Data NIK tidak ditemukan'); ?>" disabled style="background: #f9fafb; color: #6b7280; font-style: italic;">
                <?php endif; ?>
            </div>

        </form>
    </div>

    <div class="panel" style="margin-top: 24px;">
        <h2 style="font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid #e5e7eb; padding-bottom: 12px;">Ubah Password</h2>
        <form method="POST" action="<?php echo e(route('profile.password')); ?>">
            <?php echo csrf_field(); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/profile/index.blade.php ENDPATH**/ ?>