@extends('layouts.app')

@section('title', 'Approval Regu')

@section('content')
    <h1>Approval Regu</h1>
    <p class="muted" style="margin-bottom:16px;">Pengajuan absen masuk/pulang terlewat dari anggota regu kamu. Teruskan ke atasan jika valid.</p>

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>Petugas</th>
                    <th>Regu</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Alasan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->user->nama ?? '-' }}</td>
                        <td>{{ $item->user->regu ?? '-' }}</td>
                        <td>{{ $item->tanggal?->format('d/m/Y') }}</td>
                        <td>
                            @if($item->approval_masuk_status === 'pending_ketua')
                                <span class="badge pending">Absen Masuk</span>
                            @else
                                <span class="badge pending">Absen Pulang</span>
                            @endif
                        </td>
                        <td>{{ $item->jam_masuk ?? '-' }}</td>
                        <td>{{ $item->jam_pulang ?? '-' }}</td>
                        <td>{{ $item->approval_masuk_status === 'pending_ketua' ? $item->approval_masuk_reason : $item->approval_pulang_reason }}</td>
                        <td>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                @if($item->approval_masuk_status === 'pending_ketua')
                                    <form method="POST" action="{{ route('petugas.approval-regu.forward-masuk', $item->id_absensi) }}" style="margin:0;">
                                        @csrf
                                        <button type="submit">Teruskan ke Atasan</button>
                                    </form>
                                    <form method="POST" action="{{ route('petugas.approval-regu.reject-masuk', $item->id_absensi) }}" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="danger">Tolak</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('petugas.approval-regu.forward', $item->id_absensi) }}" style="margin:0;">
                                        @csrf
                                        <button type="submit">Teruskan ke Atasan</button>
                                    </form>
                                    <form method="POST" action="{{ route('petugas.approval-regu.reject', $item->id_absensi) }}" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="danger">Tolak</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="muted">Belum ada request dari anggota regu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links('pagination.simple') }}
@endsection
