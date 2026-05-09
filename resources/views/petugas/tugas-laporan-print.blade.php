<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tugas Harian - {{ $user->nama }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 18px; }
        .info { margin-bottom: 20px; }
        .info table { width: auto; border: none; }
        .info td { padding: 2px 10px 2px 0; border: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .footer { margin-top: 50px; text-align: right; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>LAPORAN TUGAS HARIAN PETUGAS</h1>
        <p>Aplikasi Absensi PPSU</p>
    </div>

    <div class="info">
        <table>
            <tr><td><strong>Nama</strong></td><td>: {{ $user->nama }}</td></tr>
            <tr><td><strong>NIK/Username</strong></td><td>: {{ $user->username }}</td></tr>
            <tr><td><strong>Bulan</strong></td><td>: {{ $month ? \Carbon\Carbon::create()->month($month)->translatedFormat('F') : 'Semua Data' }}</td></tr>
            <tr><td><strong>Tanggal Cetak</strong></td><td>: {{ now()->translatedFormat('d F Y H:i') }}</td></tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Uraian Tugas</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->tanggal_mulai->translatedFormat('d/m/Y H:i') }}</td>
                    <td>{{ $item->tanggal_selesai?->translatedFormat('d/m/Y H:i') ?? '-' }}</td>
                    <td>{{ $item->uraian }}</td>
                    <td>{{ strtoupper($item->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data laporan tugas ditemukan.</td>
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
