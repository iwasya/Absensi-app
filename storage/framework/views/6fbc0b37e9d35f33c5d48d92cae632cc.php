<?php $__env->startSection('title', 'Detail Absensi'); ?>

<?php $__env->startSection('content'); ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1>Detail Absensi - <?php echo e($item->tanggal->translatedFormat('d F Y')); ?></h1>
        <a href="<?php echo e(url()->previous()); ?>" class="back-btn">Kembali</a>
    </div>

    <div class="grid">
        <div class="panel">
            <h2>Informasi Petugas</h2>
            <table class="detail-table">
                <tr><th>Nama</th><td>: <?php echo e($item->user->nama); ?></td></tr>
                <tr><th>Username</th><td>: <?php echo e($item->user->username); ?></td></tr>
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
                <div class="photo-wrapper">
                    <img src="<?php echo e(asset('storage/' . $item->foto_masuk)); ?>" alt="Foto Masuk">
                </div>
            <?php else: ?>
                <p class="muted">Tidak ada foto masuk.</p>
            <?php endif; ?>
            <table class="detail-table">
                <tr><th>Lokasi</th><td>: <?php echo e($item->lokasi_masuk ?? '-'); ?></td></tr>
                <tr><th>Koordinat</th><td>: <?php echo e($item->latitude_masuk ?? '-'); ?>, <?php echo e($item->longitude_masuk ?? '-'); ?></td></tr>
                <?php if($item->latitude_masuk): ?>
                    <tr>
                        <td colspan="2">
                            <a href="https://www.google.com/maps?q=<?php echo e($item->latitude_masuk); ?>,<?php echo e($item->longitude_masuk); ?>" target="_blank" class="map-link">Lihat di Google Maps</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

        <div class="panel">
            <h2>Data Pulang</h2>
            <?php if($item->foto_pulang): ?>
                <div class="photo-wrapper">
                    <img src="<?php echo e(asset('storage/' . $item->foto_pulang)); ?>" alt="Foto Pulang">
                </div>
            <?php else: ?>
                <p class="muted">Tidak ada foto pulang.</p>
            <?php endif; ?>
            <table class="detail-table">
                <tr><th>Lokasi</th><td>: <?php echo e($item->lokasi_pulang ?? '-'); ?></td></tr>
                <tr><th>Koordinat</th><td>: <?php echo e($item->latitude_pulang ?? '-'); ?>, <?php echo e($item->longitude_pulang ?? '-'); ?></td></tr>
                <?php if($item->latitude_pulang): ?>
                    <tr>
                        <td colspan="2">
                            <a href="https://www.google.com/maps?q=<?php echo e($item->latitude_pulang); ?>,<?php echo e($item->longitude_pulang); ?>" target="_blank" class="map-link">Lihat di Google Maps</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <style>
        .detail-table { width: 100%; border-collapse: collapse; }
        .detail-table th { text-align: left; width: 140px; padding: 8px 0; font-weight: 600; color: var(--muted); }
        .detail-table td { padding: 8px 0; color: var(--text-color); }

        /* Back button - dark mode compatible */
        .back-btn {
            display: inline-block;
            background: var(--soft-bg);
            color: var(--soft-text);
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            border: 1px solid var(--border-color);
            transition: all 0.2s;
        }
        .back-btn:hover {
            filter: brightness(0.95);
            color: var(--text-color);
        }

        /* Photo wrapper - dark mode compatible */
        .photo-wrapper {
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
            margin-bottom: 16px;
            border: 4px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: var(--soft-bg);
        }
        .photo-wrapper img {
            width: 100%;
            display: block;
        }

        /* Map link - dark mode compatible */
        .map-link {
            color: var(--primary);
            text-decoration: underline;
        }
        .map-link:hover {
            filter: brightness(1.2);
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/petugas/absensi-detail.blade.php ENDPATH**/ ?>