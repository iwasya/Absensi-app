<?php $__env->startSection('title', 'Input Tugas Harian'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .task-input-page {
        max-width: 980px;
        margin: 0 auto;
    }

    .task-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        align-items: center;
        margin-bottom: 16px;
        padding: 18px;
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
    }

    .task-hero h1 {
        margin: 0;
    }

    .task-hero p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .task-date-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 36px;
        padding: 7px 14px;
        border-radius: 99px;
        background: var(--primary-soft);
        border: 1px solid var(--primary-border);
        color: var(--primary2);
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .task-date-pill svg {
        width: 14px;
        height: 14px;
        flex: 0 0 auto;
    }

    .task-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 16px;
        align-items: start;
    }

    .task-panel {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 18px;
        margin-bottom: 16px;
    }

    .task-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 14px;
        margin-bottom: 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .task-panel-head h2 {
        margin: 0;
    }

    .task-panel-head p {
        margin: 5px 0 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .task-panel-badge {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 99px;
        background: var(--primary-soft);
        border: 1px solid var(--primary-border);
        color: var(--primary2);
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .task-form {
        display: grid;
        gap: 14px;
    }

    .task-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .task-textarea {
        min-height: 160px;
        line-height: 1.6;
    }

    .task-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 2px;
    }

    .task-actions .muted {
        font-size: 12px;
        line-height: 1.5;
    }

    .task-submit {
        min-height: 40px;
        padding-inline: 18px;
    }

    .task-side {
        position: sticky;
        top: 88px;
    }

    .task-summary-list {
        display: grid;
        gap: 10px;
    }

    .task-summary-item {
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 11px;
        background: var(--bg-color);
    }

    .task-summary-label {
        margin-bottom: 4px;
        color: var(--muted);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .task-summary-value {
        color: var(--text-color);
        font-size: 13px;
        font-weight: 600;
        line-height: 1.5;
    }

    .task-event-list {
        display: grid;
        gap: 10px;
    }

    .task-event-item {
        display: grid;
        gap: 7px;
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 11px;
        background: var(--bg-color);
    }

    .task-event-title {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .task-event-name {
        color: var(--text-color);
        font-size: 13px;
        font-weight: 600;
    }

    .task-event-desc {
        color: var(--muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .task-empty {
        padding: 16px;
        border: 1px dashed var(--border2);
        border-radius: 11px;
        background: var(--bg-color);
        color: var(--muted);
        font-size: 12px;
        line-height: 1.6;
    }

    @media (max-width: 880px) {
        .task-hero,
        .task-grid {
            grid-template-columns: 1fr;
        }

        .task-side {
            position: static;
        }

        .task-date-pill {
            width: fit-content;
        }
    }

    @media (max-width: 620px) {
        .task-hero,
        .task-panel {
            padding: 16px;
        }

        .task-form-grid {
            grid-template-columns: 1fr;
        }

        .task-panel-head,
        .task-actions {
            display: grid;
        }

        .task-submit {
            width: 100%;
        }
    }
</style>

<div class="task-input-page">
    <section class="task-hero">
        <div>
            <h1>Input Tugas Harian</h1>
            <p>Catat pekerjaan yang dilakukan hari ini agar laporan harian tersusun rapi dan mudah diperiksa atasan.</p>
        </div>
        <div class="task-date-pill">
            <svg fill="none" viewBox="0 0 16 16" aria-hidden="true"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 1v3M11 1v3M2 7h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            <?php echo e(\Carbon\Carbon::now()->translatedFormat('l, d F Y')); ?>

        </div>
    </section>

    <div class="task-grid">
        <section class="task-panel">
            <div class="task-panel-head">
                <div>
                    <h2>Kirim Laporan</h2>
                    <p>Isi waktu pelaksanaan dan uraian pekerjaan dengan jelas.</p>
                </div>
                <span class="task-panel-badge">Laporan Baru</span>
            </div>

            <form method="POST" action="<?php echo e(route('petugas.tugas.store')); ?>" class="task-form">
                <?php echo csrf_field(); ?>

                <div class="task-form-grid">
                    <div>
                        <label>Mulai</label>
                        <input type="datetime-local"
                               name="tanggal_mulai"
                               value="<?php echo e(now()->format('Y-m-d\TH:i')); ?>"
                               required>
                    </div>

                    <div>
                        <label>Selesai</label>
                        <input type="datetime-local"
                               name="tanggal_selesai"
                               value="<?php echo e(now()->format('Y-m-d\TH:i')); ?>">
                    </div>
                </div>

                <div>
                    <label>Uraian Tugas</label>
                    <textarea name="uraian" class="task-textarea" required placeholder="Contoh: Membersihkan area taman, menyapu jalan lingkungan, mengangkut sampah, atau tugas lain yang dikerjakan hari ini."></textarea>
                </div>

                <div class="task-actions">
                    <div class="muted">Pastikan laporan sudah sesuai sebelum dikirim.</div>
                    <button type="submit" class="task-submit">Kirim Laporan</button>
                </div>
            </form>
        </section>

        <aside class="task-side">
            <section class="task-panel">
                <div class="task-panel-head">
                    <div>
                        <h2>Info Hari Ini</h2>
                        <p>Ringkasan periode dan agenda kerja.</p>
                    </div>
                </div>

                <div class="task-summary-list">
                    <div class="task-summary-item">
                        <div class="task-summary-label">Periode Aktif</div>
                        <div class="task-summary-value"><?php echo e($periodeAktif?->nama_periode ?? '-'); ?></div>
                    </div>
                    <div class="task-summary-item">
                        <div class="task-summary-label">Tanggal</div>
                        <div class="task-summary-value"><?php echo e(\Carbon\Carbon::now()->translatedFormat('d F Y')); ?></div>
                    </div>
                </div>
            </section>

            <section class="task-panel">
                <div class="task-panel-head">
                    <div>
                        <h2>Kalender Hari Ini</h2>
                        <p>Agenda khusus yang tercatat pada kalender.</p>
                    </div>
                </div>

                <?php if($jadwalHariIni->isNotEmpty()): ?>
                    <div class="task-event-list">
                        <?php $__currentLoopData = $jadwalHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jadwal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="task-event-item">
                                <div class="task-event-title">
                                    <span class="badge <?php echo e($jadwal->jenis_event); ?>">
                                        <?php echo e($jadwal->jenis_event); ?>

                                    </span>
                                    <span class="task-event-name"><?php echo e($jadwal->nama_event ?? '-'); ?></span>
                                </div>
                                <?php if($jadwal->keterangan): ?>
                                    <div class="task-event-desc"><?php echo e($jadwal->keterangan); ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="task-empty">Belum ada agenda khusus pada kalender hari ini.</div>
                <?php endif; ?>
            </section>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/petugas/tugas-input.blade.php ENDPATH**/ ?>