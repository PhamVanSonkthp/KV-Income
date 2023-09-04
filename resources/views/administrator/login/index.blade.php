<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin - {{env('APP_NAME')}}</title>

    <meta name="promotion" content="Admin - {{env('APP_NAME')}}">
    <meta name="Description" content="Admin - {{env('APP_NAME')}}">

    <meta property="og:url" content="{{env('APP_URL') . "/admin"}}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Admin - {{env('APP_NAME')}}" />
    <meta property="og:description" content="Admin - {{env('APP_NAME')}}" />
    <meta property="og:image" content="{{ env('APP_URL') . \App\Models\Helper::logoImagePath() }}" />

    <link rel="shortcut icon" href="{{ env('APP_URL') . \App\Models\Helper::logoImagePath() }}">

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

</head>
<body>
<main>
    <div class="content-main layout__logo">
        <div class="row">
            <h1 class="title text-primary text-center fw-bold">ADMIN</h1>
            <div class="logo">
                <img src="{{ \App\Models\Helper::logoImagePath() }}" alt="" class="all_logo">
            </div>
            <div class="form__login bg-primary">
                <form action="" autocomplete="off" method="POST">
                    @csrf
                    <h3 class="title__page">Login</h3>
                    <div class="form-group">
                        <label class="fw-bold" for="ID">ID:</label>
                        <input type="text" name="id" class="form-control" placeholder="Enter ID" id="ID">
                    </div>
                    <div class="form-group">
                        <label class="fw-bold" for="password">Password:</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter password" id="password">
                        <svg xmlns="http://www.w3.org/2000/svg" class="eye eye-open hide" fill="none" viewBox="0 0 24 24" stroke="#ADB1B8" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" class="eye eye-close" fill="none" viewBox="0 0 24 24" stroke="#ADB1B8" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </div>
                    <div class="form-group button__login">
                        <button class="form-control btn__login btn btn-primary">Login</button>
                    </div>
                    @if (Session::has('message'))
                        <div class="alert alert-danger text-center">
                            <p>{{ Session::get('message') }}</p>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</main>

<script type="text/javascript" src="{{ asset('assets/js/jquery.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/eyePass.js') }}"></script>

</body>

</html>
