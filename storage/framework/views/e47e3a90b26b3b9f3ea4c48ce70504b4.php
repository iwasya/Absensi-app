<?php
    $app_theme = \App\Models\Pengaturan::getNilai('app_theme', 'light');
    $app_name = \App\Models\Pengaturan::getNilai('app_name', 'Absensi PPSU') ?: 'Absensi PPSU';
    $app_logo = \App\Models\Pengaturan::getNilai('app_logo');
    $app_brand_display = \App\Models\Pengaturan::getNilai('app_brand_display', 'logo_name');
    $app_icon = \App\Models\Pengaturan::getNilai('app_icon');
    $app_icon_mode = \App\Models\Pengaturan::getNilai('app_icon_mode', 'upload');
    $app_icon_href = null;

    if (! in_array($app_brand_display, ['logo_name', 'logo_only', 'name_only'], true)) {
        $app_brand_display = 'logo_name';
    }

    if ($app_icon_mode === 'manual') {
        $iconText = strtoupper(substr(\App\Models\Pengaturan::getNilai('app_icon_text', 'A') ?: 'A', 0, 2));
        $iconBg = \App\Models\Pengaturan::getNilai('app_icon_bg', '#2563eb');
        $iconColor = \App\Models\Pengaturan::getNilai('app_icon_color', '#ffffff');

        if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $iconBg)) {
            $iconBg = '#2563eb';
        }

        if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $iconColor)) {
            $iconColor = '#ffffff';
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" rx="14" fill="' . $iconBg . '"/><text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle" font-family="Arial,sans-serif" font-size="' . (strlen($iconText) > 1 ? '24' : '32') . '" font-weight="700" fill="' . $iconColor . '">' . htmlspecialchars($iconText, ENT_QUOTES, 'UTF-8') . '</text></svg>';
        $app_icon_href = 'data:image/svg+xml,' . rawurlencode($svg);
    } elseif ($app_icon) {
        $app_icon_href = Storage::url($app_icon);
    }
