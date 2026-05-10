<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pantau Absensi Petugas</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 15px; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { margin: 0; font-size: 18px; }
        .info { margin-bottom: 15px; }
        .info table { width: auto; border: none; }
        .info td { padding: 2px 10px 2px 0; border: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 40px; text-align: right; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>LAPORAN MONITORING ABSENSI PETUGAS</h1>
        <p>Aplikasi Absensi PPSU</p>
    </div>

    <div class="info">
        <table>
            <tr><td><strong>Petugas</strong></td><td>: {{ $selectedUser ? $selectedUser->nama : 'Semua Petugas' }}</td></tr>
            <tr><td><strong>Bulan</strong></td><td>: {{ $month ? \Carbon\Carbon::create()->month($month)->translatedFormat('F') : 'Semua Data' }}</td></tr>
            <tr><td><strong>Tanggal Cetak</strong></td><td>: {{ now()->translatedFormat('d F Y H:i') }}</td></tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Petugas</th>
                <th>Tempat Tugas</th>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->user->nama ?? '-' }}</td>
                    <td>{{ $item->user->tempatTugas->nama_tempat ?? '-' }}</td>
                    <td>{{ $item->tanggal->translatedFormat('d/m/Y') }}</td>
                    <td>{{ $item->jam_masuk ?? '-' }}</td>
                    <td>{{ $item->jam_pulang ?? '-' }}</td>
                    <td>{{ strtoupper($item->status) }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data absensi ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak secara otomatis oleh Sistem Absensi</p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Cetak Ulang</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Tutup Halaman</button>
    </div>
</body>
</html>
