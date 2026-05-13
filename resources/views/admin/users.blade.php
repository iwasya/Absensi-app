@extends('layouts.app')

@section('title', 'Kelola Users')

@section('content')
<style>
    .user-tools {
        display: grid;
        gap: 12px;
        margin-bottom: 18px;
    }

    .user-accordion {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        overflow: hidden;
    }

    .user-accordion summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        color: var(--text-color);
        cursor: pointer;
        font-weight: 700;
        list-style: none;
    }

    .user-accordion summary::-webkit-details-marker {
        display: none;
    }

    .user-accordion summary::after {
        content: "+";
        width: 26px;
        height: 26px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: var(--soft-bg);
        color: var(--soft-text);
        font-size: 18px;
        line-height: 1;
        flex: 0 0 auto;
    }

    .user-accordion[open] summary::after {
        content: "-";
    }

    .user-accordion-body {
        padding: 0 16px 16px;
        border-top: 1px solid var(--border-color);
    }

    .user-accordion-body .form-grid,
    .user-accordion-body .filter-bar {
        margin-top: 16px;
    }

    .import-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin: 14px 0 16px;
    }

    .import-form-row {
        display: flex;
        gap: 12px;
        align-items: end;
        flex-wrap: wrap;
    }

    .import-form-row > div {
        flex: 1 1 260px;
    }

    .secondary-button {
        display: inline-block;
        background: var(--soft-bg);
        color: var(--soft-text);
        padding: 9px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
    }

    .success-button {
        display: inline-block;
        background: #059669;
        color: #fff;
        padding: 10px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 700;
    }
</style>

<h1>Kelola Users</h1>

<div class="user-tools">
    <details class="user-accordion">
        <summary>Tambah User</summary>
        <div class="user-accordion-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="form-grid">
                    <div><label>Nama</label><input name="nama" required></div>
                    <div><label>Username / NIK</label><input name="username" required placeholder="NIK sebagai username"></div>
                    <div><label>NIK</label><input name="nik" required placeholder="Nomor Induk Kependudukan"></div>
                    <div><label>Email</label><input type="email" name="email" required></div>
                    <div><label>Password</label><input type="password" name="password" required></div>
                    <div><label>Role</label><select name="id_role" required>@foreach($roles as $role)<option value="{{ $role->id_role }}">{{ $role->nama_role }}</option>@endforeach</select></div>
                    <div><label>Tempat Tugas</label><select name="id_tempat"><option value="">-</option>@foreach($tempatTugas as $tempat)<option value="{{ $tempat->id_tempat }}">{{ $tempat->nama_tempat }}</option>@endforeach</select></div>
                    <button type="submit">Tambah</button>
                </div>
            </form>
        </div>
    </details>

    <details class="user-accordion">
        <summary>Import Users dari Excel</summary>
        <div class="user-accordion-body">
            <p class="muted" style="margin:16px 0 0;">Import data user secara massal menggunakan file Excel. NIK akan di-encrypt otomatis.</p>
            <div class="import-actions">
                <a href="{{ route('admin.users.template') }}" class="success-button">Download Template Excel</a>
            </div>
            <form method="POST" action="{{ route('admin.users.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="import-form-row">
                    <div><label>File Excel</label><input type="file" name="file" accept=".xlsx,.xls" required></div>
                    <button type="submit">Upload dan Import</button>
                </div>
            </form>
        </div>
    </details>

    <details class="user-accordion" {{ request()->filled('search') || request()->filled('role') ? 'open' : '' }}>
        <summary>Filter Users</summary>
        <div class="user-accordion-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="filter-bar">
                <div class="filter-control" style="flex:1; min-width:200px;">
                    <input type="text" name="search" value="{{ request("search") }}" placeholder="Cari nama, username, email..." style="width:100%;">
                </div>
                <div class="filter-control">
                    <select name="role" style="width:100%;">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id_role }}" {{ request('role') == $role->id_role ? 'selected' : '' }}>{{ $role->nama_role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-control" style="max-width:120px;">
                    <select name="per_page" onchange="this.form.submit()" style="width:100%;">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / hal</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : (request('per_page') ? '' : 'selected') }}>25 / hal</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / hal</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / hal</option>
                    </select>
                </div>
                <button type="submit">Filter</button>
                @if(request()->hasAny(['search', 'role']) && (request('search') != '' || request('role') != ''))
                    <a href="{{ route('admin.users.index') }}" class="secondary-button">Reset</a>
                @endif
            </form>
        </div>
    </details>
</div>

<div style="display:flex; align-items:center; gap:16px; margin-bottom:12px; flex-wrap:wrap;">

    <form method="POST"
          action="{{ route('admin.users.bulk-delete') }}"
          id="bulkDeleteForm"
          style="display:flex; align-items:center; gap:14px; margin:0;">

        @csrf
        @method('DELETE')

        <button type="submit"
                class="danger"
                style="
                    background:#dc2626;
                    color:white;
                    padding:9px 16px;
                    border:none;
                    border-radius:6px;
                    cursor:pointer;
                "
                onclick="return confirmBulkDelete()">

            Hapus Terpilih
            (<span id="selectedCount">0</span>)
        </button>

        <label for="deleteNikData"
               style="display:flex; align-items:center; gap:8px; cursor:pointer; margin:0;">

            <input type="checkbox"
                   name="delete_nik_data"
                   value="1"
                   id="deleteNikData">

            <span>Hapus juga data NIK sensitif</span>

        </label>

    </form>

