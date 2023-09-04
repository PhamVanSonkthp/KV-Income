@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')

@endsection

@section('content')


    <div class="content-main">
        <div class="content-main__header">
            <h3 class="main-header__title fw-bold">{{ $title }}</h3>
            <hr>
            <div class="form-group__layout">
                <div class="form-group">
                    <label for="id" class="fw-bold">ID</label>
                    <input type="text" id="id" class="form-control input__field" value="{{ $item->user_id }}">
                </div>
                <div class="form-group">
                    <label for="name" class="fw-bold">Admin user</label>
                    <input type="text" id="name" class="form-control input__field" value="{{ optional($item->user)->name }}">
                </div>
                <div class="form-group">
                    <label for="time" class="fw-bold">Create time </label>
                    <input type="text" id="time" class="form-control input__field" value="{{ \App\Models\Helper::convert_date_from_db($item->created_at) }}">
                </div>
                <div class="form-group">
                    <label for="active" class="fw-bold">Active</label>
                    <input type="text" id="active" class="form-control input__field" value=" {{$item->event}}">
                </div>
                <div class="form-group">
                    <label for="model_id" class="fw-bold">Model id</label>
                    <input type="text" id="model_id" class="form-control input__field" value="{{$item->auditable_type}}">
                </div>
                <div class="form-group">
                    <label for="model_id" class="fw-bold">Data</label>
                    <div class="form-control">
                        @foreach((json_decode(($item->new_values) , true)) as $key=>$value)
                            <p>
                                {{$key}} : {{$value}}
                            </p>
                        @endforeach
                    </div>
                </div>
            </div>
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

@endsection
