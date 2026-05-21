@extends('layouts.app')

@section('title', 'Approval Regu')

@section('content')
    <h1>Approval Regu</h1>
    <p class="muted" style="margin-bottom:16px;">Request lupa absen pulang dari anggota regu kamu. Teruskan ke atasan jika valid.</p>

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>Petugas</th>
                    <th>Regu</th>
                    <th>Tanggal</th>
                    <th>Masuk</th>
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
                        <td>{{ $item->jam_masuk ?? '-' }}</td>
                        <td>{{ $item->approval_pulang_reason }}</td>
                        <td>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                <form method="POST" action="{{ route('petugas.approval-regu.forward', $item->id_absensi) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit">Teruskan ke Atasan</button>
                                </form>
                                <form method="POST" action="{{ route('petugas.approval-regu.reject', $item->id_absensi) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="danger">Tolak</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="muted">Belum ada request dari anggota regu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $items->links('pagination.simple') }}
@endsection
