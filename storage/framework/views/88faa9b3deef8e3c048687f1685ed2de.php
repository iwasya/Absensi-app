<?php $__env->startSection('title', 'Input Tugas Harian'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Input Tugas Harian</h1>

    
    <p class="muted">
        Hari ini: <?php echo e(\Carbon\Carbon::now()->translatedFormat('l, d F Y')); ?>

    </p>

    <?php if($jadwalHariIni->isNotEmpty()): ?>
        <div class="panel">
            <h2>Kalender Hari Ini</h2>
            <?php $__currentLoopData = $jadwalHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jadwal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p>
                    <span class="badge <?php echo e($jadwal->jenis_event); ?>">
                        <?php echo e($jadwal->jenis_event); ?>

                    </span>
                    <?php echo e($jadwal->nama_event ?? '-'); ?>

                    <span class="muted"><?php echo e($jadwal->keterangan); ?></span>
                </p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <div class="panel">
        <h2>Kirim Laporan</h2>
        <p class="muted">
            Periode aktif: <?php echo e($periodeAktif?->nama_periode ?? '-'); ?>

        </p>

        <form method="POST" action="<?php echo e(route('petugas.tugas.store')); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-grid">
                <div>
                    <label>Mulai</label>
                    <input type="datetime-local" 
                           name="tanggal_mulai" 
                           value="<?php echo e(now()->format('Y-m-d\TH:i')); ?>" 
                           required>
                </div>

                <div>
                    <label>Selesai</label>
                    <input type="datetime-local" 
                           name="tanggal_selesai" 
                           value="<?php echo e(now()->format('Y-m-d\TH:i')); ?>">
                </div>
            </div>

            <label style="margin-top:12px">Uraian</label>
            <textarea name="uraian" required></textarea>

            <button type="submit" style="margin-top:12px">
                Kirim Laporan
            </button>
        </form>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/petugas/tugas-input.blade.php ENDPATH**/ ?>