@extends('layouts.app')

@section('title', 'Filter Users')

@section('content')
<style>
    main {
        max-width: 100% !important;
        padding: 24px 28px !important;
    }

    .users-page {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .users-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .users-header h1 {
        margin: 0;
        font-size: 22px;
    }

    .users-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
    }

    .users-card-body {
        padding: 24px;
    }

    .filter-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-bottom: 20px;
    }

    .filter-control {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .filter-control input,
    .filter-control select {
        min-height: 44px;
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        background: var(--bg-color);
        color: var(--text-color);
    }

    .filter-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-btn,
    .users-btn-secondary {
        border-radius: 10px;
        padding: 12px 18px;
        font-weight: 700;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .filter-btn {
        background: var(--primary);
        color: #fff;
    }

    .users-btn-secondary {
        background: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-color);
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        border: 1px solid var(--border-color);
        background: var(--bg-color);
        color: var(--text-color);
        font-size: 13px;
    }

    .filter-chip button {
        border: none;
        background: transparent;
        cursor: pointer;
        padding: 0;
        color: var(--muted);
    }

    @media (max-width: 760px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="users-page">
    <div class="users-header">
        <div>
            <h1>Filter Users</h1>
            <p style="margin: 8px 0 0; color: var(--muted);">Gunakan filter untuk menyaring daftar pengguna.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="users-btn-secondary">Kembali ke Daftar Users</a>
    </div>

    <div class="users-card">
        <div class="users-card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm">
                <div class="filter-grid">
                    <div class="filter-control">
                        <label for="search">Cari User</label>
                        <input id="search" name="search" type="text" value="{{ old('search', $filters['search'] ?? '') }}" placeholder="Cari nama, username, atau email...">
                    </div>

                    <div class="filter-control">
                        <label for="role">Role</label>
                        <select id="role" name="role">
                            <option value="">📋 Semua Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id_role }}" {{ (string)($filters['role'] ?? '') === (string)$role->id_role ? 'selected' : '' }}>
                                    👤 {{ $role->nama_role }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="filter-btn">🔍 Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="users-btn-secondary">Reset Filter</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function removeFilter(filterName) {
        const form = document.getElementById('filterForm');
        if (!form) {
            return;
        }

        if (filterName === 'search') {
            form.querySelector('[name="search"]').value = '';
        } else if (filterName === 'role') {
            form.querySelector('[name="role"]').value = '';
        }
        form.submit();
    }
</script>
@endsection
