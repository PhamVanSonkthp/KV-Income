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
                    <label for="charge" class="fw-bold">Service charge</label>
                    <input type="text" id="charge" class="form-control input__field" value="${{ number_format($item->service_charge) }}">
                </div>
                <div class="form-group">
                    <label for="code" class="fw-bold">Code</label>
                    <input type="text" id="code" class="form-control input__field" value="{{ $item->code }}">
                </div>
                <div class="form-group">
                    <label for="tips" class="fw-bold">Tips</label>
                    <input type="text" id="tips" class="form-control input__field" value="${{ number_format($item->tips) }}">
                </div>
                <div class="form-group">
                    <label for="phone" class="fw-bold">Staff</label>
                    <input type="text" id="phone" class="form-control input__field" value="{{ optional($item->user)->name }}">
                </div>
                <div class="form-group">
                    <label for="deposit" class="fw-bold">Deposit</label>
                    <input type="text" id="deposit" name="deposit" class="form-control input__field" placeholder="Enter deposit" value="${{ isset($item->deposit) && !empty($item->deposit) ? $item->deposit : 0 }}">
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="form-group">
                            <label for="Payment" class="fw-bold">Payment</label>
                            <input type="text" id="Payment" class="form-control input__field" value="{{ optional($item->paymentType)->name }}">
                        </div>
                        <div class="form-group" style="padding-top: 0">
                            <label for="create" class="fw-bold">Create time</label>
                            <input type="text" id="create" class="form-control input__field" value="{{ \App\Models\Helper::convert_date_from_db($item->created_at) }}">
                        </div>
                        <div class="form-group">
                            <label for="branch" class="fw-bold">Branch</label>
                            <input type="text" id="branch" class="form-control input__field" value="{{ optional($item->branch)->name }}">
                        </div>
                        <div class="form-group">
                            <label for="note" class="fw-bold">Note</label>
                            <input type="text" id="note" class="form-control input__field" value="{{ $item->note }}">
                        </div>
                        <div class="form-group">
                            <label for="create_by" class="fw-bold">Admin user create</label>
                            <input type="text" id="create_by" class="form-control input__field" value="{{ optional($item->user_create)->name }}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="bill" class="fw-bold">Scan bill</label>
                    @include('administrator.components.upload_multiple_images', ['post_api' => $imageMultiplePostUrl, 'delete_api' => $imageMultipleDeleteUrl , 'sort_api' => $imageMultipleSortUrl, 'table' => $table , 'images' => $imagesPath,'relate_id' => $relateImageTableId])
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
    callAjaxNotLoad(
        'GET',
        '{{ route('ajax.administrator.orders.choose') }}',
        {
            'staff_id' : {{ isset($item) && !empty($item) ? $item->user_id : 0 }},
        },
        (response) => {
            $('#branch').val(response.value);
        }
    );

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

