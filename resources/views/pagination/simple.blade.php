<div class="pagination">
    <div class="muted">
        @if(method_exists($paginator, 'total'))
            Menampilkan {{ $paginator->count() }} dari {{ $paginator->total() }} data
            <br>
        @endif
        Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
    </div>
    <div class="pager-links">
        @if ($paginator->onFirstPage())
            <span class="disabled">Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">Sebelumnya</a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">Berikutnya</a>
        @else
            <span class="disabled">Berikutnya</span>
        @endif
    </div>
</div>
