@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
    <h1>Absensi</h1>
    @include('partials.periode-filter')

    <div class="panel">
        <h2>Hari Ini</h2>
        <p class="muted">Periode aktif: {{ $periodeAktif?->nama_periode ?? '-' }}</p>
        <p>Status: <span class="badge {{ $today?->status }}">{{ $today?->status ?? 'belum_absen' }}</span></p>
        <p>Masuk: {{ $today?->jam_masuk ?? '-' }} | Pulang: {{ $today?->jam_pulang ?? '-' }}</p>
    </div>

    <div class="grid">
        <div class="panel">
            <h2>Absen Masuk</h2>
            <form method="POST" action="{{ route('petugas.absensi.masuk') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-grid">
                    <div><label>Foto Masuk</label><input type="file" name="foto_masuk" accept="image/*"></div>
                    <div><label>Latitude</label><input name="latitude_masuk" id="lat_masuk"></div>
                    <div><label>Longitude</label><input name="longitude_masuk" id="lng_masuk"></div>
                    <div><label>Lokasi</label><input name="lokasi_masuk"></div>
                </div>
                <label style="margin-top:12px">Keterangan</label>
                <textarea name="keterangan"></textarea>
                <button type="submit" style="margin-top:12px">Simpan Masuk</button>
            </form>
        </div>

        <div class="panel">
            <h2>Absen Pulang</h2>
            <form method="POST" action="{{ route('petugas.absensi.pulang') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-grid">
                    <div><label>Foto Pulang</label><input type="file" name="foto_pulang" accept="image/*"></div>
                    <div><label>Latitude</label><input name="latitude_pulang" id="lat_pulang"></div>
                    <div><label>Longitude</label><input name="longitude_pulang" id="lng_pulang"></div>
                    <div><label>Lokasi</label><input name="lokasi_pulang"></div>
                </div>
                <button type="submit" style="margin-top:12px">Simpan Pulang</button>
            </form>
        </div>
    </div>

    <table>
        <thead><tr><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th><th>Lokasi</th><th>Keterangan</th></tr></thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->jam_masuk ?? '-' }}</td>
                    <td>{{ $item->jam_pulang ?? '-' }}</td>
                    <td><span class="badge {{ $item->status }}">{{ $item->status }}</span></td>
                    <td>{{ $item->lokasi_masuk ?? '-' }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">Belum ada riwayat absensi.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}

    <script>
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                ['masuk', 'pulang'].forEach(function (type) {
                    var lat = document.getElementById('lat_' + type);
                    var lng = document.getElementById('lng_' + type);
                    if (lat && lng) {
                        lat.value = position.coords.latitude.toFixed(8);
                        lng.value = position.coords.longitude.toFixed(8);
                    }
                });
            });
        }
    </script>
@endsection
