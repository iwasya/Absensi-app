@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h1 style="margin: 0;">Activity Log</h1>
        <a href="{{ route('admin.logs.export') }}" style="background: #10b981; color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px;">Export CSV</a>
    </div>
    <div class="panel" style="margin-bottom: 24px;">        <form action="{{ route('admin.logs.index') }}" method="GET" class="filter-bar">            <div class="filter-control" style="max-width:120px;">                <label>Per Halaman</label>                <select name="per_page" onchange="this.form.submit()" style="width:100%;">                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / hal</option>                    <option value="15" {{ request('per_page') == 15 ? 'selected' : (request('per_page') ? '' : 'selected') }}>15 / hal</option>                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / hal</option>                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / hal</option>                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / hal</option>                </select>            </div>        </form>    </div>    <table>
        <thead><tr><th>Waktu</th><th>User</th><th>Aktivitas</th><th>Modul</th><th>Status</th><th>IP</th><th>Device</th></tr></thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->created_at }}</td>
                    <td>{{ $item->user->nama ?? '-' }}</td>
                    <td>{{ $item->aktivitas }}</td>
                    <td>{{ $item->modul }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->ip_address }}</td>
                    <td>{{ $item->device }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $items->links('pagination.simple') }}
@endsection

