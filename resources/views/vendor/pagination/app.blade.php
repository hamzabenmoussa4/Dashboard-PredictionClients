@if ($paginator->hasPages())
    <nav class="pager" role="navigation" aria-label="Pagination Navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="pager-btn disabled">‹ Précédent</span>
        @else
            <a class="pager-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Précédent</a>
        @endif

        {{-- Pagination Elements --}}
        <div class="pager-pages">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="pager-dots">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pager-page active">{{ $page }}</span>
                        @else
                            <a class="pager-page" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a class="pager-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Suivant ›</a>
        @else
            <span class="pager-btn disabled">Suivant ›</span>
        @endif
    </nav>

    <style>
        .pager{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            margin-top:16px;
            padding:12px;
            border:1px solid #e5e7eb;
            border-radius:12px;
            background:#fff;
            flex-wrap:wrap;
        }
        .pager-btn{
            padding:10px 12px;
            border:1px solid #e5e7eb;
            border-radius:10px;
            text-decoration:none;
            font-weight:800;
            font-size:14px;
            color:#111827;
            background:#f9fafb;
        }
        .pager-btn:hover{
            background:#eef2ff;
            border-color:#6366f1;
        }
        .pager-btn.disabled{
            opacity:.5;
            cursor:not-allowed;
            pointer-events:none;
        }
        .pager-pages{
            display:flex;
            gap:6px;
            flex-wrap:wrap;
            align-items:center;
            justify-content:center;
        }
        .pager-page{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:40px;
            height:40px;
            border:1px solid #e5e7eb;
            border-radius:10px;
            text-decoration:none;
            font-weight:900;
            color:#111827;
            background:#fff;
        }
        .pager-page:hover{
            background:#eef2ff;
            border-color:#6366f1;
        }
        .pager-page.active{
            background:#111827;
            color:#fff;
            border-color:#111827;
        }
        .pager-dots{
            padding:0 8px;
            font-weight:900;
            color:#6b7280;
        }
    </style>
@endif
