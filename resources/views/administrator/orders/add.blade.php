@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')

    <link rel="stylesheet" href="{{ asset('assets/administrator/css/vendors/date-picker.css') }}">

@endsection

@section('content')

    <div class="content-main">
        <div class="content-main__header">
            <h3 class="main-header__title fw-bold">Infomation {{ $title }}</h3>
            <hr>
            <div class="form-group__layout">
                <div class="form-group">
                    <label for="code" class="fw-bold">Code</label>
                    <input type="text" id="code" name="code" class="form-control input__field" placeholder="Enter code">
                </div>
                <div class="form-group">
                    <label for="charge" class="fw-bold">Service charge</label>
                    <input type="text" id="charge" name="charge" class="form-control input__field" placeholder="Enter charge">
                </div>
                <div class="form-group">
                    <label for="staff" class="fw-bold">Staff</label>
                    <select name="staff" class="select2_init" id="staff" onchange="chooseStaff()">
                        <option value="">Choose staff</option>
                        @foreach($staffs as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="tips" class="fw-bold">Tips</label>
                    <input type="text" id="tips" name="tips" class="form-control input__field" placeholder="Enter tips">
                </div>
                <div class="form-group">
                    <label for="deposit" class="fw-bold">Deposit</label>
                    <input type="text" id="deposit" name="deposit" class="form-control input__field" placeholder="Enter deposit">
                </div>
                <div class="form-group">
                    <label for="branch" class="fw-bold">Branch</label>
                    <input type="text" id="branch" name="branch" class="form-control input__field" placeholder="Enter branch">
                </div>
                <div class="form-group">
                    <label for="payment" class="fw-bold">Payment</label>
                    <select name="payment" class="select2_init" id="payment">
                        <option value="">Choose payment</option>
                        @foreach($payment as $pay)
                            <option value="{{ $pay->id }}">{{ $pay->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="notes" class="fw-bold">Note</label>
                    <input type="text" id="notes" name="note" class="form-control input__field" placeholder="Enter note">
                </div>
                <div class="form-group">
                    <label for="avatar" class="fw-bold">Scan bill</label>
                    @include('administrator.components.upload_multiple_images', ['post_api' => $imageMultiplePostUrl, 'delete_api' => $imageMultipleDeleteUrl , 'sort_api' => $imageMultipleSortUrl, 'table' => $table , 'images' => $imagesPath,'relate_id' => $relateImageTableId])
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script>
        $("#payment").select2({
            placeholder: "Choose payment",
            width: '100%',
        });
        $("#staff").select2({
            placeholder: "Choose staff",
            width: '100%',
        });

        function chooseStaff() {
            callAjax(
                'GET',
                '{{ route('ajax.administrator.orders.choose') }}',
                {
                    'staff_id' : $('select[name="staff"]').val(),
                },
                (response) => {
                    $('input[name="branch"]').val(response.value);
                }
            );
        }

        function create{{ $prefixView }}() {
            callAjax(
                'POST',
                '{{ route('administrator.'.$prefixView.'.store') }}',
                {
                    'code' : $('input[name="code"]').val(),
                    'charge' : $('input[name="charge"]').val(),
                    'staff' : $('select[name="staff"]').val(),
                    'tips' : $('input[name="tips"]').val(),
                    'payment' : $('select[name="payment"]').val(),
                    'note' : $('input[name="note"]').val(),
                    'deposit' : $('input[name="deposit"]').val(),
                },
                (response) => {
                    if(response.status == true){
                        window.location.href = (response.url);
                    }else{
                        $.toastr.error(response.message, {

                            time: 10000,

                            // 'top-left', 'top-center', 'top-right', 'right-bottom', 'bottom-center', 'left-bottom'
                            position: 'top-center',

                            // 'lg', 'sm', 'xs'
                            size: 'lg',

                            // callback
                            callback: function () {}

                        });
                    }
                }
            )
        }
    </script>

@endsection

