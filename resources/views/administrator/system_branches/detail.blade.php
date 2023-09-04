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
                    <input type="text" id="id" class="form-control input__field" value="{{ $item->id }}">
                </div>
                <div class="form-group">
                    <label for="name" class="fw-bold">Name</label>
                    <input type="text" id="name" class="form-control input__field" value="{{ $item->name }}">
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
