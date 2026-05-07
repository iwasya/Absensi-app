@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
    <h1>Activity Log</h1>
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
