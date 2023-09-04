@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@endsection

@section('content')

    <div class="content-main">
        <div class="content-main__header">
            <h3 class="main-header__title fw-bold">Infomation {{ $title }}</h3>
            <hr>
            <div class="form-group__layout">
                <div class="form-group">
                    <label for="code" class="fw-bold">Code</label>
                    <input type="text" id="code" class="form-control input__field" value="{{ isset($item) && !empty($item) ? $item->code : '' }}" placeholder="Enter code">
                </div>
                <div class="form-group">
                    <label for="charge" class="fw-bold">Service charge</label>
                    <input type="text" id="charge" name="charge" class="form-control input__field" placeholder="Enter charge" value="{{ isset($item) && !empty($item) ? $item->service_charge : '' }}">
                </div>
                <div class="form-group">
                    <label for="created_at" class="fw-bold">Created at</label>
                    <input type="text" id="created_at" name="created_at" class="form-control input__field" placeholder="Enter date" value="{{ isset($item) && !empty($item) ? \App\Models\Helper::convert_date_from_db($item->created_at) : '' }}">
                    <span class="icon">
                         <i class="fa-regular fa-calendar"></i>
                    </span>
                </div>
                <div class="form-group">
                    <label for="staff" class="fw-bold">Staff</label>
                    <select name="staff" class="select2_init" onchange="chooseStaff()" id="staff">
                        <option value="">Choose staff</option>
                        @foreach($staffs as $staff)
                            <option value="{{ $staff->id }}" {{ isset($item) && !empty($item) && $item->user_id == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="tips" class="fw-bold">Tips</label>
                    <input type="text" id="tips" name="tips" class="form-control input__field" placeholder="Enter tips" value="{{ isset($item) && !empty($item) ? $item->tips : '' }}">
                </div>
                <div class="form-group">
                    <label for="branch" class="fw-bold">Branch</label>
                    <input type="text" id="branch" name="branch" class="form-control input__field" placeholder="Enter branch">
                </div>
                <div class="form-group">
                    <label for="deposit" class="fw-bold">Deposit</label>
                    <input type="text" id="deposit" name="deposit" class="form-control input__field" placeholder="Enter deposit" value="{{ isset($item->deposit) && !empty($item->deposit) ? $item->deposit : 0 }}">
                </div>
                <div class="form-group">
                    <label for="payment" class="fw-bold">Payment</label>
                    <select name="payment" class="select2_init" id="payment">
                        <option value="">Choose payment</option>
                        @foreach($payment as $pay)
                            <option value="{{ $pay->id }}" {{ isset($item) && !empty($item) && $item->payment_type_id == $pay->id ? 'selected' : '' }}>{{ $pay->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="notes" class="fw-bold">Note</label>
                    <input type="text" id="notes" name="note" class="form-control input__field" value="{{ isset($item) && !empty($item) ? $item->note : '' }}" placeholder="Enter note">
                </div>
                <div class="form-group">
                    <label for="avatar" class="fw-bold">Scan bill</label>
                    @include('administrator.components.upload_multiple_images', ['post_api' => $imageMultiplePostUrl, 'delete_api' => $imageMultipleDeleteUrl , 'sort_api' => $imageMultipleSortUrl, 'table' => $table , 'images' => $imagesPath,'relate_id' => $relateImageTableId])
                </div>
            </div>
        </div>
    </div>

    <style>
        .icon{
            position: absolute;
            right: 10px;
            top: 55px;
        }
        #created_at{
            background-color: #fff;
        }
    </style>
@endsection

@section('js')

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $('#created_at').flatpickr({
            enableTime: true,
            dateFormat: "m/d/Y H:i:ss",
        });
        $("#payment").select2({
            placeholder: "Choose payment",
            width: '100%',
        });
        $("#staff").select2({
            placeholder: "Choose staff",
            width: '100%',
        });

        callAjaxNotLoad(
            'GET',
            '{{ route('ajax.administrator.orders.choose') }}',
            {
                'staff_id' : {{ isset($item) && !empty($item) ? $item->user_id : 0 }},
            },
            (response) => {
                $('input[name="branch"]').val(response.value);
            }
        );

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

        function update{{ $prefixView }}(id) {
            callAjax(
                'PUT',
                '{{ route('administrator.'.$prefixView.'.update') }}?id='+id,
                {
                    'charge' : $('input[name="charge"]').val(),
                    'staff' : $('select[name="staff"]').val(),
                    'tips' : $('input[name="tips"]').val(),
                    'payment' : $('select[name="payment"]').val(),
                    'note' : $('input[name="note"]').val(),
                    'deposit' : $('input[name="deposit"]').val(),
                    'created_at' : $('input[name="created_at"]').val(),
                },
                (response) => {
                    if(response.status == true){
                        window.location.href = (response.url);
                    }else{
                        $.toastr.error(response.message, {

                            time: 3000,

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

