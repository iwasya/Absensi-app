@extends('layouts.app')

@section('title', 'Lap. Tugas Harian')

@section('content')
    <h1>Lap. Tugas Harian</h1>
    <div class="panel">
        <p class="muted">Riwayat laporan tugas harian kamu. Laporan akan muncul di akun atasan untuk approve/reject.</p>
        <a href="{{ route('petugas.tugas.input') }}">Input tugas baru</a>
    </div>

    <!-- @include('partials.periode-filter') -->

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
    {{ $items->links('pagination.simple') }}
@endsection
