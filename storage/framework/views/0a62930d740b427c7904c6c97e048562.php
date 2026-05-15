<?php $__env->startSection('title', 'Activity Log'); ?>

<?php $__env->startSection('content'); ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h1 style="margin: 0;">Activity Log</h1>
        <a href="<?php echo e(route('admin.logs.export')); ?>" style="background: #10b981; color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px;">Export CSV</a>
    </div>
    <div class="panel" style="margin-bottom: 24px;">        <form action="<?php echo e(route('admin.logs.index')); ?>" method="GET" class="filter-bar">            <div class="filter-control" style="max-width:120px;">                <label>Per Halaman</label>                <select name="per_page" onchange="this.form.submit()" style="width:100%;">                    <option value="10" <?php echo e(request('per_page') == 10 ? 'selected' : ''); ?>>10 / hal</option>                    <option value="15" <?php echo e(request('per_page') == 15 ? 'selected' : (request('per_page') ? '' : 'selected')); ?>>15 / hal</option>                    <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25 / hal</option>                    <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50 / hal</option>                    <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100 / hal</option>                </select>            </div>        </form>    </div>    <table>
        <thead><tr><th>Waktu</th><th>User</th><th>Aktivitas</th><th>Modul</th><th>Status</th><th>IP</th><th>Device</th></tr></thead>
        <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item->created_at); ?></td>
                    <td><?php echo e($item->user->nama ?? '-'); ?></td>
                    <td><?php echo e($item->aktivitas); ?></td>
                    <td><?php echo e($item->modul); ?></td>
                    <td><?php echo e($item->status); ?></td>
                    <td><?php echo e($item->ip_address); ?></td>
                    <td><?php echo e($item->device); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo e($items->links('pagination.simple')); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/admin/logs.blade.php ENDPATH**/ ?>