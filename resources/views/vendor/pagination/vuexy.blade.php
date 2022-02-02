@if ($paginator->hasPages())
<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center mt-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
        <li class="page-item prev disabled" aria-disabled="true" aria-label="@lang('pagination.previous')"><a
                class="page-link" href="#"></a>
        </li>
        @else
        <li class="page-item prev" aria-label="@lang('pagination.previous')"><a class="page-link"
                href="{{ $paginator->previousPageUrl() }}"></a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
        <li class="page-item disabled" aria-disabled="true">{{ $element }}</li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <li class="page-item active" aria-current="page"><a class="page-link" href="#">{{$page}}</a></li>
        @else
        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
        @endif
        @endforeach
        @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
        <li class="page-item next"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"
                aria-label="@lang('pagination.next')"></a></li>
        @else
        <li class="page-item next disabled"><a class="page-link" href="#"></a></li>
        @endif
    </ul>
</nav>
@endif
