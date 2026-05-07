@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
    <h1>Notifikasi</h1>
    <table>
        <thead><tr><th>Judul</th><th>Pesan</th><th>Tipe</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->judul }}</td>
                    <td>{{ $item->pesan }}</td>
                    <td>{{ $item->tipe }}</td>
                    <td>{{ $item->status_baca ? 'Dibaca' : 'Baru' }}</td>
                    <td>
                        @if(! $item->status_baca)
                            <form method="POST" action="{{ route('notifikasi.read', $item->id_notifikasi) }}">
                                @csrf
                                <button type="submit">Tandai dibaca</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Belum ada notifikasi.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
