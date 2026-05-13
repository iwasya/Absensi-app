<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Izin Cuti - <?php echo e($cuti->user->nama); ?></title>
    <style>
        @page {
            size: A4;
            margin: 4cm 3cm 4cm 3cm;
        }
        body { 
            font-family: 'Times New Roman', serif; 
            line-height: 1.5; 
            color: #000; 
            margin: 0; 
            font-size: 12pt; 
            background: white;
        }
        .letter-wrapper {
            width: 100%;
        }
        .kop-surat { text-align: center; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 2px; }
        .kop-surat-line { border-bottom: 1px solid #000; margin-top: 2px; margin-bottom: 20px; }
        .kop-surat h2 { margin: 0; font-size: 14pt; text-transform: uppercase; font-weight: bold; }
        .kop-surat h3 { margin: 0; font-size: 16pt; text-transform: uppercase; font-weight: bold; }
        .kop-surat p { margin: 2px 0; font-size: 10pt; }
        .nomor-surat { text-align: center; margin-bottom: 25px; margin-top: 10px; }
        .nomor-surat h3 { margin: 0; text-decoration: underline; text-transform: uppercase; font-size: 12pt; font-weight: bold; }
        .content { margin-bottom: 30px; }
        .content p { margin: 10px 0; text-align: justify; text-indent: 40px; }
        .data-table { width: 100%; margin: 15px 0; border-collapse: collapse; }
        .data-table td { padding: 3px 0; vertical-align: top; }
        .data-table td:first-child { width: 180px; padding-left: 40px; }
        .signature-wrapper { margin-top: 30px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 220px; position: relative; }
        .signature-space { height: 70px; }
        .stamp { position: absolute; top: 5px; left: 50%; transform: translateX(-50%); opacity: 0.5; pointer-events: none; }
        .footer-note { 
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8pt; 
            font-style: italic; 
            border-top: 1px solid #ddd; 
            padding-top: 5px; 
            color: #666; 
            text-align: center;
        }
        @media print {
            .no-print { display: none; }
            body { background: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="letter-wrapper">
        <div class="kop-surat">
            <h2>PEMERINTAH PROVINSI DKI JAKARTA</h2>
            <h3>DINAS LINGKUNGAN HIDUP</h3>
            <p>Jl. Mandala V No.12, Cililitan, Kec. Kramat jati, Kota Jakarta Timur, DKI Jakarta 13640</p>
        </div>
        <div class="kop-surat-line"></div>

    <div class="nomor-surat">
        <h3>SURAT IZIN <?php echo e(strtoupper($cuti->jenis_cuti)); ?></h3>
        <p>Nomor: <?php echo e(date('Y')); ?>/SC-DLH/<?php echo e($cuti->id_cuti); ?></p>
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini, menerangkan bahwa:</p>
        <table class="data-table">
            <tr><td>Nama</td><td>: <strong><?php echo e($cuti->user->nama); ?></strong></td></tr>
            <tr><td>NIK / Username</td><td>: <?php echo e($cuti->user->username); ?></td></tr>
            <tr><td>Unit Kerja</td><td>: <?php echo e($cuti->user->tempatTugas->nama_tempat ?? '-'); ?></td></tr>
            <tr><td>Jabatan</td><td>: <?php echo e($cuti->user->role->nama_role ?? '-'); ?></td></tr>
        </table>

        <p>Telah mengajukan permohonan cuti dan <strong>DISETUJUI</strong> untuk melaksanakan cuti dengan rincian sebagai berikut:</p>
        <table class="data-table">
            <tr><td>Jenis Cuti</td><td>: <?php echo e($cuti->jenis_cuti); ?></td></tr>
            <tr><td>Mulai Tanggal</td><td>: <?php echo e($cuti->tanggal_mulai->translatedFormat('d F Y')); ?></td></tr>
            <tr><td>Sampai Tanggal</td><td>: <?php echo e($cuti->tanggal_selesai->translatedFormat('d F Y')); ?></td></tr>
            <tr><td>Lama Cuti</td><td>: <?php echo e($cuti->tanggal_mulai->diffInDays($cuti->tanggal_selesai) + 1); ?> Hari Kerja</td></tr>
            <tr><td>Alasan Cuti</td><td>: <?php echo e($cuti->alasan); ?> <?php echo e($cuti->alasan_lainnya ? '('.$cuti->alasan_lainnya.')' : ''); ?></td></tr>
            <tr><td>Alamat Selama Cuti</td><td>: <?php echo e($cuti->alamat_cuti); ?></td></tr>
            <tr><td>Petugas Pengganti</td><td>: <?php echo e($cuti->pengganti->nama ?? '-'); ?></td></tr>
        </table>

        <p>Demikian surat izin cuti ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div style="text-align: right; margin-bottom: 20px;">
        Jakarta, <?php echo e(now()->translatedFormat('d F Y')); ?>

    </div>

    <div class="signature-wrapper">
        <div class="signature-box">
            <p>Petugas Pengganti,</p>
            <div class="signature-space">
                <!-- Placeholder for digital signature if needed -->
                <p style="margin-top: 40px; color: #ccc; font-size: 8pt;">(Tanda Tangan Digital)</p>
            </div>
            <p><strong><?php echo e($cuti->pengganti->nama ?? '..........................'); ?></strong></p>
        </div>

        <div class="signature-box">
            <p>Menyetujui,<br>Atasan Langsung</p>
            <div class="signature-space">
                <div class="stamp">
                    <svg width="100" height="100" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="blue" stroke-width="2" stroke-dasharray="5,5" />
                        <text x="50" y="45" font-family="Arial" font-size="8" text-anchor="middle" fill="blue">DLH PROV DKI</text>
                        <text x="50" y="60" font-family="Arial" font-size="10" font-weight="bold" text-anchor="middle" fill="blue">APPROVED</text>
                    </svg>
                </div>
                <p style="margin-top: 40px; color: #ccc; font-size: 8pt;">(Tanda Tangan Digital)</p>
            </div>
            <p><strong><?php echo e($cuti->approver->nama ?? '..........................'); ?></strong></p>
        </div>
    </div>

    <div class="signature-wrapper" style="justify-content: center; margin-top: 30px;">
        <div class="signature-box">
            <p>Hormat Saya,</p>
            <div class="signature-space"></div>
            <p><strong><?php echo e($cuti->user->nama); ?></strong></p>
        </div>
    </div>

    <div class="footer-note">
        * Dokumen ini diterbitkan secara otomatis oleh Sistem Absensi PPSU dan dianggap sah secara digital.
    </div>
    </div>

    <div class="no-print" style="margin-top: 40px; text-align: center; background: #f3f4f6; padding: 20px; border-radius: 8px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #059669; color: white; border: none; border-radius: 4px; font-weight: bold;">Cetak Surat Cuti</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; background: #6b7280; color: white; border: none; border-radius: 4px; font-weight: bold; margin-left: 10px;">Tutup</button>
    </div>
</body>
</html>
<?php /**PATH D:\Project_absensi\Absensi-app\resources\views/petugas/cuti_print.blade.php ENDPATH**/ ?>