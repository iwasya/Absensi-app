@extends('layouts.app')

@section('title', 'Dashboard Atasan')

@section('content')
    <h1>Dashboard Atasan</h1>
    <div class="grid">
        <div class="stat">Cuti pending<strong>{{ $cutiPending->count() }}</strong></div>
        <div class="stat">Tugas pending<strong>{{ $tugasPending->count() }}</strong></div>
        <div class="stat">Absensi hari ini<strong>{{ $absensiHariIni->count() }}</strong></div>
    </div>

    <div class="panel">
        <h2>Absensi Hari Ini</h2>
        @foreach($absensiHariIni as $item)
            <p>{{ $item->user->nama ?? '-' }} - <span class="badge {{ $item->status }}">{{ $item->status }}</span> {{ $item->jam_masuk }}</p>
        @endforeach
    </div>
@endsection
