<?php $__env->startSection('title', 'Tempat Tugas'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Tempat Tugas</h1>
    <div class="panel" style="margin-bottom: 24px;">        <form action="<?php echo e(route('admin.tempat.index')); ?>" method="GET" class="filter-bar">            <div class="filter-control" style="max-width:120px;">                <label>Per Halaman</label>                <select name="per_page" onchange="this.form.submit()" style="width:100%;">                    <option value="10" <?php echo e(request('per_page') == 10 ? 'selected' : ''); ?>>10 / hal</option>                    <option value="15" <?php echo e(request('per_page') == 15 ? 'selected' : (request('per_page') ? '' : 'selected')); ?>>15 / hal</option>                    <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25 / hal</option>                    <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50 / hal</option>                    <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100 / hal</option>                </select>            </div>        </form>    </div>    <div class="panel">
        <form method="POST" action="<?php echo e(route('admin.tempat.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-grid">
                <div><label>Nama Tempat</label><input name="nama_tempat" required></div>
                <div><label>Alamat</label><input name="alamat"></div>
                <div><label>Latitude</label><input type="number" step="0.000001" min="-90" max="90" name="latitude" placeholder="-6.209286"></div>
                <div><label>Longitude</label><input type="number" step="0.000001" min="-180" max="180" name="longitude" placeholder="106.871253"></div>
                <button type="submit">Tambah</button>
            </div>
        </form>
    </div>
    <table>
        <thead><tr><th>Nama</th><th>Alamat</th><th>Latitude</th><th>Longitude</th><th>Aksi</th><th>Hapus</th></tr></thead>
        <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <form method="POST" action="<?php echo e(route('admin.tempat.update', $item->id_tempat)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <td><input name="nama_tempat" value="<?php echo e($item->nama_tempat); ?>" required></td>
                        <td><input name="alamat" value="<?php echo e($item->alamat); ?>"></td>
                        <td><input type="number" step="0.000001" min="-90" max="90" name="latitude" value="<?php echo e($item->latitude); ?>"></td>
                        <td><input type="number" step="0.000001" min="-180" max="180" name="longitude" value="<?php echo e($item->longitude); ?>"></td>
                        <td><button type="submit">Simpan</button></td>
                    </form>
                    <td><form method="POST" action="<?php echo e(route('admin.tempat.delete', $item->id_tempat)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="danger">Hapus</button></form></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo e($items->links('pagination.simple')); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/admin/tempat.blade.php ENDPATH**/ ?>