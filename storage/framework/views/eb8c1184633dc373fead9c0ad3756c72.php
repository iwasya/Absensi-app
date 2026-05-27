<?php $__env->startSection('title', 'Kelola Users'); ?>

<?php $__env->startSection('content'); ?>
<style>
    main {
        max-width: 100% !important;
        padding: 24px 28px !important;
    }

    .users-page {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .users-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .users-header h1 {
        margin: 0;
        font-size: 22px;
        line-height: 1.2;
    }

    .users-count-pill {
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

    .users-tools {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        align-items: start;
    }

    .users-action-card {
        min-height: 92px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 16px;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        background: var(--panel-bg);
        color: var(--text-color);
        cursor: pointer;
        text-align: left;
        transition: border-color .18s, background .18s, box-shadow .18s, transform .18s;
    }

    .users-action-card:hover {
        border-color: var(--primary-border);
        background: var(--primary-soft);
        box-shadow: 0 5px 16px rgba(14, 165, 201, .08);
        transform: translateY(-1px);
    }

    .users-action-title {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-color);
    }

    .users-action-sub {
        display: block;
        margin-top: 4px;
        font-size: 12px;
        color: var(--muted);
    }

    .users-action-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bg-color);
        color: var(--primary);
        border: 1px solid var(--border-color);
        flex: 0 0 auto;
        font-size: 12px;
        font-weight: 700;
    }

    .users-page-view {
        display: none;
    }

    .users-page-view.active {
        display: block;
    }

    .users-form-page {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
    }

    .users-form-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
        border-bottom: 1px solid var(--border-color);
    }

    .users-form-head h2 {
        margin: 0;
        font-size: 18px;
        color: var(--text-color);
    }

    .users-form-sub {
        margin-top: 4px;
        font-size: 12px;
        color: var(--muted);
    }

    .users-back-btn {
        min-height: 34px;
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        background: var(--bg-color);
        color: var(--text-color);
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
    }

    .users-form-body {
        padding: 18px;
    }

    .users-form-section {
        display: grid;
        gap: 12px;
        margin-bottom: 16px;
    }

    .users-section-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-color);
    }

    .choice-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .choice-grid-scroll {
        max-height: 132px;
        overflow: auto;
        padding-right: 4px;
    }

    .choice-pill {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        background: var(--bg-color);
        color: var(--text-color);
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        user-select: none;
    }

    .choice-pill input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .choice-pill:has(input:checked) {
        border-color: var(--primary);
        background: var(--primary-soft);
        color: var(--primary2);
        box-shadow: inset 0 0 0 1px var(--primary-border);
    }

    @media (max-width: 768px) {
        .users-tools {
            grid-template-columns: 1fr;
        }
    }

    .users-panel,
    .users-table-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
    }

    .users-panel summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 15px 16px;
        color: var(--text-color);
        cursor: pointer;
        font-weight: 600;
        list-style: none;
    }

    .users-panel summary::-webkit-details-marker {
        display: none;
    }

    .users-panel summary::after {
        content: "+";
        width: 26px;
        height: 26px;
        display: grid;
        place-items: center;
        border-radius: 8px;
        background: var(--bg-color);
        color: var(--primary);
        border: 1px solid var(--border-color);
        font-size: 17px;
        line-height: 1;
        flex: 0 0 auto;
    }

    .users-panel[open] summary::after {
        content: "-";
    }

    .users-panel-body {
        padding: 16px;
        border-top: 1px solid var(--border-color);
    }

    .users-panel .form-grid,
    .users-panel .filter-bar {
        margin: 0;
        padding: 0;
        border: 0;
        background: transparent;
    }

    .users-panel .filter-bar {
        align-items: end;
    }

    .users-panel-note {
        margin: 0 0 14px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    /* Import Excel Styles */
    .import-container {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .import-template-btn {
        align-self: flex-start;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 16px;
        background: linear-gradient(135deg, var(--green), #22c55e);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(34, 197, 94, 0.2);
    }

    .import-template-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }

    .import-drop-zone {
        position: relative;
        padding: 28px 16px;
        border: 2px dashed var(--border-color);
        border-radius: 10px;
        background: var(--bg-color);
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .import-drop-zone:hover {
        border-color: var(--primary);
        background: rgba(59, 130, 246, 0.02);
    }

    .import-drop-zone.dragover {
        border-color: var(--primary);
        background: rgba(59, 130, 246, 0.05);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .import-drop-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .import-drop-icon {
        font-size: 32px;
        opacity: 0.6;
    }

    .import-drop-title {
        font-weight: 600;
        color: var(--text-color);
        font-size: 14px;
    }

    .import-drop-hint {
        font-size: 12px;
        color: var(--muted);
    }

    .import-file-input {
        display: none;
    }

    .import-filename {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 10px 12px;
        background: var(--primary-soft);
        border: 1px solid var(--primary-border);
        border-radius: 8px;
        color: var(--primary);
        font-size: 13px;
        margin-top: 12px;
    }

    .import-filename strong {
        flex: 1;
        word-break: break-all;
    }

    .import-filename button {
        background: none;
        border: none;
        color: var(--primary);
        cursor: pointer;
        font-size: 14px;
        padding: 0;
        flex-shrink: 0;
    }

    .import-actions-footer {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .import-actions-footer button,
    .import-actions-footer a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .import-btn-upload {
        background: var(--primary);
        color: white;
    }

    .import-btn-upload:hover:not(:disabled) {
        background: #2563eb;
    }

    .import-btn-upload:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .import-btn-cancel {
        background: var(--bg-color);
        color: var(--text-color);
        border: 1px solid var(--border-color);
    }

    .import-btn-cancel:hover {
        background: var(--border-color);
    }

    /* Filter Styles */
    .filter-container {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .filter-bar {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
    }

    .filter-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-control {
        display: flex;
        flex-direction: column;
        gap: 6px;
        flex: 1 1 200px;
    }

    .filter-control label {
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-control input[type="text"],
    .filter-control select {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 9px 11px;
        font-size: 13px;
        color: var(--text-color);
        transition: all 0.2s ease;
    }

    .filter-control input[type="text"]:focus,
    .filter-control select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filter-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-btn-submit {
        background: var(--primary);
        color: white;
    }

    .filter-btn-submit:hover {
        background: #2563eb;
    }

    .filter-btn-reset {
        background: var(--bg-color);
        color: var(--text-color);
        border: 1px solid var(--border-color);
    }

    .filter-btn-reset:hover {
        background: var(--border-color);
    }

    .filter-chips {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        background: var(--primary-soft);
        border: 1px solid var(--primary-border);
        border-radius: 20px;
        color: var(--primary);
        font-size: 12px;
        font-weight: 500;
    }

    .filter-chip button {
        background: none;
        border: none;
        color: var(--primary);
        cursor: pointer;
        font-size: 14px;
        padding: 0;
        display: flex;
        align-items: center;
    }

    .perpage-group {
        display: flex;
        gap: 6px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--panel-bg);
        padding: 2px;
    }

    .perpage-btn {
        flex: 1;
        padding: 8px 12px;
        border: none;
        background: transparent;
        color: var(--text-color);
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .perpage-btn:hover {
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary);
    }

    .perpage-btn.active {
        background: var(--primary);
        color: white;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .import-actions,
    .import-form-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: end;
    }

    .import-actions {
        margin-bottom: 14px;
    }

    .import-form-row > div {
        flex: 1 1 240px;
    }

    .users-button-secondary,
    .users-button-success {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 9px 13px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        border: 1px solid transparent;
    }

    .users-button-secondary {
        background: var(--bg-color);
        color: var(--text-color);
        border-color: var(--border2);
    }

    .users-button-success {
        background: var(--green);
        color: #fff;
    }

    .users-bulkbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 13px 15px;
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
    }

    .users-bulk-form {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin: 0;
    }

    .users-danger {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--red);
        color: #fff;
        padding: 9px 14px;
    }

    .users-danger:hover {
        background: #dc2626;
    }

    .users-checkbox-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        color: var(--muted);
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
    }

    .users-checkbox-label input,
    .users-check {
        width: 16px;
        height: 16px;
        margin: 0;
        accent-color: var(--primary);
    }

    .users-result-text {
        color: var(--muted);
        font-size: 13px;
    }

    .users-table-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .users-table {
        width: 100%;
        min-width: 1060px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }

    .users-table th {
        padding: 12px 14px;
        background: var(--bg-color);
        color: var(--muted);
        border-bottom: 1px solid var(--border-color);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .users-table td {
        padding: 13px 14px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: top;
    }

    .users-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .users-table tbody tr:hover {
        background: var(--bg-color);
    }

    .users-check-cell {
        width: 48px;
        text-align: center;
    }

    .users-name-field,
    .users-select {
        min-width: 0;
    }

    .users-info-grid {
        display: grid;
        gap: 8px;
    }

    .users-info-grid input,
    .users-table input,
    .users-table select {
        min-height: 36px;
        font-size: 12.5px;
    }

    .users-role-badge {
        display: inline-flex;
        align-items: center;
        margin-top: 8px;
        padding: 4px 8px;
        border-radius: 99px;
        background: var(--primary-soft);
        color: var(--primary2);
        border: 1px solid var(--primary-border);
        font-size: 11px;
        font-weight: 600;
    }

    .users-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .users-btn {
        min-width: 72px;
        min-height: 34px;
        padding: 8px 11px;
        border-radius: 8px;
        font-size: 12.5px;
    }

    .users-btn-save {
        background: var(--primary);
    }

    .users-btn-delete {
        background: var(--red);
    }

    .users-empty {
        padding: 38px 18px;
        text-align: center;
        color: var(--muted);
    }

    .users-empty strong {
        display: block;
        margin-bottom: 4px;
        color: var(--text-color);
        font-size: 14px;
    }

    .users-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 14px 15px;
        border-top: 1px solid var(--border-color);
    }

    @media (max-width: 640px) {
        main {
            padding: 16px !important;
        }

        .users-header h1 {
            font-size: 20px;
        }

        .users-bulkbar {
            align-items: stretch;
        }

        .users-bulk-form,
        .users-danger {
            width: 100%;
        }

        .users-danger {
            justify-content: center;
        }
    }
</style>

<div class="users-page">
    <div class="users-header">
        <div>
            <h1>Kelola Users</h1>
        </div>
        <div class="users-count-pill">
            <?php echo e($items->total()); ?> total user
        </div>
    </div>

    <div class="users-tools">
        <a href="<?php echo e(route('admin.users.create')); ?>" class="users-action-card">
            <div>
                <span class="users-action-title">Tambah User</span>
                <span class="users-action-sub">Buat user baru dengan form terpisah di halaman khusus.</span>
            </div>
            <div class="users-action-icon">＋</div>
        </a>

        <a href="<?php echo e(route('admin.users.import.form')); ?>" class="users-action-card">
            <div>
                <span class="users-action-title">Import Users dari Excel</span>
                <span class="users-action-sub">Unggah file Excel untuk menambahkan banyak user sekaligus.</span>
            </div>
            <div class="users-action-icon">📁</div>
        </a>
    </div>

    
    <details class="users-panel" <?php echo e(($filters['search'] || $filters['role']) ? 'open' : ''); ?>>
        <summary>
            <span style="display:inline-flex;align-items:center;gap:10px;">
                🔎 Filter &amp; Cari Users
                <?php if($filters['search'] || $filters['role']): ?>
                    <span style="padding:2px 10px;background:var(--primary);color:#fff;border-radius:99px;font-size:11px;font-weight:700;">Aktif</span>
                <?php endif; ?>
            </span>
        </summary>

        <div class="users-panel-body">
            <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" id="filterForm">
                <div class="filter-row" style="margin-bottom:14px;">

                    <div class="filter-control">
                        <label for="filterSearch">Cari User</label>
                        <input id="filterSearch" name="search" type="text"
                               value="<?php echo e($filters['search']); ?>"
                               placeholder="Nama, username, atau email…">
                    </div>

                    <div class="filter-control">
                        <label for="filterRole">Role</label>
                        <select id="filterRole" name="role">
                            <option value="">📋 Semua Role</option>
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($role->id_role); ?>"
                                    <?php echo e((string)$filters['role'] === (string)$role->id_role ? 'selected' : ''); ?>>
                                    👤 <?php echo e($role->nama_role); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="filter-control" style="flex:0 1 auto;">
                        <label>Per halaman</label>
                        <div class="perpage-group">
                            <?php $__currentLoopData = [10, 25, 50, 100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button"
                                        class="perpage-btn <?php echo e($filters['per_page'] == $pp ? 'active' : ''); ?>"
                                        onclick="setPerPage(<?php echo e($pp); ?>)"><?php echo e($pp); ?></button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="filter-btn filter-btn-submit">🔍 Terapkan</button>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="filter-btn filter-btn-reset">✕ Reset</a>
                </div>
            </form>

            <?php if($filters['search'] || $filters['role']): ?>
                <div class="filter-chips" style="margin-top:14px;">
                    <?php if($filters['search']): ?>
                        <span class="filter-chip">
                            Pencarian: "<?php echo e($filters['search']); ?>"
                            <button type="button" onclick="removeFilter('search')" aria-label="Hapus filter pencarian">×</button>
                        </span>
                    <?php endif; ?>
                    <?php if($filters['role']): ?>
                        <?php $activeRole = $roles->firstWhere('id_role', $filters['role']); ?>
                        <span class="filter-chip">
                            Role: <?php echo e($activeRole->nama_role ?? '—'); ?>

                            <button type="button" onclick="removeFilter('role')" aria-label="Hapus filter role">×</button>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </details>

    <div class="users-bulkbar">
        <form method="POST" action="<?php echo e(route('admin.users.bulk-delete')); ?>" id="bulkDeleteForm" class="users-bulk-form">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>

            <button type="submit" class="users-danger" onclick="return confirmBulkDelete()">
                Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>

            <label for="deleteNikData" class="users-checkbox-label">
                <input type="checkbox" name="delete_nik_data" value="1" id="deleteNikData">
                <span>Hapus juga data NIK sensitif</span>
            </label>
        </form>

        <div class="users-result-text">
            Menampilkan <?php echo e($items->count()); ?> dari <?php echo e($items->total()); ?> data
        </div>
    </div>

    <div class="users-table-card">
        <div class="users-table-scroll">
            <table class="users-table">
                <thead>
                    <tr>
                        <th class="users-check-cell">
                            <input type="checkbox" id="selectAll" class="users-check" aria-label="Pilih semua user">
                        </th>
                        <th style="min-width:190px;">Nama</th>
                        <th style="min-width:300px;">Akun</th>
                        <th style="min-width:190px;">Role</th>
                        <th style="min-width:220px;">Tempat Tugas</th>
                        <th style="min-width:260px;">Regu / Shift / Status</th>
                        <th style="min-width:260px;">Data Diri</th>
                        <th style="min-width:170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $currentRole = $roles->firstWhere('id_role', $item->id_role);
                        ?>
                        <tr>
                            <td class="users-check-cell">
                                <input type="checkbox" name="user_ids[]" value="<?php echo e($item->id_user); ?>" class="user-checkbox users-check" form="bulkDeleteForm" aria-label="Pilih <?php echo e($item->nama); ?>">
                            </td>

                            <td>
                                <label for="nama_<?php echo e($item->id_user); ?>" class="sr-only">Nama</label>
                                <input id="nama_<?php echo e($item->id_user); ?>" class="users-name-field" type="text" name="nama_<?php echo e($item->id_user); ?>" value="<?php echo e($item->nama); ?>">
                            </td>

                            <td>
                                <div class="users-info-grid">
                                    <label for="username_<?php echo e($item->id_user); ?>" class="sr-only">Username</label>
                                    <input id="username_<?php echo e($item->id_user); ?>" type="text" name="username_<?php echo e($item->id_user); ?>" value="<?php echo e($item->username); ?>" placeholder="Username">

                                    <label for="email_<?php echo e($item->id_user); ?>" class="sr-only">Email</label>
                                    <input id="email_<?php echo e($item->id_user); ?>" type="email" name="email_<?php echo e($item->id_user); ?>" value="<?php echo e($item->email); ?>" placeholder="Email">

                                    <label for="password_<?php echo e($item->id_user); ?>" class="sr-only">Password baru</label>
                                    <input id="password_<?php echo e($item->id_user); ?>" type="password" name="password_<?php echo e($item->id_user); ?>" placeholder="Password baru opsional">
                                </div>
                            </td>

                            <td>
                                <label for="id_role_<?php echo e($item->id_user); ?>" class="sr-only">Role</label>
                                <select id="id_role_<?php echo e($item->id_user); ?>" class="users-select" name="id_role_<?php echo e($item->id_user); ?>">
                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($role->id_role); ?>" <?php if($item->id_role == $role->id_role): echo 'selected'; endif; ?>>
                                            <?php echo e($role->nama_role); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <span class="users-role-badge"><?php echo e($currentRole->nama_role ?? 'Role'); ?></span>
                            </td>

                            <td>
                                <label for="id_tempat_<?php echo e($item->id_user); ?>" class="sr-only">Tempat tugas</label>
                                <select id="id_tempat_<?php echo e($item->id_user); ?>" class="users-select" name="id_tempat_<?php echo e($item->id_user); ?>">
                                    <option value="">-</option>
                                    <?php $__currentLoopData = $tempatTugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tempat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($tempat->id_tempat); ?>" <?php if($item->id_tempat == $tempat->id_tempat): echo 'selected'; endif; ?>>
                                            <?php echo e($tempat->nama_tempat); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>

                            <td>
                                <div class="users-info-grid">
                                    <input type="text" name="regu_<?php echo e($item->id_user); ?>" value="<?php echo e($item->regu); ?>" placeholder="Regu">
                                    <select name="shift_<?php echo e($item->id_user); ?>" class="users-select">
                                        <option value="">Shift</option>
                                        <option value="Shift 1" <?php if($item->shift === 'Shift 1'): echo 'selected'; endif; ?>>Shift 1</option>
                                        <option value="Shift 2" <?php if($item->shift === 'Shift 2'): echo 'selected'; endif; ?>>Shift 2</option>
                                        <option value="Shift 3" <?php if($item->shift === 'Shift 3'): echo 'selected'; endif; ?>>Shift 3</option>
                                    </select>
                                    <select name="status_aktif_<?php echo e($item->id_user); ?>" class="users-select">
                                        <option value="aktif" <?php if(($item->status_aktif ?? 'aktif') === 'aktif'): echo 'selected'; endif; ?>>Aktif</option>
                                        <option value="nonaktif" <?php if($item->status_aktif === 'nonaktif'): echo 'selected'; endif; ?>>Nonaktif</option>
                                    </select>
                                    <small class="muted"><?php echo e($item->is_ketua_regu ? 'Ketua ditentukan atasan' : 'Anggota regu'); ?></small>
                                </div>
                            </td>

                            <td>
                                <div class="users-info-grid">
                                    <input type="text" name="no_hp_<?php echo e($item->id_user); ?>" value="<?php echo e($item->no_hp); ?>" placeholder="No HP">
                                    <input type="text" name="jabatan_<?php echo e($item->id_user); ?>" value="<?php echo e($item->jabatan); ?>" placeholder="Jabatan">
                                    <input type="text" name="alamat_<?php echo e($item->id_user); ?>" value="<?php echo e($item->alamat); ?>" placeholder="Alamat">
                                </div>
                            </td>

                            <td>
                                <div class="users-actions">
                                    <button type="button" onclick="updateUser(<?php echo e($item->id_user); ?>)" class="users-btn users-btn-save">
                                        Simpan
                                    </button>
                                    <button type="button" onclick="deleteUser(<?php echo e($item->id_user); ?>)" class="users-btn users-btn-delete">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8">
                                <div class="users-empty">
                                    <strong>Belum ada user</strong>
                                    Data user akan tampil di sini setelah ditambahkan atau diimport.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="users-footer">
            <div class="users-result-text">
                Menampilkan <?php echo e($items->count()); ?> dari <?php echo e($items->total()); ?> data
            </div>
            <?php echo e($items->links('pagination.simple')); ?>

        </div>
    </div>
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    document.querySelectorAll('.user-checkbox').forEach(cb => cb.addEventListener('change', updateSelectedCount));

    function updateSelectedCount() {
        document.getElementById('selectedCount').textContent = document.querySelectorAll('.user-checkbox:checked').length;
    }

    function confirmBulkDelete() {
        const count = document.querySelectorAll('.user-checkbox:checked').length;
        if (count === 0) { alert('Pilih minimal 1 user untuk dihapus'); return false; }
        const nikMsg = document.getElementById('deleteNikData').checked ? ' dan data NIK sensitif' : '';
        return confirm('Hapus ' + count + ' user' + nikMsg + '?');
    }

    function updateUser(id) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('_method', 'PUT');
        formData.append('nama', document.querySelector('[name="nama_' + id + '"]').value);
        formData.append('username', document.querySelector('[name="username_' + id + '"]').value);
        formData.append('email', document.querySelector('[name="email_' + id + '"]').value);
        const pass = document.querySelector('[name="password_' + id + '"]').value;
        if (pass) formData.append('password', pass);
        formData.append('id_role', document.querySelector('[name="id_role_' + id + '"]').value);
        formData.append('id_tempat', document.querySelector('[name="id_tempat_' + id + '"]').value);
        formData.append('regu', document.querySelector('[name="regu_' + id + '"]').value);
        formData.append('shift', document.querySelector('[name="shift_' + id + '"]').value);
        formData.append('status_aktif', document.querySelector('[name="status_aktif_' + id + '"]').value);
        formData.append('no_hp', document.querySelector('[name="no_hp_' + id + '"]').value);
        formData.append('jabatan', document.querySelector('[name="jabatan_' + id + '"]').value);
        formData.append('alamat', document.querySelector('[name="alamat_' + id + '"]').value);

        fetch('/admin/users/' + id, { method: 'POST', body: formData })
            .then(res => res.ok ? alert('Berhasil diupdate') : alert('Gagal update'))
            .catch(() => alert('Error'));
    }

    function deleteUser(id) {
        if (!confirm('Hapus user ini?')) return;
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('_method', 'DELETE');
        fetch('/admin/users/' + id, { method: 'POST', body: formData })
            .then(res => { if (res.ok) location.reload(); else alert('Gagal hapus'); })
            .catch(() => alert('Error'));
    }

    // Import Excel Drag & Drop Handler
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const selectedFileDiv = document.getElementById('selectedFile');
    const fileNameSpan = document.getElementById('fileName');
    const actionButtons = document.getElementById('actionButtons');

    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
        });

        dropZone.addEventListener('drop', handleDrop, false);
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        handleFileSelect({ target: { files } });
    }

    function handleFileSelect(event) {
        const files = event.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            if (file.name.match(/\.(xlsx|xls)$/i)) {
                fileNameSpan.textContent = file.name;
                selectedFileDiv.style.display = 'flex';
                actionButtons.style.display = 'flex';
            } else {
                alert('Hanya file Excel (.xlsx, .xls) yang diperbolehkan!');
                clearFile();
            }
        }
    }

    function clearFile() {
        fileInput.value = '';
        selectedFileDiv.style.display = 'none';
        actionButtons.style.display = 'none';
        fileNameSpan.textContent = '';
    }

    // Filter Per-Page Handler
    function setPerPage(count) {
        const form = document.getElementById('filterForm');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'per_page';
        input.value = count;
        form.appendChild(input);
        form.submit();
    }

    // Remove Filter Chip
    function removeFilter(filterName) {
        const form = document.getElementById('filterForm');
        if (filterName === 'search') {
            form.querySelector('[name="search"]').value = '';
        } else if (filterName === 'role') {
            form.querySelector('[name="role"]').value = '';
        }
        form.submit();
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/admin/users.blade.php ENDPATH**/ ?>