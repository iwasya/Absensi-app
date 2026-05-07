@extends('layouts.app')

@section('title', 'Input Tugas Harian')

@section('content')
    <h1>Input Tugas Harian</h1>

    @if($jadwalHariIni->isNotEmpty())
        <div class="panel">
            <h2>Kalender Hari Ini</h2>
            @foreach($jadwalHariIni as $jadwal)
                <p>
                    <span class="badge {{ $jadwal->jenis_event }}">{{ $jadwal->jenis_event }}</span>
                    {{ $jadwal->nama_event ?? '-' }}
                    <span class="muted">{{ $jadwal->keterangan }}</span>
                </p>
            @endforeach
        </div>
    @endif

    <div class="panel">
        <h2>Kirim Laporan</h2>
        <p class="muted">Periode aktif: {{ $periodeAktif?->nama_periode ?? '-' }}</p>
        <form method="POST" action="{{ route('petugas.tugas.store') }}">
            @csrf
            <div class="form-grid">
                <div><label>Mulai</label><input type="datetime-local" name="tanggal_mulai" required></div>
                <div><label>Selesai</label><input type="datetime-local" name="tanggal_selesai"></div>
            </div>
            <label style="margin-top:12px">Uraian</label>
            <textarea name="uraian" required></textarea>
            <button type="submit" style="margin-top:12px">Kirim Laporan</button>
        </form>
    </div>
@endsection
