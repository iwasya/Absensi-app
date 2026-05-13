<?php $__env->startSection('title', 'Dashboard Atasan'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        .dashboard-calendar-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .dashboard-calendar-nav {
            display: flex;
            gap: 8px;
        }

        .dashboard-calendar-nav a {
            padding: 8px 10px;
            border-radius: 6px;
            background: var(--soft-bg);
            color: var(--soft-text);
            font-size: 13px;
        }

        .dashboard-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            background: var(--panel-bg);
        }

        .dashboard-day-name,
        .dashboard-day {
            min-width: 0;
            border-right: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }

        .dashboard-day-name:nth-child(7n),
        .dashboard-day:nth-child(7n) {
            border-right: 0;
        }

        .dashboard-day-name {
            padding: 9px;
            background: var(--bg-color);
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            text-align: center;
        }

        .dashboard-day {
            min-height: 112px;
            padding: 8px;
            background: var(--panel-bg);
        }

        .dashboard-day.outside-month {
            background: var(--bg-color);
            color: var(--muted);
        }

        .dashboard-day.today {
            box-shadow: inset 0 0 0 2px var(--primary);
        }

        .dashboard-date {
            margin-bottom: 6px;
            font-weight: 700;
        }

        .dashboard-event {
            display: block;
            margin-top: 5px;
            padding: 5px 6px;
            border-radius: 6px;
            background: var(--soft-bg);
            color: var(--soft-text);
            font-size: 11px;
            line-height: 1.25;
        }

        .dashboard-event.absen {
            background: var(--info-soft-bg);
            color: var(--info-soft-text);
            font-weight: 700;
        }

        .dashboard-event.tugas {
            background: var(--warning-soft-bg);
            color: var(--warning-soft-text);
            font-weight: 700;
        }

        .dashboard-event.libur,
        .dashboard-event.cuti_bersama {
            background: var(--danger-soft-bg);
            color: var(--danger-soft-text);
        }

        .dashboard-event.kegiatan {
            background: var(--success-bg);
            color: var(--success-text);
        }

        .activity-list {
            display: grid;
            gap: 10px;
        }

        .activity-item {
            display: grid;
            gap: 4px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .activity-item:last-child {
            border-bottom: 0;
        }

        .activity-meta {
            color: var(--muted);
            font-size: 12px;
        }

        @media (max-width: 760px) {
            .dashboard-calendar-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-day-name {
                display: none;
            }

            .dashboard-day {
                min-height: auto;
                border-right: 0;
            }
        }
    </style>

    <h1>Dashboard Atasan</h1>
    <div class="grid">
        <div class="stat">Cuti pending<strong><?php echo e($cutiPending->count()); ?></strong></div>
        <div class="stat">Tugas pending<strong><?php echo e($tugasPending->count()); ?></strong></div>
        <div class="stat">Absensi hari ini<strong><?php echo e($absensiHariIni->count()); ?></strong></div>
        <div class="stat">Absensi bulan ini<strong><?php echo e($absensiBulanIni); ?></strong></div>
    </div>

    <div class="panel">
        <h2>Aktivitas Terbaru Petugas</h2>
        <div class="activity-list">
            <?php $__empty_1 = true; $__currentLoopData = $aktivitasTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="activity-item">
                    <div>
                        <span class="badge"><?php echo e(ucfirst(str_replace('_', ' ', $activity->modul))); ?></span>
                        <?php echo e($activity->user->nama ?? '-'); ?> - <?php echo e($activity->aktivitas); ?>

                    </div>
                    <div class="activity-meta">
                        <?php echo e($activity->created_at?->format('d/m/Y H:i') ?? '-'); ?>

                        <?php if($activity->user?->tempatTugas): ?>
                            - <?php echo e($activity->user->tempatTugas->nama_tempat); ?>

                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="muted">Belum ada aktivitas terbaru dari petugas.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel">
        <div class="dashboard-calendar-head">
            <h2>Kalender <?php echo e($currentMonth->translatedFormat('F Y')); ?></h2>
            <div class="dashboard-calendar-nav">
                <a href="<?php echo e(route('dashboard', ['month' => $previousMonth->month, 'year' => $previousMonth->year])); ?>">Sebelumnya</a>
                <a href="<?php echo e(route('dashboard', ['month' => $nextMonth->month, 'year' => $nextMonth->year])); ?>">Berikutnya</a>
            </div>
        </div>

        <div class="dashboard-calendar-grid">
            <?php $__currentLoopData = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="dashboard-day-name"><?php echo e($dayName); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $dateKey = $day->format('Y-m-d');
                    $dayEvents = $events->get($dateKey, collect());
                    $absensiCount = $absensiByDate->get($dateKey, 0);
                    $tugasCount = $tugasByDate->get($dateKey, 0);
                ?>

                <div class="dashboard-day <?php echo e($day->month !== $currentMonth->month ? 'outside-month' : ''); ?> <?php echo e($day->isToday() ? 'today' : ''); ?>">
                    <div class="dashboard-date"><?php echo e($day->day); ?></div>

                    <?php if($absensiCount > 0): ?>
                        <span class="dashboard-event absen"><?php echo e($absensiCount); ?> absen</span>
                    <?php endif; ?>

                    <?php if($tugasCount > 0): ?>
                        <span class="dashboard-event tugas"><?php echo e($tugasCount); ?> tugas</span>
                    <?php endif; ?>

                    <?php $__currentLoopData = $dayEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="dashboard-event <?php echo e($event->jenis_event); ?>">
                            <?php echo e($event->nama_event ?: ucfirst(str_replace('_', ' ', $event->jenis_event))); ?>

                        </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="panel">
        <h2>Absensi Hari Ini</h2>
        <?php $__empty_1 = true; $__currentLoopData = $absensiHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <p><?php echo e($item->user->nama ?? '-'); ?> - <span class="badge <?php echo e($item->status); ?>"><?php echo e($item->status); ?></span> <?php echo e($item->jam_masuk); ?></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="muted">Belum ada absensi hari ini.</p>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/atasan/dashboard.blade.php ENDPATH**/ ?>