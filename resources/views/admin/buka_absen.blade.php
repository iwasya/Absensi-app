@extends('layouts.app')

@section('title', 'Akses Absen Telat')

@section('content')
    <h1>Akses Absen Telat</h1>
    <p class="muted" style="margin-top:-10px; margin-bottom:20px;">Berikan akses khusus kepada petugas agar bisa melakukan absen masuk pada hari ini meskipun sudah melewati jam batas (07:15).</p>

    <div class="panel">
        <form method="POST" action="{{ route('admin.buka-absen.store') }}">
            @csrf
            <div class="form-grid">
                <div>
                    <label>Pilih Petugas</label>
                    <select name="id_user" required>
                        <option value="">-- Pilih --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id_user }}">{{ $user->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" style="align-self: end; background: #059669;">Buka Akses Hari Ini</button>
            </div>
        </form>
    </div>

    <h2>Riwayat Akses Dibuka (Hari Ini)</h2>`n`n    <div class="panel" style="margin-bottom: 24px;">`n        <form action="{{ route('admin.buka-absen.index') }}" method="GET" class="filter-bar">`n            <div class="filter-control" style="max-width:120px;">`n                <label>Per Halaman</label>`n                <select name="per_page" onchange="this.form.submit()" style="width:100%;">`n                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / hal</option>`n                    <option value="15" {{ request('per_page') == 15 ? 'selected' : (request('per_page') ? '' : 'selected') }}>15 / hal</option>`n                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / hal</option>`n                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / hal</option>`n                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / hal</option>`n                </select>`n            </div>`n        </form>`n    </div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>User</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->user->nama ?? '-' }}</td>
                    <td><span class="badge pending">{{ $item->status }}</span></td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="muted" style="text-align: center;">Belum ada akses telat yang dibuka hari ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection

