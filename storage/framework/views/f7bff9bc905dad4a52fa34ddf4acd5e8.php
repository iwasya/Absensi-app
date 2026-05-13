<?php $__env->startSection('title', 'Pengajuan Cuti'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Pengajuan Cuti</h1>
    <div class="panel">
        <h2>Ajukan Cuti</h2>
        <p class="muted">
            Kuota cuti tahun ini: <?php echo e($cutiTerpakaiTahunIni); ?> dari <?php echo e($batasCutiTahunan); ?> kali terpakai.
            Sisa <?php echo e(max($batasCutiTahunan - $cutiTerpakaiTahunIni, 0)); ?> kali.
        </p>
        <form method="POST" action="<?php echo e(route('petugas.cuti.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-grid">
                <div><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai" required></div>
                <div><label>Tanggal Selesai</label><input type="date" name="tanggal_selesai" required></div>
                <div>
                    <label>Jenis Cuti</label>
                    <select name="jenis_cuti" required>
                        <option value="Tahunan">Tahunan</option>
                        <option value="Besar">Besar</option>
                    </select>
                </div>
                <div>
                    <label>Alasan</label>
                    <select name="alasan" id="alasan_select" required>
                        <option value="Sakit">Sakit</option>
                        <option value="Urusan Keluarga">Urusan Keluarga</option>
                        <option value="Lamaran/Menikah">Lamaran/Menikah</option>
                        <option value="Anggota Keluarga Meninggal">Anggota Keluarga Meninggal</option>
                        <option value="Anggota Keluarga Sakit">Anggota Keluarga Sakit</option>
                        <option value="Anggota Keluarga Menikah">Anggota Keluarga Menikah</option>
                        <option value="Kegiatan Agama atau Budaya">Kegiatan Agama atau Budaya</option>
                        <option value="Musibah/Bencana">Musibah/Bencana</option>
                        <option value="Alasan Lainnya">Alasan Lainnya</option>
                    </select>
                </div>
                <div id="alasan_lainnya_wrapper" style="display: none;">
                    <label>Sebutkan Alasan Lainnya</label>
                    <input type="text" name="alasan_lainnya">
                </div>
                <div>
                    <label>Pendamping Pengganti</label>
                    <select name="id_pengganti" required>
                        <option value="">-- Pilih Petugas Pengganti --</option>
                        <?php $__currentLoopData = $petugasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id_user); ?>"><?php echo e($p->nama); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <label style="margin-top:12px">Alamat Selama Cuti</label>
            <textarea name="alamat_cuti" required placeholder="Tuliskan alamat lengkap selama masa cuti..."></textarea>
            <button type="submit" style="margin-top:12px">Kirim Pengajuan</button>
        </form>
    </div>

    <table>
        <thead><tr><th>Mulai</th><th>Selesai</th><th>Jenis</th><th>Alasan</th><th>Pengganti</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($item->tanggal_mulai->format('d/m/Y')); ?></td>
                    <td><?php echo e($item->tanggal_selesai->format('d/m/Y')); ?></td>
                    <td><?php echo e($item->jenis_cuti); ?></td>
                    <td>
                        <?php echo e($item->alasan); ?>

                        <?php if($item->alasan == 'Alasan Lainnya'): ?>
                            <br><small class="muted">(<?php echo e($item->alasan_lainnya); ?>)</small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($item->pengganti->nama ?? '-'); ?></td>
                    <td><span class="badge <?php echo e($item->status); ?>"><?php echo e($item->status); ?></span></td>
                    <td>
                        <?php if($item->status === 'approve'): ?>
                            <a href="<?php echo e(route('petugas.cuti.print', $item->id_cuti)); ?>" target="_blank" class="button" style="padding: 4px 8px; font-size: 11px; background: #059669; color: white; border-radius: 4px; text-decoration: none;">Cetak Surat</a>
                        <?php else: ?>
                            <span class="muted" style="font-size: 11px;">Menunggu Approval</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="muted">Belum ada pengajuan cuti.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($items->links('pagination.simple')); ?>


    <script>
        document.getElementById('alasan_select').addEventListener('change', function() {
            var wrapper = document.getElementById('alasan_lainnya_wrapper');
            if (this.value === 'Alasan Lainnya') {
                wrapper.style.display = 'block';
                wrapper.querySelector('input').required = true;
            } else {
                wrapper.style.display = 'none';
                wrapper.querySelector('input').required = false;
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/petugas/cuti.blade.php ENDPATH**/ ?>