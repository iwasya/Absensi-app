@extends('layouts.app')

@section('title', 'Approve Tugas')

@section('content')
    <h1>Approve Tugas</h1>
    @include('partials.periode-filter')

    <table>
        <thead><tr><th>Petugas</th><th>Waktu</th><th>Uraian</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->user->nama ?? '-' }}</td>
                    <td>{{ $item->tanggal_mulai->format('d/m/Y H:i') }} - {{ $item->tanggal_selesai?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>{{ $item->uraian }}</td>
                    <td><span class="badge {{ $item->status }}">{{ $item->status }}</span></td>
                    <td class="actions">
                        @if($item->status === 'pending')
                            <form method="POST" action="{{ route('atasan.tugas.approve', $item->id_tugas) }}">
                                @csrf
                                <button type="submit">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('atasan.tugas.reject', $item->id_tugas) }}">
                                @csrf
                                <button type="submit" class="danger">Reject</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Belum ada laporan tugas.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