</div>

<table class="user-table">

    <thead>
        <tr>
            <th style="width:50px;" class="checkbox-cell">
                <input type="checkbox" id="selectAll">
            </th>

            <th style="min-width:180px;">Nama</th>

            <th style="min-width:260px;">
                Username / Email
            </th>

            <th style="min-width:180px;">
                Role
            </th>

            <th style="min-width:200px;">
                Tempat
            </th>

            <th style="min-width:170px;">
                Aksi
            </th>
        </tr>
    </thead>

    <tbody>

        @foreach($items as $item)

        <tr>

            <td class="checkbox-cell">
                <input type="checkbox"
                       name="user_ids[]"
                       value="{{ $item->id_user }}"
                       class="user-checkbox"
                       form="bulkDeleteForm">
            </td>

            <td>
                <input type="text"
                       name="nama_{{ $item->id_user }}"
                       value="{{ $item->nama }}">
            </td>

            <td>

                <div class="user-info">

                    <input type="text"
                           name="username_{{ $item->id_user }}"
                           value="{{ $item->username }}"
                           placeholder="Username">

                    <input type="email"
                           name="email_{{ $item->id_user }}"
                           value="{{ $item->email }}"
                           placeholder="Email">

                    <input type="password"
                           name="password_{{ $item->id_user }}"
                           placeholder="Password baru opsional">

                </div>

            </td>

            <td>

                <select name="id_role_{{ $item->id_user }}">

                    @foreach($roles as $role)

                        <option value="{{ $role->id_role }}"
                            @selected($item->id_role == $role->id_role)>

                            {{ $role->nama_role }}

                        </option>

                    @endforeach

                </select>

            </td>

            <td>

                <select name="id_tempat_{{ $item->id_user }}">

                    <option value="">-</option>

                    @foreach($tempatTugas as $tempat)

                        <option value="{{ $tempat->id_tempat }}"
                            @selected($item->id_tempat == $tempat->id_tempat)>

                            {{ $tempat->nama_tempat }}

                        </option>

                    @endforeach

                </select>

            </td>

            <td>

                <div class="action-buttons">

                    <button type="button"
                            onclick="updateUser({{ $item->id_user }})"
                            class="btn btn-save">

                        Simpan
                    </button>

                    <button type="button"
                            onclick="deleteUser({{ $item->id_user }})"
                            class="btn btn-delete">

                        Hapus
                    </button>

                </div>

            </td>

        </tr>

        @endforeach

    </tbody>

</table>

<div style="margin-top:20px; display:flex; justify-content:space-between; align-items:center;">
    <div style="color:#6b7280; font-size:14px;">
        Menampilkan {{ $items->count() }} dari {{ $items->total() }} data
    </div>
    {{ $items->links('pagination.simple') }}
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    document.querySelectorAll('.user-checkbox').forEach(cb => cb.addEventListener('change', updateSelectedCount));

    function updateSelectedCount() {
        document.getElementById('selectedCount').textContent = document.querySelectorAll('.user-checkbox:checked').length;
    }

    function confirmBulkDelete() {
        const count = document.querySelectorAll('.user-checkbox:checked').length;
        if (count === 0) { alert('Pilih minimal 1 user untuk dihapus'); return false; }
        const nikMsg = document.getElementById('deleteNikData').checked ? ' dan data NIK sensitif' : '';
        return confirm('Hapus ' + count + ' user' + nikMsg + '?');
    }

    function updateUser(id) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('_method', 'PUT');
        formData.append('nama', document.querySelector('[name="nama_' + id + '"]').value);
        formData.append('username', document.querySelector('[name="username_' + id + '"]').value);
        formData.append('email', document.querySelector('[name="email_' + id + '"]').value);
        const pass = document.querySelector('[name="password_' + id + '"]').value;
        if (pass) formData.append('password', pass);
        formData.append('id_role', document.querySelector('[name="id_role_' + id + '"]').value);
        formData.append('id_tempat', document.querySelector('[name="id_tempat_' + id + '"]').value);

        fetch('/admin/users/' + id, { method: 'POST', body: formData })
            .then(res => res.ok ? alert('Berhasil diupdate') : alert('Gagal update'))
            .catch(() => alert('Error'));
    }

    function deleteUser(id) {
        if (!confirm('Hapus user ini?')) return;
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('_method', 'DELETE');
        fetch('/admin/users/' + id, { method: 'POST', body: formData })
            .then(res => { if (res.ok) location.reload(); else alert('Gagal hapus'); })
            .catch(() => alert('Error'));
    }
</script>
@endsection
