

<?php $__env->startSection('title', 'Kalender'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Kalender</h1>
    <div class="panel" style="margin-bottom: 24px;">        <form action="<?php echo e(route('admin.kalender.index')); ?>" method="GET" class="filter-bar">            <div class="filter-control" style="max-width:120px;">                <label>Per Halaman</label>                <select name="per_page" onchange="this.form.submit()" style="width:100%;">                    <option value="10" <?php echo e(request('per_page') == 10 ? 'selected' : ''); ?>>10 / hal</option>                    <option value="15" <?php echo e(request('per_page') == 15 ? 'selected' : (request('per_page') ? '' : 'selected')); ?>>15 / hal</option>                    <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25 / hal</option>                    <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50 / hal</option>                    <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100 / hal</option>                </select>            </div>        </form>    </div>    <div class="panel">
        <p class="muted">Tanggal libur, cuti bersama, atau kegiatan yang diisi di sini otomatis tampil di menu Kalender petugas.</p>
        <form method="POST" action="<?php echo e(route('admin.kalender.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-grid">
                <div><label>Tanggal</label><input type="date" name="tanggal" required></div>
                <div><label>Nama Event</label><input name="nama_event"></div>
                <div><label>Jenis</label><select name="jenis_event"><option value="libur">libur</option><option value="kegiatan">kegiatan</option><option value="cuti_bersama">cuti_bersama</option></select></div>
                <div><label>Keterangan</label><input name="keterangan"></div>
                <button type="submit">Tambah</button>
            </div>
        </form>
    </div>
    <table>
        <thead><tr><th>Tanggal</th><th>Nama</th><th>Jenis</th><th>Keterangan</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item->tanggal->format('d/m/Y')); ?></td>
                    <td><?php echo e($item->nama_event); ?></td>
                    <td><?php echo e($item->jenis_event); ?></td>
                    <td><?php echo e($item->keterangan); ?></td>
                    <td><form method="POST" action="<?php echo e(route('admin.kalender.delete', $item->id_kalender)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="danger">Hapus</button></form></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo e($items->links('pagination.simple')); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/admin/kalender.blade.php ENDPATH**/ ?>