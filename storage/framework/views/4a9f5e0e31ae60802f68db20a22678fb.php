<?php $__env->startSection('title', 'Detail Absensi'); ?>

<?php $__env->startSection('content'); ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1>Detail Absensi - <?php echo e($item->tanggal->translatedFormat('d F Y')); ?></h1>
        <a href="<?php echo e(url()->previous()); ?>" class="button" style="background: #6b7280; color: white; padding: 8px 16px; border-radius: 6px; font-weight: bold; text-decoration: none;">Kembali</a>
    </div>

    <div class="grid">
        <div class="panel">
            <h2>Informasi Petugas</h2>
            <table class="detail-table">
                <tr><th>Nama</th><td>: <?php echo e($item->user->nama); ?></td></tr>
                <tr><th>NIK/Username</th><td>: <?php echo e($item->user->username); ?></td></tr>
                <tr><th>Tempat Tugas</th><td>: <?php echo e($item->user->tempatTugas->nama_tempat ?? '-'); ?></td></tr>
                <tr><th>Status</th><td>: <span class="badge <?php echo e($item->status); ?>"><?php echo e(strtoupper($item->status)); ?></span></td></tr>
                <tr><th>Keterangan</th><td>: <?php echo e($item->keterangan ?? '-'); ?></td></tr>
            </table>
        </div>

        <div class="panel">
            <h2>Waktu Absensi</h2>
            <table class="detail-table">
                <tr><th>Jam Masuk</th><td>: <?php echo e($item->jam_masuk ?? '-'); ?></td></tr>
                <tr><th>Jam Pulang</th><td>: <?php echo e($item->jam_pulang ?? '-'); ?></td></tr>
                <tr><th>Tanggal Data</th><td>: <?php echo e($item->tanggal->translatedFormat('d/m/Y')); ?></td></tr>
            </table>
        </div>
    </div>

    <div class="grid" style="margin-top: 24px;">
        <div class="panel">
            <h2>Data Masuk</h2>
            <?php if($item->foto_masuk): ?>
                <img src="<?php echo e(asset('storage/' . $item->foto_masuk)); ?>" style="width: 100%; max-width: 400px; border-radius: 8px; margin-bottom: 16px; border: 4px solid #fff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <?php else: ?>
                <p class="muted">Tidak ada foto masuk.</p>
            <?php endif; ?>
            <table class="detail-table">
                <tr><th>Lokasi</th><td>: <?php echo e($item->lokasi_masuk ?? '-'); ?></td></tr>
                <tr><th>Koordinat</th><td>: <?php echo e($item->latitude_masuk ?? '-'); ?>, <?php echo e($item->longitude_masuk ?? '-'); ?></td></tr>
                <?php if($item->latitude_masuk): ?>
                    <tr>
                        <td colspan="2">
                            <a href="https://www.google.com/maps?q=<?php echo e($item->latitude_masuk); ?>,<?php echo e($item->longitude_masuk); ?>" target="_blank" style="color: #2563eb; text-decoration: underline;">Lihat di Google Maps</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

        <div class="panel">
            <h2>Data Pulang</h2>
            <?php if($item->foto_pulang): ?>
                <img src="<?php echo e(asset('storage/' . $item->foto_pulang)); ?>" style="width: 100%; max-width: 400px; border-radius: 8px; margin-bottom: 16px; border: 4px solid #fff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <?php else: ?>
                <p class="muted">Tidak ada foto pulang.</p>
            <?php endif; ?>
            <table class="detail-table">
                <tr><th>Lokasi</th><td>: <?php echo e($item->lokasi_pulang ?? '-'); ?></td></tr>
                <tr><th>Koordinat</th><td>: <?php echo e($item->latitude_pulang ?? '-'); ?>, <?php echo e($item->longitude_pulang ?? '-'); ?></td></tr>
                <?php if($item->latitude_pulang): ?>
                    <tr>
                        <td colspan="2">
                            <a href="https://www.google.com/maps?q=<?php echo e($item->latitude_pulang); ?>,<?php echo e($item->longitude_pulang); ?>" target="_blank" style="color: #2563eb; text-decoration: underline;">Lihat di Google Maps</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <style>
        .detail-table { width: 100%; border-collapse: collapse; }
        .detail-table th { text-align: left; width: 140px; padding: 8px 0; font-weight: 600; color: #4b5563; }
        .detail-table td { padding: 8px 0; color: #1f2937; }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/petugas/absensi-detail.blade.php ENDPATH**/ ?>