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
                <div class="form-group">
                    <label for="code" class="fw-bold">Code</label>
                    <input type="text" id="code" class="form-control input__field" value="{{ $item->code }}">
                </div>
                <div class="form-group">
                    <label for="phone" class="fw-bold">Phone number</label>
                    <input type="text" id="phone" class="form-control input__field" value="{{ $item->phone }}">
                </div>
                <div class="form-group">
                    <label for="start" class="fw-bold">Create time</label>
                    <input type="text" id="start" class="form-control input__field" value="{{ \App\Models\Helper::convert_date_from_db($item->created_at) }}">
                </div>
                <div class="form-group">
                    <label for="birthday" class="fw-bold">Birthday</label>
                    <input type="text" id="birthday" class="form-control input__field" value="{{ \App\Models\Helper::convert_date_from_db2($item->date_of_birth) }}">
                </div>
                <div class="form-group">
                    <label for="start" class="fw-bold">Start date</label>
                    <input type="text" id="start" class="form-control input__field" value="{{ \App\Models\Helper::convert_date_from_db2($item->start) }}">
                </div>
                <div class="form-group">
                    <label for="branch" class="fw-bold">Branch</label>
                    <input type="text" id="branch" class="form-control input__field" value="{{ optional($item->branch)->name }}">
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="form-group">
                            <label for="address" class="fw-bold">Address</label>
                            <input type="text" id="address" class="form-control input__field" value="{{ $item->address }}">
                        </div>
                        <div class="form-group">
                            <label for="create_by" class="fw-bold">Admin user create</label>
                            <input type="text" id="create_by" class="form-control input__field" value="{{ optional($item->user)->name }}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="avatar" class="fw-bold">Avatar</label>
                    @include('administrator.components.upload_image', ['post_api' => $imagePostUrl, 'table' => $table, 'image' => $imagePathSingple , 'relate_id' => $relateImageTableId])
                </div>
            </div>
        </div>
    </div>

@endsection

<style>
    .swal2-popup{
        font-size: 14px !important;
    }
    .swal2-html-container{
        font-size: 32px !important;
        font-weight: bold !important;
        text-transform: uppercase;
        color: #E45555 !important;
    }
    .btn{
        font-size: 18px !important;
    }
    .btn-danger {
        background-color: #E45555;
        border-color: #E45555;
    }
</style>

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

