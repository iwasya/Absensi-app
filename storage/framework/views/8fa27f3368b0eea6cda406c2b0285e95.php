<?php $__env->startSection('title', 'Dashboard Atasan'); ?>

<?php $__env->startSection('content'); ?>
<style>
    main {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 24px 28px !important;
    }

    .lead-dashboard {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .lead-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .lead-header h1 {
        margin: 0;
        font-size: 22px;
        line-height: 1.2;
    }

    .lead-header-sub {
        margin-top: 5px;
        color: var(--muted);
        font-size: 13px;
    }

    .lead-date-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 7px 13px;
        border: 1px solid var(--primary-border);
        border-radius: 99px;
        background: var(--primary-soft);
        color: var(--primary2);
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .lead-date-pill svg {
        width: 15px;
        height: 15px;
    }

    .lead-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .lead-stat-card,
    .lead-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
    }

    .lead-stat-card {
        padding: 16px;
    }

    .lead-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .lead-stat-icon {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        flex: 0 0 auto;
    }

    .lead-stat-icon svg {
        width: 18px;
        height: 18px;
    }

    .lead-stat-badge {
        display: inline-flex;
        align-items: center;
        min-height: 24px;
        padding: 4px 8px;
        border-radius: 99px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .lead-stat-label {
        color: var(--muted);
        font-size: 12px;
        margin-bottom: 5px;
    }

    .lead-stat-value {
        color: var(--text-color);
        font-size: 24px;
        font-weight: 700;
        line-height: 1;
    }

    .lead-stat-hint {
        margin-top: 6px;
        color: var(--muted);
        font-size: 11.5px;
    }

    .bg-primary-soft {
        background: var(--primary-soft);
        color: var(--primary);
    }

    .bg-green-soft {
        background: var(--green-soft);
        color: var(--green-dark);
    }

    .bg-amber-soft {
        background: var(--amber-soft);
        color: var(--amber-dark);
    }

    .bg-red-soft {
        background: var(--red-soft);
        color: var(--red-dark);
    }

    .lead-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(340px, .9fr);
        gap: 14px;
        align-items: start;
    }

    .lead-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .lead-card-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        color: var(--text-color);
        font-size: 15px;
        font-weight: 600;
    }

    .lead-card-title svg {
        width: 17px;
        height: 17px;
        color: var(--primary);
    }

    .lead-card-body {
        padding: 14px 16px;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .activity-item:first-child {
        padding-top: 0;
    }

    .activity-item:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .activity-avatar {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: var(--primary-soft);
        color: var(--primary);
        font-size: 12px;
        font-weight: 800;
        flex: 0 0 auto;
    }

    .activity-main {
        min-width: 0;
        flex: 1;
    }

    .activity-line {
        color: var(--text-color);
        font-size: 13px;
        line-height: 1.45;
    }

    .activity-line strong {
        font-weight: 700;
    }

    .activity-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 5px;
        color: var(--muted);
        font-size: 12px;
    }

    .activity-module {
        margin-left: auto;
        flex: 0 0 auto;
    }

    .lead-calendar-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
        flex-wrap: wrap;
    }

    .lead-calendar-nav {
        display: flex;
        gap: 8px;
    }

    .lead-calendar-nav a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 7px 10px;
        border-radius: 8px;
        background: var(--bg-color);
        color: var(--text-color);
        border: 1px solid var(--border2);
        font-size: 12px;
        font-weight: 600;
    }

    .lead-calendar-nav a:hover {
        background: var(--primary-soft);
        color: var(--primary);
        border-color: var(--primary-border);
    }

    .lead-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        background: var(--border-color);
        gap: 1px;
    }

    .lead-day-name {
        padding: 9px;
        background: var(--bg-color);
        color: var(--muted);
        font-size: 11px;
        font-weight: 800;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .lead-day {
        min-height: 116px;
        padding: 9px;
        background: var(--panel-bg);
    }

    .lead-day.outside-month {
        background: var(--bg-color);
        color: var(--muted);
    }

    .lead-day.today {
        box-shadow: inset 0 0 0 2px var(--primary);
    }

    .lead-date {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        margin-bottom: 6px;
        border-radius: 8px;
        color: var(--text-color);
        font-size: 12px;
        font-weight: 800;
    }

    .lead-day.today .lead-date {
        background: var(--primary);
        color: #fff;
    }

    .lead-event {
        display: block;
        margin-top: 5px;
        padding: 5px 7px;
        border-radius: 7px;
        background: var(--bg-color);
        color: var(--muted);
        border: 1px solid var(--border-color);
        font-size: 11px;
        line-height: 1.25;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .lead-event.absen {
        background: var(--primary-soft);
        color: var(--primary2);
        border-color: var(--primary-border);
        font-weight: 700;
    }

    .lead-event.tugas {
        background: var(--amber-soft);
        color: var(--amber-dark);
        border-color: #fde68a;
        font-weight: 700;
    }

    .lead-event.libur,
    .lead-event.cuti_bersama {
        background: var(--red-soft);
        color: var(--red-dark);
        border-color: #fca5a5;
    }

    .lead-event.kegiatan {
        background: var(--green-soft);
        color: var(--green-dark);
        border-color: #a7f3d0;
    }

    .today-list {
        display: flex;
        flex-direction: column;
    }

    .today-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .today-item:first-child {
        padding-top: 0;
    }

    .today-item:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .today-user {
        min-width: 0;
        flex: 1;
    }

    .today-name {
        color: var(--text-color);
        font-size: 13px;
        font-weight: 700;
    }

    .today-time {
        margin-top: 3px;
        color: var(--muted);
        font-family: 'DM Mono', monospace;
        font-size: 12px;
    }

    .lead-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 28px 14px;
        text-align: center;
        color: var(--muted);
        font-size: 13px;
    }

    .lead-empty-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: var(--bg-color);
        color: var(--muted);
        border: 1px solid var(--border-color);
    }

    .lead-empty-icon svg {
        width: 19px;
        height: 19px;
    }

    @media (max-width: 1080px) {
        .lead-stat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .lead-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        main {
            padding: 16px !important;
        }

        .lead-stat-grid {
            grid-template-columns: 1fr;
        }

        .lead-calendar-grid {
            grid-template-columns: 1fr;
        }

        .lead-day-name {
            display: none;
        }

        .lead-day {
            min-height: auto;
        }
    }
