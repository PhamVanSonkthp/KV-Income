@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')

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
                <th scope="col">Admin user</th>
                <th scope="col">Model id</th>
                <th scope="col" width="20%">Created time</th>
                <th scope="col" width="10%%">Action</th>
                <th scope="col" width="30%">Data</th>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr id="{{ $item->id }}">
                        <th scope="row">
                            <label class="control control--checkbox">
                                <input type="checkbox" value="{{ $item->id }}">
                                <div class="control__indicator"></div>
                            </label>
                        </th>
                        <td>
                            <a href="{{ route('administrator.audits.detail', ['id' => $item->id]) }}">
                                {{ $item->id }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('administrator.audits.detail', ['id' => $item->id]) }}">
                                {{ optional($item->user)->name}}
                            </a>

                        </td>
                        <td>{{$item->auditable_type}}</td>
                        <td>
                            {{ \App\Models\Helper::convert_date_from_db($item->created_at) }}
                        </td>
                        <td>
                            {{$item->event}}
                        </td>
                        <td>
                            @foreach((json_decode(($item->new_values) , true)) as $key=>$value)
                                <p>
                                    {{$key}} : {{$value}}
                                </p>
                            @endforeach
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

@endsection
