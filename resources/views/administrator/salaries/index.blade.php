@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')

@endsection

@section('content')
    @php
        $total = $subtotal_tips = $subtotal_cash = 0;
    @endphp
    @foreach($branches as $branch)
        @php
            $value = \App\Models\Order::with('user');
            if(request('begin')){
                $value = $value->whereDate('created_at', '>=', request('begin'));
            }

            if(request('end')){
                $value = $value->whereDate('created_at', '<=', request('end'));
            }

            $subtotal = $value->whereHas('user', function ($query) use ($branch){
                    $query->where('branch_id', $branch->id);
            })->sum('service_charge');

            $total_tips = $value->whereHas('user', function ($query) use ($branch){
                    $query->where('branch_id', $branch->id);
            })->sum('tips');
            $total_cash = $value->where('payment_type_id', 4)->whereHas('user', function ($query) use ($branch){
                    $query->where('branch_id', $branch->id);
            })->sum(\Illuminate\Support\Facades\DB::raw('service_charge - deposit'));
            $subtotal_tips += $total_tips;
            $subtotal_cash += $total_cash;
            $total += $subtotal;
        @endphp
    @endforeach
    <div class="content-main">
        <div class="content-main__header">
            <div class="header__filter">
                <div class="title__total" style="display: flex; align-items: center; gap: 50px">
                    <h3 class="main-header__title fw-bold">Total salary: ${{ number_format($total, 2) }}</h3>
                    <h3 class="main-header__title fw-bold">Total cash return: ${{ number_format($subtotal_cash - $subtotal_tips, 2) }}</h3>
                </div>
                <div class="form__search">
                    <input type="text" name="daterange" value="{{request('begin') ? date('m/d/Y', strtotime(request('begin'))).' - ' : ''}} {{ request('end') ? date('m/d/Y', strtotime(request('end'))) : ''}}" class="form-control" id="time" autocomplete="off">
                    <span class="icon__down" style="top: 27px; right: 12px">
                        <i class="fa-regular fa-calendar"></i>
                    </span>
                </div>
            </div>

            <hr>
            <div class="statistical row">
                <div id="donutchart" class="col-md-6"></div>
                <div class="info__chart col-md-6">
                    @foreach($branches as $branch)
                        @php
                            $value = \App\Models\Order::with('user');
                            if(request('begin')){
                                $value = $value->whereDate('created_at', '>=', request('begin'));
                            }

                            if(request('end')){
                                $value = $value->whereDate('created_at', '<=', request('end'));
                            }
                            $salary = $value->whereHas('user', function ($query) use ($branch){
                                   $query->where('branch_id', $branch->id);
                               })->sum('service_charge');

                        @endphp
                        <div class="info__chart--item">
                            <a href="{{ route('administrator.salaries.branch', ['id' => $branch->id]) }}" class="custom_button form-control" style="background-color: {{ $branch->color }}">
                                <span class="total">${{ number_format($salary, '2') }}
                                    <i class="fa-solid fa-chevron-right"></i>
                                </span>
                                <span class="branch_name">
                                    {{ $branch->name }}
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="content-main__header">
            <h3 class="main-header__title fw-bold">Employee salary</h3>
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
                    <th scope="col" width="15%">Branch</th>
                    <th scope="col">Total cash</th>
                    <th scope="col">Total service charge</th>
                    <th scope="col">Cash return</th>
                    <th scope="col">Total tips</th>
                    <th scope="col">60/40</th>
                    <th scope="col">60/40/2</th>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        @php
                            $value = \App\Models\Order::with('user')->where('user_id', $item->id);
                           if(request('begin')){
                               $value->whereDate('created_at', '>=', request('begin'));
                           }

                           if(request('end')){
                                $value->whereDate('created_at', '<=', request('end'));
                           }
                           $total = $value->sum('service_charge');
                           $total_tips = $value->sum('tips');
                           $total_cash = $value->where('payment_type_id', 4)->sum(\Illuminate\Support\Facades\DB::raw('service_charge - deposit'));
                           $salary = $total * 60 / 100;
                           $half_salary = $salary / 2;

                        @endphp
                        <tr id="item{{ $item->id }}">
                            <th scope="row">
                                <label class="control control--checkbox">
                                    <input type="checkbox">
                                    <div class="control__indicator"></div>
                                </label>
                            </th>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ \App\Models\Helper::convert_date_from_db($item->created_at) }}</td>
                            <td>
                                {{ optional($item->branch)->name }}
                            </td>
                            <td>${{ number_format($total_cash, 2) }}</td>
                            <td>${{ number_format($total, '2') }}</td>
                            <td>${{ number_format($total_cash - $total_tips, 2) }}</td>
                            <td>${{ number_format($total_tips, '2') }}</td>
                            <td>${{ number_format($salary, '2') }}</td>
                            <td>${{ number_format($half_salary, '2') }}</td>
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

    <script type="text/javascript" src="{{ asset('vendor/chart/apex-chart/apex-chart.js') }}"></script>

    <script>
        // donut chart
        var options9 = {

            chart: {
                width: 500,
                type: 'donut',
            },
            legend: {
                show: false,
            },
            series: @json($total_order),
            labels: @json($name_br),
            colors: @json($color_br),
        }

        var chart9 = new ApexCharts(
            document.querySelector("#donutchart"),
            options9
        );

        chart9.render();
    </script>

    <script>
        $('input[name="daterange"]').on('apply.daterangepicker', function (ev, picker) {
            window.location.href = '{{ route('administrator.'.$prefixView.'.index') }}'+'?begin='+picker.startDate.format('YYYY-MM-DD')+'&end='+picker.endDate.format('YYYY-MM-DD');
        })
    </script>

@endsection

