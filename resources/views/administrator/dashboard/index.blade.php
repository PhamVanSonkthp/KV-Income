@extends('administrator.layouts.master')

@section('title')
    <title>{{ $title }}</title>
@endsection

@section('name')
    <h4 class="page-title">{{ $page }}</h4>
@endsection

@section('css')

@endsection

@section('content')

        <div class="content-main home__layout">
            <div class="list__menu">
                <div class="list__menu--item">
                    <a href="{{ route('administrator.roles.index') }}" class="dashboard__activity">
                        Admin groups
                    </a>
                </div>
                <div class="list__menu--item">
                    <a href="{{ route('administrator.users.index') }}" class="dashboard__activity">
                        Admin Users
                    </a>
                </div>
                <div class="list__menu--item">
                    <a href="{{ route('administrator.audits.index') }}" class="dashboard__activity">
                        Admin Activity Logs
                    </a>
                </div>
                <div class="list__menu--item">
                    <a href="{{ route('administrator.employees.index') }}" class="dashboard__activity">
                        Staffs
                    </a>
                </div>
                <div class="list__menu--item">
                    <a href="{{ route('administrator.system_branches.index') }}" class="dashboard__activity">
                        Branches
                    </a>
                </div>
                <div class="list__menu--item">
                    <a href="{{ route('administrator.orders.index') }}" class="dashboard__activity">
                        Orders
                    </a>
                </div>
                <div class="list__menu--item">
                    <a href="{{ route('administrator.salaries.index') }}" class="dashboard__activity">
                        Employee Salaries
                    </a>
                </div>
            </div>
        </div>


@endsection

@section('js')

    <script>
        @if(session()->has('message'))
        $.toastr.error('{{ session()->get('message') }}', {
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
