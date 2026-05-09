@extends('layouts.app')

@section('title', 'Detail Absensi')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1>Detail Absensi - {{ $item->tanggal->translatedFormat('d F Y') }}</h1>
        <a href="{{ url()->previous() }}" class="button" style="background: #6b7280; color: white; padding: 8px 16px; border-radius: 6px; font-weight: bold; text-decoration: none;">Kembali</a>
    </div>

    <div class="grid">
        <div class="panel">
            <h2>Informasi Petugas</h2>
            <table class="detail-table">
                <tr><th>Nama</th><td>: {{ $item->user->nama }}</td></tr>
                <tr><th>NIK/Username</th><td>: {{ $item->user->username }}</td></tr>
                <tr><th>Tempat Tugas</th><td>: {{ $item->user->tempatTugas->nama_tempat ?? '-' }}</td></tr>
                <tr><th>Status</th><td>: <span class="badge {{ $item->status }}">{{ strtoupper($item->status) }}</span></td></tr>
                <tr><th>Keterangan</th><td>: {{ $item->keterangan ?? '-' }}</td></tr>
            </table>
        </div>

        <div class="panel">
            <h2>Waktu Absensi</h2>
            <table class="detail-table">
                <tr><th>Jam Masuk</th><td>: {{ $item->jam_masuk ?? '-' }}</td></tr>
                <tr><th>Jam Pulang</th><td>: {{ $item->jam_pulang ?? '-' }}</td></tr>
                <tr><th>Tanggal Data</th><td>: {{ $item->tanggal->translatedFormat('d/m/Y') }}</td></tr>
            </table>
        </div>
    </div>

    <div class="grid" style="margin-top: 24px;">
        <div class="panel">
            <h2>Data Masuk</h2>
            @if($item->foto_masuk)
                <img src="{{ asset('storage/' . $item->foto_masuk) }}" style="width: 100%; max-width: 400px; border-radius: 8px; margin-bottom: 16px; border: 4px solid #fff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            @else
                <p class="muted">Tidak ada foto masuk.</p>
            @endif
            <table class="detail-table">
                <tr><th>Lokasi</th><td>: {{ $item->lokasi_masuk ?? '-' }}</td></tr>
                <tr><th>Koordinat</th><td>: {{ $item->latitude_masuk ?? '-' }}, {{ $item->longitude_masuk ?? '-' }}</td></tr>
                @if($item->latitude_masuk)
                    <tr>
                        <td colspan="2">
                            <a href="https://www.google.com/maps?q={{ $item->latitude_masuk }},{{ $item->longitude_masuk }}" target="_blank" style="color: #2563eb; text-decoration: underline;">Lihat di Google Maps</a>
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="panel">
            <h2>Data Pulang</h2>
            @if($item->foto_pulang)
                <img src="{{ asset('storage/' . $item->foto_pulang) }}" style="width: 100%; max-width: 400px; border-radius: 8px; margin-bottom: 16px; border: 4px solid #fff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            @else
                <p class="muted">Tidak ada foto pulang.</p>
            @endif
            <table class="detail-table">
                <tr><th>Lokasi</th><td>: {{ $item->lokasi_pulang ?? '-' }}</td></tr>
                <tr><th>Koordinat</th><td>: {{ $item->latitude_pulang ?? '-' }}, {{ $item->longitude_pulang ?? '-' }}</td></tr>
                @if($item->latitude_pulang)
                    <tr>
                        <td colspan="2">
                            <a href="https://www.google.com/maps?q={{ $item->latitude_pulang }},{{ $item->longitude_pulang }}" target="_blank" style="color: #2563eb; text-decoration: underline;">Lihat di Google Maps</a>
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    </div>

    <style>
        .detail-table { width: 100%; border-collapse: collapse; }
        .detail-table th { text-align: left; width: 140px; padding: 8px 0; font-weight: 600; color: #4b5563; }
        .detail-table td { padding: 8px 0; color: #1f2937; }
    </style>
@endsection
