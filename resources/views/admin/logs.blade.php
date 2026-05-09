@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h1 style="margin: 0;">Activity Log</h1>
        <a href="{{ route('admin.logs.export') }}" style="background: #10b981; color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px;">Export CSV</a>
    </div>
    <table>
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
