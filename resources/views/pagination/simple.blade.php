@if ($paginator->hasPages())
    <div class="pagination">
        <div class="muted">
            Halaman {{ $paginator->currentPage() }}
            @if(method_exists($paginator, 'lastPage'))
                dari {{ $paginator->lastPage() }}
            @endif
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
@endif
