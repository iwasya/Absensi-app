<?php $__env->startSection('title', 'Pengajuan Cuti'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .cuti-page {
        max-width: 1120px;
        margin: 0 auto;
    }

    .cuti-hero {
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

    .cuti-hero h1 {
        margin: 0;
    }

    .cuti-hero p {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .cuti-quota-pill {
        display: inline-flex;
        align-items: center;
        min-height: 36px;
        padding: 7px 14px;
        border-radius: 99px;
        background: var(--primary-soft);
        border: 1px solid var(--primary-border);
        color: var(--primary2);
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .cuti-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 16px;
        align-items: start;
    }

    .cuti-panel {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 18px;
        margin-bottom: 16px;
    }

    .cuti-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 14px;
        margin-bottom: 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .cuti-panel-head h2 {
        margin: 0;
    }

    .cuti-panel-head p {
        margin: 5px 0 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .cuti-panel-badge {
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

    .cuti-form {
        display: grid;
        gap: 14px;
    }

    .cuti-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .cuti-full {
        grid-column: 1 / -1;
    }

    .cuti-textarea {
        min-height: 120px;
        line-height: 1.6;
    }

    .cuti-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 2px;
    }

    .cuti-actions .muted {
        font-size: 12px;
        line-height: 1.5;
    }

    .cuti-submit {
        min-height: 40px;
        padding-inline: 18px;
    }

    .cuti-side {
        position: sticky;
        top: 88px;
    }

    .cuti-summary {
        display: grid;
        gap: 10px;
    }

    .cuti-summary-item {
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 11px;
        background: var(--bg-color);
    }

    .cuti-summary-label {
        margin-bottom: 4px;
        color: var(--muted);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .cuti-summary-value {
        color: var(--text-color);
        font-size: 18px;
        font-weight: 700;
        line-height: 1.2;
    }

    .cuti-summary-note {
        margin-top: 4px;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .cuti-table-wrap {
        overflow-x: auto;
    }

    .cuti-table-wrap table {
        min-width: 860px;
    }

    .cuti-date {
        white-space: nowrap;
        font-weight: 600;
    }

    .cuti-reason {
        min-width: 190px;
        line-height: 1.5;
    }

    .cuti-action-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 7px;
        background: var(--green);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .cuti-action-link:hover {
        color: #fff;
        background: #059669;
    }

    .field-error {
        margin-top: 5px;
        color: var(--red-dark);
        font-size: 12px;
        line-height: 1.4;
    }

    .cuti-empty {
        text-align: center;
        padding: 22px 14px;
    }

    @media (max-width: 920px) {
        .cuti-hero,
        .cuti-grid {
            grid-template-columns: 1fr;
        }

        .cuti-side {
            position: static;
        }

        .cuti-quota-pill {
            width: fit-content;
        }
    }

    @media (max-width: 640px) {
        .cuti-hero,
        .cuti-panel {
            padding: 16px;
        }

        .cuti-form-grid {
            grid-template-columns: 1fr;
        }

        .cuti-full {
            grid-column: auto;
        }

        .cuti-panel-head,
        .cuti-actions {
            display: grid;
        }

        .cuti-submit {
            width: 100%;
        }
    }
</style>

<?php
    $sisaCuti = max($batasCutiTahunan - $cutiTerpakaiTahunIni, 0);
?>

<div class="cuti-page">
    <section class="cuti-hero">
        <div>
            <h1>Pengajuan Cuti</h1>
            <p>Ajukan cuti dengan data yang lengkap agar proses persetujuan oleh atasan lebih cepat.</p>
        </div>
        <div class="cuti-quota-pill">Sisa <?php echo e($sisaCuti); ?> dari <?php echo e($batasCutiTahunan); ?> kali</div>
    </section>

    <div class="cuti-grid">
        <section class="cuti-panel">
            <div class="cuti-panel-head">
                <div>
                    <h2>Ajukan Cuti</h2>
                    <p>Isi periode cuti, alasan, petugas pengganti, dan alamat selama cuti.</p>
                </div>
                <span class="cuti-panel-badge">Form Baru</span>
            </div>

            <form method="POST" action="<?php echo e(route('petugas.cuti.store')); ?>" class="cuti-form">
                <?php echo csrf_field(); ?>
                <div class="cuti-form-grid">
                    <div>
                        <label for="tanggal_mulai">Tanggal Mulai</label>
                        <input id="tanggal_mulai" type="date" name="tanggal_mulai" value="<?php echo e(old('tanggal_mulai')); ?>" required>
                        <?php $__errorArgs = ['tanggal_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="field-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="tanggal_selesai">Tanggal Selesai</label>
                        <input id="tanggal_selesai" type="date" name="tanggal_selesai" value="<?php echo e(old('tanggal_selesai')); ?>" required>
                        <?php $__errorArgs = ['tanggal_selesai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="field-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="jenis_cuti">Jenis Cuti</label>
                        <select id="jenis_cuti" name="jenis_cuti" required>
                            <option value="Tahunan" <?php if(old('jenis_cuti') === 'Tahunan'): echo 'selected'; endif; ?>>Tahunan</option>
                            <option value="Besar" <?php if(old('jenis_cuti') === 'Besar'): echo 'selected'; endif; ?>>Besar</option>
                        </select>
                        <?php $__errorArgs = ['jenis_cuti'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="field-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="alasan_select">Alasan</label>
                        <select name="alasan" id="alasan_select" required>
                            <option value="Sakit" <?php if(old('alasan') === 'Sakit'): echo 'selected'; endif; ?>>Sakit</option>
                            <option value="Urusan Keluarga" <?php if(old('alasan') === 'Urusan Keluarga'): echo 'selected'; endif; ?>>Urusan Keluarga</option>
                            <option value="Lamaran/Menikah" <?php if(old('alasan') === 'Lamaran/Menikah'): echo 'selected'; endif; ?>>Lamaran/Menikah</option>
                            <option value="Anggota Keluarga Meninggal" <?php if(old('alasan') === 'Anggota Keluarga Meninggal'): echo 'selected'; endif; ?>>Anggota Keluarga Meninggal</option>
                            <option value="Anggota Keluarga Sakit" <?php if(old('alasan') === 'Anggota Keluarga Sakit'): echo 'selected'; endif; ?>>Anggota Keluarga Sakit</option>
                            <option value="Anggota Keluarga Menikah" <?php if(old('alasan') === 'Anggota Keluarga Menikah'): echo 'selected'; endif; ?>>Anggota Keluarga Menikah</option>
                            <option value="Kegiatan Agama atau Budaya" <?php if(old('alasan') === 'Kegiatan Agama atau Budaya'): echo 'selected'; endif; ?>>Kegiatan Agama atau Budaya</option>
                            <option value="Musibah/Bencana" <?php if(old('alasan') === 'Musibah/Bencana'): echo 'selected'; endif; ?>>Musibah/Bencana</option>
                            <option value="Alasan Lainnya" <?php if(old('alasan') === 'Alasan Lainnya'): echo 'selected'; endif; ?>>Alasan Lainnya</option>
                        </select>
                        <?php $__errorArgs = ['alasan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="field-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div id="alasan_lainnya_wrapper" style="display: none;">
                        <label for="alasan_lainnya">Sebutkan Alasan Lainnya</label>
                        <input id="alasan_lainnya" type="text" name="alasan_lainnya" value="<?php echo e(old('alasan_lainnya')); ?>" placeholder="Tuliskan alasan cuti">
                        <?php $__errorArgs = ['alasan_lainnya'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="field-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="id_pengganti">Pendamping Pengganti</label>
                        <select id="id_pengganti" name="id_pengganti" required>
                            <option value="">-- Pilih Petugas Pengganti --</option>
                            <?php $__currentLoopData = $petugasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->id_user); ?>" <?php if(old('id_pengganti') == $p->id_user): echo 'selected'; endif; ?>><?php echo e($p->nama); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['id_pengganti'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="field-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="cuti-full">
                        <label for="alamat_cuti">Alamat Selama Cuti</label>
                        <textarea id="alamat_cuti" name="alamat_cuti" class="cuti-textarea" required placeholder="Tuliskan alamat lengkap selama masa cuti..."><?php echo e(old('alamat_cuti')); ?></textarea>
                        <?php $__errorArgs = ['alamat_cuti'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="field-error"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="cuti-actions">
                    <div class="muted">Pastikan tanggal dan petugas pengganti sudah benar sebelum dikirim.</div>
                    <button type="submit" class="cuti-submit">Kirim Pengajuan</button>
                </div>
            </form>
        </section>

        <aside class="cuti-side">
            <section class="cuti-panel">
                <div class="cuti-panel-head">
                    <div>
                        <h2>Kuota Cuti</h2>
                        <p>Ringkasan pemakaian cuti tahun ini.</p>
                    </div>
                </div>

                <div class="cuti-summary">
                    <div class="cuti-summary-item">
                        <div class="cuti-summary-label">Terpakai</div>
                        <div class="cuti-summary-value"><?php echo e($cutiTerpakaiTahunIni); ?></div>
                        <div class="cuti-summary-note">Dari batas <?php echo e($batasCutiTahunan); ?> kali cuti.</div>
                    </div>
                    <div class="cuti-summary-item">
                        <div class="cuti-summary-label">Sisa</div>
                        <div class="cuti-summary-value"><?php echo e($sisaCuti); ?></div>
                        <div class="cuti-summary-note">Sisa kesempatan cuti tahun ini.</div>
                    </div>
                </div>
            </section>
        </aside>
    </div>

    <section class="cuti-panel">
        <div class="cuti-panel-head">
            <div>
                <h2>Riwayat Pengajuan</h2>
                <p>Pantau status pengajuan cuti dan cetak surat jika sudah disetujui.</p>
            </div>
            <span class="cuti-panel-badge"><?php echo e($items->total() ?? $items->count()); ?> Data</span>
        </div>

        <div class="cuti-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Jenis</th>
                        <th>Alasan</th>
                        <th>Pengganti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="cuti-date"><?php echo e($item->tanggal_mulai->format('d/m/Y')); ?></td>
                            <td class="cuti-date"><?php echo e($item->tanggal_selesai->format('d/m/Y')); ?></td>
                            <td><?php echo e($item->jenis_cuti); ?></td>
                            <td class="cuti-reason">
                                <?php echo e($item->alasan); ?>

                                <?php if($item->alasan == 'Alasan Lainnya'): ?>
                                    <br><small class="muted">(<?php echo e($item->alasan_lainnya); ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($item->pengganti->nama ?? '-'); ?></td>
                            <td><span class="badge <?php echo e($item->status); ?>"><?php echo e($item->status); ?></span></td>
                            <td>
                                <?php if($item->status === 'approve'): ?>
                                    <a href="<?php echo e(route('petugas.cuti.print', $item->id_cuti)); ?>" target="_blank" class="cuti-action-link">Cetak Surat</a>
                                <?php else: ?>
                                    <span class="muted" style="font-size: 11px;">Menunggu Approval</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="muted cuti-empty">Belum ada pengajuan cuti.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php echo e($items->links('pagination.simple')); ?>

    </section>
</div>

<script>
    var alasanSelect = document.getElementById('alasan_select');
    var wrapper = document.getElementById('alasan_lainnya_wrapper');

    function syncAlasanLainnya() {
        if (!alasanSelect || !wrapper) return;

        var input = wrapper.querySelector('input');
        var isLainnya = alasanSelect.value === 'Alasan Lainnya';
        wrapper.style.display = isLainnya ? 'block' : 'none';
        if (input) input.required = isLainnya;
    }

    if (alasanSelect) {
        alasanSelect.addEventListener('change', syncAlasanLainnya);
        syncAlasanLainnya();
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/petugas/cuti.blade.php ENDPATH**/ ?>