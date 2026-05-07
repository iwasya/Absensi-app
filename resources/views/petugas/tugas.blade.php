@extends('layouts.app')

@section('title', 'Laporan Tugas')

@section('content')
    <h1>Laporan Tugas</h1>
    <div class="panel">
        <h2>Kirim Laporan</h2>
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

    <table>
        <thead><tr><th>Mulai</th><th>Selesai</th><th>Uraian</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->tanggal_mulai->format('d/m/Y H:i') }}</td>
                    <td>{{ $item->tanggal_selesai?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>{{ $item->uraian }}</td>
                    <td><span class="badge {{ $item->status }}">{{ $item->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Belum ada laporan tugas.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
