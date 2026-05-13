@extends('layouts.app')

@section('title', 'Sanksi')

@section('content')
    <h1>Sanksi</h1>
    <div class="panel" style="margin-bottom: 24px;">
        <form action="{{ route('admin.sanksi.index') }}" method="GET" class="filter-bar">
            <div class="filter-control" style="max-width:120px;">
                <label>Per Halaman</label>
                <select name="per_page" onchange="this.form.submit()" style="width:100%;">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / hal</option>
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : (request('per_page') ? '' : 'selected') }}>15 / hal</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / hal</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / hal</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / hal</option>
                </select>
            </div>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Jenis</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->user->nama ?? '-' }}</td>
                    <td>{{ $item->jenis_sanksi }}</td>
                    <td>{{ $item->tanggal?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection

