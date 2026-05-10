@extends('layouts.app')

@section('title', 'Dashboard Petugas')

@section('content')
    <h1>Dashboard Petugas</h1>

    <div id="realtime-clock" style="font-size: 1.25rem; font-weight: bold; color: #1f2937; background: #fff; padding: 12px 20px; border-radius: 8px; border: 1px solid #e5e7eb; display: inline-block; margin-bottom: 20px;">
        Memuat waktu...
    </div>
    
    <script>
        function updateClock() {
            const now = new Date();
            
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const dateStr = `${dayName}, ${date} ${monthName} ${year}`;
            const timeStr = `${hours}:${minutes}:${seconds}`;
            
            const clockEl = document.getElementById('realtime-clock');
            if (clockEl) {
                clockEl.innerHTML = `<div style="font-size: 0.9rem; color: #6b7280; font-weight: normal; margin-bottom: 4px;">${dateStr}</div><div>${timeStr}</div>`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    <div class="grid">
        <div class="stat">Status hari ini<strong>{{ $absensiHariIni ? ucwords(str_replace('_', ' ', $absensiHariIni->status)) : 'Tidak Absen' }}</strong></div>
        <div class="stat">Jam masuk<strong>{{ $absensiHariIni?->jam_masuk ?? '-' }}</strong></div>
        <div class="stat">Jam pulang<strong>{{ $absensiHariIni?->jam_pulang ?? '-' }}</strong></div>
        <div class="stat">Notifikasi belum dibaca<strong>{{ $notifikasiBelumBaca }}</strong></div>
    </div>

    <div class="panel">
        <h2>Kalender Terdekat</h2>
        @forelse($kalender as $item)
            <p><span class="badge">{{ $item->jenis_event }}</span> {{ $item->tanggal->format('d/m/Y') }} - {{ $item->nama_event }}</p>
        @empty
            <p class="muted">Belum ada event kalender.</p>
        @endforelse
    </div>
@endsection
