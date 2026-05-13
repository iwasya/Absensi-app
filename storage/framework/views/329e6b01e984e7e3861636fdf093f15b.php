

<?php $__env->startSection('title', 'Kelola Users'); ?>

<?php $__env->startSection('content'); ?>
<h1>Kelola Users</h1>

<div class="LeftbarDropDown_linksWithDropDownContainer__cards__nqWee LeftbarDropDown_outsideIndiaCards__SXiln">
    <h2>Tambah User</h2>
    <form method="POST" action="<?php echo e(route('admin.users.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-grid">
            <div><label>Nama</label><input name="nama" required></div>
            <div><label>Username / NIK</label><input name="username" required placeholder="NIK sebagai username"></div>
            <div><label>NIK</label><input name="nik" required placeholder="Nomor Induk Kependudukan"></div>
            <div><label>Email</label><input type="email" name="email" required></div>
            <div><label>Password</label><input type="password" name="password" required></div>
            <div><label>Role</label><select name="id_role" required><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($role->id_role); ?>"><?php echo e($role->nama_role); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <div><label>Tempat Tugas</label><select name="id_tempat"><option value="">-</option><?php $__currentLoopData = $tempatTugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tempat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($tempat->id_tempat); ?>"><?php echo e($tempat->nama_tempat); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
            <button type="submit">Tambah</button>
        </div>
    </form>
</div>

<div class="LeftbarDropDown_linksWithDropDownContainer__cards__nqWee LeftbarDropDown_outsideIndiaCards__SXiln" style="margin-top:24px;">
    <h2>Import Users dari Excel</h2>
    <p style="color:#6b7280; margin-bottom:16px;">Import data user secara massal menggunakan file Excel. NIK akan di-encrypt otomatis.</p>
    <div style="display:flex; gap:12px; margin-bottom:16px;">
        <a href="<?php echo e(route('admin.users.template')); ?>" class="button" style="background:#059669; color:white; padding:10px 16px; border-radius:6px; text-decoration:none; font-weight:bold;">Download Template Excel</a>
    </div>
    <form method="POST" action="<?php echo e(route('admin.users.import')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div style="display:flex; gap:12px; align-items:end;">
            <div style="flex:1;"><label>File Excel</label><input type="file" name="file" accept=".xlsx,.xls" required></div>
            <button type="submit" style="background:#2563eb; color:white; padding:10px 20px; border-radius:6px; font-weight:bold;">Upload dan Import</button>
        </div>
    </form>
</div>

<form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="filter-bar" style="margin:24px 0 16px;">
    <div class="filter-control" style="flex:1; min-width:200px;">
        <input type="text" name="search" value="<?php echo e(request("search")); ?>" placeholder="Cari nama, username, email..." style="width:100%;">
    </div>
    <div class="filter-control">
        <select name="role" style="width:100%;">
            <option value="">Semua Role</option>
            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($role->id_role); ?>" <?php echo e(request('role') == $role->id_role ? 'selected' : ''); ?>><?php echo e($role->nama_role); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="filter-control" style="max-width:120px;">
        <select name="per_page" onchange="this.form.submit()" style="width:100%;">
            <option value="10" <?php echo e(request('per_page') == 10 ? 'selected' : ''); ?>>10 / hal</option>
            <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : (request('per_page') ? '' : 'selected')); ?>>25 / hal</option>
            <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50 / hal</option>
            <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100 / hal</option>
        </select>
    </div>
    <button type="submit" style="background:#2563eb; color:#fff; padding:9px 16px;">Filter</button>
    <?php if(request()->hasAny(['search', 'role']) && (request('search') != '' || request('role') != '')): ?>
        <a href="<?php echo e(route('admin.users.index')); ?>" style="background:#f3f4f6; color:#374151; padding:9px 12px; border-radius:6px; text-decoration:none; font-weight:bold; font-size:14px;">Reset</a>
    <?php endif; ?>
</form>

<div style="display:flex; align-items:center; gap:16px; margin-bottom:12px; flex-wrap:wrap;">

    <form method="POST"
          action="<?php echo e(route('admin.users.bulk-delete')); ?>"
          id="bulkDeleteForm"
          style="display:flex; align-items:center; gap:14px; margin:0;">

        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>

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

        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <tr>

            <td class="checkbox-cell">
                <input type="checkbox"
                       name="user_ids[]"
                       value="<?php echo e($item->id_user); ?>"
                       class="user-checkbox"
                       form="bulkDeleteForm">
            </td>

            <td>
                <input type="text"
                       name="nama_<?php echo e($item->id_user); ?>"
                       value="<?php echo e($item->nama); ?>">
            </td>

            <td>

                <div class="user-info">

                    <input type="text"
                           name="username_<?php echo e($item->id_user); ?>"
                           value="<?php echo e($item->username); ?>"
                           placeholder="Username">

                    <input type="email"
                           name="email_<?php echo e($item->id_user); ?>"
                           value="<?php echo e($item->email); ?>"
                           placeholder="Email">

                    <input type="password"
                           name="password_<?php echo e($item->id_user); ?>"
                           placeholder="Password baru opsional">

                </div>

            </td>

            <td>

                <select name="id_role_<?php echo e($item->id_user); ?>">

                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <option value="<?php echo e($role->id_role); ?>"
                            <?php if($item->id_role == $role->id_role): echo 'selected'; endif; ?>>

                            <?php echo e($role->nama_role); ?>


                        </option>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </select>

            </td>

            <td>

                <select name="id_tempat_<?php echo e($item->id_user); ?>">

                    <option value="">-</option>

                    <?php $__currentLoopData = $tempatTugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tempat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <option value="<?php echo e($tempat->id_tempat); ?>"
                            <?php if($item->id_tempat == $tempat->id_tempat): echo 'selected'; endif; ?>>

                            <?php echo e($tempat->nama_tempat); ?>


                        </option>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </select>

            </td>

            <td>

                <div class="action-buttons">

                    <button type="button"
                            onclick="updateUser(<?php echo e($item->id_user); ?>)"
                            class="btn btn-save">

                        Simpan
                    </button>

                    <button type="button"
                            onclick="deleteUser(<?php echo e($item->id_user); ?>)"
                            class="btn btn-delete">

                        Hapus
                    </button>

                </div>

            </td>

        </tr>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </tbody>

</table>

<div style="margin-top:20px; display:flex; justify-content:space-between; align-items:center;">
    <div style="color:#6b7280; font-size:14px;">
        Menampilkan <?php echo e($items->count()); ?> dari <?php echo e($items->total()); ?> data
    </div>
    <?php echo e($items->links('pagination.simple')); ?>

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/admin/users.blade.php ENDPATH**/ ?>