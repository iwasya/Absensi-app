<?php $__env->startSection('title', 'Akses Absen Telat'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Akses Absen Telat</h1>
    <p class="muted" style="margin-top:-10px; margin-bottom:20px;">Berikan akses khusus kepada petugas agar bisa melakukan absen masuk pada hari ini meskipun sudah melewati jam batas (07:15).</p>

    <div class="panel">
        <form method="POST" action="<?php echo e(route('admin.buka-absen.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-grid">
                <div>
                    <label>Pilih Petugas</label>
                    <select name="id_user" required>
                        <option value="">-- Pilih --</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id_user); ?>"><?php echo e($user->nama); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button type="submit" style="align-self: end; background: #059669;">Buka Akses Hari Ini</button>
            </div>
        </form>
    </div>

    <h2>Riwayat Akses Dibuka (Hari Ini)</h2>

    <div class="panel" style="margin-bottom: 24px;">
        <form action="<?php echo e(route('admin.buka-absen.index')); ?>" method="GET" class="filter-bar">
            <div class="filter-control" style="max-width:120px;">
                <label>Per Halaman</label>
                <select name="per_page" onchange="this.form.submit()" style="width:100%;">
                    <option value="10" <?php echo e(request('per_page') == 10 ? 'selected' : ''); ?>>10 / hal</option>
                    <option value="15" <?php echo e(request('per_page') == 15 ? 'selected' : (request('per_page') ? '' : 'selected')); ?>>15 / hal</option>
                    <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25 / hal</option>
                    <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50 / hal</option>
                    <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100 / hal</option>
                </select>
            </div>
        </form>
    </div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>User</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($item->tanggal->format('d/m/Y')); ?></td>
                    <td><?php echo e($item->user->nama ?? '-'); ?></td>
                    <td><span class="badge pending"><?php echo e($item->status); ?></span></td>
                    <td><?php echo e($item->keterangan); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="muted" style="text-align: center;">Belum ada akses telat yang dibuka hari ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($items->links('pagination.simple')); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/admin/buka_absen.blade.php ENDPATH**/ ?>