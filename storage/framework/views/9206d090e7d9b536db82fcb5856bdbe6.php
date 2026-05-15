<?php $__env->startSection('title', 'Sanksi'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Sanksi</h1>
    <div class="panel" style="margin-bottom: 24px;">
        <form action="<?php echo e(route('admin.sanksi.index')); ?>" method="GET" class="filter-bar">
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
                <th>User</th>
                <th>Jenis</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item->user->nama ?? '-'); ?></td>
                    <td><?php echo e($item->jenis_sanksi); ?></td>
                    <td><?php echo e($item->tanggal?->format('d/m/Y') ?? '-'); ?></td>
                    <td><?php echo e($item->keterangan); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo e($items->links('pagination.simple')); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/admin/sanksi.blade.php ENDPATH**/ ?>