@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')

@endsection

@section('content')
    @php
        $total = $subtotal_tips = $subtotal_cash = 0;
    @endphp
    @foreach($items as $item)
        @php
            $value = \App\Models\Order::where('user_id', $item->id);
            if(request('begin')){
                $value = $value->whereDate('created_at', '>=', request('begin'));
            }

            if(request('end')){
                $value = $value->whereDate('created_at', '<=', request('end'));
            }

            $subtotal = $value->sum('service_charge');
            $subtotal_tips += $value->sum('tips');
            $subtotal_cash += $value->where('payment_type_id', 4)->sum(\Illuminate\Support\Facades\DB::raw('service_charge - deposit'));
            $total += $subtotal;
        @endphp
    @endforeach

    <div class="content-main">
        <div class="content-main__header">
            <div class="header__filter">
                <div class="title__total" style="display: flex; align-items: center; gap: 50px">
                    <h3 class="main-header__title fw-bold">{{ $name_br }}: ${{ number_format($total, '2') }}</h3>
                    <h3 class="main-header__title fw-bold">TOTAL CASH RETURN: ${{ number_format($subtotal_cash - $subtotal_tips, '2') }}</h3>
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
                <div id="column-chart"></div>
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
                            <td>
                                <a href="{{ route('administrator.salaries.detail', ['id' => $item->id]) }}">
                                    {{ $item->id }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('administrator.salaries.detail', ['id' => $item->id]) }}">
                                    {{ $item->name }}
                                </a>
                            </td>
                            <td>{{ \App\Models\Helper::convert_date_from_db2($item->start) }}</td>
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
        var options3 = {
            chart: {
                height: 300,
                type: 'bar',
                toolbar:{
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    endingShape: 'rounded',
                    columnWidth: '50%',
                    dataLabels: {
                        position: 'top',
                    },
                },
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(2);
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            series: [ {
                name: 'Salary',
                data: @json($total_order)
            }, {
                name: 'Cash Return',
                data: @json($cash_return)
            }],
            xaxis: {
                categories: @json($name_user),
            },

            yaxis: {
                labels: {
                    formatter: function (val) {
                        return "$" + val.toFixed(2);
                    }
                }
            },

            fill: {
                opacity: 1

            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "$" + val.toFixed(2);
                    }
                }
            },
            colors:['#F9A71D', '#00E39A']
        }

        var chart3 = new ApexCharts(
            document.querySelector("#column-chart"),
            options3
        );

        chart3.render();
    </script>

    <script>
        $('input[name="daterange"]').on('apply.daterangepicker', function (ev, picker) {
            window.location.href = '{{ route('administrator.'.$prefixView.'.branch', ['id' => $id]) }}'+'?begin='+picker.startDate.format('YYYY-MM-DD')+'&end='+picker.endDate.format('YYYY-MM-DD');
        })
    </script>

@endsection

