<?php $__env->startSection('title', 'Kalender Monitoring'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        .calendar-event.absensi-count {
            background: var(--info-soft-bg);
            color: var(--info-soft-text);
            font-weight: 700;
        }

        .calendar-event.tugas-count {
            background: var(--warning-soft-bg);
            color: var(--warning-soft-text);
            font-weight: 700;
        }
    </style>

    <h1>Kalender Monitoring</h1>

    <div class="panel">
        <div class="calendar-head">
            <a href="<?php echo e(route('atasan.kalender.index', ['month' => $previousMonth->month, 'year' => $previousMonth->year])); ?>">Sebelumnya</a>
            <h2><?php echo e($currentMonth->translatedFormat('F Y')); ?></h2>
            <a href="<?php echo e(route('atasan.kalender.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year])); ?>">Berikutnya</a>
        </div>

        <div class="calendar-grid">
            <?php $__currentLoopData = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="calendar-day-name"><?php echo e($dayName); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $dateKey = $day->format('Y-m-d');
                    $dayEvents = $events->get($dateKey, collect());
                    $absensiCount = $absensiByDate->get($dateKey, 0);
                    $tugasCount = $tugasByDate->get($dateKey, 0);
                ?>
                <div class="calendar-cell <?php echo e($day->month !== $currentMonth->month ? 'muted-day' : ''); ?>">
                    <div class="calendar-date"><?php echo e($day->format('d')); ?></div>

                    <?php if($absensiCount > 0): ?>
                        <div class="calendar-event absensi-count"><?php echo e($absensiCount); ?> absen</div>
                    <?php endif; ?>

                    <?php if($tugasCount > 0): ?>
                        <div class="calendar-event tugas-count"><?php echo e($tugasCount); ?> tugas</div>
                    <?php endif; ?>

                    <?php $__currentLoopData = $dayEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="calendar-event <?php echo e($event->jenis_event); ?>">
                            <strong><?php echo e($event->nama_event ?? ucfirst($event->jenis_event)); ?></strong><br>
                            <span><?php echo e($event->keterangan); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="panel">
        <h3>Keterangan</h3>
        <div style="display: flex; gap: 16px; flex-wrap: wrap; font-size: 14px;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 12px; height: 12px; background: #fee2e2; border-radius: 2px;"></span> Libur
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 12px; height: 12px; background: #dcfce7; border-radius: 2px;"></span> Kegiatan
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 12px; height: 12px; background: #eef2ff; border-radius: 2px;"></span> Jumlah Absensi
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 12px; height: 12px; background: #fff7ed; border-radius: 2px;"></span> Jumlah Tugas
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/atasan/kalender.blade.php ENDPATH**/ ?>