<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Admin Infinity Ltd" name="description">
    <meta content="Pham Son" name="author">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page }}</title>
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ env('APP_URL') . \App\Models\Helper::logoImagePath() }}">

    @yield('title')

    <!-- Google font-->
    <link rel="shortcut icon" href="{{ env('APP_URL') . \App\Models\Helper::logoImagePath() }}">

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link rel="stylesheet" href="{{ asset('assets/css/select2.css') }}">

    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/toastr.min.css')}}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/daterangepicker.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <link rel="stylesheet" type="text/css" href="{{asset('/assets/administrator/css/vendors/jquery-ui.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('/assets/administrator/css/order-image.css')}}" >

    <script type="text/javascript" src="{{ asset('assets/js/jquery.min.js') }}"></script>

    <script src="{{asset('/assets/administrator/js/jquery.ui.min.js')}}"></script>

    @yield('css')
</head>

<body>

@include('administrator.components.header')

<main id="main">
    @yield('content')

    <div id="loader" class="lds-dual-ring load overlay_irt"></div>
</main>


<script type="text/javascript" src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/moment.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/daterangepicker.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/select2.full.min.js') }}"></script>

<script src="{{asset('vendor/sweet-alert-2/sweetalert2@11.js')}}"></script>

<script type="text/javascript" src="{{asset('assets/js/toastr.min.js')}}"></script>

<script type="text/javascript" src="{{ asset('assets/js/eyePass.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/main.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/helper/main_helper.js') }}"></script>

<script>

    function viewBirthOfDay() {

        const searchParams = new URLSearchParams(window.location.search)
        searchParams.set('date_of_birth', new Date().toISOString().slice(0, 10))
        window.location.search = searchParams.toString()
    }

    function isEmptyInput(id, is_alert = false, message_alert = "", is_focus = false){
        if (!$('#' + id).val().trim()) {
            if(is_alert){
                alert(message_alert)
            }

            if(is_focus){
                $('#' + id).focus()
            }

            return true
        }
        return false
    }

    function callAjaxMultipart(method = "GET", url, data, success, error, on_process = null, is_loading = true){
        $.ajax({
            type: method,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: false,
            contentType: false,
            processData: false,
            data: data,
            url: url,
            beforeSend: function () {
                if(is_loading){
                    showLoading()
                }
            },
            success: function (response) {
                if(is_loading){
                    hideLoading()
                }
                success(response)
            },
            error: function (err) {
                if(is_loading){
                    hideLoading()
                }
                Swal.fire(
                    {
                        icon: 'error',
                        title: err.responseText,
                    }
                );
                error(err)
            },
            xhr:function (){
                // get the native XmlHttpRequest object
                var xhr = $.ajaxSettings.xhr() ;
                // set the onprogress event handler
                xhr.upload.onprogress = function(evt){
                    // console.log('progress', evt.loaded/evt.total*100)

                    if (on_process){
                        on_process(evt.loaded/evt.total*100)
                    }

                } ;
                // set the onload event handler
                xhr.upload.onload = function(){

                } ;
                // return the customized object

                return xhr ;
            }
        });
    }

    function callAjax(method = "GET", url, data, success, error){
        $.ajax({
            type: method,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: true,
            data: data,
            url: url,
            beforeSend: function() {
                $('#loader').removeClass('load');
            },
            success: function (response) {
                success(response)
            },
            complete: function(){
                $('#loader').addClass('load');
            },
            error: function (err) {
                Swal.fire(
                    {
                        icon: 'error',
                        title: err.responseText,
                    }
                );
            },
        });
    }

    function callAjaxNotLoad(method = "GET", url, data, success, error) {
        $.ajax({
            type: method,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: true,
            data: data,
            url: url,

            success: function (response) {
                success(response)
            },

            error: function (err) {
                Swal.fire(
                    {
                        icon: 'error',
                        title: err.responseText,
                    }
                );
            },
        });
    }

    @if(isset($prefixView) && !empty($prefixView))
        function delay(callback, ms) {
            var timer = 0;
            return function() {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback.apply(context, args);
                }, ms || 0);
            };
        }

        $('input[name="key"]').keyup(delay(function (e) {
            callAjaxNotLoad(
                'GET',
                '{{ route('ajax.administrator.'.$prefixView.'.search') }}',
                {
                    'key' : $(this).val(),
                },
                (response) => {
                    $('.show_list').html(response.html).show();
                }
            )
        }, 500))
    @endif

    function hideModal(id){
        $('#' + id).modal('hide');
    }

    function showModal(id){
        $('#' + id).modal('show');
    }

    function isCheckedInput(id){
        return $("#" + id).is(":checked") == "true" || $("#" + id).is(":checked") == true
    }

    function convertDate(i) {
        const d = new Date(i);

        return d.toLocaleString();
    }

</script>

@yield('js')

</body>


</html>
