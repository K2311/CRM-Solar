@if ($paginator->hasPages())
    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--border); font-size: 0.875rem;">
        
        <div style="color: var(--text-muted);">
            Showing <span style="font-weight: 600; color: var(--text-main);">{{ $paginator->firstItem() }}</span> 
            to <span style="font-weight: 600; color: var(--text-main);">{{ $paginator->lastItem() }}</span> 
            of <span style="font-weight: 600; color: var(--text-main);">{{ $paginator->total() }}</span> results
        </div>

        <div style="display: flex; gap: 0.25rem;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="btn btn-outline" style="opacity: 0.5; pointer-events: none; padding: 0.4rem 0.8rem;">&laquo; Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-outline" style="padding: 0.4rem 0.8rem;">&laquo; Prev</a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="btn btn-outline" style="opacity: 0.5; pointer-events: none; padding: 0.4rem 0.8rem;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="btn btn-primary" style="padding: 0.4rem 0.8rem; background: var(--primary); border-color: var(--primary);">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="btn btn-outline" style="padding: 0.4rem 0.8rem;">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-outline" style="padding: 0.4rem 0.8rem;">Next &raquo;</a>
            @else
                <span class="btn btn-outline" style="opacity: 0.5; pointer-events: none; padding: 0.4rem 0.8rem;">Next &raquo;</span>
            @endif
        </div>
    </div>
@endif
