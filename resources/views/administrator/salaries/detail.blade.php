@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')

@endsection

@section('content')
    @php
        $total = 0;
    @endphp
    @php
        $value = \App\Models\Order::where('user_id', $id);
        if(request('begin')){
            $value = $value->whereDate('created_at', '>=', request('begin'));
        }

        if(request('end')){
            $value = $value->whereDate('created_at', '<=', request('end'));
        }

        $subtotal = $value->sum('service_charge');
        $total += $subtotal;
    @endphp
    <div class="content-main">
        <div class="content-main__header">
            <div class="header__filter">
                <h3 class="main-header__title fw-bold">{{ $name }}: ${{ number_format($total, '2') }}</h3>
                <div class="form__search">
                    <input type="text" name="daterange" value="{{request('begin') ? date('m/d/Y', strtotime(request('begin'))).' - ' : ''}} {{ request('end') ? date('m/d/Y', strtotime(request('end'))) : ''}}" class="form-control" id="time" autocomplete="off">
                    <span class="icon__down" style="top: 27px; right: 12px">
                        <i class="fa-regular fa-calendar"></i>
                    </span>
                </div>
            </div>

            <hr>
            @include('administrator.roles.total')
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead style="background-color: #ADB1B9;">
                    <th scope="col" width="3%">
                        <label class="control control--checkbox">
                            <input type="checkbox" class="js-check-all">
                            <div class="control__indicator"></div>
                        </label>
                    </th>
                    <th scope="col" width="7%">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Created time</th>
                    <th scope="col">Branch</th>
                    <th scope="col">Service charge</th>
                    <th scope="col">Tipss</th>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        <tr id="item{{ $item->id }}">
                            <th scope="row">
                                <label class="control control--checkbox">
                                    <input type="checkbox">
                                    <div class="control__indicator"></div>
                                </label>
                            </th>
                            <td>
                                {{ $item->id }}
                            </td>
                            <td>
                                {{ optional($item->user)->name }}
                            </td>
                            <td>{{ \App\Models\Helper::convert_date_from_db($item->created_at) }}</td>
                            <td>
                                {{ optional(optional($item->user)->branch)->name }}
                            </td>
                            <td>${{ number_format($item->service_charge, '2') }}</td>
                            <td>${{ number_format($item->tips, '2') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @include('administrator.roles.total')
        </div>
    </div>


    <style>
        .total__show{
            padding: 30px 0 16px;
        }
    </style>
@endsection

@section('js')

    <script>
        $('input[name="daterange"]').on('apply.daterangepicker', function (ev, picker) {
            window.location.href = '{{ route('administrator.'.$prefixView.'.detail', ['id' => $id]) }}'+'?begin='+picker.startDate.format('YYYY-MM-DD')+'&end='+picker.endDate.format('YYYY-MM-DD');
        })
    </script>

@endsection

