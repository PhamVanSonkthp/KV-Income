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
                    <input type="text" id="name" name="name" class="form-control input__field" placeholder="Enter {{ $title }}">
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


        function create{{ $prefixView }}() {

            callAjax(
                'POST',
                '{{ route('administrator.'.$prefixView.'.store') }}',
                {
                    'name' : $('input[name="name"]').val(),
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

