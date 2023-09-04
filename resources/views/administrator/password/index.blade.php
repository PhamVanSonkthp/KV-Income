@extends('administrator.layouts.master')

@include('administrator.password.header')

@section('css')

@endsection

@section('content')

    <div class="container-fluid list-products">
        <div class="row">
            <!-- Individual column searching (text inputs) Starts-->

            @if (Session::has('error'))
                <div class="text-danger mb-4" style="font-weight: bold;">
                    {{ Session::get('error') }}
                </div>
            @endif

            @if (Session::has('success'))
                <div class="text-success mb-4" style="font-weight: bold;">
                    {{ Session::get('success') }}
                </div>
            @endif

            <form action="{{route('administrator.password.update') }}" method="post"
                  enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="col-md-6">

                    <div class="content-main__header">
                        <div class="form-group">
                            <label>Old password</label>
                            <input type="password" name="old_password" class="form-control @error('old_password') is-invalid @enderror" required autocomplete="off">
                            @error('old_password')
                            <div class="alert alert-danger">{{$message}}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label>New password</label>
                            <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required autocomplete="off">
                            @error('new_password')
                            <div class="alert alert-danger">{{$message}}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label>Confirm new password</label>
                            <input type="password" name="new_password_confirm" class="form-control @error('new_password_confirm') is-invalid @enderror" required autocomplete="off">
                            @error('new_password_confirm')
                            <div class="alert alert-danger">{{$message}}</div>
                            @enderror
                        </div>

                        <button style="width: auto;" type="submit" class="custom_button btn__create mt-3">Save</button>
                    </div>


                </div>
            </form>

        </div>
    </div>

@endsection

@section('js')

@endsection
