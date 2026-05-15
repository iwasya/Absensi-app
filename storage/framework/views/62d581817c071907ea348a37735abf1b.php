<?php $__env->startSection('title', 'Dashboard Petugas'); ?>

<?php $__env->startSection('content'); ?>

<style>
    main { max-width: 100% !important; margin: 0 !important; padding: 24px 28px !important; }

    .dash-wrap { display: flex; flex-direction: column; gap: 18px; }

    /* â”€â”€ Header â”€â”€ */
    .dash-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .dash-header h1 { font-size: 20px; font-weight: 600; color: var(--text-color); margin: 0; }
    .dash-header-sub { font-size: 12px; color: var(--muted); margin-top: 3px; }
    .dash-header-sub strong { color: var(--text-color); }
    .dash-date-pill {
        background: var(--primary-soft); border: 1px solid var(--primary-border);
        border-radius: 99px; padding: 5px 14px;
        font-size: 12px; font-weight: 500; color: var(--primary2);
        display: flex; align-items: center; gap: 6px; white-space: nowrap;
    }
    .dash-date-pill svg { width: 13px; height: 13px; }

    /* â”€â”€ Stat Cards â”€â”€ */
    .stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; }
    .stat-card { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 14px; padding: 16px; transition: box-shadow .2s; }
    .stat-card:hover { box-shadow: 0 4px 16px rgba(14,165,201,.08); }
    .stat-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
    .stat-ico { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-ico svg { width: 17px; height: 17px; }
    .stat-badge { font-size: 10px; font-weight: 600; padding: 3px 8px; border-radius: 99px; }
    .stat-lbl { font-size: 11px; color: var(--muted); margin-bottom: 4px; }
    .stat-val { font-size: 22px; font-weight: 600; line-height: 1; }
    .stat-hint { font-size: 11px; margin-top: 4px; }
    .stat-bar { height: 3px; background: var(--bg-color); border-radius: 99px; margin-top: 8px; overflow: hidden; }
    .stat-bar-fill { height: 100%; border-radius: 99px; }
    .bg-green  { background: var(--green-soft); }
    .bg-amber  { background: var(--amber-soft); }
    .bg-red    { background: var(--red-soft); }
    .bg-accent { background: var(--primary-soft); }

    /* â”€â”€ Mid Row â”€â”€ */
    .mid-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .card-box { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 14px; overflow: hidden; }
    .card-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid var(--border-color); }
    .card-title { font-size: 14px; font-weight: 600; color: var(--text-color); display: flex; align-items: center; gap: 8px; }
    .card-title svg { width: 16px; height: 16px; color: var(--primary); }
    .card-link { font-size: 12px; font-weight: 500; color: var(--primary); text-decoration: none; }
    .card-link:hover { color: var(--primary2); }
    .card-body { padding: 16px 18px; }

    /* â”€â”€ Donut â”€â”€ */
    .donut-section { display: flex; align-items: center; justify-content: center; gap: 24px; padding: 8px 0; }
    .donut-wrap { position: relative; width: 100px; height: 100px; flex-shrink: 0; }
    .donut-label { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .donut-num { font-size: 20px; font-weight: 700; color: var(--text-color); }
    .donut-sub { font-size: 10px; color: var(--muted); }
    .legend-list { display: flex; flex-direction: column; gap: 8px; }
    .legend-item { display: flex; align-items: center; gap: 7px; font-size: 12px; color: var(--muted); }
    .legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }

    /* â”€â”€ Feed â”€â”€ */
    .feed-list { display: flex; flex-direction: column; }
    .feed-item { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--border-color); }
    .feed-item:last-child { border-bottom: none; }
    .feed-ava { width: 32px; height: 32px; border-radius: 8px; background: var(--primary-soft); color: var(--primary2); font-size: 11px; font-weight: 600; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .feed-info { flex: 1; overflow: hidden; }
    .feed-name { font-size: 13px; font-weight: 500; color: var(--text-color); }
    .feed-desc { font-size: 11px; color: var(--muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .feed-badge { font-size: 10px; font-weight: 600; padding: 3px 9px; border-radius: 99px; white-space: nowrap; }
    .fb-ok   { background: var(--green-soft); color: var(--green-dark); }
    .fb-late { background: var(--red-soft);   color: var(--red-dark); }
    .fb-info { background: var(--primary-soft); color: var(--primary2); }

    /* â”€â”€ Bottom Row â”€â”€ */
    .bot-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    /* â”€â”€ Calendar â”€â”€ */
    .cal-nav { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid var(--border-color); }
    .cal-month { font-size: 14px; font-weight: 600; color: var(--text-color); }
    .cal-arrow { width: 28px; height: 28px; border-radius: 7px; border: 1px solid var(--border2); background: var(--panel-bg); color: var(--muted); font-size: 16px; line-height: 1; cursor: pointer; display: flex; align-items: center; justify-content: center; font-family: inherit; transition: background .15s; }
    .cal-arrow:hover { background: var(--bg-color); color: var(--text-color); }
    .cal-body { padding: 12px 16px 14px; }
    .cal-day-labels { display: grid; grid-template-columns: repeat(7,1fr); gap: 2px; margin-bottom: 4px; }
    .cal-day-lbl { text-align: center; font-size: 10px; font-weight: 600; color: var(--muted); text-transform: uppercase; padding: 4px 0; }
    .cal-grid { display: grid; grid-template-columns: repeat(7,1fr); gap: 2px; }
    .cal-cell { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 11px; border-radius: 7px; color: var(--text-color); position: relative; cursor: default; }
    .cal-cell.other { color: var(--border2); }
    .cal-cell.today { background: var(--primary-soft); color: var(--primary); font-weight: 700; border: 1px solid var(--primary-border); }
    .cal-cell .cal-dot { position: absolute; bottom: 3px; left: 50%; transform: translateX(-50%); width: 4px; height: 4px; border-radius: 50%; }
    .cal-legend { display: flex; gap: 12px; padding: 10px 16px 14px; border-top: 1px solid var(--border-color); }
    .cal-leg-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--muted); }
    .cal-leg-dot { width: 7px; height: 7px; border-radius: 50%; }

    /* â”€â”€ Detail Hari Ini â”€â”€ */
    .detail-card-body { padding: 16px 18px; display: flex; flex-direction: column; gap: 10px; }
    .detail-row { background: var(--bg-color); border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; }
    .detail-label { font-size: 11px; color: var(--muted); margin-bottom: 4px; }
    .detail-time { font-size: 22px; font-weight: 700; font-family: 'DM Mono', monospace; line-height: 1; }
    .detail-task-row { background: var(--bg-color); border-radius: 10px; padding: 12px 14px; display: flex; flex-direction: column; gap: 7px; }
    .detail-task-desc { font-size: 12.5px; color: var(--text-color); line-height: 1.6; }

    /* â”€â”€ Empty â”€â”€ */
    .empty-state { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 28px 16px; text-align: center; }
    .empty-ico { width: 44px; height: 44px; border-radius: 12px; background: var(--bg-color); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; }
    .empty-ico svg { width: 20px; height: 20px; color: var(--muted); }
    .empty-title { font-size: 13px; font-weight: 500; color: var(--text-color); }
    .empty-sub   { font-size: 12px; color: var(--muted); }

    @media (max-width: 991px) { .stat-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 860px) { .mid-grid, .bot-grid { grid-template-columns: 1fr; } }
    @media (max-width: 600px) { .stat-grid { grid-template-columns: repeat(2,1fr); gap: 8px; } main { padding: 16px !important; } }
</style>

<div class="dash-wrap">

    
    <div class="dash-header">
        <div>
            <h1>Halo, <?php echo e(auth()->user()->nama ?? 'Petugas'); ?></h1>
            <div class="dash-header-sub">
                <?php echo e(now()->translatedFormat('l, d F Y')); ?>

                <?php if(auth()->user()->tempat_tugas): ?>
                    - <?php echo e(auth()->user()->tempat_tugas->nama_tempat ?? ''); ?>

                <?php endif; ?>
            </div>
        </div>
        <div class="dash-date-pill">
            <svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            <?php echo e(now()->translatedFormat('F Y')); ?>

        </div>
    </div>

    
    <?php
        $totalHadir = $rekapBulan['hadir'] ?? 0;
        $totalTelat = $rekapBulan['telat'] ?? 0;
        $totalAbsen = $rekapBulan['tidak_hadir'] ?? 0;
        $totalHariKerja = $rekapBulan['hari_kerja'] ?? 1;
        $totalKehadiran = $totalHadir + $totalTelat;
    ?>
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico bg-accent"><svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="var(--primary)" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="var(--primary)" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                <span class="stat-badge" style="background:var(--primary-soft);color:var(--primary2)">Cuti</span>
            </div>
            <div class="stat-lbl">Sisa cuti tahunan</div>
            <div class="stat-val" style="color:var(--primary)"><?php echo e($sisaCutiTahunIni ?? 12); ?></div>
            <div class="stat-hint" style="color:var(--primary2)">hari tersisa</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico bg-amber"><svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="#F59E0B" stroke-width="1.3"/><path d="M8 5v3l2 2" stroke="#F59E0B" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                <span class="stat-badge" style="background:var(--amber-soft);color:var(--amber-dark)">Perhatian</span>
            </div>
            <div class="stat-lbl">Jumlah telat</div>
            <div class="stat-val" style="color:var(--amber)"><?php echo e($totalTelat); ?></div>
            <div class="stat-hint" style="color:var(--amber-dark)">kali bulan ini</div>
            <?php if($totalHariKerja > 0): ?>
            <div class="stat-bar"><div class="stat-bar-fill" style="width:<?php echo e(min(($totalTelat/$totalHariKerja)*100,100)); ?>%;background:var(--amber)"></div></div>
            <?php endif; ?>
        </div>
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico bg-green"><svg fill="none" viewBox="0 0 16 16"><path d="M3 8l3.5 3.5L13 5" stroke="#10B981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                <span class="stat-badge" style="background:var(--green-soft);color:var(--green-dark)"><?php echo e($totalAbsen == 0 ? 'Bagus' : 'Perhatian'); ?></span>
            </div>
            <div class="stat-lbl">Tidak absen pagi/sore</div>
            <div class="stat-val" style="color:<?php echo e($totalAbsen == 0 ? 'var(--green)' : 'var(--red)'); ?>"><?php echo e($totalAbsen); ?></div>
            <div class="stat-hint" style="color:var(--green-dark)">bulan ini</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-ico" style="background:rgba(99,102,241,.1)"><svg fill="none" viewBox="0 0 16 16"><path d="M2 4h12M2 8h9M2 12h6" stroke="#6366F1" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                <span class="stat-badge" style="background:rgba(99,102,241,.1);color:#4338CA"><?php echo e($tugasDisetujui ?? 0); ?>/<?php echo e($totalTugas ?? 0); ?></span>
            </div>
            <div class="stat-lbl">Tugas disetujui</div>
            <div class="stat-val" style="color:#6366F1"><?php echo e($tugasDisetujui ?? 0); ?></div>
            <div class="stat-hint" style="color:#4338CA">dari <?php echo e($totalTugas ?? 0); ?> tugas</div>
            <?php if(($totalTugas ?? 0) > 0): ?>
            <div class="stat-bar"><div class="stat-bar-fill" style="width:<?php echo e((($tugasDisetujui ?? 0)/($totalTugas ?? 1))*100); ?>%;background:#6366F1"></div></div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="mid-grid">
        
        <div class="card-box">
            <div class="card-head">
                <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 2v6l4.2 4.2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Absensi <?php echo e(now()->translatedFormat('F')); ?></span>
                <a href="<?php echo e(route('petugas.absensi.index')); ?>" class="card-link">Lihat detail</a>
            </div>
            <div class="card-body">
                <div class="donut-section">
                    <?php
                        $totalAll = max($totalHadir + $totalTelat + $totalAbsen, 1);
                        $pctHadir = ($totalHadir / $totalAll) * 188;
                        $pctTelat = ($totalTelat / $totalAll) * 188;
                        $pctAbsen = ($totalAbsen / $totalAll) * 188;
                    ?>
                    <div class="donut-wrap">
                        <svg width="100" height="100" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="30" fill="none" stroke="var(--border-color)" stroke-width="12"/>
                            <circle cx="50" cy="50" r="30" fill="none" stroke="var(--green)" stroke-width="12" stroke-dasharray="<?php echo e($pctHadir); ?> <?php echo e(188 - $pctHadir); ?>" stroke-dashoffset="25" stroke-linecap="round"/>
                            <?php if($totalTelat > 0): ?>
                            <circle cx="50" cy="50" r="30" fill="none" stroke="var(--amber)" stroke-width="12" stroke-dasharray="<?php echo e($pctTelat); ?> <?php echo e(188 - $pctTelat); ?>" stroke-dashoffset="<?php echo e(25 - $pctHadir); ?>" stroke-linecap="round"/>
                            <?php endif; ?>
                            <?php if($totalAbsen > 0): ?>
                            <circle cx="50" cy="50" r="30" fill="none" stroke="var(--red)" stroke-width="12" stroke-dasharray="<?php echo e($pctAbsen); ?> <?php echo e(188 - $pctAbsen); ?>" stroke-dashoffset="<?php echo e(25 - $pctHadir - $pctTelat); ?>" stroke-linecap="round"/>
                            <?php endif; ?>
                        </svg>
                        <div class="donut-label"><div class="donut-num"><?php echo e($totalKehadiran); ?></div><div class="donut-sub">hari</div></div>
                    </div>
                    <div class="legend-list">
                        <div class="legend-item"><div class="legend-dot" style="background:var(--green)"></div>Tepat waktu (<?php echo e($totalHadir); ?>)</div>
                        <div class="legend-item"><div class="legend-dot" style="background:var(--amber)"></div>Telat (<?php echo e($totalTelat); ?>)</div>
                        <div class="legend-item"><div class="legend-dot" style="background:var(--red)"></div>Tidak hadir (<?php echo e($totalAbsen); ?>)</div>
                        <div class="legend-item"><div class="legend-dot" style="background:var(--border-color)"></div>Libur / Weekend</div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card-box">
            <div class="card-head">
                <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Aktivitas terbaru</span>
                <a href="<?php echo e(route('petugas.absensi.index')); ?>" class="card-link">Semua</a>
            </div>
            <div class="card-body">
                <div class="feed-list">
                    <?php $__empty_1 = true; $__currentLoopData = ($aktivitasTerbaru ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="feed-item">
                            <div class="feed-ava"><?php echo e(strtoupper(substr(auth()->user()->nama ?? 'U', 0, 2))); ?></div>
                            <div class="feed-info">
                                <div class="feed-name"><?php echo e($act->aktivitas ?? ucfirst($act->modul ?? 'Aktivitas')); ?></div>
                                <div class="feed-desc">
                                    <?php echo e($act->created_at?->format('H:i') ?? '--:--'); ?>

                                    -
                                    <?php echo e($act->created_at?->translatedFormat('l, d M') ?? '-'); ?>

                                </div>
                            </div>
                            <?php
    $actStatus = $act->status ?? $act->modul ?? '-';
    $fb = match($actStatus) {
        'hadir' => 'fb-ok',
        'terlambat', 'telat' => 'fb-late',
        default => 'fb-info',
    };
?>
<span class="feed-badge <?php echo e($fb); ?>"><?php echo e(ucfirst($actStatus)); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="empty-state" style="padding:24px 12px;">
                            <div class="empty-ico"><svg fill="none" viewBox="0 0 16 16"><path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                            <div class="empty-title">Belum ada aktivitas</div>
                            <div class="empty-sub">Aktivitas kehadiran akan muncul di sini</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bot-grid">
        
        <div class="card-box">
            <div class="cal-nav">
                <button class="cal-arrow" id="prev-btn">&#8249;</button>
                <div class="cal-month" id="cal-title"><?php echo e(now()->translatedFormat('F Y')); ?></div>
                <button class="cal-arrow" id="next-btn">&#8250;</button>
            </div>
            <div class="cal-body">
                <div class="cal-day-labels">
                    <div class="cal-day-lbl">Min</div><div class="cal-day-lbl">Sen</div><div class="cal-day-lbl">Sel</div>
                    <div class="cal-day-lbl">Rab</div><div class="cal-day-lbl">Kam</div><div class="cal-day-lbl">Jum</div>
                    <div class="cal-day-lbl">Sab</div>
                </div>
                <div class="cal-grid" id="cal-grid"></div>
            </div>
            <div class="cal-legend">
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--primary)"></div>Hari ini</div>
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--green)"></div>Hadir</div>
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--amber)"></div>Telat</div>
                <div class="cal-leg-item"><div class="cal-leg-dot" style="background:var(--red)"></div>Tidak Absen</div>
            </div>
        </div>

        
        <div class="card-box">
            <div class="card-head">
                <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3l2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Detail hari ini</span>
                <span class="dash-date-pill" style="font-size:11px;padding:3px 10px;"><?php echo e(now()->translatedFormat('l, d M')); ?></span>
            </div>
            <div class="detail-card-body">
                <div class="detail-row">
                    <div>
                        <div class="detail-label">Jam masuk</div>
                        <div class="detail-time" style="color:var(--green)"><?php echo e($absensiHariIni?->jam_masuk ?? '--:--'); ?></div>
                    </div>
                    <?php if($absensiHariIni?->jam_masuk): ?>
                        <span class="feed-badge <?php echo e(in_array($absensiHariIni->status, ['telat','terlambat']) ? 'fb-late' : 'fb-ok'); ?>">
                            <?php echo e(in_array($absensiHariIni->status, ['telat','terlambat']) ? 'Telat' : 'Tepat'); ?>

                        </span>
                    <?php else: ?>
                        <span class="feed-badge" style="background:var(--bg-color);color:var(--muted);border:1px solid var(--border2)">Belum</span>
                    <?php endif; ?>
                </div>
                <div class="detail-row">
                    <div>
                        <div class="detail-label">Jam pulang</div>
                        <div class="detail-time" style="color:var(--primary)"><?php echo e($absensiHariIni?->jam_pulang ?? '--:--'); ?></div>
                    </div>
                    <?php if($absensiHariIni?->jam_pulang): ?>
                        <span class="feed-badge fb-ok">Tepat</span>
                    <?php else: ?>
                        <span class="feed-badge" style="background:var(--bg-color);color:var(--muted);border:1px solid var(--border2)">Belum</span>
                    <?php endif; ?>
                </div>
                <div class="detail-task-row">
                    <div class="detail-label">Tugas hari ini</div>
                    <?php if(isset($tugasHariIni) && $tugasHariIni): ?>
                        <div class="detail-task-desc"><?php echo e($tugasHariIni->uraian ?? 'Tidak ada tugas'); ?></div>
                        <span class="feed-badge fb-info" style="width:fit-content"><?php echo e(ucfirst($tugasHariIni->status ?? 'pending')); ?></span>
                    <?php else: ?>
                        <div class="detail-task-desc" style="color:var(--muted)">Belum ada tugas diinput hari ini</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card-box">
        <div class="card-head">
            <span class="card-title"><svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>Kalender Terdekat</span>
            <span style="font-size:11px;color:var(--muted)"><?php echo e($kalender->count()); ?> event</span>
        </div>
        <div class="card-body">
            <?php $__empty_1 = true; $__currentLoopData = $kalender; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="feed-item">
                    <div class="feed-ava" style="background:var(--primary-soft);color:var(--primary);"><svg fill="none" viewBox="0 0 16 16" width="14" height="14"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                    <div class="feed-info">
                        <div class="feed-name"><?php echo e($item->nama_event); ?></div>
                        <div class="feed-desc"><?php echo e($item->tanggal->format('d/m/Y')); ?></div>
                    </div>
                    <span class="feed-badge fb-info"><?php echo e($item->jenis_event); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-state" style="padding:24px 12px;">
                    <div class="empty-ico"><svg fill="none" viewBox="0 0 20 20"><rect x="2" y="3" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M6 1v3M14 1v3M2 8h16" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg></div>
                    <div class="empty-title">Belum ada event kalender</div>
                    <div class="empty-sub">Event dan jadwal akan muncul di sini</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function() {
    var curDate = new Date(), today = new Date();
    var months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    var hadirDays = <?php echo json_encode($kalenderHadir ?? []); ?>;
    var telatDays = <?php echo json_encode($kalenderTelat ?? []); ?>;
    var absenDays = <?php echo json_encode($kalenderAbsen ?? []); ?>;

    function buildCal() {
        var g = document.getElementById('cal-grid'), t = document.getElementById('cal-title');
        if (!g || !t) return;
        t.textContent = months[curDate.getMonth()] + ' ' + curDate.getFullYear();
        g.innerHTML = '';
        var first = new Date(curDate.getFullYear(), curDate.getMonth(), 1).getDay();
        var daysInM = new Date(curDate.getFullYear(), curDate.getMonth()+1, 0).getDate();
        var prevDays = new Date(curDate.getFullYear(), curDate.getMonth(), 0).getDate();
        var isCurM = curDate.getFullYear()===today.getFullYear() && curDate.getMonth()===today.getMonth();
        for (var i=0;i<first;i++){var d=document.createElement('div');d.className='cal-cell other';d.textContent=prevDays-first+1+i;g.appendChild(d);}
        for (var n=1;n<=daysInM;n++){
            var c=document.createElement('div');c.className='cal-cell';c.textContent=n;
            if(isCurM&&n===today.getDate())c.classList.add('today');
            if(isCurM){var dot=null;
                if(hadirDays.indexOf(n)!==-1){dot=document.createElement('div');dot.className='cal-dot';dot.style.background='var(--green)';}
                else if(telatDays.indexOf(n)!==-1){dot=document.createElement('div');dot.className='cal-dot';dot.style.background='var(--amber)';}
                else if(absenDays.indexOf(n)!==-1){dot=document.createElement('div');dot.className='cal-dot';dot.style.background='var(--red)';}
                if(dot)c.appendChild(dot);
            }
            g.appendChild(c);
        }
        var remain=7-((first+daysInM)%7);if(remain<7)for(var j=1;j<=remain;j++){var d2=document.createElement('div');d2.className='cal-cell other';d2.textContent=j;g.appendChild(d2);}
    }
    buildCal();
    var pb=document.getElementById('prev-btn'),nb=document.getElementById('next-btn');
    if(pb)pb.addEventListener('click',function(){curDate=new Date(curDate.getFullYear(),curDate.getMonth()-1,1);buildCal();});
    if(nb)nb.addEventListener('click',function(){curDate=new Date(curDate.getFullYear(),curDate.getMonth()+1,1);buildCal();});
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/petugas/dashboard.blade.php ENDPATH**/ ?>