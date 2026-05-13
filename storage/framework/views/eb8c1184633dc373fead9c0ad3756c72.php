<?php $__env->startSection('title', 'Kelola Users'); ?>

<?php $__env->startSection('content'); ?>
    <h1>Kelola Users</h1>
    <div class="panel">
        <h2>Tambah User</h2>
        <form method="POST" action="<?php echo e(route('admin.users.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-grid">
                <div><label>Nama</label><input name="nama" required></div>
                <div><label>Username</label><input name="username" required></div>
                <div><label>Email</label><input type="email" name="email" required></div>
                <div><label>Password</label><input type="password" name="password" required></div>
                <div><label>Role</label><select name="id_role" required><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($role->id_role); ?>"><?php echo e($role->nama_role); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                <div><label>Tempat Tugas</label><select name="id_tempat"><option value="">-</option><?php $__currentLoopData = $tempatTugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tempat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($tempat->id_tempat); ?>"><?php echo e($tempat->nama_tempat); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                <button type="submit">Tambah</button>
            </div>
        </form>
    </div>

    <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" class="filter-bar" style="margin-bottom: 16px;">
        <div class="filter-control" style="flex: 1; min-width: 200px;">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama, username, email..." style="width: 100%;">
        </div>
        <div class="filter-control">
            <select name="role" style="width: 100%;">
                <option value="">Semua Role</option>
                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($role->id_role); ?>" <?php echo e(request('role') == $role->id_role ? 'selected' : ''); ?>><?php echo e($role->nama_role); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" style="background:#2563eb; color:#fff; padding:9px 16px;">Filter</button>
        <?php if(request()->hasAny(['search', 'role']) && (request('search') != '' || request('role') != '')): ?>
            <a href="<?php echo e(route('admin.users.index')); ?>" style="background:#f3f4f6; color:#374151; padding:9px 12px; border-radius:6px; text-decoration:none; font-weight:bold; font-size:14px;">Reset</a>
        <?php endif; ?>
    </form>

    <table>
        <thead><tr><th>Nama</th><th>Login</th><th>Role</th><th>Tempat</th><th>Update</th><th>Hapus</th></tr></thead>
        <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <form method="POST" action="<?php echo e(route('admin.users.update', $item->id_user)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <td><input name="nama" value="<?php echo e($item->nama); ?>" required></td>
                        <td>
                            <input name="username" value="<?php echo e($item->username); ?>" required>
                            <input type="email" name="email" value="<?php echo e($item->email); ?>" required style="margin-top:6px">
                            <input type="password" name="password" placeholder="Password baru opsional" style="margin-top:6px">
                        </td>
                        <td><select name="id_role"><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($role->id_role); ?>" <?php if($item->id_role == $role->id_role): echo 'selected'; endif; ?>><?php echo e($role->nama_role); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></td>
                        <td><select name="id_tempat"><option value="">-</option><?php $__currentLoopData = $tempatTugas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tempat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($tempat->id_tempat); ?>" <?php if($item->id_tempat == $tempat->id_tempat): echo 'selected'; endif; ?>><?php echo e($tempat->nama_tempat); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></td>
                        <td><button type="submit">Simpan</button></td>
                    </form>
                    <td>
                        <form method="POST" action="<?php echo e(route('admin.users.delete', $item->id_user)); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo e($items->links('pagination.simple')); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project_absensi\Absensi-app\resources\views/admin/users.blade.php ENDPATH**/ ?>