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
                <select class="form-control" name="admin_user">
                    <option value="">All admin user</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('admin_group') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                <span class="icon__down">
                    <i class="fa-solid fa-chevron-down text-secondary"></i>
                </span>
            </div>
        </div>
        <div class="action__form">
            <button class="custom_button btn__filter" onclick="onSearchQuery()">Filter</button>
            <a href="{{ route('administrator.audits.index') }}" class="custom_button btn__reset">Reset</a>
        </div>
    </div>
</div>


<script>

    function onSearchQuery() {
        addUrlParameterObjects([
            {name: "admin_user", value: $('select[name="admin_user"]').val()},
        ])
    }


    $('input[name="daterange"]').on('apply.daterangepicker', function (ev, picker) {
        window.location.href = '{{ route('administrator.audits.index') }}'+'?begin='+picker.startDate.format('YYYY-MM-DD')+'&end='+picker.endDate.format('YYYY-MM-DD');
    })
</script>
