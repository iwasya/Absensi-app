@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
    <h1>Absensi</h1>
    <!-- @include('partials.periode-filter') -->

    <div class="panel">
        <h2>Hari Ini</h2>
        <p class="muted">Periode aktif: {{ $periodeAktif?->nama_periode ?? '-' }}</p>
        <p>Status: <span class="badge {{ $today?->status }}">{{ $today?->status ?? 'belum_absen' }}</span></p>
        <p>Masuk: {{ $today?->jam_masuk ?? '-' }} | Pulang: {{ $today?->jam_pulang ?? '-' }}</p>
    </div>

    <div class="grid">
        <div class="panel">
            <h2>Absen Masuk <span style="font-size:12px; font-weight:normal; color:#6b7280;">(Buka: 07:00 - 07:15)</span></h2>
            @if($today?->jam_masuk)
                <div class="success" style="margin-top:12px;">Anda sudah absen masuk hari ini pada {{ $today->jam_masuk }}.</div>
            @elseif(now()->format('H:i:s') > '07:15:00' && $today?->status !== 'akses_dibuka')
                <div class="error" style="margin-top:12px;">Waktu absen masuk telah habis. Anda tercatat Tidak Absen.</div>
            @elseif(now()->format('H:i:s') < '07:00:00' && $today?->status !== 'akses_dibuka')
                <div class="muted" style="margin-top:12px; padding:12px; background:#f3f4f6; border-radius:6px;">Absen masuk belum dibuka. Silakan kembali jam 07:00.</div>
            @else
                @if($today?->status === 'akses_dibuka')
                    <div class="success" style="margin-top:12px; font-weight:bold; padding:12px; background:#ecfdf5; border:1px solid #a7f3d0; color:#047857; border-radius:6px; margin-bottom:12px;">Akses khusus diberikan oleh Admin. Anda dapat melakukan absen telat.</div>
                @endif
                <form id="form_masuk" method="POST" action="{{ route('petugas.absensi.masuk') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid">
                        <div style="grid-column: 1 / -1;">
                            <label>Foto Masuk (Kamera)</label>
                            <div class="camera-wrap" id="camera_wrap_masuk">
                                <video id="video_masuk" width="100%" style="max-width:400px; background:#111; border-radius:8px; display:none;" autoplay playsinline></video>
                                <canvas id="canvas_masuk" style="display:none;"></canvas>
                                <img id="photo_masuk" style="width:100%; max-width:400px; border-radius:8px; display:none; margin-bottom:8px;" />
                                <input type="hidden" name="foto_masuk" id="foto_masuk_input">
                                <div style="margin-top:8px; display:flex; gap:8px;">
                                    <button type="button" id="btn_open_cam_masuk" class="dark">Buka Kamera</button>
                                    <button type="button" id="btn_capture_masuk" style="display:none; background:#059669;">Ambil Foto</button>
                                    <button type="button" id="btn_retake_masuk" class="danger" style="display:none;">Ulangi Foto</button>
                                </div>
                            </div>
                        </div>
                        <div><label>Latitude</label><input name="latitude_masuk" id="lat_masuk"></div>
                        <div><label>Longitude</label><input name="longitude_masuk" id="lng_masuk"></div>
                        <div><label>Lokasi</label><input name="lokasi_masuk"></div>
                    </div>
                    <label style="margin-top:12px">Keterangan</label>
                    <textarea name="keterangan"></textarea>
                    <button type="submit" style="margin-top:12px">Simpan Masuk</button>
                </form>
            @endif
        </div>

        <div class="panel">
            <h2>Absen Pulang <span style="font-size:12px; font-weight:normal; color:#6b7280;">(Buka: 16:00 - 23:59)</span></h2>
            @if(!$today?->jam_masuk && $today?->status !== 'tidak_absen')
                <div class="muted" style="margin-top:12px; padding:12px; background:#f3f4f6; border-radius:6px;">Silakan absen masuk terlebih dahulu.</div>
            @elseif($today?->jam_pulang)
                <div class="success" style="margin-top:12px;">Anda sudah absen pulang hari ini pada {{ $today->jam_pulang }}.</div>
            @elseif($today?->status === 'tidak_absen' || (now()->format('H:i:s') > '07:15:00' && !$today?->jam_masuk && $today?->status !== 'akses_dibuka'))
                <div class="error" style="margin-top:12px;">Anda tercatat Tidak Absen hari ini sehingga tidak bisa absen pulang.</div>
            @elseif(now()->format('H:i:s') < '16:00:00')
                <div class="muted" style="margin-top:12px; padding:12px; background:#f3f4f6; border-radius:6px;">Absen pulang belum dibuka. Silakan kembali jam 16:00.</div>
            @else
                <form id="form_pulang" method="POST" action="{{ route('petugas.absensi.pulang') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid">
                        <div style="grid-column: 1 / -1;">
                            <label>Foto Pulang (Kamera)</label>
                            <div class="camera-wrap" id="camera_wrap_pulang">
                                <video id="video_pulang" width="100%" style="max-width:400px; background:#111; border-radius:8px; display:none;" autoplay playsinline></video>
                                <canvas id="canvas_pulang" style="display:none;"></canvas>
                                <img id="photo_pulang" style="width:100%; max-width:400px; border-radius:8px; display:none; margin-bottom:8px;" />
                                <input type="hidden" name="foto_pulang" id="foto_pulang_input">
                                <div style="margin-top:8px; display:flex; gap:8px;">
                                    <button type="button" id="btn_open_cam_pulang" class="dark">Buka Kamera</button>
                                    <button type="button" id="btn_capture_pulang" style="display:none; background:#059669;">Ambil Foto</button>
                                    <button type="button" id="btn_retake_pulang" class="danger" style="display:none;">Ulangi Foto</button>
                                </div>
                            </div>
                        </div>
                        <div><label>Latitude</label><input name="latitude_pulang" id="lat_pulang"></div>
                        <div><label>Longitude</label><input name="longitude_pulang" id="lng_pulang"></div>
                        <div><label>Lokasi</label><input name="lokasi_pulang"></div>
                    </div>
                    <button type="submit" style="margin-top:12px">Simpan Pulang</button>
                </form>
            @endif
        </div>
    </div>

    <div class="panel" style="margin-top: 24px; margin-bottom: 24px;">
        <form action="{{ route('petugas.absensi.index') }}" method="GET" class="filter-bar">
            <div class="filter-control">
                <label>Bulan</label>
                <select name="month">
                    <option value="">-- Pilih Bulan --</option>
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="filter-control">
                <label>Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Status atau keterangan...">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit">Tampilkan</button>
                <a href="{{ route('petugas.absensi.index') }}" class="button" style="background:#f3f4f6; color:#374151; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Reset</a>
                <a href="{{ route('petugas.absensi.print', request()->all()) }}" target="_blank" style="background: #059669; color: white; padding: 10px 15px; border-radius: 6px; font-weight: bold;">Cetak</a>
            </div>
        </form>
    </div>

    <table>
        <thead><tr><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->jam_masuk ?? '-' }}</td>
                    <td>{{ $item->jam_pulang ?? '-' }}</td>
                    <td><span class="badge {{ $item->status }}">{{ $item->status }}</span></td>
                    <td>
                        <a href="{{ route('absensi.detail', $item->id_absensi) }}" class="button" style="padding: 4px 8px; font-size: 12px; background: #6366f1; color: white; border-radius: 4px; text-decoration: none;">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Belum ada riwayat absensi.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}

    <script>
        function calculateDistance(lat1, lon1, lat2, lon2) {
            var R = 6371e3; // metres
            var dLat = (lat2-lat1) * Math.PI / 180;
            var dLon = (lon2-lon1) * Math.PI / 180;
            var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon/2) * Math.sin(dLon/2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        var tempatLat = {{ isset($tempatTugas) && $tempatTugas->latitude ? $tempatTugas->latitude : 'null' }};
        var tempatLng = {{ isset($tempatTugas) && $tempatTugas->longitude ? $tempatTugas->longitude : 'null' }};
        var namaTempat = "{{ isset($tempatTugas) ? $tempatTugas->nama_tempat : '' }}";

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var userLat = position.coords.latitude;
                var userLng = position.coords.longitude;
                
                var inRange = true;
                if (tempatLat !== null && tempatLng !== null) {
                    var dist = calculateDistance(userLat, userLng, tempatLat, tempatLng);
                    if (dist > 100) {
                        inRange = false;
                    }
                }

                ['masuk', 'pulang'].forEach(function (type) {
                    var lat = document.getElementById('lat_' + type);
                    var lng = document.getElementById('lng_' + type);
                    var loc = document.querySelector('input[name="lokasi_' + type + '"]');

                    if (lat && lng) {
                        lat.value = userLat.toFixed(8);
                        lng.value = userLng.toFixed(8);
                    }
                    
                    if (loc) {
                        loc.readOnly = true;
                        if (!inRange) {
                            loc.value = "Di luar area kantor";
                            loc.style.color = "red";
                            loc.style.fontWeight = "bold";
                        } else {
                            loc.value = namaTempat || "Area Kantor";
                            loc.style.color = "green";
                            loc.style.fontWeight = "bold";
                        }
                    }
                });

                if (!inRange) {
                    alert('Anda berada di luar area kantor! Jarak Anda terlalu jauh dari lokasi yang diizinkan.');
                    document.querySelectorAll('button[type="submit"]').forEach(function(btn) {
                        btn.disabled = true;
                        btn.style.opacity = '0.5';
                        btn.style.cursor = 'not-allowed';
                    });
                }
            });
        }

        function setupCamera(type) {
            var video = document.getElementById('video_' + type);
            var canvas = document.getElementById('canvas_' + type);
            var photo = document.getElementById('photo_' + type);
            var input = document.getElementById('foto_' + type + '_input');
            
            var btnOpen = document.getElementById('btn_open_cam_' + type);
            var btnCapture = document.getElementById('btn_capture_' + type);
            var btnRetake = document.getElementById('btn_retake_' + type);
            
            var stream = null;

            if (!btnOpen) return;

            btnOpen.addEventListener('click', async function() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                    video.srcObject = stream;
                    video.style.display = 'block';
                    photo.style.display = 'none';
                    btnOpen.style.display = 'none';
                    btnCapture.style.display = 'block';
                    btnRetake.style.display = 'none';
                    input.value = '';
                } catch (err) {
                    alert('Akses kamera ditolak atau perangkat kamera tidak ditemukan.');
                    console.error(err);
                }
            });

            btnCapture.addEventListener('click', function() {
                if (!stream) return;
                
                var maxWidth = 640;
                var maxHeight = 480;
                var width = video.videoWidth;
                var height = video.videoHeight;

                if (width > height) {
                    if (width > maxWidth) {
                        height *= maxWidth / width;
                        width = maxWidth;
                    }
                } else {
                    if (height > maxHeight) {
                        width *= maxHeight / height;
                        height = maxHeight;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                var context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, width, height);
                
                // Kompresi foto menjadi format JPEG dengan kualitas 70%
                var data = canvas.toDataURL('image/jpeg', 0.7);
                photo.src = data;
                input.value = data;
                
                video.style.display = 'none';
                photo.style.display = 'block';
                btnCapture.style.display = 'none';
                btnRetake.style.display = 'block';
                
                stream.getTracks().forEach(function(track) { track.stop(); });
                stream = null;
            });

            btnRetake.addEventListener('click', function() {
                btnOpen.click();
            });

            var form = document.getElementById('form_' + type);
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!input.value) {
                        e.preventDefault();
                        alert('Silakan ambil foto terlebih dahulu sebelum menyimpan absensi.');
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            setupCamera('masuk');
            setupCamera('pulang');
        });
    </script>
@endsection
