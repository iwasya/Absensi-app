<?php $__env->startSection('title', 'Dashboard Admin'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Dashboard Admin</h1>
    <div class="grid">
        <div class="stat">Total user<strong><?php echo e($totalUsers); ?></strong></div>
        <div class="stat">Petugas PPSU<strong><?php echo e($totalPetugas); ?></strong></div>
        <div class="stat">Absensi hari ini<strong><?php echo e($totalAbsensiHariIni); ?></strong></div>
        <div class="stat">Cuti pending<strong><?php echo e($cutiPending); ?></strong></div>
        <div class="stat">Tugas pending<strong><?php echo e($tugasPending); ?></strong></div>
        <div class="stat">Periode aktif<strong><?php echo e($periodeAktif?->nama_periode ?? '-'); ?></strong></div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>