<?php $__env->startSection('title', 'Dashboard Petugas'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        .dashboard-clock {
            display: inline-block;
            margin-bottom: 20px;
            padding: 12px 20px;
            background: var(--panel-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .dashboard-clock-date {
            margin-bottom: 4px;
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 400;
        }

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

        .dashboard-event.task {
            background: var(--info-soft-bg);
            color: var(--info-soft-text);
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

    <h1>Dashboard Petugas</h1>

    <div id="realtime-clock" class="dashboard-clock">
        Memuat waktu...
    </div>
    
    <script>
        function updateClock() {
            const now = new Date();
            
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const dateStr = `${dayName}, ${date} ${monthName} ${year}`;
            const timeStr = `${hours}:${minutes}:${seconds}`;
            
            const clockEl = document.getElementById('realtime-clock');
            if (clockEl) {
                clockEl.innerHTML = `<div class="dashboard-clock-date">${dateStr}</div><div>${timeStr}</div>`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    <div class="grid">
        <div class="stat">Status hari ini<strong><?php echo e($absensiHariIni ? ucwords(str_replace('_', ' ', $absensiHariIni->status)) : 'Tidak Absen'); ?></strong></div>
        <div class="stat">Jam masuk<strong><?php echo e($absensiHariIni?->jam_masuk ?? '-'); ?></strong></div>
        <div class="stat">Jam pulang<strong><?php echo e($absensiHariIni?->jam_pulang ?? '-'); ?></strong></div>
        <div class="stat">Notifikasi belum dibaca<strong><?php echo e($notifikasiBelumBaca); ?></strong></div>
        <div class="stat">Cuti terpakai tahun ini<strong><?php echo e($cutiTerpakaiTahunIni); ?> / 12</strong></div>
        <div class="stat">Sisa cuti tahun ini<strong><?php echo e($sisaCutiTahunIni); ?></strong></div>
    </div>

    <div class="panel">
        <h2>Aktivitas Terbaru</h2>
        <div class="activity-list">
            <?php $__empty_1 = true; $__currentLoopData = $aktivitasTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="activity-item">
                    <div>
                        <span class="badge"><?php echo e(ucfirst(str_replace('_', ' ', $activity->modul))); ?></span>
                        <?php echo e($activity->aktivitas); ?>

                    </div>
                    <div class="activity-meta"><?php echo e($activity->created_at?->format('d/m/Y H:i') ?? '-'); ?></div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="muted">Belum ada aktivitas terbaru.</p>
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
                    $dayTugas = $tugasByDate[$dateKey] ?? collect();
                ?>

                <div class="dashboard-day <?php echo e($day->month !== $currentMonth->month ? 'outside-month' : ''); ?> <?php echo e($day->isToday() ? 'today' : ''); ?>">
                    <div class="dashboard-date"><?php echo e($day->day); ?></div>

                    <?php $__currentLoopData = $dayEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="dashboard-event <?php echo e($event->jenis_event); ?>">
                            <?php echo e($event->nama_event ?: ucfirst(str_replace('_', ' ', $event->jenis_event))); ?>

                        </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php $__currentLoopData = $dayTugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tugas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="dashboard-event task">
                            Tugas: <?php echo e(\Illuminate\Support\Str::limit($tugas->uraian, 34)); ?>

                        </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/petugas/dashboard.blade.php ENDPATH**/ ?>