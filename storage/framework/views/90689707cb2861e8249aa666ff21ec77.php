<?php $__env->startSection('title', 'Kalender'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .calendar-page {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .calendar-admin-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .calendar-admin-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .calendar-admin-title h1 {
        margin: 0;
        font-size: 22px;
        line-height: 1.2;
    }

    .calendar-title-icon {
        width: 40px;
        height: 40px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: var(--primary-soft);
        color: var(--primary);
        border: 1px solid var(--primary-border);
        flex: 0 0 auto;
    }

    .calendar-title-icon svg,
    .calendar-card-title svg,
    .event-type svg {
        width: 18px;
        height: 18px;
    }

    .calendar-count {
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

    .calendar-top-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 12px;
        align-items: start;
    }

    .calendar-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
    }

    .calendar-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .calendar-card-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        color: var(--text-color);
        font-size: 15px;
        font-weight: 600;
    }

    .calendar-card-title svg {
        color: var(--primary);
    }

    .calendar-card-body {
        padding: 16px;
    }

    .calendar-note {
        margin: 0 0 14px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .calendar-filter-card .filter-bar {
        margin: 0;
        padding: 16px;
        border: 0;
        border-radius: 0;
        background: transparent;
    }

    .calendar-form-grid {
        display: grid;
        grid-template-columns: 160px minmax(180px, 1fr) 180px minmax(180px, 1fr) auto;
        gap: 12px;
        align-items: end;
    }

    .calendar-table-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .calendar-admin-table {
        width: 100%;
        min-width: 860px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }

    .calendar-admin-table th {
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

    .calendar-admin-table td {
        padding: 13px 14px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .calendar-admin-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .calendar-admin-table tbody tr:hover {
        background: var(--bg-color);
    }

    .date-stack {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
    }

    .date-icon {
        width: 34px;
        height: 34px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: var(--primary-soft);
        color: var(--primary);
        border: 1px solid var(--primary-border);
        flex: 0 0 auto;
    }

    .date-icon svg {
        width: 16px;
        height: 16px;
    }

    .date-value {
        font-weight: 600;
        color: var(--text-color);
    }

    .event-name {
        font-weight: 600;
        color: var(--text-color);
    }

    .event-empty-text {
        color: var(--muted);
    }

    .event-type {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 30px;
        padding: 6px 10px;
        border-radius: 99px;
        border: 1px solid var(--border-color);
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .event-type.is-libur,
    .event-type.is-cuti_bersama {
        background: var(--red-soft);
        color: var(--red-dark);
        border-color: #fca5a5;
    }

    .event-type.is-kegiatan {
        background: var(--green-soft);
        color: var(--green-dark);
        border-color: #a7f3d0;
    }

    .event-description {
        max-width: 420px;
        color: var(--muted);
        line-height: 1.5;
    }

    .calendar-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .calendar-btn-danger {
        min-height: 34px;
        padding: 8px 12px;
        border-radius: 8px;
        background: var(--red);
        font-size: 12.5px;
    }

    .calendar-empty {
        padding: 36px 18px;
        text-align: center;
        color: var(--muted);
    }

    .calendar-empty-icon {
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

    .calendar-empty-icon svg {
        width: 20px;
        height: 20px;
    }

    .calendar-empty strong {
        display: block;
        margin-bottom: 4px;
        color: var(--text-color);
        font-size: 14px;
    }

    .calendar-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 14px 15px;
        border-top: 1px solid var(--border-color);
    }

    .calendar-result {
        color: var(--muted);
        font-size: 13px;
    }

    @media (max-width: 1100px) {
        .calendar-form-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .calendar-form-grid button {
            width: fit-content;
        }
    }

    @media (max-width: 880px) {
        .calendar-top-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .calendar-admin-title h1 {
            font-size: 20px;
        }

        .calendar-form-grid {
            grid-template-columns: 1fr;
        }

        .calendar-form-grid button {
            width: 100%;
        }
    }
</style>

<div class="calendar-page">
    <div class="calendar-admin-header">
        <div class="calendar-admin-title">
            <span class="calendar-title-icon" aria-hidden="true">
                <svg fill="none" viewBox="0 0 20 20">
                    <rect x="3" y="4" width="14" height="13" rx="2" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M7 2.5v3M13 2.5v3M3 8h14M7 11h.01M10 11h.01M13 11h.01M7 14h.01M10 14h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </span>
            <h1>Kalender</h1>
        </div>
        <div class="calendar-count"><?php echo e($items->total()); ?> total event</div>
    </div>

    <div class="calendar-top-grid">
        <div class="calendar-card">
            <div class="calendar-card-head">
                <h2 class="calendar-card-title">
                    <svg fill="none" viewBox="0 0 16 16">
                        <path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Tambah Event
                </h2>
            </div>
            <div class="calendar-card-body">
                <p class="calendar-note">Tanggal libur, cuti bersama, atau kegiatan yang diisi di sini otomatis tampil di menu Kalender petugas.</p>
                <form method="POST" action="<?php echo e(route('admin.kalender.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="calendar-form-grid">
                        <div>
                            <label for="tanggal">Tanggal</label>
                            <input id="tanggal" type="date" name="tanggal" required>
                        </div>
                        <div>
                            <label for="nama_event">Nama Event</label>
                            <input id="nama_event" name="nama_event" placeholder="Contoh: Libur nasional">
                        </div>
                        <div>
                            <label for="jenis_event">Jenis</label>
                            <select id="jenis_event" name="jenis_event">
                                <option value="libur">Libur</option>
                                <option value="kegiatan">Kegiatan</option>
                                <option value="cuti_bersama">Cuti Bersama</option>
                            </select>
                        </div>
                        <div>
                            <label for="keterangan">Keterangan</label>
                            <input id="keterangan" name="keterangan" placeholder="Opsional">
                        </div>
                        <button type="submit">Tambah</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="calendar-card calendar-filter-card">
            <div class="calendar-card-head">
                <h2 class="calendar-card-title">
                    <svg fill="none" viewBox="0 0 16 16">
                        <path d="M3 5h10M5 8h6M7 11h2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Tampilan
                </h2>
            </div>
            <form action="<?php echo e(route('admin.kalender.index')); ?>" method="GET" class="filter-bar">
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

    <div class="calendar-card">
        <div class="calendar-table-scroll">
            <table class="calendar-admin-table">
                <thead>
                    <tr>
                        <th style="width:190px;">Tanggal</th>
                        <th>Nama</th>
                        <th style="width:190px;">Jenis</th>
                        <th>Keterangan</th>
                        <th style="width:110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $jenisLabel = ucwords(str_replace('_', ' ', $item->jenis_event));
                        ?>
                        <tr>
                            <td>
                                <div class="date-stack">
                                    <span class="date-icon" aria-hidden="true">
                                        <svg fill="none" viewBox="0 0 16 16">
                                            <rect x="2.5" y="3.5" width="11" height="10" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
                                            <path d="M5 2v3M11 2v3M2.5 7h11" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                                        </svg>
                                    </span>
                                    <span class="date-value"><?php echo e($item->tanggal->format('d/m/Y')); ?></span>
                                </div>
                            </td>
                            <td>
                                <?php if($item->nama_event): ?>
                                    <div class="event-name"><?php echo e($item->nama_event); ?></div>
                                <?php else: ?>
                                    <span class="event-empty-text">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="event-type is-<?php echo e($item->jenis_event); ?>">
                                    <?php if($item->jenis_event === 'kegiatan'): ?>
                                        <svg fill="none" viewBox="0 0 16 16">
                                            <path d="M3 8l3 3 7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    <?php elseif($item->jenis_event === 'cuti_bersama'): ?>
                                        <svg fill="none" viewBox="0 0 16 16">
                                            <path d="M3 4h10M4 8h8M5 12h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                    <?php else: ?>
                                        <svg fill="none" viewBox="0 0 16 16">
                                            <path d="M8 2.5v11M3 6h10M4 10h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                    <?php endif; ?>
                                    <?php echo e($jenisLabel); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($item->keterangan): ?>
                                    <div class="event-description"><?php echo e($item->keterangan); ?></div>
                                <?php else: ?>
                                    <span class="event-empty-text">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="calendar-actions">
                                    <form method="POST" action="<?php echo e(route('admin.kalender.delete', $item->id_kalender)); ?>" style="margin:0;" onsubmit="return confirm('Hapus event kalender ini?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="calendar-btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5">
                                <div class="calendar-empty">
                                    <div class="calendar-empty-icon" aria-hidden="true">
                                        <svg fill="none" viewBox="0 0 20 20">
                                            <rect x="3" y="4" width="14" height="13" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                            <path d="M7 2.5v3M13 2.5v3M3 8h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                    <strong>Belum ada event kalender</strong>
                                    Data kalender akan tampil di sini setelah ditambahkan.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="calendar-footer">
            <div class="calendar-result">Menampilkan <?php echo e($items->count()); ?> dari <?php echo e($items->total()); ?> data</div>
            <?php echo e($items->links('pagination.simple')); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/admin/kalender.blade.php ENDPATH**/ ?>