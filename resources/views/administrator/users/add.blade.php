@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')


@endsection

@section('content')

    <div class="content-main">
        <div class="content-main__header">
            <h3 class="main-header__title fw-bold">Infomation {{ $title }}</h3>
            <hr>
            <div class="form-group__layout">
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
                    <label for="phone" class="fw-bold">Phone</label>
                    <input type="text" id="phone" name="phone" class="form-control input__field" placeholder="Enter Phone">
                </div>
                <div class="form-group">
                    <label for="admin_groups" class="fw-bold">Admin groups </label>
                    <select name="admin_groups[]" class="js-example-basic-multiple" multiple>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="branch" class="fw-bold">Branch</label>
                    @foreach($branchs as $branch)
                        <div class="branch__item">
                            <input type="checkbox" value="{{ $branch->id }}" name="branch" id="branch{{ $branch->id }}">
                            <label for="branch{{ $branch->id }}">{{ $branch->name }}</label>
                        </div>
                    @endforeach
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
</style>

@section('js')

<script>
    $(".js-example-basic-multiple").select2({
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
        var branch = [];

        $('input[name="branch"]:checked').each(function () {
            branch.push($(this).val());
        })

        callAjax(
            'POST',
            '{{ route('administrator.'.$prefixView.'.store') }}',
            {
                'name' : $('input[name="name"]').val(),
                'password' : $('input[name="password"]').val(),
                'phone' : $('input[name="phone"]').val(),
                'branch' : branch,
                'admin_group' : $('select[name="admin_groups[]"]').val(),
                'is_admin' : 1,
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

