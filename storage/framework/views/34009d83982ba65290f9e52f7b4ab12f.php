<?php $__env->startSection('title', 'Pantau Absensi'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Pantau Absensi</h1>
    <div class="panel" style="margin-bottom: 24px;">
        <form action="<?php echo e(route('atasan.absensi.index')); ?>" method="GET" class="filter-bar">
            <div class="filter-control">
                <label>Bulan</label>
                <select name="month">
                    <option value="">-- Semua Bulan --</option>
                    <?php for($m=1; $m<=12; $m++): ?>
                        <option value="<?php echo e($m); ?>" <?php echo e(request('month') == $m ? 'selected' : ''); ?>>
                            <?php echo e(\Carbon\Carbon::create()->month($m)->translatedFormat('F')); ?>

                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="filter-control">
                <label>User</label>
                <select name="id_user">
                    <option value="">-- Semua Petugas --</option>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id_user); ?>" <?php echo e(request('id_user') == $user->id_user ? 'selected' : ''); ?>><?php echo e($user->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="filter-control">
                <label>Status</label>
                <select name="status">
                    <option value="">-- Semua Status --</option>
                    <option value="hadir" <?php echo e(request('status') == 'hadir' ? 'selected' : ''); ?>>Hadir</option>
                    <option value="terlambat" <?php echo e(request('status') == 'terlambat' ? 'selected' : ''); ?>>Terlambat</option>
                    <option value="tidak_absen" <?php echo e(request('status') == 'tidak_absen' ? 'selected' : ''); ?>>Tidak Absen</option>
                </select>
            </div>
            <div class="filter-control">
                <label>Cari</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Nama, lokasi, atau keterangan...">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit">Tampilkan</button>
                <a href="<?php echo e(route('atasan.absensi.index')); ?>" class="button" style="background:#f3f4f6; color:#374151; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Reset</a>
                <a href="<?php echo e(route('atasan.absensi.print', request()->all())); ?>" target="_blank" style="background: #059669; color: white; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Cetak</a>
            </div>
        </form>
    </div>

    <table>
        <thead><tr><th>Petugas</th><th>Tempat</th><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($item->user->nama ?? '-'); ?></td>
                    <td><?php echo e($item->user->tempatTugas->nama_tempat ?? '-'); ?></td>
                    <td><?php echo e($item->tanggal->format('d/m/Y')); ?></td>
                    <td><?php echo e($item->jam_masuk ?? '-'); ?></td>
                    <td><?php echo e($item->jam_pulang ?? '-'); ?></td>
                    <td><span class="badge <?php echo e($item->status); ?>"><?php echo e($item->status); ?></span></td>
                    <td>
                        <a href="<?php echo e(route('absensi.detail', $item->id_absensi)); ?>" class="button" style="padding: 4px 8px; font-size: 12px; background: #6366f1; color: white; border-radius: 4px; text-decoration: none;">Detail</a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="muted">Belum ada data absensi.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($items->links('pagination.simple')); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/atasan/absensi.blade.php ENDPATH**/ ?>