</style>

<div class="lead-dashboard">
    <div class="lead-header">
        <div>
            <h1>Dashboard Atasan</h1>
            <div class="lead-header-sub">Ringkasan persetujuan, aktivitas petugas, dan kalender operasional.</div>
        </div>
        <div class="lead-date-pill">
            <svg fill="none" viewBox="0 0 16 16">
                <rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
                <path d="M5 1.5v3M11 1.5v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <?php echo e(now()->translatedFormat('l, d F Y')); ?>

        </div>
    </div>

    <div class="lead-stat-grid">
        <div class="lead-stat-card">
            <div class="lead-stat-top">
                <div class="lead-stat-icon bg-red-soft">
                    <svg fill="none" viewBox="0 0 16 16">
                        <path d="M8 2v4l2.5 2.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.4"/>
                    </svg>
                </div>
                <span class="lead-stat-badge bg-red-soft">Cuti</span>
            </div>
            <div class="lead-stat-label">Cuti pending</div>
            <div class="lead-stat-value"><?php echo e($cutiPending->count()); ?></div>
            <div class="lead-stat-hint">Menunggu keputusan atasan</div>
        </div>

        <div class="lead-stat-card">
            <div class="lead-stat-top">
                <div class="lead-stat-icon bg-amber-soft">
                    <svg fill="none" viewBox="0 0 16 16">
                        <path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="lead-stat-badge bg-amber-soft">Tugas</span>
            </div>
            <div class="lead-stat-label">Tugas pending</div>
            <div class="lead-stat-value"><?php echo e($tugasPending->count()); ?></div>
            <div class="lead-stat-hint">Laporan perlu ditinjau</div>
        </div>

        <div class="lead-stat-card">
            <div class="lead-stat-top">
                <div class="lead-stat-icon bg-green-soft">
                    <svg fill="none" viewBox="0 0 16 16">
                        <path d="M3 8l3.5 3.5L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <span class="lead-stat-badge bg-green-soft">Hari ini</span>
            </div>
            <div class="lead-stat-label">Absensi hari ini</div>
            <div class="lead-stat-value"><?php echo e($absensiHariIni->count()); ?></div>
            <div class="lead-stat-hint">Petugas sudah tercatat</div>
        </div>

        <div class="lead-stat-card">
            <div class="lead-stat-top">
                <div class="lead-stat-icon bg-primary-soft">
                    <svg fill="none" viewBox="0 0 16 16">
                        <rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
                        <path d="M5 1.5v3M11 1.5v3M2 7h12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="lead-stat-badge bg-primary-soft">Bulan ini</span>
            </div>
            <div class="lead-stat-label">Absensi bulan ini</div>
            <div class="lead-stat-value"><?php echo e($absensiBulanIni); ?></div>
            <div class="lead-stat-hint">Total catatan absensi</div>
        </div>
    </div>

    <div class="lead-grid">
        <div class="lead-card">
            <div class="lead-card-head">
                <h2 class="lead-card-title">
                    <svg fill="none" viewBox="0 0 16 16">
                        <path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Aktivitas Terbaru Petugas
                </h2>
            </div>
            <div class="lead-card-body">
                <div class="activity-list">
                    <?php $__empty_1 = true; $__currentLoopData = $aktivitasTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="activity-item">
                            <div class="activity-avatar"><?php echo e(strtoupper(substr($activity->user->nama ?? 'U', 0, 1))); ?></div>
                            <div class="activity-main">
                                <div class="activity-line">
                                    <strong><?php echo e($activity->user->nama ?? '-'); ?></strong>
                                    <?php echo e($activity->aktivitas); ?>

                                </div>
                                <div class="activity-meta">
                                    <span><?php echo e($activity->created_at?->format('d/m/Y H:i') ?? '-'); ?></span>
                                    <?php if($activity->user?->tempatTugas): ?>
                                        <span><?php echo e($activity->user->tempatTugas->nama_tempat); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="badge activity-module"><?php echo e(ucfirst(str_replace('_', ' ', $activity->modul))); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="lead-empty">
                            <div class="lead-empty-icon">
                                <svg fill="none" viewBox="0 0 20 20">
                                    <path d="M4 6h12M4 10h8M4 14h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                            Belum ada aktivitas terbaru dari petugas.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="lead-card">
            <div class="lead-card-head">
                <h2 class="lead-card-title">
                    <svg fill="none" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M8 5v3l2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Absensi Hari Ini
                </h2>
                <span class="lead-date-pill"><?php echo e($absensiHariIni->count()); ?> data</span>
            </div>
            <div class="lead-card-body">
                <div class="today-list">
                    <?php $__empty_1 = true; $__currentLoopData = $absensiHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="today-item">
                            <div class="activity-avatar"><?php echo e(strtoupper(substr($item->user->nama ?? 'U', 0, 1))); ?></div>
                            <div class="today-user">
                                <div class="today-name"><?php echo e($item->user->nama ?? '-'); ?></div>
                                <div class="today-time"><?php echo e($item->jam_masuk ?? '--:--'); ?></div>
                            </div>
                            <span class="badge <?php echo e($item->status); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $item->status))); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="lead-empty">
                            <div class="lead-empty-icon">
                                <svg fill="none" viewBox="0 0 20 20">
                                    <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M10 6v4l2.5 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                            Belum ada absensi hari ini.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="lead-card">
        <div class="lead-calendar-head">
            <h2 class="lead-card-title">
                <svg fill="none" viewBox="0 0 16 16">
                    <rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
                    <path d="M5 1.5v3M11 1.5v3M2 7h12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
                Kalender <?php echo e($currentMonth->translatedFormat('F Y')); ?>

            </h2>
            <div class="lead-calendar-nav">
                <a href="<?php echo e(route('dashboard', ['month' => $previousMonth->month, 'year' => $previousMonth->year])); ?>">Sebelumnya</a>
                <a href="<?php echo e(route('dashboard', ['month' => $nextMonth->month, 'year' => $nextMonth->year])); ?>">Berikutnya</a>
            </div>
        </div>

        <div class="lead-calendar-grid">
            <?php $__currentLoopData = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="lead-day-name"><?php echo e($dayName); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $dateKey = $day->format('Y-m-d');
                    $dayEvents = $events->get($dateKey, collect());
                    $absensiCount = $absensiByDate->get($dateKey, 0);
                    $tugasCount = $tugasByDate->get($dateKey, 0);
                ?>

                <div class="lead-day <?php echo e($day->month !== $currentMonth->month ? 'outside-month' : ''); ?> <?php echo e($day->isToday() ? 'today' : ''); ?>">
                    <div class="lead-date"><?php echo e($day->day); ?></div>

                    <?php if($absensiCount > 0): ?>
                        <span class="lead-event absen"><?php echo e($absensiCount); ?> absen</span>
                    <?php endif; ?>

                    <?php if($tugasCount > 0): ?>
                        <span class="lead-event tugas"><?php echo e($tugasCount); ?> tugas</span>
                    <?php endif; ?>

                    <?php $__currentLoopData = $dayEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="lead-event <?php echo e($event->jenis_event); ?>">
                            <?php echo e($event->nama_event ?: ucfirst(str_replace('_', ' ', $event->jenis_event))); ?>

                        </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/atasan/dashboard.blade.php ENDPATH**/ ?>