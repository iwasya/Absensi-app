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
                    <form method="POST" action="{{ route('admin.periode.update', $item->id_periode) }}">
                        @csrf
                        @method('PUT')
                        <td><input type="number" name="tahun" min="2000" max="2100" value="{{ $item->tanggal_mulai->format('Y') }}" required></td>
                        <td>{{ $item->tanggal_mulai->format('d/m/Y') }} - {{ $item->tanggal_selesai->format('d/m/Y') }}</td>
                        <td><select name="status"><option value="aktif" @selected($item->status === 'aktif')>aktif</option><option value="nonaktif" @selected($item->status === 'nonaktif')>nonaktif</option></select></td>
                        <td><button type="submit">Simpan</button></td>
                    </form>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection
