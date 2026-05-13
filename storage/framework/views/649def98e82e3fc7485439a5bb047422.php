<?php $__env->startSection('title', 'Periode'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Periode</h1>
    <div class="panel" style="margin-bottom: 24px;">`n        <form action="<?php echo e(route('admin.periode.index')); ?>" method="GET" class="filter-bar">`n            <div class="filter-control" style="max-width:120px;">`n                <label>Per Halaman</label>`n                <select name="per_page" onchange="this.form.submit()" style="width:100%;">`n                    <option value="10" <?php echo e(request('per_page') == 10 ? 'selected' : ''); ?>>10 / hal</option>`n                    <option value="15" <?php echo e(request('per_page') == 15 ? 'selected' : (request('per_page') ? '' : 'selected')); ?>>15 / hal</option>`n                    <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25 / hal</option>`n                    <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50 / hal</option>`n                    <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100 / hal</option>`n                </select>`n            </div>`n        </form>`n    </div>`n`n    <div class="panel">
        <p class="muted">Periode dibuat per tahun. Contoh tahun 2025 otomatis menjadi 01/01/2025 - 31/12/2025.</p>
        <form method="POST" action="<?php echo e(route('admin.periode.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-grid">
                <div><label>Tahun</label><input type="number" name="tahun" min="2000" max="2100" value="<?php echo e(date('Y')); ?>" required></div>
                <div><label>Status</label><select name="status"><option value="aktif">aktif</option><option value="nonaktif">nonaktif</option></select></div>
                <button type="submit">Tambah</button>
            </div>
        </form>
    </div>
    <table>
        <thead><tr><th>Tahun</th><th>Rentang</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <form method="POST" action="<?php echo e(route('admin.periode.update', $item->id_periode)); ?>" style="display:contents;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <td><input type="number" name="tahun" min="2000" max="2100" value="<?php echo e($item->tanggal_mulai->format('Y')); ?>" required></td>
                        <td><?php echo e($item->tanggal_mulai->format('d/m/Y')); ?> - <?php echo e($item->tanggal_selesai->format('d/m/Y')); ?></td>
                        <td><select name="status"><option value="aktif" <?php if($item->status === 'aktif'): echo 'selected'; endif; ?>>aktif</option><option value="nonaktif" <?php if($item->status === 'nonaktif'): echo 'selected'; endif; ?>>nonaktif</option></select></td>
                        <td style="display:flex; gap:8px; align-items:center;">
                            <button type="submit" style="padding:8px 16px; border:1px solid #d1d5db; border-radius:6px; background:#fff; color:#1f2937; font-weight:700; cursor:pointer; font-size:14px;">Simpan</button>
                            <form method="POST" action="<?php echo e(route('admin.periode.delete', $item->id_periode)); ?>" style="display:inline; margin:0;" onsubmit="return confirm('Hapus periode ini?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" style="padding:8px 16px; border:0; border-radius:6px; background:#dc2626; color:#fff; font-weight:700; cursor:pointer; font-size:14px;">Hapus</button>
                            </form>
                        </td>
                    </form>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo e($items->links('pagination.simple')); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/admin/periode.blade.php ENDPATH**/ ?>