<div class="content-main__header bg-primary">
    <label for="input_search_query" class="fw-bold">Created time</label>
    <div class="action__form-search">
        <div class="form__search">
            <div>
                <input type="text" name="daterange" class="form-control" id="input_search_query" autocomplete="off" value="{{request('begin') ? date('m/d/Y', strtotime(request('begin'))).' - ' : ''}} {{ request('end') ? date('m/d/Y', strtotime(request('end'))) : ''}}">
                <span class="icon__down">
                    <i class="fa-regular fa-calendar"></i>
                </span>
            </div>
        </div>
        <div class="action__form">
            <a href="{{ route('administrator.'.$prefixView.'.create') }}" class="custom_button btn__create">Create</a>
            <button class="custom_button btn__filter" onclick="onSearchQuery()">Filter</button>
            <a href="{{ route('administrator.'.$prefixView.'.index') }}" class="custom_button btn__reset">Reset</a>
        </div>
    </div>
</div>


<script>

    function onSearchQuery() {
        addUrlParameterObjects([
            {name: "branch_id", value: $('select[name="branch_id"]').val()},
        ])
    }

    function deleteparamurl(searchParams) {
        searchParams.delete("branch_id")
    }


    $('input[name="daterange"]').on('apply.daterangepicker', function (ev, picker) {
        window.location.href = '{{ route('administrator.'.$prefixView.'.index') }}'+'?begin='+picker.startDate.format('YYYY-MM-DD')+'&end='+picker.endDate.format('YYYY-MM-DD');
    })

</script>
