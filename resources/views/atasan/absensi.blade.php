@extends('layouts.app')

@section('title', 'Pantau Absensi')

@section('content')
    <h1>Pantau Absensi</h1>
    @include('partials.periode-filter')

    <table>
        <thead><tr><th>Petugas</th><th>Tempat</th><th>Tanggal</th><th>Masuk</th><th>Pulang</th><th>Status</th><th>Lokasi</th></tr></thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->user->nama ?? '-' }}</td>
                    <td>{{ $item->user->tempatTugas->nama_tempat ?? '-' }}</td>
                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->jam_masuk ?? '-' }}</td>
                    <td>{{ $item->jam_pulang ?? '-' }}</td>
                    <td><span class="badge {{ $item->status }}">{{ $item->status }}</span></td>
                    <td>{{ $item->lokasi_masuk ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">Belum ada data absensi.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
