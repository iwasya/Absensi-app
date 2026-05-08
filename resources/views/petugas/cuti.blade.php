@extends('layouts.app')

@section('title', 'Pengajuan Cuti')

@section('content')
    <h1>Pengajuan Cuti</h1>
    <div class="panel">
        <h2>Ajukan Cuti</h2>
        <form method="POST" action="{{ route('petugas.cuti.store') }}">
            @csrf
            <div class="form-grid">
                <div><label>Tanggal Mulai</label><input type="date" name="tanggal_mulai" required></div>
                <div><label>Tanggal Selesai</label><input type="date" name="tanggal_selesai" required></div>
                <div><label>Jenis Cuti</label><input name="jenis_cuti" placeholder="Tahunan, sakit, izin"></div>
            </div>
            <label style="margin-top:12px">Alasan</label>
            <textarea name="alasan"></textarea>
            <button type="submit" style="margin-top:12px">Kirim Pengajuan</button>
        </form>
    </div>

    <!-- @include('partials.periode-filter') -->

    <table>
        <thead><tr><th>Mulai</th><th>Selesai</th><th>Jenis</th><th>Alasan</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->tanggal_mulai->format('d/m/Y') }}</td>
                    <td>{{ $item->tanggal_selesai->format('d/m/Y') }}</td>
                    <td>{{ $item->jenis_cuti ?? '-' }}</td>
                    <td>{{ $item->alasan ?? '-' }}</td>
                    <td><span class="badge {{ $item->status }}">{{ $item->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Belum ada pengajuan cuti.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