?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-theme="<?php echo e($app_theme); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', $app_name); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&family=DM+Mono:wght@500&display=swap" rel="stylesheet">
    <?php if($app_icon_href): ?>
        <link rel="icon" href="<?php echo e($app_icon_href); ?>">
        <link rel="apple-touch-icon" href="<?php echo e($app_icon_href); ?>">
    <?php endif; ?>
    <style>
        :root {
            --bg-color:     #F0F4F8;
            --text-color:   #1E293B;
            --sidebar-bg:   #1A2E3B;
            --sidebar-text: #BAD9E3;
            --panel-bg:     #FFFFFF;
            --border-color: #E2E8F0;
            --border2:      #CBD5E1;
            --primary:      #0EA5C9;
            --primary2:     #0284A8;
            --primary-soft: #E0F5FB;
            --primary-border:#BAE8F5;
            --muted:        #94A3B8;
            --input-bg:     #F8FAFC;
            --green:        #10B981;
            --green-soft:   #D1FAE5;
            --green-dark:   #065F46;
            --amber:        #F59E0B;
            --amber-soft:   #FEF3C7;
            --amber-dark:   #92400E;
            --red:          #EF4444;
            --red-soft:     #FEE2E2;
            --red-dark:     #7F1D1D;
            --sb-w:         240px;
        }

        /* Dark mode overrides (tema dari DB tetap berfungsi) */
        [data-theme="dark"] {
            --bg-color:     #0F172A;
            --text-color:   #E2E8F0;
            --sidebar-bg:   #020617;
            --sidebar-text: #7AAFBF;
            --panel-bg:     #1E293B;
            --border-color: #334155;
            --border2:      #475569;
            --primary:      #38BDF8;
            --primary2:     #0EA5C9;
            --primary-soft: #0C2D3F;
            --primary-border:#164E63;
            --muted:        #64748B;
            --input-bg:     #0F172A;
        }

        /* ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ RESET ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body { margin: 0 !important; padding: 0 !important; }

        body {
            margin: 0 !important;
            padding: 0 !important;
            font-family: 'DM Sans', Arial, sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
            transition: background-color .3s, color .3s;
            min-height: 100vh;
        }

        a { color: var(--primary); text-decoration: none; font-weight: 500; }
        a:hover { color: var(--primary2); }

        /* 
           APP SHELL
         */
        .app-shell {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: grid;
            grid-template-columns: var(--sb-w) minmax(0, 1fr);
        }

        /* 
           SIDEBAR
         */
        .sidebar {
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: sticky;
            top: 0;
            min-height: 100vh;
            max-height: 100vh;
            overflow-y: auto;
            align-self: start;
            display: flex;
            flex-direction: column;
            transition: background-color .3s, left .3s;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.1) transparent;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

        /* Brand */
        .brand {
            padding: 20px 16px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand.logo_only { justify-content: center; }
        .brand.name_only { align-items: flex-start; }
        .brand-logo-box {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: var(--primary);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .brand-logo-img {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.10);
            padding: 5px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .brand-logo-box svg { width: 22px; height: 22px; color: #fff; }
        .brand-text strong {
            display: block;
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            line-height: 1.3;
            overflow-wrap: anywhere;
        }
        .brand-text span {
            font-size: 11px;
            color: #5B9BB5;
        }

        /* User card */
        .sb-user {
            margin: 0 10px 12px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 10px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 9px;
        }
        .sb-ava {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
            letter-spacing: .5px;
        }
        .sb-ava img { width: 100%; height: 100%; object-fit: cover; }
        .sb-user-info { overflow: hidden; }
        .sb-user-name   { font-size: 13px; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sb-user-role   { font-size: 11px; color: #5B9BB5; margin-top: 2px; }
        .sb-user-tempat { display: flex; align-items: center; gap: 5px; font-size: 11px; color: #5B9BB5; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sb-dot         { width: 6px; height: 6px; border-radius: 50%; background: var(--green); flex-shrink: 0; }

        /* Nav */
        .nav-section {
            font-size: 10px;
            font-weight: 600;
            color: #3A6678;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 12px 16px 5px;
        }
        nav { padding: 0 8px; display: flex; flex-direction: column; gap: 2px; }
        nav a {
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--sidebar-text);
            background: transparent;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 400;
            transition: background .15s, color .15s;
        }
        nav a:hover  { background: rgba(255,255,255,.08); color: #fff; }
        nav a.active { background: var(--primary); color: #fff; font-weight: 500; }
        nav a svg    { width: 16px; height: 16px; flex-shrink: 0; opacity: .75; }
        nav a:hover svg, nav a.active svg { opacity: 1; }

        /* Dropdown in nav */
        .dropdown { display: flex; flex-direction: column; gap: 2px; }
        .dropdown > button {
            width: 100%;
            text-align: left;
            color: var(--sidebar-text);
            background: transparent;
            border: none;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 12.5px;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background .15s, color .15s;
        }
        .dropdown > button:hover { background: rgba(255,255,255,.08); color: #fff; }
        .dropdown-menu {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 2px 0 2px 12px;
        }
        .dropdown-menu a {
            font-size: 12px;
            background: rgba(255,255,255,.04);
            color: #7AAFBF;
        }
        .dropdown-menu a:hover { background: rgba(255,255,255,.08); color: #fff; }

        /* Sidebar footer */
        .sb-foot {
            margin-top: auto;
            padding: 10px 10px;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .sb-periode-wrap {
            position: relative;
        }
        .sb-periode-wrap svg {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px; height: 14px;
            color: var(--sidebar-text);
            pointer-events: none;
        }
        .sb-periode-form { margin: 0; }
        .sb-periode-form select {
            width: 100%;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.10);
            color: var(--sidebar-text);
            border-radius: 8px;
            padding: 9px 32px 9px 12px;
            font-size: 12px;
            font-family: inherit;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            outline: none;
            transition: background .15s, border-color .15s;
        }
        .sb-periode-form select:hover { background: rgba(255,255,255,.10); border-color: rgba(255,255,255,.18); }

        /* 
           CONTENT SHELL
         */
        .content-shell { min-width: 0; display: flex; flex-direction: column; margin: 0; padding: 0; }

        /* 
           HEADER / TOPBAR
         */
        header {
            margin-top: 0;
            background: var(--panel-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 11px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            position: sticky;
            top: 0;
            z-index: 30;
            transition: background-color .3s, border-color .3s;
        }
        .header-left { display: flex; align-items: center; gap: 12px; }
        .header-title { font-size: 16px; font-weight: 600; color: var(--text-color); }
        .header-role  { font-size: 12px; color: var(--muted); margin-top: 2px; }

        /* Hamburger */
        .hamburger {
            display: none;
            background: var(--bg-color);
            border: 1px solid var(--border2);
            border-radius: 7px;
            cursor: pointer;
            padding: 6px;
            flex-direction: column;
            gap: 4px;
            flex-shrink: 0;
        }
        .hamburger span {
            display: block;
            width: 16px;
            height: 1.5px;
            background: var(--muted);
            border-radius: 2px;
            transition: transform .3s, opacity .3s;
        }
        .hamburger.active span:nth-child(1) { transform: translateY(5.5px) rotate(45deg); }
        .hamburger.active span:nth-child(2) { opacity: 0; }
        .hamburger.active span:nth-child(3) { transform: translateY(-5.5px) rotate(-45deg); }

        .top-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .notification-wrap {
            position: relative;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        /* Header clock ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â pill style */
        .header-clock {
            background: var(--primary-soft);
            border: 1px solid var(--primary-border);
            border-radius: 99px;
            padding: 6px 16px;
            display: flex;
            align-items: center;
            gap: 0;
        }
        .header-clock-time {
            font-size: 15px;
            font-weight: 600;
            color: var(--primary);
            font-family: 'DM Mono', monospace;
            letter-spacing: .05em;
            line-height: 1;
        }
        .header-clock-date { display: none; }

        /* Notification button */
        /* Ganti bagian ini di CSS */

.notification-button {
    position: relative;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-color);
    border: 1.5px solid var(--border2);
    color: var(--muted);
    border-radius: 10px;
    cursor: pointer;
    transition: background .15s, color .15s, border-color .15s;
    padding: 0;
    flex-shrink: 0;
    line-height: 1;
}
.notification-button:hover {
    background: var(--primary-soft);
    color: var(--primary);
    border-color: var(--primary-border);
}
.notification-button svg {
    width: 18px;
    height: 18px;
    display: block;
    margin: 0;
    pointer-events: none;
}
.notification-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 8px;
    height: 8px;
    background: var(--red);
    border-radius: 50%;
    border: 2px solid var(--panel-bg);
    animation: pulse-dot 2s infinite;
    pointer-events: none;
}
@keyframes pulse-dot {
    0%, 100% { transform: scale(1); opacity: 1; }
    50%       { transform: scale(1.4); opacity: .75; }
}

        /* Notification & Profile panels */
        .notification-panel {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: min(340px, calc(100vw - 32px));
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 16px 48px rgba(15,23,42,.14);
            z-index: 50;
            overflow: hidden;
        }
        .notification-panel.open { display: block; }
        .notification-head {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .notification-head strong { font-size: 13px; color: var(--text-color); }
        .notification-list { max-height: 320px; overflow-y: auto; }
        .notification-item {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-color);
            transition: background .12s;
        }
        .notification-item:last-child { border-bottom: 0; }
        .notification-item:hover { background: var(--bg-color); }
        .notification-title   { font-size: 12.5px; font-weight: 600; color: var(--text-color); margin-bottom: 4px; }
        .notification-message { font-size: 12px; color: var(--muted); line-height: 1.5; margin-bottom: 8px; }
        .notification-empty   { padding: 18px 14px; font-size: 12px; color: var(--muted); text-align: center; }
        .notification-read-button {
            background: var(--primary-soft);
            color: var(--primary2);
            border: 1px solid var(--primary-border);
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 500;
            border-radius: 6px;
            cursor: pointer;
            font-family: inherit;
        }
        .notification-read-button:hover { background: var(--primary-border); }

        /* Profile panel */
        #profilePanel {
            width: 200px;
            padding: 6px;
        }
        #profilePanel a, #profilePanel button {
            display: block;
            width: 100%;
            padding: 9px 11px;
            border-radius: 7px;
            font-size: 12.5px;
            text-align: left;
            font-family: inherit;
            cursor: pointer;
            transition: background .12s;
        }
        #profilePanel a {
            color: var(--text-color);
            font-weight: 400;
            background: transparent;
        }
        #profilePanel a:hover { background: var(--bg-color); }
        #profilePanel button {
            background: var(--red-soft);
            color: var(--red-dark);
            border: 1px solid #FCA5A5;
            font-weight: 600;
            margin-top: 4px;
        }
        #profilePanel button:hover { background: #FEE2E2; }

        /* Sidebar overlay mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 40;
            opacity: 0;
            transition: opacity .3s;
        }
        .sidebar-overlay.show { display: block; opacity: 1; }

        /* 
           MAIN CONTENT
         */
        main {
            width: 100%;
            max-width: 1200px;
            margin: 24px auto;
            padding: 0 22px;
            flex: 1;
        }
        h1 { font-size: 20px; font-weight: 600; color: var(--text-color); margin-bottom: 18px; }
        h2 { font-size: 16px; font-weight: 600; color: var(--text-color); margin-bottom: 12px; }

        /* Flash messages */
        .success {
            padding: 11px 14px;
            border-radius: 9px;
            margin-bottom: 16px;
            background: var(--green-soft);
            color: var(--green-dark);
            border: 1px solid #A7F3D0;
            font-size: 13px;
        }
        .error {
            padding: 11px 14px;
            border-radius: 9px;
            margin-bottom: 16px;
            background: var(--red-soft);
            color: var(--red-dark);
            border: 1px solid #FCA5A5;
            font-size: 13px;
        }

        /* Grid / Panel / Stat (keep existing class names) */
        .grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
        .panel, .stat {
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 13px;
            padding: 16px;
            margin-bottom: 16px;
            transition: background-color .3s, border-color .3s;
            font-size: 13px;
        }
        .stat { margin-bottom: 0; }
        .stat strong {
            display: block;
            font-size: 24px;
            font-weight: 600;
            margin-top: 8px;
            color: var(--primary);
        }
        .muted { color: var(--muted); }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 13px;
            overflow: hidden;
            font-size: 13px;
            transition: background-color .3s, border-color .3s;
        }
        th, td {
            padding: 10px 14px;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
            vertical-align: top;
            color: var(--text-color);
        }
        th {
            background: var(--bg-color);
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: var(--bg-color); }

        /* Form inputs */
        input, select, textarea {
            width: 100%;
            border: 1.5px solid var(--border2);
            border-radius: 9px;
            padding: 9px 11px;
            font-size: 13px;
            font-family: inherit;
            background: var(--input-bg);
            color: var(--text-color);
            outline: none;
            transition: border-color .18s, box-shadow .18s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(14,165,201,.1);
        }
        textarea { min-height: 74px; resize: vertical; }
        label {
            display: block;
            font-weight: 500;
            font-size: 12.5px;
            margin-bottom: 5px;
            color: var(--text-color);
        }
        .form-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            align-items: end;
        }

        /* Buttons */
        button {
            border: 0;
            border-radius: 8px;
            background: var(--primary);
            color: #fff;
            padding: 9px 14px;
            font-weight: 500;
            font-family: inherit;
            font-size: 13px;
            cursor: pointer;
            transition: background .15s;
        }
        button:hover    { background: var(--primary2); }
        button.danger   { background: var(--red); }
        button.danger:hover { background: #DC2626; }
        button.dark     { background: #1F2937; }
        button.dark:hover { background: #111827; }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 99px;
            background: var(--primary-soft);
            color: var(--primary2);
            border: 1px solid var(--primary-border);
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge.pending           { background: var(--amber-soft); color: var(--amber-dark); border-color: #FDE68A; }
        .badge.approve, .badge.hadir, .badge.kegiatan { background: var(--green-soft); color: var(--green-dark); border-color: #A7F3D0; }
        .badge.reject, .badge.telat { background: var(--red-soft); color: var(--red-dark); border-color: #FCA5A5; }
        .badge.libur, .badge.cuti_bersama { background: var(--red-soft); color: var(--red-dark); border-color: #FCA5A5; }

        /* Actions / Filter / Pagination */
        .actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .filter-bar {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            flex-wrap: wrap;
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 13px;
            padding: 12px 14px;
            margin-bottom: 16px;
        }
        .filter-bar .filter-control { min-width: 220px; }
        .filter-bar button, .filter-bar a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12.5px;
        }
        .filter-bar a {
            background: var(--bg-color);
            color: var(--text-color);
            border: 1px solid var(--border2);
            font-weight: 500;
        }
        .filter-bar a:hover { background: var(--border-color); }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 16px;
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 11px 14px;
            font-size: 12.5px;
        }
        .pagination .pager-links { display: flex; gap: 6px; }
        .pagination a, .pagination span.disabled {
            padding: 6px 10px;
            border-radius: 7px;
            background: var(--bg-color);
            color: var(--text-color);
            border: 1px solid var(--border2);
            font-size: 12.5px;
            font-weight: 500;
        }
        .pagination a:hover { background: var(--primary-soft); border-color: var(--primary-border); color: var(--primary); }
        .pagination span.disabled { color: var(--muted); }

        /* Calendar */
        .calendar-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            border: 1px solid var(--border-color);
            border-radius: 13px;
            overflow: hidden;
            background: var(--panel-bg);
        }
        .calendar-day-name {
            background: var(--bg-color);
            padding: 10px;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--muted);
            border-right: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            text-align: center;
        }
        .calendar-cell {
            min-height: 118px;
            padding: 8px;
            border-right: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            background: var(--panel-bg);
        }
        .calendar-cell:nth-child(7n), .calendar-day-name:nth-child(7n) { border-right: 0; }
        .calendar-cell.muted-day { background: var(--bg-color); color: var(--muted); }
        .calendar-date { font-weight: 600; margin-bottom: 6px; font-size: 13px; }
        .calendar-event {
            display: block;
            margin: 4px 0;
            padding: 5px 7px;
            border-radius: 6px;
            background: var(--bg-color);
            font-size: 11.5px;
            line-height: 1.3;
            border: 1px solid var(--border-color);
        }
        .calendar-event.libur, .calendar-event.cuti_bersama {
            background: var(--red-soft);
            color: var(--red-dark);
            border-color: #FCA5A5;
        }
        .calendar-event.kegiatan {
            background: var(--green-soft);
            color: var(--green-dark);
            border-color: #A7F3D0;
        }

        .inline { display: inline; }

        /* 
           RESPONSIVE
         */
        @media (max-width: 760px) {
            .app-shell { display: block; }
            .sidebar {
                position: fixed;
                left: calc(-1 * var(--sb-w));
                top: 0; bottom: 0;
                width: var(--sb-w);
                z-index: 50;
                max-height: 100vh;
                transition: left .3s;
            }
            .sidebar.open { left: 0; }
            .hamburger { display: flex; }
            header { flex-wrap: wrap; }
            .header-left { flex: 1; }
            .top-actions {
                width: 100%;
                justify-content: flex-end;
                padding-top: 10px;
                border-top: 1px solid var(--border-color);
                margin-top: 8px;
            }
            .notification-panel {
                position: fixed;
                top: 72px;
                right: 16px;
                max-height: calc(100vh - 96px);
            }
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

        
        <div class="brand <?php echo e($app_brand_display); ?>">
            <?php if($app_brand_display !== 'name_only'): ?>
                <?php if($app_logo): ?>
                <img src="<?php echo e(Storage::url($app_logo)); ?>" alt="Logo" class="brand-logo-img">
                <?php else: ?>
                <div class="brand-logo-box">
                    <svg fill="none" viewBox="0 0 18 18"><path d="M3 9a6 6 0 1012 0A6 6 0 003 9z" stroke="#fff" stroke-width="1.4"/><path d="M9 6v3l2 1.5" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/></svg>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if($app_brand_display !== 'logo_only' || ! $app_logo): ?>
            <div class="brand-text">
                <strong><?php echo e($app_name); ?></strong>
                <span>Kel. Pisangan baru</span>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="sb-user">
            <div class="sb-ava">
                <?php if(auth()->user()->foto_profil): ?>
                    <img src="<?php echo e(Storage::url(auth()->user()->foto_profil)); ?>" alt="Foto">
                <?php else: ?>
                    <?php
                        $namaParts = explode(' ', trim(auth()->user()->nama ?? 'U'));
                        $inisial = strtoupper(substr($namaParts[0], 0, 1));
                        if (count($namaParts) > 1) $inisial .= strtoupper(substr($namaParts[1], 0, 1));
                    ?>
                    <?php echo e($inisial); ?>

                <?php endif; ?>
            </div>
            <div class="sb-user-info">
                <div class="sb-user-name"><?php echo e(auth()->user()->nama ?? 'Pengguna'); ?></div>
                <div class="sb-user-role"><?php echo e(auth()->user()->role->nama_role ?? ''); ?></div>
                <?php if(auth()->user()->tempat_tugas): ?>
                    <div class="sb-user-tempat">
                        <span class="sb-dot"></span>
                        <?php echo e(auth()->user()->tempat_tugas->nama_tempat); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <nav>
            <div class="nav-section">Menu</div>
            <a href="<?php echo e(route('dashboard')); ?>"
               class="<?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <svg fill="none" viewBox="0 0 16 16"><rect x="1" y="1" width="6" height="6" rx="1.5" fill="currentColor"/><rect x="9" y="1" width="6" height="6" rx="1.5" fill="currentColor"/><rect x="1" y="9" width="6" height="6" rx="1.5" fill="currentColor"/><rect x="9" y="9" width="6" height="6" rx="1.5" fill="currentColor"/></svg>
                Beranda
            </a>

            <?php if(auth()->user()->isPetugas()): ?>
                <a href="<?php echo e(route('petugas.absensi.index')); ?>"
                   class="<?php echo e(request()->routeIs('petugas.absensi.*') ? 'active' : ''); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Absensi
                </a>
                <div class="dropdown" id="dd-tugas">
                    <button type="button" onclick="toggleDropdown('dd-tugas')"
                        style="<?php echo e(request()->routeIs('petugas.tugas.*') ? 'background:var(--primary);color:#fff;' : ''); ?>">
                        <span style="display:flex;align-items:center;gap:9px;">
                            <svg fill="none" viewBox="0 0 16 16" style="width:16px;height:16px;flex-shrink:0;opacity:.85;"><path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                            Tugas Harian
                        </span>
                        <svg class="dd-chevron" fill="none" viewBox="0 0 16 16" style="width:12px;height:12px;flex-shrink:0;transition:transform .2s;<?php echo e(request()->routeIs('petugas.tugas.*') ? 'transform:rotate(180deg);' : ''); ?>">
                            <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="dropdown-menu" style="<?php echo e(request()->routeIs('petugas.tugas.*') ? '' : 'display:none'); ?>">
                        <a href="<?php echo e(route('petugas.tugas.input')); ?>"
                           class="<?php echo e(request()->routeIs('petugas.tugas.input') ? 'active' : ''); ?>">
                            <svg fill="none" viewBox="0 0 16 16" style="width:13px;height:13px;"><path d="M3 8h10M8 3v10" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                            Input Tugas Harian
                        </a>
                        <a href="<?php echo e(route('petugas.tugas.laporan')); ?>"
                           class="<?php echo e(request()->routeIs('petugas.tugas.laporan*') ? 'active' : ''); ?>">
                            <svg fill="none" viewBox="0 0 16 16" style="width:13px;height:13px;"><path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                            Lap. Tugas Harian
                        </a>
                        <a href="<?php echo e(route('petugas.tugas.kalender')); ?>"
                           class="<?php echo e(request()->routeIs('petugas.tugas.kalender') ? 'active' : ''); ?>">
                            <svg fill="none" viewBox="0 0 16 16" style="width:13px;height:13px;"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                            Kalender
                        </a>
                    </div>
                </div>
                <a href="<?php echo e(route('petugas.cuti.index')); ?>"
                   class="<?php echo e(request()->routeIs('petugas.cuti.*') ? 'active' : ''); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><path d="M8 2v4l2.5 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/></svg>
                    Cuti
                </a>
                <a href="<?php echo e(route('petugas.sanksi.index')); ?>"
                   class="<?php echo e(request()->routeIs('petugas.sanksi.*') ? 'active' : ''); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><path d="M2 12L6 8l3 3 5-6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Sanksi
                </a>
            <?php endif; ?>

            <?php if(auth()->user()->isAtasan()): ?>
                <div class="nav-section">Atasan</div>
                <a href="<?php echo e(route('atasan.absensi.index')); ?>" class="<?php echo e(request()->routeIs('atasan.absensi.*') ? 'active' : ''); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><path d="M3 8l3.5 3.5L13 5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Pantau Absensi
                </a>
                <a href="<?php echo e(route('atasan.cuti.index')); ?>" class="<?php echo e(request()->routeIs('atasan.cuti.*') ? 'active' : ''); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><path d="M8 2v4l2.5 2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/></svg>
                    Approve Cuti
                </a>
                <a href="<?php echo e(route('atasan.tugas.index')); ?>" class="<?php echo e(request()->routeIs('atasan.tugas.*') ? 'active' :''); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Approve Tugas
                </a>
                <a href="<?php echo e(route('atasan.kalender.index')); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Kalender
                </a>
                <a href="<?php echo e(route('atasan.sanksi.index')); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><path d="M2 12L6 8l3 3 5-6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Sanksi
                </a>
            <?php endif; ?>

            <?php if(auth()->user()->isAdmin()): ?>
                <div class="nav-section">Admin</div>
                <?php if(auth()->user()->role->nama_role === 'Admin Absensi'): ?>
                    <a href="<?php echo e(route('admin.pengaturan.index')); ?>">
                        <svg fill="none" viewBox="0 0 16 16"><path d="M8 10a2 2 0 100-4 2 2 0 000 4z" stroke="currentColor" stroke-width="1.3"/><path d="M13.7 9.5l.8-.5v-2l-.8-.5a6 6 0 00-.6-1.4l.3-.9-1.4-1.4-.9.3a6 6 0 00-1.4-.6L9.5 2h-3l-.2.5a6 6 0 00-1.4.6l-.9-.3L2.6 4.2l.3.9a6 6 0 00-.6 1.4L2 7v2l.3.5a6 6 0 00.6 1.4l-.3.9 1.4 1.4.9-.3a6 6 0 001.4.6l.2.5h3l.5-.5a6 6 0 001.4-.6l.9.3 1.4-1.4-.3-.9a6 6 0 00.6-1.4z" stroke="currentColor" stroke-width="1.3"/></svg>
                        Pengaturan
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.users.index')); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><circle cx="6" cy="5" r="3" stroke="currentColor" stroke-width="1.3"/><path d="M1 14c0-3 2.2-5 5-5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><path d="M11 9l1.5 1.5L15 8" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.3"/></svg>
                    Users
                </a>
                <a href="<?php echo e(route('admin.tempat.index')); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><path d="M8 2a4 4 0 00-4 4c0 3 4 8 4 8s4-5 4-8a4 4 0 00-4-4z" stroke="currentColor" stroke-width="1.3"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                    Tempat
                </a>
                <a href="<?php echo e(route('admin.periode.index')); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Periode
                </a>
                <a href="<?php echo e(route('admin.kalender.index')); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><path d="M8 2L2 5v4c0 3.5 2.5 6.5 6 7 3.5-.5 6-3.5 6-7V5L8 2z" stroke="currentColor" stroke-width="1.3"/></svg>
                    Kalender
                </a>
                <a href="<?php echo e(route('admin.buka-absen.index')); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M8 5v3l2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Akses Telat
                </a>
                <a href="<?php echo e(route('admin.sanksi.index')); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><path d="M2 12L6 8l3 3 5-6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Sanksi
                </a>
                <a href="<?php echo e(route('admin.data-sensitif.index')); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><rect x="3" y="7" width="10" height="7" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 7V5a3 3 0 016 0v2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Data Sensitif
                </a>
                <a href="<?php echo e(route('admin.logs.index')); ?>">
                    <svg fill="none" viewBox="0 0 16 16"><path d="M2 4h12M2 7h9M2 10h6M2 13h4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Log
                </a>
            <?php endif; ?>
        </nav>

        <div class="sb-foot">
            <div class="sb-periode-wrap">
                <form method="POST" action="<?php echo e(route('set.periode')); ?>" class="sb-periode-form">
                    <?php echo csrf_field(); ?>
                    <select name="global_periode_id" onchange="this.form.submit()">
                        <?php $__currentLoopData = $globalPeriodes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id_periode); ?>"
                                <?php echo e((isset($globalSelectedPeriode) && $globalSelectedPeriode->id_periode == $p->id_periode) ? 'selected' : ''); ?>>
                                Periode <?php echo e(\Carbon\Carbon::parse($p->tanggal_mulai)->format('Y')); ?>

                                <?php echo e($p->status === 'aktif' ? '(Aktif)' : ''); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </form>
                <svg fill="none" viewBox="0 0 16 16" pointer-events="none">
                    <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

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
                    <div class="header-title"><?php echo $__env->yieldContent('title', $app_name); ?></div>
                    <?php if(auth()->guard()->check()): ?>
                        <div class="header-role"><?php echo e(auth()->user()->role->nama_role ?? ''); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if(auth()->guard()->check()): ?>
            <div class="top-actions">

                
                <div class="header-clock">
                    <div class="header-clock-time" id="header-clock-time">--:--:--</div>
                    <div class="header-clock-date" id="header-clock-date">Memuat...</div>
                </div>

                
                <?php
                    $unreadNotifications = \App\Models\Notifikasi::where('id_user', auth()->id())->where('status_baca', false)->count();
                    $headerNotifications = \App\Models\Notifikasi::where('id_user', auth()->id())->latest('id_notifikasi')->limit(5)->get();
                ?>
                <div class="notification-wrap" id="notificationWrap">
                    <button type="button" class="notification-button" id="notificationToggle"
                            aria-label="Buka notifikasi" aria-expanded="false">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"
          fill="currentColor"/>
</svg>
                        <?php if($unreadNotifications > 0): ?>
                            <span class="notification-badge" id="notificationBadge"></span>
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
                                        <?php if(!$notification->status_baca): ?>
                                            <form method="POST"
                                                  action="<?php echo e(route('notifikasi.read', $notification->id_notifikasi)); ?>"
                                                  data-notification-read-form>
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

                
                <div class="notification-wrap" id="profileWrap">
                    <button type="button" id="profileToggle" aria-expanded="false"
                            title="<?php echo e(auth()->user()->nama ?? 'Profil'); ?>"
                            style="background:none;border:none;cursor:pointer;padding:0;display:flex;border-radius:50%;">
                        <?php if(auth()->user()->foto_profil): ?>
                            <img src="<?php echo e(Storage::url(auth()->user()->foto_profil)); ?>" alt="Foto"
                                 style="width:42px;height:42px;border-radius:50%;object-fit:cover;border:2.5px solid var(--primary-border);transition:border-color .15s;">
                        <?php else: ?>
                            <div style="width:42px;height:42px;border-radius:50%;background:var(--primary);color:#fff;display:grid;place-items:center;font-weight:700;font-size:15px;border:2.5px solid var(--primary-border);letter-spacing:.5px;">
                                <?php echo e(strtoupper(substr(auth()->user()->nama ?? 'U', 0, 1))); ?>

                            </div>
                        <?php endif; ?>
                    </button>
                    <div class="notification-panel" id="profilePanel">
                        <a href="<?php echo e(route('profile.index')); ?>">Lihat Profil</a>
                        <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin:0">
                            <?php echo csrf_field(); ?>
                            <button type="submit">Logout</button>
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
    // ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Sidebar Dropdown Toggle ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
    function toggleDropdown(id) {
        var dd     = document.getElementById(id);
        var menu   = dd.querySelector('.dropdown-menu');
        var chevron= dd.querySelector('.dd-chevron');
        var isOpen = menu.style.display !== 'none';
        menu.style.display = isOpen ? 'none' : '';
        if (chevron) chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
    }

    // ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Hamburger / Sidebar ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
    var hamburgerMenu  = document.getElementById('hamburgerMenu');
    var sidebar        = document.querySelector('.sidebar');
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

    // ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Notification panel ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
    var notificationToggle    = document.getElementById('notificationToggle');
    var notificationPanel     = document.getElementById('notificationPanel');
    var notificationWrap      = document.getElementById('notificationWrap');
    var notificationBadge     = document.getElementById('notificationBadge');
    var notificationUnreadText= document.getElementById('notificationUnreadText');

    // ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Profile panel ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
    var profileToggle = document.getElementById('profileToggle');
    var profilePanel  = document.getElementById('profilePanel');
    var profileWrap   = document.getElementById('profileWrap');

    if (profileToggle && profilePanel && profileWrap) {
        profileToggle.addEventListener('click', function () {
            var isOpen = profilePanel.classList.toggle('open');
            profileToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (notificationPanel && notificationPanel.classList.contains('open')) {
                notificationPanel.classList.remove('open');
                if (notificationToggle) notificationToggle.setAttribute('aria-expanded', 'false');
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
                if (profileToggle) profileToggle.setAttribute('aria-expanded', 'false');
            }
        });
        document.addEventListener('click', function (event) {
            if (!notificationWrap.contains(event.target)) {
                notificationPanel.classList.remove('open');
                notificationToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Mark as read (AJAX ÃƒÂ¢Ã¢â€šÂ¬Ã¢â‚¬Â fungsi asli dipertahankan) ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
        notificationPanel.querySelectorAll('[data-notification-read-form]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                var button     = form.querySelector('button');
                var item       = form.closest('[data-notification-id]');
                var statusBadge= item ? item.querySelector('[data-status-badge]') : null;

                if (button) { button.disabled = true; button.textContent = 'Menyimpan...'; }

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN'    : form.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept'          : 'application/json'
                    }
                })
                .then(function (response) {
                    if (!response.ok) throw new Error('Gagal.');
                    return response.json();
                })
                .then(function (data) {
                    if (statusBadge) {
                        statusBadge.classList.remove('pending');
                        statusBadge.classList.add('approve');
                        statusBadge.textContent = 'Dibaca';
                    }
                    form.remove();
                    if (notificationUnreadText) notificationUnreadText.textContent = data.unread_count + ' belum dibaca';
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
                    if (button) { button.disabled = false; button.textContent = 'Tandai dibaca'; }
                    alert('Notifikasi belum bisa ditandai. Coba lagi.');
                });
            });
        });
    }

    // ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ Realtime clock di topbar ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬ÃƒÂ¢Ã¢â‚¬ÂÃ¢â€šÂ¬
    (function () {
        var days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        var months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        function tickClock() {
            var now  = new Date();
            var h    = String(now.getHours()).padStart(2,'0');
            var m    = String(now.getMinutes()).padStart(2,'0');
            var s    = String(now.getSeconds()).padStart(2,'0');
            var dateStr = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
            var t = document.getElementById('header-clock-time');
            var d = document.getElementById('header-clock-date');
            if (t) t.textContent = h + ':' + m + ':' + s;
            if (d) d.textContent = dateStr;
        }
        tickClock();
        setInterval(tickClock, 1000);
    })();
</script>
</body>
</html>
<?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/layouts/app.blade.php ENDPATH**/ ?>