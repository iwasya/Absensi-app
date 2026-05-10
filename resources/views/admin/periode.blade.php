@extends('layouts.app')

@section('title', 'Periode')

@section('content')
    <h1>Periode</h1>
    <div class="panel">
        <p class="muted">Periode dibuat per tahun. Contoh tahun 2025 otomatis menjadi 01/01/2025 - 31/12/2025.</p>
        <form method="POST" action="{{ route('admin.periode.store') }}">
            @csrf
            <div class="form-grid">
                <div><label>Tahun</label><input type="number" name="tahun" min="2000" max="2100" value="{{ date('Y') }}" required></div>
                <div><label>Status</label><select name="status"><option value="aktif">aktif</option><option value="nonaktif">nonaktif</option></select></div>
                <button type="submit">Tambah</button>
            </div>
        </form>
    </div>
    <table>
        <thead><tr><th>Tahun</th><th>Rentang</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <form method="POST" action="{{ route('admin.periode.update', $item->id_periode) }}" style="display:contents;">
                        @csrf
                        @method('PUT')
                        <td><input type="number" name="tahun" min="2000" max="2100" value="{{ $item->tanggal_mulai->format('Y') }}" required></td>
                        <td>{{ $item->tanggal_mulai->format('d/m/Y') }} - {{ $item->tanggal_selesai->format('d/m/Y') }}</td>
                        <td><select name="status"><option value="aktif" @selected($item->status === 'aktif')>aktif</option><option value="nonaktif" @selected($item->status === 'nonaktif')>nonaktif</option></select></td>
                        <td style="display:flex; gap:8px; align-items:center;">
                            <button type="submit" style="padding:8px 16px; border:1px solid #d1d5db; border-radius:6px; background:#fff; color:#1f2937; font-weight:700; cursor:pointer; font-size:14px;">Simpan</button>
                            <form method="POST" action="{{ route('admin.periode.delete', $item->id_periode) }}" style="display:inline; margin:0;" onsubmit="return confirm('Hapus periode ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding:8px 16px; border:0; border-radius:6px; background:#dc2626; color:#fff; font-weight:700; cursor:pointer; font-size:14px;">Hapus</button>
                            </form>
                        </td>
                    </form>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
