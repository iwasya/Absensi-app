<?php $__env->startSection('title', 'Akses Absen Telat'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .late-page {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .late-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .late-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .late-title h1 {
        margin: 0;
        font-size: 22px;
        line-height: 1.2;
    }

    .late-title-icon {
        width: 40px;
        height: 40px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: var(--amber-soft);
        color: var(--amber-dark);
        border: 1px solid #fde68a;
        flex: 0 0 auto;
    }

    .late-title-icon svg,
    .late-card-title svg,
    .late-date-icon svg {
        width: 18px;
        height: 18px;
    }

    .late-count {
        display: inline-flex;
        align-items: center;
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

    .late-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
    }

    .late-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .late-card-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        color: var(--text-color);
        font-size: 15px;
        font-weight: 600;
    }

    .late-card-title svg {
        color: var(--primary);
    }

    .late-card-body {
        padding: 16px;
    }

    .late-note {
        margin: 0 0 14px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .late-top-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 12px;
        align-items: start;
    }

    .late-form-grid {
        display: grid;
        grid-template-columns: minmax(240px, 1fr) auto;
        gap: 12px;
        align-items: end;
    }

    .late-submit {
        background: var(--green);
        min-height: 38px;
        white-space: nowrap;
    }

    .late-submit:hover {
        background: #059669;
    }

    .late-filter-card .filter-bar {
        margin: 0;
        padding: 16px;
        border: 0;
        border-radius: 0;
        background: transparent;
    }

    .late-table-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .late-table {
        width: 100%;
        min-width: 760px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }

    .late-table th {
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

    .late-table td {
        padding: 13px 14px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .late-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .late-table tbody tr:hover {
        background: var(--bg-color);
    }

    .late-date {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
    }

    .late-date-icon {
        width: 34px;
        height: 34px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: var(--amber-soft);
        color: var(--amber-dark);
        border: 1px solid #fde68a;
        flex: 0 0 auto;
    }

    .late-date-value,
    .late-user-name {
        color: var(--text-color);
        font-weight: 600;
    }

    .late-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 30px;
        padding: 6px 10px;
        border-radius: 99px;
        border: 1px solid #fde68a;
        background: var(--amber-soft);
        color: var(--amber-dark);
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        text-transform: capitalize;
    }

    .late-status::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: currentColor;
    }

    .late-description {
        max-width: 520px;
        color: var(--muted);
        line-height: 1.5;
    }

    .late-empty {
        padding: 36px 18px;
        text-align: center;
        color: var(--muted);
    }

    .late-empty-icon {
        width: 44px;
        height: 44px;
        display: grid;
        place-items: center;
        margin: 0 auto 10px;
        border-radius: 12px;
        background: var(--bg-color);
        color: var(--muted);
        border: 1px solid var(--border-color);
    }

    .late-empty-icon svg {
        width: 20px;
        height: 20px;
    }

    .late-empty strong {
        display: block;
        margin-bottom: 4px;
        color: var(--text-color);
        font-size: 14px;
    }

    .late-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 14px 15px;
        border-top: 1px solid var(--border-color);
    }

    .late-result {
        color: var(--muted);
        font-size: 13px;
    }

    @media (max-width: 880px) {
        .late-top-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .late-title h1 {
            font-size: 20px;
        }

        .late-form-grid {
            grid-template-columns: 1fr;
        }

        .late-submit {
            width: 100%;
        }
    }
</style>

<div class="late-page">
    <div class="late-header">
        <div class="late-title">
            <span class="late-title-icon" aria-hidden="true">
                <svg fill="none" viewBox="0 0 20 20">
                    <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M10 6v4l2.5 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5 3.5 3.5 5M15 3.5 16.5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </span>
            <h1>Akses Absen Telat</h1>
        </div>
        <div class="late-count"><?php echo e($items->total()); ?> akses hari ini</div>
    </div>

    <div class="late-top-grid">
        <div class="late-card">
            <div class="late-card-head">
                <h2 class="late-card-title">
                    <svg fill="none" viewBox="0 0 16 16">
                        <path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Buka Akses
                </h2>
            </div>
            <div class="late-card-body">
                <p class="late-note">Berikan akses khusus kepada petugas agar bisa melakukan absen masuk pada hari ini meskipun sudah melewati jam batas 07:15.</p>
                <form method="POST" action="<?php echo e(route('admin.buka-absen.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="late-form-grid">
                        <div>
                            <label for="id_user">Pilih Petugas</label>
                            <select id="id_user" name="id_user" required>
                                <option value="">Pilih petugas</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id_user); ?>"><?php echo e($user->nama); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <button type="submit" class="late-submit">Buka Akses Hari Ini</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="late-card late-filter-card">
            <div class="late-card-head">
                <h2 class="late-card-title">
                    <svg fill="none" viewBox="0 0 16 16">
                        <path d="M3 5h10M5 8h6M7 11h2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Tampilan
                </h2>
            </div>
            <form action="<?php echo e(route('admin.buka-absen.index')); ?>" method="GET" class="filter-bar">
                <div class="filter-control" style="width:100%;">
                    <label for="per_page">Per Halaman</label>
                    <select id="per_page" name="per_page" onchange="this.form.submit()" style="width:100%;">
                        <option value="10" <?php echo e(request('per_page') == 10 ? 'selected' : ''); ?>>10 / hal</option>
                        <option value="15" <?php echo e(request('per_page') == 15 ? 'selected' : (request('per_page') ? '' : 'selected')); ?>>15 / hal</option>
                        <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25 / hal</option>
                        <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50 / hal</option>
                        <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100 / hal</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="late-card">
        <div class="late-card-head">
            <h2 class="late-card-title">
                <svg fill="none" viewBox="0 0 16 16">
                    <path d="M2 4h12M2 8h9M2 12h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Riwayat Akses Dibuka Hari Ini
            </h2>
        </div>

        <div class="late-table-scroll">
            <table class="late-table">
                <thead>
                    <tr>
                        <th style="width:190px;">Tanggal</th>
                        <th>User</th>
                        <th style="width:170px;">Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="late-date">
                                    <span class="late-date-icon" aria-hidden="true">
                                        <svg fill="none" viewBox="0 0 16 16">
                                            <rect x="2.5" y="3.5" width="11" height="10" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
                                            <path d="M5 2v3M11 2v3M2.5 7h11" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                                        </svg>
                                    </span>
                                    <span class="late-date-value"><?php echo e($item->tanggal->format('d/m/Y')); ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="late-user-name"><?php echo e($item->user->nama ?? '-'); ?></span>
                            </td>
                            <td>
                                <span class="late-status"><?php echo e($item->status); ?></span>
                            </td>
                            <td>
                                <div class="late-description"><?php echo e($item->keterangan ?: '-'); ?></div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4">
                                <div class="late-empty">
                                    <div class="late-empty-icon" aria-hidden="true">
                                        <svg fill="none" viewBox="0 0 20 20">
                                            <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/>
                                            <path d="M10 6v4l2.5 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <strong>Belum ada akses telat</strong>
                                    Akses telat yang dibuka hari ini akan tampil di sini.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="late-footer">
            <div class="late-result">Menampilkan <?php echo e($items->count()); ?> dari <?php echo e($items->total()); ?> data</div>
            <?php echo e($items->links('pagination.simple')); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/admin/buka_absen.blade.php ENDPATH**/ ?>