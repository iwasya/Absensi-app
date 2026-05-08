<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f7fb; color: #1f2937; }
        .app-shell { min-height: 100vh; display: grid; grid-template-columns: 260px minmax(0, 1fr); }
        .sidebar { background: #111827; color: #f9fafb; padding: 18px; position: sticky; top: 0; min-height: 100vh; align-self: start; }
        .brand { padding-bottom: 18px; border-bottom: 1px solid rgba(255, 255, 255, 0.12); margin-bottom: 16px; }
        .brand strong { display: block; font-size: 18px; margin-bottom: 6px; }
        .sidebar .muted { color: #cbd5e1; }
        .content-shell { min-width: 0; }
        header { background: #fff; border-bottom: 1px solid #e5e7eb; padding: 14px 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .top-actions { display: flex; align-items: center; gap: 10px; }
        .top-actions a { display: inline-block; color: #374151; background: #f3f4f6; padding: 9px 12px; border-radius: 6px; font-size: 14px; }
        .top-actions a:hover { background: #e5e7eb; color: #111827; }
        .notification-wrap { position: relative; }
        .notification-button { position: relative; width: 40px; height: 40px; display: grid; place-items: center; background: #f3f4f6; color: #374151; border-radius: 999px; }
        .notification-button:hover { background: #e5e7eb; color: #111827; }
        .notification-button svg { width: 20px; height: 20px; }
        .notification-badge { position: absolute; top: -4px; right: -4px; min-width: 18px; height: 18px; padding: 0 5px; display: grid; place-items: center; background: #dc2626; color: #fff; border-radius: 999px; font-size: 11px; font-weight: 700; line-height: 1; }
        .notification-panel { display: none; position: absolute; top: calc(100% + 10px); right: 0; width: min(360px, calc(100vw - 32px)); background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 18px 50px rgba(15, 23, 42, 0.14); z-index: 40; overflow: hidden; }
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
        .panel, .stat { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px; margin-bottom: 18px; }
        .stat strong { display: block; font-size: 26px; margin-top: 8px; }
        .muted { color: #6b7280; }
        .success, .error { padding: 12px 14px; border-radius: 6px; margin-bottom: 16px; }
        .success { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        table { width: 100%; border-collapse: collapse; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        th, td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; vertical-align: top; font-size: 14px; }
        th { background: #f9fafb; color: #374151; }
        tr:last-child td { border-bottom: 0; }
        input, select, textarea { width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 9px 10px; font-size: 14px; }
        textarea { min-height: 74px; resize: vertical; }
        label { display: block; font-weight: 700; font-size: 13px; margin-bottom: 5px; }
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
        .pagination { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 16px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; }
        .pagination .pager-links { display: flex; gap: 8px; }
        .pagination a, .pagination span.disabled { padding: 8px 10px; border-radius: 6px; background: #f3f4f6; color: #374151; font-size: 14px; }
        .pagination span.disabled { color: #9ca3af; }
        .filter-bar { display: flex; align-items: end; gap: 12px; flex-wrap: wrap; }
        .filter-bar .filter-control { min-width: 260px; }
        .filter-bar button, .filter-bar a { display: inline-block; padding: 9px 12px; border-radius: 6px; }
        .filter-bar a { background: #f3f4f6; color: #374151; }
        .calendar-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; background: #fff; }
        .calendar-day-name { background: #f9fafb; padding: 10px; font-weight: 700; color: #4b5563; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; text-align: center; }
        .calendar-cell { min-height: 118px; padding: 8px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; background: #fff; }
        .calendar-cell:nth-child(7n), .calendar-day-name:nth-child(7n) { border-right: 0; }
        .calendar-cell.muted-day { background: #f9fafb; color: #9ca3af; }
        .calendar-date { font-weight: 700; margin-bottom: 6px; }
        .calendar-event { display: block; margin: 5px 0; padding: 6px; border-radius: 6px; background: #f3f4f6; font-size: 12px; line-height: 1.25; }
        .calendar-event.libur, .calendar-event.cuti_bersama { background: #fee2e2; color: #991b1b; }
        .calendar-event.kegiatan { background: #dcfce7; color: #166534; }
        .inline { display: inline; }
        @media (max-width: 760px) {
            .app-shell { grid-template-columns: 1fr; }
            .sidebar { position: static; min-height: auto; }
            header { align-items: flex-start; flex-direction: column; }
            table { display: block; overflow-x: auto; }
            .calendar-grid { grid-template-columns: 1fr; }
            .calendar-day-name { display: none; }
            .calendar-cell { min-height: auto; border-right: 0; }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        @auth
        <aside class="sidebar">
            <div class="brand">
                <strong>{{ config('app.name') }}</strong>
                <div class="muted">{{ auth()->user()->nama }} - {{ auth()->user()->role->nama_role ?? '' }}</div>
            </div>
            <nav>
                <div class="nav-section">Utama</div>
                <a href="{{ route('dashboard') }}">Dashboard</a>
                @if(auth()->user()->isPetugas())
                    <div class="nav-section">Petugas</div>
                    <a href="{{ route('petugas.absensi.index') }}">Absensi</a>
                    <a href="{{ route('petugas.cuti.index') }}">Cuti</a>
                    <div class="dropdown">
                        <button type="button">Tugas</button>
                        <div class="dropdown-menu">
                            <a href="{{ route('petugas.tugas.input') }}">Input Tugas Harian</a>
                            <a href="{{ route('petugas.tugas.laporan') }}">Lap. Tugas Harian</a>
                            <a href="{{ route('petugas.tugas.kalender') }}">Kalender</a>
                        </div>
                    </div>
                    <a href="{{ route('petugas.sanksi.index') }}">Sanksi</a>
                @endif
                @if(auth()->user()->isAtasan())
                    <div class="nav-section">Atasan</div>
                    <a href="{{ route('atasan.absensi.index') }}">Pantau Absensi</a>
                    <a href="{{ route('atasan.cuti.index') }}">Approve Cuti</a>
                    <a href="{{ route('atasan.tugas.index') }}">Approve Tugas</a>
                    <a href="{{ route('atasan.sanksi.index') }}">Sanksi</a>
                @endif
                @if(auth()->user()->isAdmin())
                    <div class="nav-section">Admin</div>
                    <a href="{{ route('admin.users.index') }}">Users</a>
                    <a href="{{ route('admin.tempat.index') }}">Tempat</a>
                    <a href="{{ route('admin.periode.index') }}">Periode</a>
                    <a href="{{ route('admin.kalender.index') }}">Kalender</a>
                    <a href="{{ route('admin.buka-absen.index') }}">Akses Telat</a>
                    <a href="{{ route('admin.sanksi.index') }}">Sanksi</a>
                    <a href="{{ route('admin.data-sensitif.index') }}">Data Sensitif</a>
                    <a href="{{ route('admin.logs.index') }}">Log</a>
                @endif
                <div class="nav-section">Akun</div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="dark">Logout</button>
                </form>
            </nav>
        </aside>
        @endauth
        <div class="content-shell">
            <header>
                <div>
                    <strong>@yield('title', config('app.name'))</strong>
                    @auth
                        <div class="muted">{{ auth()->user()->role->nama_role ?? '' }}</div>
                    @endauth
                </div>
                @auth
                    <div class="top-actions">
                        
                        <form method="POST" action="{{ route('set.periode') }}" style="display:flex; align-items:center; gap:8px; margin-right:12px; border-right:1px solid #e5e7eb; padding-right:16px;">
                            @csrf
                            <label for="global_periode" style="margin:0; font-size:13px; color:#6b7280; font-weight:normal;">Periode:</label>
                            <select name="global_periode_id" id="global_periode" onchange="this.form.submit()" style="padding:4px 28px 4px 8px; font-size:13px; height:auto; width:auto; border-radius:4px;">
                                @foreach($globalPeriodes ?? [] as $p)
                                    <option value="{{ $p->id_periode }}" {{ (isset($globalSelectedPeriode) && $globalSelectedPeriode->id_periode == $p->id_periode) ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('Y') }}
                                        {{ $p->status === 'aktif' ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        @php
                            $unreadNotifications = \App\Models\Notifikasi::where('id_user', auth()->id())->where('status_baca', false)->count();
                            $headerNotifications = \App\Models\Notifikasi::where('id_user', auth()->id())->latest('id_notifikasi')->limit(5)->get();
                        @endphp
                        <div class="notification-wrap" id="notificationWrap">
                            <button type="button" class="notification-button" id="notificationToggle" aria-label="Buka notifikasi" aria-expanded="false">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                @if($unreadNotifications > 0)
                                    <span class="notification-badge" id="notificationBadge">{{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}</span>
                                @endif
                            </button>
                            <div class="notification-panel" id="notificationPanel">
                                <div class="notification-head">
                                    <strong>Notifikasi</strong>
                                    <span class="muted" id="notificationUnreadText">{{ $unreadNotifications }} belum dibaca</span>
                                </div>
                                <div class="notification-list">
                                    @forelse($headerNotifications as $notification)
                                        <div class="notification-item" data-notification-id="{{ $notification->id_notifikasi }}">
                                            <div class="notification-title">{{ $notification->judul ?? 'Notifikasi' }}</div>
                                            <div class="notification-message">{{ $notification->pesan ?? '-' }}</div>
                                            <div class="actions">
                                                <span class="badge {{ $notification->status_baca ? 'approve' : 'pending' }}" data-status-badge>
                                                    {{ $notification->status_baca ? 'Dibaca' : 'Baru' }}
                                                </span>
                                                @if(! $notification->status_baca)
                                                    <form method="POST" action="{{ route('notifikasi.read', $notification->id_notifikasi) }}" data-notification-read-form>
                                                        @csrf
                                                        <button type="submit" class="notification-read-button">Tandai dibaca</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="notification-empty">Belum ada notifikasi.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{ route('profile.index') }}" style="display:flex; align-items:center; gap:10px; margin-left:12px; padding-left:16px; border-left:1px solid #e5e7eb; text-decoration:none;">
                            @if(auth()->user()->foto_profil)
                                <img src="{{ Storage::url(auth()->user()->foto_profil) }}" alt="Foto" style="width:38px; height:38px; border-radius:50%; object-fit:cover;">
                            @else
                                <div style="width:38px; height:38px; border-radius:50%; background:#2563eb; color:#fff; display:grid; place-items:center; font-weight:bold; font-size:16px;">
                                    {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <div style="display:flex; flex-direction:column; align-items:flex-start;">
                                <span style="font-weight:700; font-size:14px; color:#111827; line-height:1.2;">{{ auth()->user()->nama ?? 'User' }}</span>
                                <span style="font-size:12px; color:#6b7280; line-height:1.2;">{{ auth()->user()->role->nama_role ?? '-' }}</span>
                            </div>
                        </a>

                    </div>
                @endauth
            </header>
            <main>
                @if(session('success'))
                    <div class="success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="error">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="error">{{ $errors->first() }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <script>
        var notificationToggle = document.getElementById('notificationToggle');
        var notificationPanel = document.getElementById('notificationPanel');
        var notificationWrap = document.getElementById('notificationWrap');
        var notificationBadge = document.getElementById('notificationBadge');
        var notificationUnreadText = document.getElementById('notificationUnreadText');

        if (notificationToggle && notificationPanel && notificationWrap) {
            notificationToggle.addEventListener('click', function () {
                var isOpen = notificationPanel.classList.toggle('open');
                notificationToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
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
