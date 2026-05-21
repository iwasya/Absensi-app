@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <h1>Dashboard Admin</h1>
    <div class="grid">
        <div class="stat">Total user<strong>{{ $totalUsers }}</strong></div>
        <div class="stat">Petugas PPSU<strong>{{ $totalPetugas }}</strong></div>
        <div class="stat">Absensi hari ini<strong>{{ $totalAbsensiHariIni }}</strong></div>
        <div class="stat">Cuti pending<strong>{{ $cutiPending }}</strong></div>
        <div class="stat">Cuti pending admin<strong>{{ $cutiPendingAdmin ?? 0 }}</strong></div>
        <div class="stat">Approval pulang<strong>{{ $approvalPulangPending ?? 0 }}</strong></div>
        <div class="stat">Tugas pending<strong>{{ $tugasPending }}</strong></div>
        <div class="stat">Periode aktif<strong>{{ $periodeAktif?->nama_periode ?? '-' }}</strong></div>
    </div>
@endsection
