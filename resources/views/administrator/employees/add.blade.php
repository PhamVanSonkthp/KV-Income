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
                    <label for="name" class="fw-bold">Name</label>
                    <input type="text" id="name" name="name" class="form-control input__field" placeholder="Enter admin user">
                </div>
                <div class="form-group">
                    <label for="password" class="fw-bold">Password</label>
                    <button class="custom_button icon_password" onclick="generatePassword()">
                        <i class="fa-solid fa-rotate"></i>
                    </button>
                    <input type="password" name="password" id="password" class="form-control input__field" placeholder="Enter password">
                </div>
                <div class="form-group">
                    <label for="phone" class="fw-bold">Phone number</label>
                    <input type="text" id="phone" name="phone" class="form-control input__field" placeholder="Enter phone number">
                </div>
                <div class="form-group">
                    <label for="start" class="fw-bold">Start date</label>
                    <input type="text" id="start" data-language="en" name="start" class="datepicker-here form-control input__field" placeholder="Enter date">
                    <span class="icon">
                         <i class="fa-regular fa-calendar"></i>
                    </span>
                </div>
                <div class="form-group">
                    <label for="birthday" class="fw-bold">Birthday</label>
                    <input type="text" id="birthday" data-language="en" name="birthday" class="datepicker-here form-control input__field" placeholder="Enter date">
                    <span class="icon">
                         <i class="fa-regular fa-calendar"></i>
                    </span>
                </div>
                <div class="form-group">
                    <label for="address" class="fw-bold">Address</label>
                    <input type="text" id="address" name="address" class="form-control input__field" placeholder="Enter address">
                </div>
                <div class="form-group">
                    <label for="branch" class="fw-bold">Branch</label>
                    <select name="branch" class="select2_init" id="branch">
                        <option value="">Choose branch</option>
                        @foreach($branchs as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"></div>
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
        color: #E45555 !important;
    }
    .btn{
        font-size: 18px !important;
    }
    .btn-danger {
        background-color: #E45555;
        border-color: #E45555;
    }
    .icon{
        position: absolute;
        right: 10px;
        top: 55px;
    }
</style>

@section('js')

    <script src="{{asset('vendor/datepicker/date-picker/datepicker.js')}}"></script>

    <script src="{{asset('vendor/datepicker/date-picker/datepicker.en.js')}}"></script>

    <script>
        $("#branch").select2({
            placeholder: "Choose Branch",
            width: '100%',
        });

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger',
            },
            confirmButtonText: 'Accept',
            buttonsStyling: false
        })

        function generatePassword() {
            callAjax(
                'GET',
                '{{ route('administrator.users.generate') }}',
                {},
                (response) => {
                    Swal.fire({
                        title: 'Reset password',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#E45555',
                        cancelButtonColor: '#ADB1B9',
                        confirmButtonText: 'Accept',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            swalWithBootstrapButtons.fire(
                                'New password',
                                response.password
                            )
                            $('input[name="password"]').val(response.password);
                        }
                    })
                },
            );
        }

        function create{{ $prefixView }}() {
            callAjax(
                'POST',
                '{{ route('administrator.'.$prefixView.'.store') }}',
                {
                    'code' : $('input[name="code"]').val(),
                    'name' : $('input[name="name"]').val(),
                    'password' : $('input[name="password"]').val(),
                    'phone' : $('input[name="phone"]').val(),
                    'start' : $('input[name="start"]').val(),
                    'birthday' : $('input[name="birthday"]').val(),
                    'address' : $('input[name="address"]').val(),
                    'branch' : $('select[name="branch"]').val(),
                    'is_admin' : 0
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

