<?php $__env->startSection('title', 'Sanksi'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Sanksi</h1>
    <div class="panel">
        <form method="POST" action="<?php echo e(route('atasan.sanksi.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-grid">
                <div>
                    <label>User</label>
                    <select name="id_user" required>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id_user); ?>"><?php echo e($user->nama); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label>Jenis Sanksi</label>
                    <select name="jenis_sanksi" required>
                        <option value="">-- Pilih Jenis Sanksi --</option>
                        <option value="Teguran Lisan">Teguran Lisan</option>
                        <option value="Teguran Tertulis">Teguran Tertulis</option>
                        <option value="SP1">SP1</option>
                        <option value="SP2">SP2</option>
                        <option value="SP3">SP3</option>
                        <option value="Pemotongan TPP">Pemotongan TPP</option>
                        <option value="Pembinaan">Pembinaan</option>
                        <option value="Hukuman Disiplin Ringan">Hukuman Disiplin Ringan</option>
                        <option value="Hukuman Disiplin Sedang">Hukuman Disiplin Sedang</option>
                        <option value="Hukuman Disiplin Berat">Hukuman Disiplin Berat</option>
                    </select>
                </div>
                <div><label>Tanggal</label><input type="date" name="tanggal"></div>
                <div><label>Keterangan</label><input name="keterangan"></div>
                <button type="submit">Tambah</button>
            </div>
        </form>
    </div>
    <div class="panel" style="margin-bottom: 24px;">
        <form action="<?php echo e(route('atasan.sanksi.index')); ?>" method="GET" class="filter-bar">
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
                    <option value="">-- Semua User --</option>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id_user); ?>" <?php echo e(request('id_user') == $user->id_user ? 'selected' : ''); ?>><?php echo e($user->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="filter-control">
                <label>Cari</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Jenis sanksi atau keterangan...">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit">Tampilkan</button>
                <a href="<?php echo e(route('atasan.sanksi.index')); ?>" class="button" style="background:#f3f4f6; color:#374151; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Reset</a>
                <a href="<?php echo e(route('atasan.sanksi.print', request()->all())); ?>" target="_blank" style="background: #059669; color: white; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Cetak</a>
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
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item->user->nama ?? '-'); ?></td>
                    <td><?php echo e($item->jenis_sanksi); ?></td>
                    <td><?php echo e($item->tanggal?->format('d/m/Y') ?? '-'); ?></td>
                    <td><?php echo e($item->keterangan); ?></td>
                    <td>
                        <form method="POST" action="<?php echo e(route('atasan.sanksi.delete', $item->id_sanksi)); ?>">
                            <?php echo csrf_field(); ?> 
                            <?php echo method_field('DELETE'); ?>
                            <button class="danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo e($items->links('pagination.simple')); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/atasan/sanksi.blade.php ENDPATH**/ ?>