<?php $__env->startSection('title', 'Sanksi Saya'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Sanksi Saya</h1>
    
    <table>
        <thead>
            <tr>
                <th>Jenis</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($item->jenis_sanksi); ?></td>
                    <td><?php echo e($item->tanggal?->format('d/m/Y') ?? '-'); ?></td>
                    <td><?php echo e($item->keterangan); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="3" style="text-align: center;">Belum ada sanksi.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($items->links('pagination.simple')); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/petugas/sanksi.blade.php ENDPATH**/ ?>