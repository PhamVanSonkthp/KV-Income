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
            <div>
                <select class="form-control" name="branch_id">
                    <option value="">All Branches</option>
                    @foreach($branchs as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                <span class="icon__down">
                    <i class="fa-solid fa-chevron-down text-secondary"></i>
                </span>
            </div>
        </div>
        <div class="action__form">
            <form id="formImport" action="{{ route('administrator.employees.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" id="file" style="display:none;" />
            </form>
            <button class="custom_button btn__create" onclick="importFile()">Import</button>
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

    function importFile() {
        $('#file').click();
    }

    $('#file').change(function () {
       $('#formImport').submit();
    })
</script>
