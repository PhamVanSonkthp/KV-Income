<div class="total__show">
    <div class="pagination-container">
        @include('administrator.components.pagination', ['paginator' => $items])
    </div>
    <div class="number__show">
        <select class="form-control" name="show">
            <option value="50" {{ request('show') == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ request('show') == 100 ? 'selected' : '' }}>100</option>
            <option value="150" {{ request('show') == 150 ? 'selected' : '' }}>150</option>
            <option value="200" {{ request('show') == 200 ? 'selected' : '' }}>200</option>
            <option value="300" {{ request('show') == 300 ? 'selected' : '' }}>300</option>
            <option value="500" {{ request('show') == 500 ? 'selected' : '' }}>500</option>
        </select>
        <span class="icon__down">
            <i class="fa-solid fa-chevron-down text-secondary"></i>
        </span>
    </div>
</div>

<script>
    $('select[name="show"]').on('change', function () {
        addUrlParameter('show', this.value)
    });
</script>
