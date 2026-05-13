<?php
    $app_theme = \App\Models\Pengaturan::getNilai('app_theme', 'light');
    $app_logo = \App\Models\Pengaturan::getNilai('app_logo');
?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-theme="<?php echo e($app_theme); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', config('app.name')); ?></title>
    <style>
        :root {
            --bg-color: #f6f7fb;
            --text-color: #1f2937;
            --sidebar-bg: #111827;
            --sidebar-text: #f9fafb;
            --panel-bg: #fff;
            --border-color: #e5e7eb;
            --primary: #2563eb;
            --muted: #6b7280;
            --input-bg: #fff;
        }
        [data-theme="dark"] {
            --bg-color: #0f172a;
            --text-color: #f8fafc;
            --sidebar-bg: #020617;
            --sidebar-text: #f8fafc;
            --panel-bg: #1e293b;
            --border-color: #334155;
            --primary: #3b82f6;
            --muted: #94a3b8;
            --input-bg: #0f172a;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: var(--bg-color); color: var(--text-color); transition: background-color 0.3s, color 0.3s; }
        .app-shell { min-height: 100vh; display: grid; grid-template-columns: 260px minmax(0, 1fr); }
        .sidebar { background: var(--sidebar-bg); color: var(--sidebar-text); padding: 18px; position: sticky; top: 0; min-height: 100vh; align-self: start; transition: background-color 0.3s; }
        .brand { padding-bottom: 18px; border-bottom: 1px solid rgba(255, 255, 255, 0.12); margin-bottom: 16px; text-align: center; }
        .brand strong { display: block; font-size: 18px; margin-bottom: 6px; }
        .sidebar .muted { color: #cbd5e1; }
        .content-shell { min-width: 0; }
        header { background: var(--panel-bg); border-bottom: 1px solid var(--border-color); padding: 14px 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; transition: background-color 0.3s, border-color 0.3s; }
        .top-actions { display: flex; align-items: center; gap: 10px; }
        .top-actions a { display: inline-block; color: #374151; background: #f3f4f6; padding: 9px 12px; border-radius: 6px; font-size: 14px; }
        .top-actions a:hover { background: #e5e7eb; color: #111827; }
        .notification-wrap { position: relative; }
        .notification-button { position: relative; width: 40px; height: 40px; display: grid; place-items: center; background: var(--bg-color); color: var(--text-color); border-radius: 999px; }
        .notification-button:hover { background: var(--border-color); }
        .notification-button svg { width: 20px; height: 20px; }
        .notification-badge { position: absolute; top: -4px; right: -4px; min-width: 18px; height: 18px; padding: 0 5px; display: grid; place-items: center; background: #dc2626; color: #fff; border-radius: 999px; font-size: 11px; font-weight: 700; line-height: 1; }
        .notification-panel { display: none; position: absolute; top: calc(100% + 10px); right: 0; width: min(360px, calc(100vw - 32px)); background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 18px 50px rgba(15, 23, 42, 0.14); z-index: 40; overflow: hidden; }
        .notification-panel.open { display: block; }
        .notification-head { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .notification-list { max-height: 360px; overflow-y: auto; }
        .notification-item { padding: 12px 14px; border-bottom: 1px solid #f3f4f6; }
        .notification-item:last-child { border-bottom: 0; }
        .notification-title { font-weight: 700; margin-bottom: 4px; }
        .notification-message { color: #4b5563; font-size: 13px; line-height: 1.4; margin-bottom: 8px; }
        .notification-empty { padding: 18px 14px; color: #6b7280; }
        .notification-read-button { background: #eef2ff; color: #3730a3; padding: 7px 9px; font-size: 12px; }
        nav { display: grid; gap: 8px; }
        a { color: #2563eb; text-decoration: none; font-weight: 700; }
        nav a { color: #e5e7eb; background: rgba(255, 255, 255, 0.08); padding: 10px 12px; border-radius: 6px; font-size: 14px; display: block; }
        nav a:hover { background: rgba(255, 255, 255, 0.14); color: #fff; }
        .nav-section { margin: 14px 0 8px; color: #93c5fd; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .dropdown { display: grid; gap: 6px; }
        .dropdown > button { width: 100%; text-align: left; color: #e5e7eb; background: rgba(255, 255, 255, 0.08); padding: 10px 12px; border-radius: 6px; font-size: 14px; }
        .dropdown-menu { display: grid; gap: 6px; padding-left: 12px; }
        .dropdown-menu a { background: rgba(255, 255, 255, 0.05); font-size: 13px; }
        main { max-width: 1180px; margin: 28px auto; padding: 0 20px; }
        h1 { margin: 0 0 18px; font-size: 26px; }
        h2 { margin: 0 0 14px; font-size: 18px; }
        .grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .panel, .stat { background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 8px; padding: 18px; margin-bottom: 18px; transition: background-color 0.3s, border-color 0.3s; }
        .stat strong { display: block; font-size: 26px; margin-top: 8px; }
        .muted { color: var(--muted); }
        .success, .error { padding: 12px 14px; border-radius: 6px; margin-bottom: 16px; }
        .success { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        table { width: 100%; border-collapse: collapse; background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; transition: background-color 0.3s, border-color 0.3s; }
        th, td { padding: 10px; border-bottom: 1px solid var(--border-color); text-align: left; vertical-align: top; font-size: 14px; color: var(--text-color); }
        th { background: var(--bg-color); color: var(--text-color); }
        tr:last-child td { border-bottom: 0; }
        input, select, textarea { width: 100%; border: 1px solid var(--border-color); border-radius: 6px; padding: 9px 10px; font-size: 14px; background: var(--input-bg); color: var(--text-color); }
        textarea { min-height: 74px; resize: vertical; }
        label { display: block; font-weight: 700; font-size: 13px; margin-bottom: 5px; color: var(--text-color); }
        .form-grid { display: grid; gap: 12px; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); align-items: end; }
        button { border: 0; border-radius: 6px; background: #2563eb; color: #fff; padding: 9px 12px; font-weight: 700; cursor: pointer; }
        button.danger { background: #dc2626; }
        button.dark { background: #1f2937; }
        .actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 999px; background: #eef2ff; color: #3730a3; font-size: 12px; font-weight: 700; }
        .badge.pending { background: #fff7ed; color: #c2410c; }
        .badge.approve, .badge.hadir, .badge.kegiatan { background: #ecfdf5; color: #047857; }
        .badge.reject, .badge.telat { background: #fef2f2; color: #b91c1c; }
        .badge.libur, .badge.cuti_bersama { background: #fef2f2; color: #b91c1c; }
        .pagination { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 16px; background: var(--panel-bg); border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; }
        .pagination .pager-links { display: flex; gap: 8px; }
        .pagination a, .pagination span.disabled { padding: 8px 10px; border-radius: 6px; background: var(--bg-color); color: var(--text-color); font-size: 14px; }
        .pagination span.disabled { color: var(--muted); }
        .filter-bar { display: flex; align-items: end; gap: 12px; flex-wrap: wrap; }
        .filter-bar .filter-control { min-width: 260px; }
        .filter-bar button, .filter-bar a { display: inline-block; padding: 9px 12px; border-radius: 6px; }
        .filter-bar a { background: var(--bg-color); color: var(--text-color); }
        .calendar-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; background: var(--panel-bg); }
        .calendar-day-name { background: var(--bg-color); padding: 10px; font-weight: 700; color: var(--muted); border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); text-align: center; }
        .calendar-cell { min-height: 118px; padding: 8px; border-right: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); background: var(--panel-bg); }
        .calendar-cell:nth-child(7n), .calendar-day-name:nth-child(7n) { border-right: 0; }
        .calendar-cell.muted-day { background: var(--bg-color); color: var(--muted); }
        .calendar-date { font-weight: 700; margin-bottom: 6px; }
        .calendar-event { display: block; margin: 5px 0; padding: 6px; border-radius: 6px; background: #f3f4f6; font-size: 12px; line-height: 1.25; }
        .calendar-event.libur, .calendar-event.cuti_bersama { background: #fee2e2; color: #991b1b; }
        .calendar-event.kegiatan { background: #dcfce7; color: #166534; }
        .inline { display: inline; }
        .hamburger { display: none; background: none; border: none; cursor: pointer; padding: 4px; flex-direction: column; gap: 5px; }
        .hamburger span { display: block; width: 24px; height: 3px; background: #1f2937; border-radius: 3px; transition: all 0.3s ease-in-out; }
        .hamburger.active span:nth-child(1) { transform: translateY(8px) rotate(45deg); }
        .hamburger.active span:nth-child(2) { opacity: 0; }
        .hamburger.active span:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 40; opacity: 0; transition: opacity 0.3s ease; }
        .sidebar-overlay.show { display: block; opacity: 1; }
        @media (max-width: 760px) {
            .app-shell { display: block; }
            .sidebar { position: fixed; left: -280px; top: 0; bottom: 0; width: 260px; z-index: 50; transition: left 0.3s ease; overflow-y: auto; }
            .sidebar.open { left: 0; }
            .hamburger { display: flex; }
            header { align-items: center; flex-direction: row; flex-wrap: wrap; }
            .header-left { display: flex; align-items: center; gap: 12px; }
            .top-actions { width: 100%; justify-content: flex-end; margin-top: 12px; border-top: 1px solid #e5e7eb; padding-top: 12px; }
            table { display: block; overflow-x: auto; }
            .calendar-grid { grid-template-columns: 1fr; }
            .calendar-day-name { display: none; }
            .calendar-cell { min-height: auto; border-right: 0; }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <?php if(auth()->guard()->check()): ?>
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <?php if($app_logo): ?>
                    <img src="<?php echo e(Storage::url($app_logo)); ?>" alt="Logo" style="max-height: 50px; max-width: 100%;">
                <?php else: ?>
                    <strong>Absensi PPSU</strong>
                <?php endif; ?>
                <!-- <div class="muted"><?php echo e(auth()->user()->nama); ?> - <?php echo e(auth()->user()->role->nama_role ?? ''); ?></div> -->
            </div>
            <nav>
                <div class="nav-section">Utama</div>
                <a href="<?php echo e(route('dashboard')); ?>">Dashboard</a>
                <?php if(auth()->user()->isPetugas()): ?>
                    <div class="nav-section">Petugas</div>
                    <a href="<?php echo e(route('petugas.absensi.index')); ?>">Absensi</a>
                    <a href="<?php echo e(route('petugas.cuti.index')); ?>">Cuti</a>
                    <div class="dropdown">
                        <button type="button">Tugas</button>
                        <div class="dropdown-menu">
                            <a href="<?php echo e(route('petugas.tugas.input')); ?>">Input Tugas Harian</a>
                            <a href="<?php echo e(route('petugas.tugas.laporan')); ?>">Lap. Tugas Harian</a>
                            <a href="<?php echo e(route('petugas.tugas.kalender')); ?>">Kalender</a>
                        </div>
                    </div>
                    <a href="<?php echo e(route('petugas.sanksi.index')); ?>">Sanksi</a>
                <?php endif; ?>
                <?php if(auth()->user()->isAtasan()): ?>
                    <div class="nav-section">Atasan</div>
                    <a href="<?php echo e(route('atasan.absensi.index')); ?>">Pantau Absensi</a>
                    <a href="<?php echo e(route('atasan.cuti.index')); ?>">Approve Cuti</a>
                    <a href="<?php echo e(route('atasan.tugas.index')); ?>">Approve Tugas</a>
                    <a href="<?php echo e(route('atasan.kalender.index')); ?>">Kalender</a>
                    <a href="<?php echo e(route('atasan.sanksi.index')); ?>">Sanksi</a>
                <?php endif; ?>
                <?php if(auth()->user()->isAdmin()): ?>
                    <div class="nav-section">Admin</div>
                    <?php if(auth()->user()->role->nama_role === 'Admin Absensi'): ?>
                    <a href="<?php echo e(route('admin.pengaturan.index')); ?>">Pengaturan</a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('admin.users.index')); ?>">Users</a>
                    <a href="<?php echo e(route('admin.tempat.index')); ?>">Tempat</a>
                    <a href="<?php echo e(route('admin.periode.index')); ?>">Periode</a>
                    <a href="<?php echo e(route('admin.kalender.index')); ?>">Kalender</a>
                    <a href="<?php echo e(route('admin.buka-absen.index')); ?>">Akses Telat</a>
                    <a href="<?php echo e(route('admin.sanksi.index')); ?>">Sanksi</a>
                    <a href="<?php echo e(route('admin.data-sensitif.index')); ?>">Data Sensitif</a>
                    <a href="<?php echo e(route('admin.logs.index')); ?>">Log</a>
                <?php endif; ?>
            </nav>
        </aside>
        <?php endif; ?>
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <div class="content-shell">
            <header>
                <div class="header-left">
                    <?php if(auth()->guard()->check()): ?>
                    <button class="hamburger" id="hamburgerMenu" aria-label="Toggle Sidebar">
                        <span></span><span></span><span></span>
                    </button>
                    <?php endif; ?>
                    <div>
                        <strong><?php echo $__env->yieldContent('title', config('app.name')); ?></strong>
                        <?php if(auth()->guard()->check()): ?>
                            <div class="muted" style="font-size: 13px;"><?php echo e(auth()->user()->role->nama_role ?? ''); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if(auth()->guard()->check()): ?>
                    <div class="top-actions">
                        
                        <form method="POST" action="<?php echo e(route('set.periode')); ?>" style="display:flex; align-items:center; gap:8px; margin-right:12px; border-right:1px solid #e5e7eb; padding-right:16px;">
                            <?php echo csrf_field(); ?>
                            <label for="global_periode" style="margin:0; font-size:13px; color:#6b7280; font-weight:normal;">Periode:</label>
                            <select name="global_periode_id" id="global_periode" onchange="this.form.submit()" style="padding:4px 28px 4px 8px; font-size:13px; height:auto; width:auto; border-radius:4px;">
                                <?php $__currentLoopData = $globalPeriodes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($p->id_periode); ?>" <?php echo e((isset($globalSelectedPeriode) && $globalSelectedPeriode->id_periode == $p->id_periode) ? 'selected' : ''); ?>>
                                        <?php echo e(\Carbon\Carbon::parse($p->tanggal_mulai)->format('Y')); ?>

                                        <?php echo e($p->status === 'aktif' ? '(Aktif)' : ''); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </form>

                        <?php
                            $unreadNotifications = \App\Models\Notifikasi::where('id_user', auth()->id())->where('status_baca', false)->count();
                            $headerNotifications = \App\Models\Notifikasi::where('id_user', auth()->id())->latest('id_notifikasi')->limit(5)->get();
                        ?>
                        <div class="notification-wrap" id="notificationWrap">
                            <button type="button" class="notification-button" id="notificationToggle" aria-label="Buka notifikasi" aria-expanded="false">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                <?php if($unreadNotifications > 0): ?>
                                    <span class="notification-badge" id="notificationBadge"><?php echo e($unreadNotifications > 99 ? '99+' : $unreadNotifications); ?></span>
                                <?php endif; ?>
                            </button>
                            <div class="notification-panel" id="notificationPanel">
                                <div class="notification-head">
                                    <strong>Notifikasi</strong>
                                    <span class="muted" id="notificationUnreadText"><?php echo e($unreadNotifications); ?> belum dibaca</span>
                                </div>
                                <div class="notification-list">
                                    <?php $__empty_1 = true; $__currentLoopData = $headerNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div class="notification-item" data-notification-id="<?php echo e($notification->id_notifikasi); ?>">
                                            <div class="notification-title"><?php echo e($notification->judul ?? 'Notifikasi'); ?></div>
                                            <div class="notification-message"><?php echo e($notification->pesan ?? '-'); ?></div>
                                            <div class="actions">
                                                <span class="badge <?php echo e($notification->status_baca ? 'approve' : 'pending'); ?>" data-status-badge>
                                                    <?php echo e($notification->status_baca ? 'Dibaca' : 'Baru'); ?>

                                                </span>
                                                <?php if(! $notification->status_baca): ?>
                                                    <form method="POST" action="<?php echo e(route('notifikasi.read', $notification->id_notifikasi)); ?>" data-notification-read-form>
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="notification-read-button">Tandai dibaca</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <div class="notification-empty">Belum ada notifikasi.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="notification-wrap" id="profileWrap" style="margin-left: 12px; padding-left: 16px; border-left: 1px solid #e5e7eb;">
                            <button type="button" style="border:none; background:none; cursor:pointer; padding:0; display:flex;" id="profileToggle" aria-expanded="false" title="<?php echo e(auth()->user()->nama ?? 'Profil'); ?>">
                                <?php if(auth()->user()->foto_profil): ?>
                                    <img src="<?php echo e(Storage::url(auth()->user()->foto_profil)); ?>" alt="Foto" style="width:38px; height:38px; border-radius:50%; object-fit:cover;">
                                <?php else: ?>
                                    <div style="width:38px; height:38px; border-radius:50%; background:var(--primary); color:#fff; display:grid; place-items:center; font-weight:bold; font-size:16px;">
                                        <?php echo e(strtoupper(substr(auth()->user()->nama ?? 'U', 0, 1))); ?>

                                    </div>
                                <?php endif; ?>
                            </button>
                            <div class="notification-panel" id="profilePanel" style="width: 200px; padding: 8px; background: var(--panel-bg); border-color: var(--border-color);">
                                <a href="<?php echo e(route('profile.index')); ?>" style="display: block; padding: 10px 12px; color: var(--text-color); text-decoration: none; border-radius: 6px; font-weight: normal; margin-bottom: 4px;" onmouseover="this.style.background='var(--bg-color)'" onmouseout="this.style.background='transparent'">Lihat Profil</a>
                                <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin: 0;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" style="width: 100%; text-align: left; background: #fef2f2; color: #b91c1c; padding: 10px 12px; border-radius: 6px; font-weight: bold;">Logout</button>
                                </form>
                            </div>
                        </div>

                    </div>
                <?php endif; ?>
            </header>
            <main>
                <?php if(session('success')): ?>
                    <div class="success"><?php echo e(session('success')); ?></div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="error"><?php echo e(session('error')); ?></div>
                <?php endif; ?>
                <?php if($errors->any()): ?>
                    <div class="error"><?php echo e($errors->first()); ?></div>
                <?php endif; ?>
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>
    <script>
        var hamburgerMenu = document.getElementById('hamburgerMenu');
        var sidebar = document.querySelector('.sidebar');
        var sidebarOverlay = document.getElementById('sidebarOverlay');

        if (hamburgerMenu && sidebar && sidebarOverlay) {
            function toggleSidebar() {
                hamburgerMenu.classList.toggle('active');
                sidebar.classList.toggle('open');
                if (sidebar.classList.contains('open')) {
                    sidebarOverlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebarOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            }

            hamburgerMenu.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', toggleSidebar);
        }

        var notificationToggle = document.getElementById('notificationToggle');
        var notificationPanel = document.getElementById('notificationPanel');
        var notificationWrap = document.getElementById('notificationWrap');
        var notificationBadge = document.getElementById('notificationBadge');
        var notificationUnreadText = document.getElementById('notificationUnreadText');

        var profileToggle = document.getElementById('profileToggle');
        var profilePanel = document.getElementById('profilePanel');
        var profileWrap = document.getElementById('profileWrap');

        if (profileToggle && profilePanel && profileWrap) {
            profileToggle.addEventListener('click', function () {
                var isOpen = profilePanel.classList.toggle('open');
                profileToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                if (notificationPanel && notificationPanel.classList.contains('open')) {
                    notificationPanel.classList.remove('open');
                    notificationToggle.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('click', function (event) {
                if (!profileWrap.contains(event.target)) {
                    profilePanel.classList.remove('open');
                    profileToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }

        if (notificationToggle && notificationPanel && notificationWrap) {
            notificationToggle.addEventListener('click', function () {
                var isOpen = notificationPanel.classList.toggle('open');
                notificationToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                if (profilePanel && profilePanel.classList.contains('open')) {
                    profilePanel.classList.remove('open');
                    profileToggle.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('click', function (event) {
                if (!notificationWrap.contains(event.target)) {
                    notificationPanel.classList.remove('open');
                    notificationToggle.setAttribute('aria-expanded', 'false');
                }
            });

            notificationPanel.querySelectorAll('[data-notification-read-form]').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    var button = form.querySelector('button');
                    var item = form.closest('[data-notification-id]');
                    var statusBadge = item ? item.querySelector('[data-status-badge]') : null;

                    if (button) {
                        button.disabled = true;
                        button.textContent = 'Menyimpan...';
                    }

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('Gagal menandai notifikasi.');
                            }

                            return response.json();
                        })
                        .then(function (data) {
                            if (statusBadge) {
                                statusBadge.classList.remove('pending');
                                statusBadge.classList.add('approve');
                                statusBadge.textContent = 'Dibaca';
                            }

                            form.remove();

                            if (notificationUnreadText) {
                                notificationUnreadText.textContent = data.unread_count + ' belum dibaca';
                            }

                            if (notificationBadge) {
                                if (data.unread_count > 0) {
                                    notificationBadge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                                } else {
                                    notificationBadge.remove();
                                    notificationBadge = null;
                                }
                            }
                        })
                        .catch(function () {
                            if (button) {
                                button.disabled = false;
                                button.textContent = 'Tandai dibaca';
                            }
                            alert('Notifikasi belum bisa ditandai. Coba lagi.');
                        });
                });
            });
        }
    </script>
</body>
</html>
<?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/layouts/app.blade.php ENDPATH**/ ?>