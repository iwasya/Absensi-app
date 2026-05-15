<?php $__env->startSection('title', 'Dashboard Atasan'); ?>

<?php $__env->startSection('content'); ?>
<style>
    main { max-width: 100% !important; margin: 0 !important; padding: 24px 28px !important; }

    .dash-wrap { display: flex; flex-direction: column; gap: 18px; }

    .dash-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .dash-header h1 { font-size: 20px; font-weight: 600; color: var(--text-color); margin: 0; }
    .dash-header-sub { font-size: 12px; color: var(--muted); margin-top: 3px; }
    .dash-date-pill {
        background: var(--primary-soft); border: 1px solid var(--primary-border);
        border-radius: 99px; padding: 5px 14px;
        font-size: 12px; font-weight: 500; color: var(--primary2);
        display: flex; align-items: center; gap: 6px; white-space: nowrap;
    }
    .dash-date-pill svg { width: 13px; height: 13px; }

    .stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .stat-card { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 14px; padding: 16px; transition: box-shadow .2s; }
    .stat-card:hover { box-shadow: 0 4px 16px rgba(14, 165, 201, .08); }
    .stat-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
    .stat-ico { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-ico svg { width: 17px; height: 17px; }
    .stat-badge { font-size: 10px; font-weight: 600; padding: 3px 8px; border-radius: 99px; }
    .stat-lbl { font-size: 11px; color: var(--muted); margin-bottom: 4px; }
    .stat-val { font-size: 22px; font-weight: 600; line-height: 1; }
    .stat-hint { font-size: 11px; margin-top: 4px; }
    .stat-bar { height: 3px; background: var(--bg-color); border-radius: 99px; margin-top: 8px; overflow: hidden; }
    .stat-bar-fill { height: 100%; border-radius: 99px; }
    .bg-green { background: var(--green-soft); }
    .bg-amber { background: var(--amber-soft); }
    .bg-red { background: var(--red-soft); }
    .bg-accent { background: var(--primary-soft); }

    .mid-grid, .bot-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .card-box { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 14px; overflow: hidden; }
    .card-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--border-color); }
    .card-title { font-size: 14px; font-weight: 600; color: var(--text-color); display: flex; align-items: center; gap: 8px; }
    .card-title svg { width: 16px; height: 16px; color: var(--primary); }
    .card-link { font-size: 12px; font-weight: 500; color: var(--primary); text-decoration: none; white-space: nowrap; }
    .card-link:hover { color: var(--primary2); }
    .card-body { padding: 16px 18px; }

    .feed-list { display: flex; flex-direction: column; }
    .feed-item { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--border-color); }
    .feed-item:first-child { padding-top: 0; }
    .feed-item:last-child { border-bottom: none; padding-bottom: 0; }
    .feed-ava { width: 32px; height: 32px; border-radius: 8px; background: var(--primary-soft); color: var(--primary2); font-size: 11px; font-weight: 600; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .feed-info { flex: 1; min-width: 0; overflow: hidden; }
    .feed-name { font-size: 13px; font-weight: 500; color: var(--text-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .feed-desc { font-size: 11px; color: var(--muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .feed-badge { font-size: 10px; font-weight: 600; padding: 3px 9px; border-radius: 99px; white-space: nowrap; }
    .fb-ok { background: var(--green-soft); color: var(--green-dark); }
    .fb-late { background: var(--red-soft); color: var(--red-dark); }
    .fb-warn { background: var(--amber-soft); color: var(--amber-dark); }
    .fb-info { background: var(--primary-soft); color: var(--primary2); }

    .approval-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .approval-panel { background: var(--bg-color); border-radius: 10px; padding: 12px 14px; min-width: 0; }
    .approval-title { display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: 12px; font-weight: 600; color: var(--text-color); margin-bottom: 8px; }
    .approval-list { display: flex; flex-direction: column; gap: 8px; }
    .approval-row { min-width: 0; }
    .approval-name { font-size: 12px; color: var(--text-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .approval-meta { font-size: 11px; color: var(--muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .cal-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid var(--border-color); }
    .cal-month { font-size: 14px; font-weight: 600; color: var(--text-color); }
    .cal-nav { display: flex; gap: 6px; }
    .cal-arrow { width: 28px; height: 28px; border-radius: 7px; border: 1px solid var(--border2); background: var(--panel-bg); color: var(--muted); font-size: 16px; line-height: 1; display: flex; align-items: center; justify-content: center; text-decoration: none; }
    .cal-arrow:hover { background: var(--bg-color); color: var(--text-color); }
    .cal-body { padding: 12px 16px 14px; }
    .cal-day-labels { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; margin-bottom: 4px; }
    .cal-day-lbl { text-align: center; font-size: 10px; font-weight: 600; color: var(--muted); text-transform: uppercase; padding: 4px 0; }
    .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; }
    .cal-cell { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 11px; border-radius: 7px; color: var(--text-color); position: relative; background: var(--bg-color); border: 1px solid transparent; cursor: pointer; font-family: inherit; }
    .cal-cell.other { color: var(--border2); opacity: .65; }
    .cal-cell.today { background: var(--primary-soft); color: var(--primary); font-weight: 700; border: 1px solid var(--primary-border); }
    .cal-cell.selected { border-color: var(--primary); box-shadow: inset 0 0 0 1px var(--primary); }
    .cal-cell .cal-dot { position: absolute; bottom: 3px; left: 50%; transform: translateX(-50%); width: 4px; height: 4px; border-radius: 50%; }
    .cal-detail { margin: 10px 16px 14px; padding: 12px 14px; border-radius: 10px; background: var(--bg-color); border: 1px solid var(--border-color); }
    .cal-detail-title { font-size: 12px; font-weight: 600; color: var(--text-color); margin-bottom: 8px; }
    .cal-detail-list { display: flex; flex-direction: column; gap: 8px; }
    .cal-detail-row { display: flex; align-items: flex-start; gap: 8px; min-width: 0; }
    .cal-detail-dot { width: 7px; height: 7px; border-radius: 50%; margin-top: 6px; flex: 0 0 auto; }
    .cal-detail-text { min-width: 0; flex: 1; }
    .cal-detail-main { font-size: 12px; color: var(--text-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cal-detail-sub { font-size: 11px; color: var(--muted); margin-top: 2px; line-height: 1.4; }
    .cal-legend { display: flex; flex-wrap: wrap; gap: 12px; padding: 10px 16px 14px; border-top: 1px solid var(--border-color); }
    .cal-leg-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--muted); }
    .cal-leg-dot { width: 7px; height: 7px; border-radius: 50%; }

    .empty-state { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 28px 16px; text-align: center; }
    .empty-ico { width: 44px; height: 44px; border-radius: 12px; background: var(--bg-color); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; }
    .empty-ico svg { width: 20px; height: 20px; color: var(--muted); }
    .empty-title { font-size: 13px; font-weight: 500; color: var(--text-color); }
    .empty-sub { font-size: 12px; color: var(--muted); }

    @media (max-width: 991px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 860px) { .mid-grid, .bot-grid, .approval-grid { grid-template-columns: 1fr; } }
    @media (max-width: 600px) { .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; } main { padding: 16px !important; } }
</style>

<div class="dash-wrap">
    <div class="dash-header">
        <div>
            <h1>Halo, <?php echo e(auth()->user()->nama ?? 'Atasan'); ?></h1>
            <div class="dash-header-sub">
                <?php echo e(now()->translatedFormat('l, d F Y')); ?>

                <?php if(auth()->user()->tempatTugas): ?>
                    - <?php echo e(auth()->user()->tempatTugas->nama_tempat ?? ''); ?>

                <?php endif; ?>
            </div>
        </div>
        <div class="dash-date-pill">
            <svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            <?php echo e(now()->translatedFormat('F Y')); ?>

        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico bg-red"><svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="var(--red)" stroke-width="1.3"/><path d="M8 5v3l2 2" stroke="var(--red)" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                <span class="stat-badge" style="background:var(--red-soft);color:var(--red-dark)">Cuti</span>
            </div>
            <div class="stat-lbl">Cuti pending</div>
            <div class="stat-val" style="color:var(--red)"><?php echo e($cutiPending->count()); ?></div>
            <div class="stat-hint" style="color:var(--red-dark)">menunggu keputusan</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico bg-amber"><svg fill="none" viewBox="0 0 16 16"><path d="M2 4h12M2 8h9M2 12h6" stroke="var(--amber)" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                <span class="stat-badge" style="background:var(--amber-soft);color:var(--amber-dark)">Tugas</span>
            </div>
            <div class="stat-lbl">Tugas pending</div>
            <div class="stat-val" style="color:var(--amber)"><?php echo e($tugasPending->count()); ?></div>
            <div class="stat-hint" style="color:var(--amber-dark)">laporan perlu ditinjau</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico bg-green"><svg fill="none" viewBox="0 0 16 16"><path d="M3 8l3.5 3.5L13 5" stroke="var(--green)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                <span class="stat-badge" style="background:var(--green-soft);color:var(--green-dark)">Hari ini</span>
            </div>
            <div class="stat-lbl">Absensi hari ini</div>
            <div class="stat-val" style="color:var(--green)"><?php echo e($absensiHariIni->count()); ?></div>
            <div class="stat-hint" style="color:var(--green-dark)">data terbaru</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico bg-accent"><svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="var(--primary)" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="var(--primary)" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                <span class="stat-badge" style="background:var(--primary-soft);color:var(--primary2)">Bulan ini</span>
            </div>
            <div class="stat-lbl">Absensi bulan ini</div>
            <div class="stat-val" style="color:var(--primary)"><?php echo e($absensiBulanIni); ?></div>
            <div class="stat-hint" style="color:var(--primary2)">total catatan absensi</div>
        </div>
    </div>

    <div class="mid-grid">
        <div class="card-box">
            <div class="card-head">
                <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Aktivitas terbaru</span>
                <a href="<?php echo e(route('atasan.absensi.index')); ?>" class="card-link">Semua</a>
            </div>
            <div class="card-body">
                <div class="feed-list">
                    <?php $__empty_1 = true; $__currentLoopData = ($aktivitasTerbaru ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="feed-item">
                            <div class="feed-ava"><?php echo e(strtoupper(substr($act->user->nama ?? 'U', 0, 2))); ?></div>
                            <div class="feed-info">
                                <div class="feed-name"><?php echo e($act->user->nama ?? '-'); ?> - <?php echo e($act->aktivitas ?? ucfirst($act->modul ?? 'Aktivitas')); ?></div>
                                <div class="feed-desc">
                                    <?php echo e($act->created_at?->format('H:i') ?? '--:--'); ?>

                                    -
                                    <?php echo e($act->created_at?->translatedFormat('l, d M') ?? '-'); ?>

                                    <?php if($act->user?->tempatTugas): ?>
                                        - <?php echo e($act->user->tempatTugas->nama_tempat); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="feed-badge fb-info"><?php echo e(ucfirst(str_replace('_', ' ', $act->modul ?? '-'))); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="empty-state">
                            <div class="empty-ico"><svg fill="none" viewBox="0 0 16 16"><path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                            <div class="empty-title">Belum ada aktivitas</div>
                            <div class="empty-sub">Aktivitas petugas akan muncul di sini</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card-box">
            <div class="card-head">
                <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><path d="M3 8l3.5 3.5L13 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>Persetujuan pending</span>
                <a href="<?php echo e(route('atasan.cuti.index')); ?>" class="card-link">Cek cuti</a>
            </div>
            <div class="card-body">
                <div class="approval-grid">
                    <div class="approval-panel">
                        <div class="approval-title">
                            <span>Cuti</span>
                            <span class="feed-badge fb-late"><?php echo e($cutiPending->count()); ?></span>
                        </div>
                        <div class="approval-list">
                            <?php $__empty_1 = true; $__currentLoopData = $cutiPending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cuti): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="approval-row">
                                    <div class="approval-name"><?php echo e($cuti->user->nama ?? '-'); ?></div>
                                    <div class="approval-meta"><?php echo e($cuti->tanggal_mulai?->format('d/m/Y') ?? '-'); ?> - <?php echo e($cuti->tanggal_selesai?->format('d/m/Y') ?? '-'); ?></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="approval-meta">Tidak ada cuti pending</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="approval-panel">
                        <div class="approval-title">
                            <span>Tugas</span>
                            <span class="feed-badge fb-warn"><?php echo e($tugasPending->count()); ?></span>
                        </div>
                        <div class="approval-list">
                            <?php $__empty_1 = true; $__currentLoopData = $tugasPending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tugas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="approval-row">
                                    <div class="approval-name"><?php echo e($tugas->user->nama ?? '-'); ?></div>
                                    <div class="approval-meta"><?php echo e($tugas->tanggal_mulai?->format('d/m/Y H:i') ?? '-'); ?></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="approval-meta">Tidak ada tugas pending</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bot-grid">
        <div class="card-box">
            <div class="cal-head">
                <div class="cal-month"><?php echo e($currentMonth->translatedFormat('F Y')); ?></div>
                <div class="cal-nav">
                    <a class="cal-arrow" href="<?php echo e(route('dashboard', ['month' => $previousMonth->month, 'year' => $previousMonth->year])); ?>">&#8249;</a>
                    <a class="cal-arrow" href="<?php echo e(route('dashboard', ['month' => $nextMonth->month, 'year' => $nextMonth->year])); ?>">&#8250;</a>
                </div>
            </div>
            <div class="cal-body">
                <div class="cal-day-labels">
                    <?php $__currentLoopData = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="cal-day-lbl"><?php echo e($dayName); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="cal-grid">
                    <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $dateKey = $day->format('Y-m-d');
                            $dayEvents = $events->get($dateKey, collect());
                            $absensiCount = $absensiByDate->get($dateKey, 0);
                            $tugasCount = $tugasByDate->get($dateKey, 0);
                            $hasHoliday = $dayEvents->isNotEmpty();
                        ?>
                        <button type="button" class="cal-cell <?php echo e($day->month !== $currentMonth->month ? 'other' : ''); ?> <?php echo e($day->isToday() ? 'today' : ''); ?>" data-date="<?php echo e($dateKey); ?>" title="<?php echo e($absensiCount); ?> absensi, <?php echo e($tugasCount); ?> tugas">
                            <?php echo e($day->day); ?>

                            <?php if($absensiCount > 0): ?>
                                <span class="cal-dot" style="background:var(--green)"></span>
                            <?php elseif($tugasCount > 0): ?>
                                <span class="cal-dot" style="background:var(--amber)"></span>
                            <?php elseif($hasHoliday): ?>
                                <span class="cal-dot" style="background:var(--red)"></span>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="cal-detail" id="atasan-cal-detail"></div>
            <div class="cal-legend">
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--primary)"></div>Hari ini</div>
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--green)"></div>Absensi</div>
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--amber)"></div>Tugas</div>
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--red)"></div>Kalender</div>
            </div>
        </div>

        <div class="card-box">
            <div class="card-head">
                <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3l2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Absensi hari ini</span>
                <a href="<?php echo e(route('atasan.absensi.index')); ?>" class="card-link"><?php echo e($absensiHariIni->count()); ?> data</a>
            </div>
            <div class="card-body">
                <div class="feed-list">
                    <?php $__empty_1 = true; $__currentLoopData = $absensiHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $badgeClass = in_array($item->status, ['hadir']) ? 'fb-ok' : (in_array($item->status, ['telat', 'terlambat']) ? 'fb-warn' : 'fb-late');
                        ?>
                        <div class="feed-item">
                            <div class="feed-ava"><?php echo e(strtoupper(substr($item->user->nama ?? 'U', 0, 2))); ?></div>
                            <div class="feed-info">
                                <div class="feed-name"><?php echo e($item->user->nama ?? '-'); ?></div>
                                <div class="feed-desc">Masuk <?php echo e($item->jam_masuk ?? '--:--'); ?> - Pulang <?php echo e($item->jam_pulang ?? '--:--'); ?></div>
                            </div>
                            <span class="feed-badge <?php echo e($badgeClass); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $item->status))); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="empty-state">
                            <div class="empty-ico"><svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3l2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                            <div class="empty-title">Belum ada absensi</div>
                            <div class="empty-sub">Data absensi hari ini akan tampil di sini</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card-box">
        <div class="card-head">
            <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Kalender terdekat</span>
            <a href="<?php echo e(route('atasan.kalender.index')); ?>" class="card-link"><?php echo e($kalender->count()); ?> event</a>
        </div>
        <div class="card-body">
            <div class="feed-list">
                <?php $__empty_1 = true; $__currentLoopData = $kalender; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="feed-item">
                        <div class="feed-ava" style="background:var(--primary-soft);color:var(--primary);">
                            <svg fill="none" viewBox="0 0 16 16" width="14" height="14"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                        </div>
                        <div class="feed-info">
                            <div class="feed-name"><?php echo e($item->nama_event); ?></div>
                            <div class="feed-desc"><?php echo e($item->tanggal->format('d/m/Y')); ?></div>
                        </div>
                        <span class="feed-badge fb-info"><?php echo e(ucfirst(str_replace('_', ' ', $item->jenis_event))); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state">
                        <div class="empty-ico"><svg fill="none" viewBox="0 0 20 20"><rect x="2" y="3" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M6 1v3M14 1v3M2 8h16" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg></div>
                        <div class="empty-title">Belum ada event kalender</div>
                        <div class="empty-sub">Event dan jadwal akan muncul di sini</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    var absensiDetails = <?php echo json_encode($absensiCalendarDetails ?? [], 15, 512) ?>;
    var tugasDetails = <?php echo json_encode($tugasCalendarDetails ?? [], 15, 512) ?>;
    var eventDetails = <?php echo json_encode($eventCalendarDetails ?? [], 15, 512) ?>;
    var selectedKey = '<?php echo e(today()->toDateString()); ?>';

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char];
        });
    }

    function dateTitle(key) {
        var date = new Date(key + 'T00:00:00');
        return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
    }

    function detailRows(key) {
        var rows = [];
        (absensiDetails[key] || []).forEach(function(item) {
            rows.push({ color: 'var(--green)', title: item.nama, meta: item.waktu + ' - ' + item.status });
        });
        (tugasDetails[key] || []).forEach(function(item) {
            rows.push({ color: 'var(--amber)', title: item.nama, meta: item.waktu + ' - ' + item.status });
        });
        (eventDetails[key] || []).forEach(function(item) {
            rows.push({ color: 'var(--red)', title: item.nama, meta: item.status });
        });
        return rows;
    }

    function renderDetail(key) {
        var box = document.getElementById('atasan-cal-detail');
        if (!box) return;

        var rows = detailRows(key);
        if (!rows.length) {
            box.innerHTML = '<div class="cal-detail-title">' + escapeHtml(dateTitle(key)) + '</div>'
                + '<div class="cal-detail-sub">Belum ada keterangan pada tanggal ini.</div>';
            return;
        }

        box.innerHTML = '<div class="cal-detail-title">' + escapeHtml(dateTitle(key)) + '</div>'
            + '<div class="cal-detail-list">'
            + rows.map(function(row) {
                return '<div class="cal-detail-row">'
                    + '<span class="cal-detail-dot" style="background:' + row.color + '"></span>'
                    + '<div class="cal-detail-text">'
                    + '<div class="cal-detail-main">' + escapeHtml(row.title) + '</div>'
                    + '<div class="cal-detail-sub">' + escapeHtml(row.meta) + '</div>'
                    + '</div>'
                    + '</div>';
            }).join('')
            + '</div>';
    }

    function markSelected() {
        document.querySelectorAll('.cal-grid .cal-cell').forEach(function(cell) {
            cell.classList.toggle('selected', cell.dataset.date === selectedKey);
        });
    }

    document.querySelectorAll('.cal-grid .cal-cell').forEach(function(cell) {
        cell.addEventListener('click', function() {
            selectedKey = cell.dataset.date;
            renderDetail(selectedKey);
            markSelected();
        });
    });

    renderDetail(selectedKey);
    markSelected();
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/atasan/dashboard.blade.php ENDPATH**/ ?>