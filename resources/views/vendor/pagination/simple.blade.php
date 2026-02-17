@if ($paginator->hasPages())
    <nav class="pager" role="navigation" aria-label="Pagination Navigation">
        @if ($paginator->onFirstPage())
            <span class="pager-btn disabled">‹ Précédent</span>
        @else
            <a class="pager-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Précédent</a>
        @endif

        @if ($paginator->hasMorePages())
            <a class="pager-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Suivant ›</a>
        @else
            <span class="pager-btn disabled">Suivant ›</span>
        @endif
    </nav>
@endif
