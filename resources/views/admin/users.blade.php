@extends('layouts.app')

@section('title', 'Kelola Users')

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
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .users-header h1 {
        margin: 0;
        font-size: 22px;
        line-height: 1.2;
    }

    .users-count-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 7px 13px;
        border: 1px solid var(--primary-border);
        border-radius: 99px;
        background: var(--primary-soft);
        color: var(--primary2);
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .users-tools {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        align-items: start;
    }

    .users-panel,
    .users-table-card {
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
    }

    .users-panel summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 15px 16px;
        color: var(--text-color);
        cursor: pointer;
        font-weight: 600;
        list-style: none;
    }

    .users-panel summary::-webkit-details-marker {
        display: none;
    }

    .users-panel summary::after {
        content: "+";
        width: 26px;
        height: 26px;
        display: grid;
        place-items: center;
        border-radius: 8px;
        background: var(--bg-color);
        color: var(--primary);
        border: 1px solid var(--border-color);
        font-size: 17px;
        line-height: 1;
        flex: 0 0 auto;
    }

    .users-panel[open] summary::after {
        content: "-";
    }

    .users-panel-body {
        padding: 16px;
        border-top: 1px solid var(--border-color);
    }

    .users-panel .form-grid,
    .users-panel .filter-bar {
        margin: 0;
        padding: 0;
        border: 0;
        background: transparent;
    }

    .users-panel .filter-bar {
        align-items: end;
    }

    .users-panel-note {
        margin: 0 0 14px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .import-actions,
    .import-form-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: end;
    }

    .import-actions {
        margin-bottom: 14px;
    }

    .import-form-row > div {
        flex: 1 1 240px;
    }

    .users-button-secondary,
    .users-button-success {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 9px 13px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        border: 1px solid transparent;
    }

    .users-button-secondary {
        background: var(--bg-color);
        color: var(--text-color);
        border-color: var(--border2);
    }

    .users-button-success {
        background: var(--green);
        color: #fff;
    }

    .users-bulkbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 13px 15px;
        background: var(--panel-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
    }

    .users-bulk-form {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin: 0;
    }

    .users-danger {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--red);
        color: #fff;
        padding: 9px 14px;
    }

    .users-danger:hover {
        background: #dc2626;
    }

    .users-checkbox-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        color: var(--muted);
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
    }

    .users-checkbox-label input,
    .users-check {
        width: 16px;
        height: 16px;
        margin: 0;
        accent-color: var(--primary);
    }

    .users-result-text {
        color: var(--muted);
        font-size: 13px;
    }

    .users-table-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .users-table {
        width: 100%;
        min-width: 1060px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }

    .users-table th {
        padding: 12px 14px;
        background: var(--bg-color);
        color: var(--muted);
        border-bottom: 1px solid var(--border-color);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .users-table td {
        padding: 13px 14px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: top;
    }

    .users-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .users-table tbody tr:hover {
        background: var(--bg-color);
    }

    .users-check-cell {
        width: 48px;
        text-align: center;
    }

    .users-name-field,
    .users-select {
        min-width: 0;
    }

    .users-info-grid {
        display: grid;
        gap: 8px;
    }

    .users-info-grid input,
    .users-table input,
    .users-table select {
        min-height: 36px;
        font-size: 12.5px;
    }

    .users-role-badge {
        display: inline-flex;
        align-items: center;
        margin-top: 8px;
        padding: 4px 8px;
        border-radius: 99px;
        background: var(--primary-soft);
        color: var(--primary2);
        border: 1px solid var(--primary-border);
        font-size: 11px;
        font-weight: 600;
    }

    .users-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .users-btn {
        min-width: 72px;
        min-height: 34px;
        padding: 8px 11px;
        border-radius: 8px;
        font-size: 12.5px;
    }

    .users-btn-save {
        background: var(--primary);
    }

    .users-btn-delete {
        background: var(--red);
    }

    .users-empty {
        padding: 38px 18px;
        text-align: center;
        color: var(--muted);
    }

    .users-empty strong {
        display: block;
        margin-bottom: 4px;
        color: var(--text-color);
        font-size: 14px;
    }

    .users-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 14px 15px;
        border-top: 1px solid var(--border-color);
    }

    @media (max-width: 1100px) {
        .users-tools {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        main {
            padding: 16px !important;
        }

        .users-header h1 {
            font-size: 20px;
        }

        .users-bulkbar {
            align-items: stretch;
        }

        .users-bulk-form,
        .users-danger {
            width: 100%;
        }

        .users-danger {
            justify-content: center;
        }
    }
</style>

<div class="users-page">
    <div class="users-header">
        <div>
            <h1>Kelola Users</h1>
        </div>
        <div class="users-count-pill">
            {{ $items->total() }} total user
        </div>
    </div>

    <div class="users-tools">
        <details class="users-panel">
            <summary>Tambah User</summary>
            <div class="users-panel-body">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div><label>Nama</label><input name="nama" required></div>
                        <div><label>Username / NIK</label><input name="username" required placeholder="NIK sebagai username"></div>
                        <div><label>NIK</label><input name="nik" required placeholder="Nomor Induk Kependudukan"></div>
                        <div><label>Email</label><input type="email" name="email" required></div>
                        <div><label>Password</label><input type="password" name="password" required></div>
                        <div>
                            <label>Role</label>
                            <select name="id_role" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id_role }}">{{ $role->nama_role }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Tempat Tugas</label>
                            <select name="id_tempat">
                                <option value="">-</option>
                                @foreach($tempatTugas as $tempat)
                                    <option value="{{ $tempat->id_tempat }}">{{ $tempat->nama_tempat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div><label>Regu</label><input name="regu" placeholder="Contoh: Regu A"></div>
                        <div>
                            <label>Shift</label>
                            <select name="shift">
                                <option value="">-</option>
                                <option value="Shift 1">Shift 1</option>
                                <option value="Shift 2">Shift 2</option>
                                <option value="Shift 3">Shift 3</option>
                            </select>
                        </div>
                        <div>
                            <label>Status User</label>
                            <select name="status_aktif">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div><label>No HP</label><input name="no_hp"></div>
                        <div><label>Jabatan</label><input name="jabatan"></div>
                        <div><label>Alamat</label><input name="alamat"></div>
                        <button type="submit">Tambah User</button>
                    </div>
                </form>
            </div>
        </details>

        <details class="users-panel">
            <summary>Import Users dari Excel</summary>
            <div class="users-panel-body">
                <p class="users-panel-note">Import data user secara massal menggunakan file Excel. NIK akan di-encrypt otomatis.</p>
                <div class="import-actions">
                    <a href="{{ route('admin.users.template') }}" class="users-button-success">Download Template Excel</a>
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

        <details class="users-panel" {{ request()->filled('search') || request()->filled('role') ? 'open' : '' }}>
            <summary>Filter Users</summary>
            <div class="users-panel-body">
                <form method="GET" action="{{ route('admin.users.index') }}" class="filter-bar">
                    <div class="filter-control" style="flex:1; min-width:200px;">
                        <label>Cari User</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, username, email..." style="width:100%;">
                    </div>
                    <div class="filter-control">
                        <label>Role</label>
                        <select name="role" style="width:100%;">
                            <option value="">Semua Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id_role }}" {{ request('role') == $role->id_role ? 'selected' : '' }}>{{ $role->nama_role }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-control" style="max-width:120px;">
                        <label>Per Halaman</label>
                        <select name="per_page" onchange="this.form.submit()" style="width:100%;">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / hal</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : (request('per_page') ? '' : 'selected') }}>25 / hal</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / hal</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / hal</option>
                        </select>
                    </div>
                    <button type="submit">Filter</button>
                    @if(request()->hasAny(['search', 'role']) && (request('search') != '' || request('role') != ''))
                        <a href="{{ route('admin.users.index') }}" class="users-button-secondary">Reset</a>
                    @endif
                </form>
            </div>
        </details>
    </div>

    <div class="users-bulkbar">
        <form method="POST" action="{{ route('admin.users.bulk-delete') }}" id="bulkDeleteForm" class="users-bulk-form">
            @csrf
            @method('DELETE')

            <button type="submit" class="users-danger" onclick="return confirmBulkDelete()">
                Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>

            <label for="deleteNikData" class="users-checkbox-label">
                <input type="checkbox" name="delete_nik_data" value="1" id="deleteNikData">
                <span>Hapus juga data NIK sensitif</span>
            </label>
        </form>

        <div class="users-result-text">
            Menampilkan {{ $items->count() }} dari {{ $items->total() }} data
        </div>
    </div>

    <div class="users-table-card">
        <div class="users-table-scroll">
            <table class="users-table">
                <thead>
                    <tr>
                        <th class="users-check-cell">
                            <input type="checkbox" id="selectAll" class="users-check" aria-label="Pilih semua user">
                        </th>
                        <th style="min-width:190px;">Nama</th>
                        <th style="min-width:300px;">Akun</th>
                        <th style="min-width:190px;">Role</th>
                        <th style="min-width:220px;">Tempat Tugas</th>
                        <th style="min-width:260px;">Regu / Shift / Status</th>
                        <th style="min-width:260px;">Data Diri</th>
                        <th style="min-width:170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $currentRole = $roles->firstWhere('id_role', $item->id_role);
                        @endphp
                        <tr>
                            <td class="users-check-cell">
                                <input type="checkbox" name="user_ids[]" value="{{ $item->id_user }}" class="user-checkbox users-check" form="bulkDeleteForm" aria-label="Pilih {{ $item->nama }}">
                            </td>

                            <td>
                                <label for="nama_{{ $item->id_user }}" class="sr-only">Nama</label>
                                <input id="nama_{{ $item->id_user }}" class="users-name-field" type="text" name="nama_{{ $item->id_user }}" value="{{ $item->nama }}">
                            </td>

                            <td>
                                <div class="users-info-grid">
                                    <label for="username_{{ $item->id_user }}" class="sr-only">Username</label>
                                    <input id="username_{{ $item->id_user }}" type="text" name="username_{{ $item->id_user }}" value="{{ $item->username }}" placeholder="Username">

                                    <label for="email_{{ $item->id_user }}" class="sr-only">Email</label>
                                    <input id="email_{{ $item->id_user }}" type="email" name="email_{{ $item->id_user }}" value="{{ $item->email }}" placeholder="Email">

                                    <label for="password_{{ $item->id_user }}" class="sr-only">Password baru</label>
                                    <input id="password_{{ $item->id_user }}" type="password" name="password_{{ $item->id_user }}" placeholder="Password baru opsional">
                                </div>
                            </td>

                            <td>
                                <label for="id_role_{{ $item->id_user }}" class="sr-only">Role</label>
                                <select id="id_role_{{ $item->id_user }}" class="users-select" name="id_role_{{ $item->id_user }}">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id_role }}" @selected($item->id_role == $role->id_role)>
                                            {{ $role->nama_role }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="users-role-badge">{{ $currentRole->nama_role ?? 'Role' }}</span>
                            </td>

                            <td>
                                <label for="id_tempat_{{ $item->id_user }}" class="sr-only">Tempat tugas</label>
                                <select id="id_tempat_{{ $item->id_user }}" class="users-select" name="id_tempat_{{ $item->id_user }}">
                                    <option value="">-</option>
                                    @foreach($tempatTugas as $tempat)
                                        <option value="{{ $tempat->id_tempat }}" @selected($item->id_tempat == $tempat->id_tempat)>
                                            {{ $tempat->nama_tempat }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <div class="users-info-grid">
                                    <input type="text" name="regu_{{ $item->id_user }}" value="{{ $item->regu }}" placeholder="Regu">
                                    <select name="shift_{{ $item->id_user }}" class="users-select">
                                        <option value="">Shift</option>
                                        <option value="Shift 1" @selected($item->shift === 'Shift 1')>Shift 1</option>
                                        <option value="Shift 2" @selected($item->shift === 'Shift 2')>Shift 2</option>
                                        <option value="Shift 3" @selected($item->shift === 'Shift 3')>Shift 3</option>
                                    </select>
                                    <select name="status_aktif_{{ $item->id_user }}" class="users-select">
                                        <option value="aktif" @selected(($item->status_aktif ?? 'aktif') === 'aktif')>Aktif</option>
                                        <option value="nonaktif" @selected($item->status_aktif === 'nonaktif')>Nonaktif</option>
                                    </select>
                                    <small class="muted">{{ $item->is_ketua_regu ? 'Ketua ditentukan atasan' : 'Anggota regu' }}</small>
                                </div>
                            </td>

                            <td>
                                <div class="users-info-grid">
                                    <input type="text" name="no_hp_{{ $item->id_user }}" value="{{ $item->no_hp }}" placeholder="No HP">
                                    <input type="text" name="jabatan_{{ $item->id_user }}" value="{{ $item->jabatan }}" placeholder="Jabatan">
                                    <input type="text" name="alamat_{{ $item->id_user }}" value="{{ $item->alamat }}" placeholder="Alamat">
                                </div>
                            </td>

                            <td>
                                <div class="users-actions">
                                    <button type="button" onclick="updateUser({{ $item->id_user }})" class="users-btn users-btn-save">
                                        Simpan
                                    </button>
                                    <button type="button" onclick="deleteUser({{ $item->id_user }})" class="users-btn users-btn-delete">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="users-empty">
                                    <strong>Belum ada user</strong>
                                    Data user akan tampil di sini setelah ditambahkan atau diimport.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="users-footer">
            <div class="users-result-text">
                Menampilkan {{ $items->count() }} dari {{ $items->total() }} data
            </div>
            {{ $items->links('pagination.simple') }}
        </div>
    </div>
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
        formData.append('regu', document.querySelector('[name="regu_' + id + '"]').value);
        formData.append('shift', document.querySelector('[name="shift_' + id + '"]').value);
        formData.append('status_aktif', document.querySelector('[name="status_aktif_' + id + '"]').value);
        formData.append('no_hp', document.querySelector('[name="no_hp_' + id + '"]').value);
        formData.append('jabatan', document.querySelector('[name="jabatan_' + id + '"]').value);
        formData.append('alamat', document.querySelector('[name="alamat_' + id + '"]').value);

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
