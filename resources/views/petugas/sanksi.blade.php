@extends('layouts.app')

@section('title', 'Sanksi Saya')

@section('content')
    <h1>Sanksi Saya</h1>
    
    <table>
        <thead>
            <tr>
                <th>Jenis</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Status Teguran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->jenis_sanksi }}</td>
                    <td>{{ $item->tanggal?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>
                        @if($item->acknowledged_at)
                            <span class="badge approve">Diakui {{ $item->acknowledged_at->format('d/m/Y H:i') }}</span>
                        @else
                            <span class="badge pending">Belum diakui</span>
                        @endif
                    </td>
                    <td>
                        @if(! $item->acknowledged_at)
                            <form method="POST" action="{{ route('petugas.sanksi.acknowledge', $item->id_sanksi) }}">
                                @csrf
                                <button type="submit">Approve Teguran</button>
                            </form>
                        @else
                            <span class="muted">Selesai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada sanksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
