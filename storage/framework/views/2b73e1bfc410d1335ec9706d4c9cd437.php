<?php $__env->startSection('title', 'Kalender Tugas'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Kalender</h1>

    <div class="panel">
        <h2>Hari Ini</h2>
        <?php $__empty_1 = true; $__currentLoopData = $todayItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <p>
                <span class="badge <?php echo e($item->jenis_event); ?>"><?php echo e($item->jenis_event); ?></span>
                <?php echo e($item->nama_event ?? '-'); ?>

                <span class="muted"><?php echo e($item->keterangan); ?></span>
            </p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="muted">Tidak ada libur atau kegiatan khusus hari ini.</p>
        <?php endif; ?>

        <?php if(isset($todayTugas) && $todayTugas->isNotEmpty()): ?>
            <h3 style="margin-top: 16px; font-size: 16px;">Tugas Hari Ini</h3>
            <?php $__currentLoopData = $todayTugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tugas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p>
                    <span class="badge <?php echo e($tugas->status == 'approved' ? 'approve' : ($tugas->status == 'rejected' ? 'reject' : 'pending')); ?>"><?php echo e(ucfirst($tugas->status)); ?></span>
                    <strong><?php echo e($tugas->uraian); ?></strong>
                </p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>

    <div class="panel">
        <div class="calendar-head">
            <a href="<?php echo e(route('petugas.tugas.kalender', ['month' => $previousMonth->month, 'year' => $previousMonth->year])); ?>">Sebelumnya</a>
            <h2><?php echo e($currentMonth->translatedFormat('F Y')); ?></h2>
            <a href="<?php echo e(route('petugas.tugas.kalender', ['month' => $nextMonth->month, 'year' => $nextMonth->year])); ?>">Berikutnya</a>
        </div>

        <div class="calendar-grid">
            <?php $__currentLoopData = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="calendar-day-name"><?php echo e($dayName); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $dateKey = $day->format('Y-m-d');
                    $dayEvents = $events->get($dateKey, collect());
                    $dayTugas = isset($tugasByDate[$dateKey]) ? collect($tugasByDate[$dateKey]) : collect();
                ?>
                <div class="calendar-cell <?php echo e($day->month !== $currentMonth->month ? 'muted-day' : ''); ?>">
                    <div class="calendar-date"><?php echo e($day->format('d')); ?></div>
                    <?php $__currentLoopData = $dayEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="calendar-event <?php echo e($event->jenis_event); ?>">
                            <strong><?php echo e($event->nama_event ?? ucfirst($event->jenis_event)); ?></strong><br>
                            <span><?php echo e($event->keterangan); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $dayTugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tugas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="calendar-event" style="background: #eef2ff; color: #3730a3; border-left: 3px solid #6366f1;">
                            <strong>Tugas:</strong> <?php echo e(\Illuminate\Support\Str::limit($tugas->uraian, 30)); ?><br>
                            <span style="font-size: 10px; font-weight: bold; text-transform: uppercase;"><?php echo e($tugas->status); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/petugas/kalender.blade.php ENDPATH**/ ?>