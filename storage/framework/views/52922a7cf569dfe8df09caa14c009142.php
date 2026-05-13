<div class="panel">
    <form method="GET" action="<?php echo e(url()->current()); ?>" class="filter-bar">
        <div class="filter-control">
            <label for="id_periode">Periode Data</label>
            <select id="id_periode" name="id_periode">
                <option value="">Semua tahun</option>

                <?php $__currentLoopData = $periodes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $periode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($periode->id_periode); ?>"
                        <?php if(optional($selectedPeriode ?? null)->id_periode === $periode->id_periode): echo 'selected'; endif; ?>>
                        
                        <?php echo e(\Carbon\Carbon::parse($periode->tanggal_mulai)->format('Y')); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </select>
        </div>

        <button type="submit">Tampilkan</button>

        <?php if(isset($selectedPeriode) && $selectedPeriode): ?>
            <a href="<?php echo e(url()->current()); ?>">Reset</a>
        <?php endif; ?>

    </form>

    <p class="muted" style="margin-bottom:0">

        <?php if(isset($selectedPeriode) && $selectedPeriode): ?>
            Menampilkan arsip tahun 
            <?php echo e(\Carbon\Carbon::parse($selectedPeriode->tanggal_mulai)->format('Y')); ?>.
        <?php else: ?>
            Menampilkan semua data. Pilih tahun untuk melihat arsip tahun sebelumnya.
        <?php endif; ?>

    </p>
</div><?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/partials/periode-filter.blade.php ENDPATH**/ ?>