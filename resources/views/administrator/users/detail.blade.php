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
                    <label for="phone" class="fw-bold">Phone</label>
                    <input type="text" id="phone" class="form-control input__field" value="{{ $item->phone }}">
                </div>
                <div class="form-group">
                    <label for="branch" class="fw-bold">Branch</label>
                    <div class="form-control">
                        @foreach($branchs as $branch)
                            <p>{{ in_array($branch->id, $arr_branch) ? $branch->name : ''  }}</p>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="form-group" style="padding-top: 0">
                            <label for="time" class="fw-bold">Create time </label>
                            <input type="text" id="time" class="form-control input__field" value="{{ \App\Models\Helper::convert_date_from_db($item->created_at) }}">
                        </div>
                        <div class="form-group">
                            <label for="admingroup" class="fw-bold">Admin groups</label>
                            <div class="form-control">
                                @foreach($roles as $role)
                                    <p>{{ in_array($role->id, $arr_role) ? $role->name : ''  }}</p>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="create_by" class="fw-bold">Admin user create</label>
                            <input type="text" id="create_by" class="form-control input__field" value="{{ optional($item->user)->name }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

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

@endsection
