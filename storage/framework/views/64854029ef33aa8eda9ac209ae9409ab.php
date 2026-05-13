<?php $__env->startSection('title', 'Lap. Tugas Harian'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Lap. Tugas Harian</h1>
    <div class="panel">
        <p class="muted">Riwayat laporan tugas harian kamu. Laporan akan muncul di akun atasan untuk approve/reject.</p>
        <a href="<?php echo e(route('petugas.tugas.input')); ?>">Input tugas baru</a>
    </div>

    <div class="panel" style="margin-bottom: 24px;">
        <form action="<?php echo e(route('petugas.tugas.laporan')); ?>" method="GET" class="filter-bar">
            <div class="filter-control">
                <label>Bulan</label>
                <select name="month">
                    <option value="">-- Pilih Bulan --</option>
                    <?php for($m=1; $m<=12; $m++): ?>
                        <option value="<?php echo e($m); ?>" <?php echo e(request('month') == $m ? 'selected' : ''); ?>>
                            <?php echo e(\Carbon\Carbon::create()->month($m)->translatedFormat('F')); ?>

                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="filter-control">
                <label>Cari</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Uraian atau status...">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit">Tampilkan</button>
                <a href="<?php echo e(route('petugas.tugas.laporan')); ?>" class="button" style="background:#f3f4f6; color:#374151; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Reset</a>
                <a href="<?php echo e(route('petugas.tugas.laporan.print', request()->all())); ?>" target="_blank" style="background: #059669; color: white; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Cetak</a>
            </div>
        </form>
    </div>

    <table>
        <thead><tr><th>Mulai</th><th>Selesai</th><th>Uraian</th><th>Status</th></tr></thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($item->tanggal_mulai->format('d/m/Y H:i')); ?></td>
                    <td><?php echo e($item->tanggal_selesai?->format('d/m/Y H:i') ?? '-'); ?></td>
                    <td><?php echo e($item->uraian); ?></td>
                    <td><span class="badge <?php echo e($item->status); ?>"><?php echo e($item->status); ?></span></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="muted">Belum ada laporan tugas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($items->links('pagination.simple')); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/petugas/tugas-laporan.blade.php ENDPATH**/ ?>