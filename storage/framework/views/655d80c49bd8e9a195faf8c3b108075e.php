<?php $__env->startSection('title', 'Approve Cuti'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Approve Cuti</h1>
    <?php echo $__env->make('partials.periode-filter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <table>
        <thead><tr><th>Petugas</th><th>Tanggal</th><th>Jenis</th><th>Alasan & Alamat</th><th>Pengganti</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($item->user->nama ?? '-'); ?></td>
                    <td><?php echo e($item->tanggal_mulai->format('d/m/Y')); ?> - <?php echo e($item->tanggal_selesai->format('d/m/Y')); ?></td>
                    <td><?php echo e($item->jenis_cuti); ?></td>
                    <td>
                        <strong><?php echo e($item->alasan); ?></strong>
                        <?php if($item->alasan == 'Alasan Lainnya'): ?>
                            <br><small class="muted">(<?php echo e($item->alasan_lainnya); ?>)</small>
                        <?php endif; ?>
                        <br><small>Alamat: <?php echo e($item->alamat_cuti); ?></small>
                    </td>
                    <td><?php echo e($item->pengganti->nama ?? '-'); ?></td>
                    <td><span class="badge <?php echo e($item->status); ?>"><?php echo e($item->status); ?></span></td>
                    <td><?php echo e($item->approver->nama ?? '-'); ?></td>
                    <td class="actions">
                        <?php if($item->status === 'pending'): ?>
                            <form method="POST" action="<?php echo e(route('atasan.cuti.approve', $item->id_cuti)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit">Approve</button>
                            </form>
                            <form method="POST" action="<?php echo e(route('atasan.cuti.reject', $item->id_cuti)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="danger">Reject</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="muted">Belum ada pengajuan cuti.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($items->links('pagination.simple')); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/atasan/cuti.blade.php ENDPATH**/ ?>