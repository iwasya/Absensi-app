@extends('layouts.app')

@section('title', 'Detail Absensi')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1>Detail Absensi - {{ $item->tanggal->translatedFormat('d F Y') }}</h1>
        <a href="{{ url()->previous() }}" class="back-btn">Kembali</a>
    </div>

    <div class="grid">
        <div class="panel">
            <h2>Informasi Petugas</h2>
            <table class="detail-table">
                <tr><th>Nama</th><td>: {{ $item->user->nama }}</td></tr>
                <tr><th>Username</th><td>: {{ $item->user->username }}</td></tr>
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
                <div class="photo-wrapper">
                    <img src="{{ asset('storage/' . $item->foto_masuk) }}" alt="Foto Masuk">
                </div>
            @else
                <p class="muted">Tidak ada foto masuk.</p>
            @endif
            <table class="detail-table">
                <tr><th>Lokasi</th><td>: {{ $item->lokasi_masuk ?? '-' }}</td></tr>
                <tr><th>Koordinat</th><td>: {{ $item->latitude_masuk ?? '-' }}, {{ $item->longitude_masuk ?? '-' }}</td></tr>
                @if($item->latitude_masuk)
                    <tr>
                        <td colspan="2">
                            <a href="https://www.google.com/maps?q={{ $item->latitude_masuk }},{{ $item->longitude_masuk }}" target="_blank" class="map-link">Lihat di Google Maps</a>
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="panel">
            <h2>Data Pulang</h2>
            @if($item->foto_pulang)
                <div class="photo-wrapper">
                    <img src="{{ asset('storage/' . $item->foto_pulang) }}" alt="Foto Pulang">
                </div>
            @else
                <p class="muted">Tidak ada foto pulang.</p>
            @endif
            <table class="detail-table">
                <tr><th>Lokasi</th><td>: {{ $item->lokasi_pulang ?? '-' }}</td></tr>
                <tr><th>Koordinat</th><td>: {{ $item->latitude_pulang ?? '-' }}, {{ $item->longitude_pulang ?? '-' }}</td></tr>
                @if($item->latitude_pulang)
                    <tr>
                        <td colspan="2">
                            <a href="https://www.google.com/maps?q={{ $item->latitude_pulang }},{{ $item->longitude_pulang }}" target="_blank" class="map-link">Lihat di Google Maps</a>
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    </div>

    <style>
        .detail-table { width: 100%; border-collapse: collapse; }
        .detail-table th { text-align: left; width: 140px; padding: 8px 0; font-weight: 600; color: var(--muted); }
        .detail-table td { padding: 8px 0; color: var(--text-color); }

        /* Back button - dark mode compatible */
        .back-btn {
            display: inline-block;
            background: var(--soft-bg);
            color: var(--soft-text);
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            border: 1px solid var(--border-color);
            transition: all 0.2s;
        }
        .back-btn:hover {
            filter: brightness(0.95);
            color: var(--text-color);
        }

        /* Photo wrapper - dark mode compatible */
        .photo-wrapper {
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
            margin-bottom: 16px;
            border: 4px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: var(--soft-bg);
        }
        .photo-wrapper img {
            width: 100%;
            display: block;
        }

        /* Map link - dark mode compatible */
        .map-link {
            color: var(--primary);
            text-decoration: underline;
        }
        .map-link:hover {
            filter: brightness(1.2);
        }
    </style>
@endsection