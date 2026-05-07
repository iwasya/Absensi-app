@extends('layouts.app')

@section('title', 'Dashboard Petugas')

@section('content')
    <h1>Dashboard Petugas</h1>
    <div class="grid">
        <div class="stat">Status hari ini<strong>{{ $absensiHariIni ? $absensiHariIni->status : 'Belum absen' }}</strong></div>
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
