@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')

@endsection

@section('content')


    <div class="content-main">
        <div class="content-main__header">
            <h3 class="main-header__title fw-bold">Information admin group</h3>
            <hr>
            <div class="form-group__layout">
                <div class="form-group">
                    <label for="id" class="fw-bold">ID</label>
                    <input type="text" id="id" class="form-control input__field" value="{{ $role->id }}" disabled>
                </div>
                <div class="form-group">
                    <label for="name" class="fw-bold">Name</label>
                    <input type="text" id="name" class="form-control input__field" value="{{ $role->name }}" disabled>
                </div>
                <div class="form-group">
                    <label for="time" class="fw-bold">Create time </label>
                    <input type="text" id="time" class="form-control input__field" value="{{ \App\Models\Helper::convert_date_from_db($role->created_at) }}" disabled>
                </div>
                <div class="form-group">
                    <label for="create_by" class="fw-bold">Admin user create</label>
                    <input type="text" id="create_by" class="form-control input__field" value="{{ optional($role->user)->name }}" disabled>
                </div>
            </div>
        </div>
        <div class="content-main__body layout__custom">
            <h3 class="main-header__title fw-bold">Role</h3>
            <hr>
            <div class="form-group detail__role">
                @foreach($premissionsParent as $premissionsParentItem)
                    @if($permissionsChecked->contains('parent_id',$premissionsParentItem->id))
                        <p>{{ $premissionsParentItem->display_name }}:
                            @foreach($premissionsParentItem->permissionsChildren as $key => $permissionsChildrenItem)
                                @if($permissionsChecked->contains('id',$permissionsChildrenItem->id))
                                    {{ $permissionsChildrenItem->name }} <span>/</span>
                                @endif
                            @endforeach
                        </p>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

@endsection

<style>
    .detail__role p span:last-child{
        display: none;
    }
</style>

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
    </script>

@endsection
