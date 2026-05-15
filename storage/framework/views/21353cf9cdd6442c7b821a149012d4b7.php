<?php $__env->startSection('title', 'Manajemen Data Sensitif (NIK)'); ?>

<?php $__env->startSection('content'); ?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
    <h1>Data Sensitif (NIK)</h1>
</div>

<div class="panel">
    <div style="margin-bottom: 16px; font-size: 14px; color: #6b7280;">
        Gunakan halaman ini untuk memasukkan NIK pengguna. NIK akan dienkripsi secara aman sebelum disimpan ke database, dan tidak dapat dilihat secara langsung oleh siapa pun selain pengguna itu sendiri melalui halaman Profil.
    </div>

    <form method="GET" action="<?php echo e(route('admin.data-sensitif.index')); ?>" class="filter-bar" style="margin-bottom: 16px;">
        <div class="filter-control" style="flex: 1; max-width: 400px;">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama, username, email..." style="width: 100%;">
        </div>
        <button type="submit" style="background:#2563eb; color:#fff; padding:9px 16px;">Cari</button>        <div class="filter-control" style="max-width:120px;">            <select name="per_page" onchange="this.form.submit()" style="width:100%;">                <option value="10" <?php echo e(request('per_page') == 10 ? 'selected' : ''); ?>>10 / hal</option>                <option value="15" <?php echo e(request('per_page') == 15 ? 'selected' : (request('per_page') ? '' : 'selected')); ?>>15 / hal</option>                <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25 / hal</option>                <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50 / hal</option>                <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100 / hal</option>            </select>        </div>
        <?php if(request()->filled('search')): ?>
            <a href="<?php echo e(route('admin.data-sensitif.index')); ?>" style="background:#f3f4f6; color:#374151; padding:9px 12px; border-radius:6px; text-decoration:none; font-weight:bold; font-size:14px;">Reset</a>
        <?php endif; ?>
    </form>

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
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($user->id_user); ?></td>
                        <td>
                            <strong><?php echo e($user->nama); ?></strong><br>
                            <span class="muted" style="font-size: 12px;"><?php echo e($user->role->nama_role ?? '-'); ?></span>
                        </td>
                        <td><?php echo e($user->username); ?></td>
                        <td>
                            <?php if($user->userSensitive && $user->userSensitive->nik_encrypted): ?>
                                <span class="badge" style="background:#ecfdf5; color:#047857;">Tersimpan (Encrypted)</span>
                            <?php else: ?>
                                <span class="badge" style="background:#fef2f2; color:#b91c1c;">Belum Ada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="<?php echo e(route('admin.data-sensitif.update')); ?>" style="display: flex; gap: 8px;">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id_user" value="<?php echo e($user->id_user); ?>">
                                <input type="text" name="nik" placeholder="Masukkan NIK baru" style="flex: 1;" maxlength="20">
                                <button type="submit" style="background:#2563eb; padding: 8px 12px; font-size: 13px;">Simpan</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 16px;">
        <?php echo e($users->links('pagination.simple')); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/admin/data_sensitif.blade.php ENDPATH**/ ?>