@extends('layouts.app')

@section('title', 'Laporan Cuti')

@section('content')
    <h1>Laporan Cuti</h1>

    <div class="panel" style="margin-bottom: 16px;">
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('admin.cuti.export') }}" class="btn btn-success" style="text-decoration: none;">
                Download CSV
            </a>
            <span style="color: #666; font-size: 13px;">
                ({{ $items->total() }} total pengajuan cuti)
            </span>
        </div>
    </div>

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>Petugas</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Alasan</th>
                    <th>Pengganti</th>
                    <th>Konfirmasi</th>
                    <th>Dokumen</th>
                    <th>Status Admin</th>
                    <th>Status Atasan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->user->nama ?? '-' }}</td>
                        <td>{{ $item->tanggal_mulai?->format('d/m/Y') }} - {{ $item->tanggal_selesai?->format('d/m/Y') }}</td>
                        <td>{{ $item->jenis_cuti }}</td>
                        <td>
                            {{ $item->alasan }}
                            @if($item->alasan_lainnya)
                                <br><small class="muted">{{ $item->alasan_lainnya }}</small>
                            @endif
                        </td>
                        <td>{{ $item->pengganti->nama ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $item->replacement_status ?? 'pending' }}">
                                {{ $item->replacement_status === 'accepted' ? 'Diterima' : ($item->replacement_status === 'rejected' ? 'Ditolak' : 'Pending') }}
                            </span>
                        </td>
                        <td>
                            @if($item->dokumen_path)
                                <a href="{{ asset('storage/' . $item->dokumen_path) }}" target="_blank">Lihat dokumen</a>
                            @else
                                <span class="muted">-</span>
                            @endif
                        </td>
                        <td><span class="badge {{ $item->admin_status }}">{{ ($item->admin_status ?? 'pending') === 'notified' ? 'diberitahu' : ($item->admin_status ?? 'pending') }}</span></td>
                        <td><span class="badge {{ $item->status }}">{{ $item->status }}</span></td>
                        <td>
                            <span class="muted">-</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="muted">Belum ada pengajuan cuti.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links('pagination.simple') }}
@endsection
