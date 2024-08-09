@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')

    <style>
        .table>:not(caption)>*>*{
            border-bottom-width: 5px;
        }
    </style>

@endsection

@section('content')

        <div class="content-main">
            @include('administrator.'.$prefixView.'.search')
            <div class="content-main__body">
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
                        <th scope="col" width="10%">Bill code</th>
                        <th scope="col" width="15%">Created time</th>
                        <th scope="col" width="20%">Branch</th>
                        <th scope="col" width="10%">Staff</th>
                        <th scope="col" width="10%">Deposit</th>
                        <th scope="col" width="10%">Payment</th>
                        <th scope="col" width="10%">Service chage</th>
                        <th scope="col" width="5%">Tips </th>
                        <th scope="col" width="1%">
                            @include('administrator.components.checkbox_delete_table')
                        </th>
                        </thead>
                        <tbody>
                        @foreach($items as $item)

                            <tr id="{{ $item->id }}" style="background-color: {{$item->backgroundColor(false)}}">
                                <th scope="row">
                                    <label class="control control--checkbox">
                                        <input type="checkbox" value="{{ $item->id }}" class="checkbox-delete-item">
                                        <div class="control__indicator"></div>
                                    </label>
                                </th>
                                <td>
                                    <a href="{{ route('administrator.'.$prefixView.'.detail', ['id' => $item->id]) }}">
                                        {{ $item->id }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('administrator.'.$prefixView.'.detail', ['id' => $item->id]) }}">
                                        {{ $item->code }}
                                    </a>

                                </td>
                                <td>{{ \App\Models\Helper::convert_date_from_db($item->created_at) }}</td>
                                <td>
                                    {{ optional(optional($item->user)->branch)->name }}
                                </td>
                                <td>
                                    {{ optional($item->user)->name }}
                                </td>
                                <td>
                                    ${{ number_format($item->deposit) }}
                                </td>
                                <td>
                                    {{ optional($item->payment)->name }}
                                </td>
                                <td>
                                    ${{ number_format($item->service_charge) }}
                                </td>
                                <td>
                                    ${{ number_format($item->tips) }}
                                </td>
                                <td>
                                    <a href="{{route('administrator.'.$prefixView.'.delete' , ['id'=> $item->id])}}"
                                       data-url="{{route('administrator.'.$prefixView.'.delete' , ['id'=> $item->id])}}"
                                       class="custom_button btn__reset action_delete delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @include('administrator.roles.total')
            </div>
        </div>

@endsection

@section('js')

    <script>
        @if(session()->has('message'))
        $.toastr.success('{{ session()->get('message') }}', {

            time: 3000,

            // 'top-left', 'top-center', 'top-right', 'right-bottom', 'bottom-center', 'left-bottom'
            position: 'top-center',

            // 'lg', 'sm', 'xs'
            size: 'lg',

            // callback
            callback: function () {}

        });
        @endif
        {{ session()->forget('message') }}
    </script>

@endsection
