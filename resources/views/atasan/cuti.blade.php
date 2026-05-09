@extends('layouts.app')

@section('title', 'Approve Cuti')

@section('content')
    <h1>Approve Cuti</h1>
    @include('partials.periode-filter')

    <table>
        <thead><tr><th>Petugas</th><th>Tanggal</th><th>Jenis</th><th>Alasan & Alamat</th><th>Pengganti</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->user->nama ?? '-' }}</td>
                    <td>{{ $item->tanggal_mulai->format('d/m/Y') }} - {{ $item->tanggal_selesai->format('d/m/Y') }}</td>
                    <td>{{ $item->jenis_cuti }}</td>
                    <td>
                        <strong>{{ $item->alasan }}</strong>
                        @if($item->alasan == 'Alasan Lainnya')
                            <br><small class="muted">({{ $item->alasan_lainnya }})</small>
                        @endif
                        <br><small>Alamat: {{ $item->alamat_cuti }}</small>
                    </td>
                    <td>{{ $item->pengganti->nama ?? '-' }}</td>
                    <td><span class="badge {{ $item->status }}">{{ $item->status }}</span></td>
                    <td>{{ $item->approver->nama ?? '-' }}</td>
                    <td class="actions">
                        @if($item->status === 'pending')
                            <form method="POST" action="{{ route('atasan.cuti.approve', $item->id_cuti) }}">
                                @csrf
                                <button type="submit">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('atasan.cuti.reject', $item->id_cuti) }}">
                                @csrf
                                <button type="submit" class="danger">Reject</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">Belum ada pengajuan cuti.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
