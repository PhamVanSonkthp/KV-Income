<nav>
    @if ($paginator->hasPages())
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <a class="page-link" href="javascript:void(0)">
                        <i class="fa-solid fa-chevron-left text-secondary"></i>
                    </a>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}">
                        <i class="fa-solid fa-chevron-left text-secondary"></i>
                    </a>
                </li>
            @endif

            <li class="page-item">
                <input type="text" value="{{ $paginator->currentPage() }}" name="paged" class="form-control input__field text-center">
            </li>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}">
                        <i class="fa-solid fa-chevron-right text-secondary"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <a class="page-link" href="javascript:void(0)">
                        <i class="fa-solid fa-chevron-right text-secondary"></i>
                    </a>
                </li>
            @endif

            <span class="page-item total__value">of {{ $paginator->lastPage() }}</span>

            @if((strpos(route('administrator.employees.index'), $_SERVER['REQUEST_URI'])) || (strpos($_SERVER['REQUEST_URI'], 'employees?') !== false) || (strpos(route('administrator.orders.index'), $_SERVER['REQUEST_URI'])) || (strpos($_SERVER['REQUEST_URI'], 'orders?') !== false))
            <li class="page-item">
                <a class="page-link text-primary" href="{{ route('administrator.'.$prefixView.'.export', ['begin' => request('begin'), 'end' => request('end')]) }}">
                    Down file
                    <i class="fa-solid fa-cloud-arrow-down text-secondary ms-2"></i>
                </a>
            </li>
            @endif
        </ul>
    @else
        <ul class="pagination">
            <li class="page-item disabled">
                <a class="page-link" href="javascript:void(0)">
                    <i class="fa-solid fa-chevron-left text-secondary"></i>
                </a>
            </li>
            <li class="page-item">
                <input type="text" value="1" name="paged" class="form-control input__field text-center">
            </li>
            <li class="page-item disabled">
                <a class="page-link" href="javascript:void(0)">
                    <i class="fa-solid fa-chevron-right text-secondary"></i>
                </a>
            </li>
            <span class="page-item total__value">of {{ $paginator->lastPage() }}</span>
            @if((strpos(route('administrator.employees.index'), $_SERVER['REQUEST_URI'])) || (strpos($_SERVER['REQUEST_URI'], 'employees?') !== false) || (strpos(route('administrator.orders.index'), $_SERVER['REQUEST_URI'])) || (strpos($_SERVER['REQUEST_URI'], 'orders?') !== false))
            <li class="page-item">
                <a class="page-link text-primary" href="{{ route('administrator.'.$prefixView.'.export', ['begin' => request('begin'), 'end' => request('end')]) }}">
                    Down file
                    <i class="fa-solid fa-cloud-arrow-down text-secondary ms-2"></i>
                </a>
            </li>
            @endif
        </ul>
    @endif
</nav>
