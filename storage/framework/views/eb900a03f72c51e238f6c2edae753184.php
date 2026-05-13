<?php if($paginator->hasPages()): ?>
    <div class="pagination">
        <div class="muted">
            Halaman <?php echo e($paginator->currentPage()); ?>

            <?php if(method_exists($paginator, 'lastPage')): ?>
                dari <?php echo e($paginator->lastPage()); ?>

            <?php endif; ?>
        </div>
        <div class="pager-links">
            <?php if($paginator->onFirstPage()): ?>
                <span class="disabled">Sebelumnya</span>
            <?php else: ?>
                <a href="<?php echo e($paginator->previousPageUrl()); ?>">Sebelumnya</a>
            <?php endif; ?>

            <?php if($paginator->hasMorePages()): ?>
                <a href="<?php echo e($paginator->nextPageUrl()); ?>">Berikutnya</a>
            <?php else: ?>
                <span class="disabled">Berikutnya</span>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH D:\kerjaan\Proyek_absensi\absensi-app\resources\views/pagination/simple.blade.php ENDPATH**/ ?>