@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')

@endsection

@section('content')

{{--    <div class="container-fluid list-products">--}}
{{--        <div class="row">--}}
{{--            <form action="{{route('administrator.'.$prefixView.'.store')}}" method="post" enctype="multipart/form-data">--}}
{{--                @csrf--}}
{{--                <div class="col-md-6">--}}

{{--                    <div class="form-group">--}}
{{--                        <label>Tên @include('administrator.components.lable_require')</label>--}}
{{--                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"--}}
{{--                               placeholder="Nhập tên" value="{{old('name')}}" required>--}}
{{--                        @error('name')--}}
{{--                        <div class="alert alert-danger">{{$message}}</div>--}}
{{--                        @enderror--}}
{{--                    </div>--}}

{{--                    <div class="form-group mt-3">--}}
{{--                        <label>Mô tả @include('administrator.components.lable_require')</label>--}}
{{--                        <input type="text" name="display_name"--}}
{{--                               class="form-control @error('display_name') is-invalid @enderror"--}}
{{--                               placeholder="Nhập mô tả" value="{{old('display_name')}}" required>--}}
{{--                        @error('display_name')--}}
{{--                        <div class="alert alert-danger">{{$message}}</div>--}}
{{--                        @enderror--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="col-md-12">--}}
{{--                    <div class="mt-3">--}}

{{--                        @foreach($premissionsParent as $premissionsParentItem)--}}
{{--                            <div class="card border-primary mb-3 col-md-12">--}}
{{--                                <div class="card-header">--}}
{{--                                    <input id="parent-{{$premissionsParentItem->id}}" class="checkbox_wrapper" type="checkbox" value="">--}}
{{--                                    <label for="parent-{{$premissionsParentItem->id}}">--}}
{{--                                        Quyền "{{$premissionsParentItem->display_name}}"--}}
{{--                                    </label>--}}

{{--                                </div>--}}
{{--                                <div class="row">--}}
{{--                                    @foreach($premissionsParentItem->permissionsChildren as $permissionsChildrenItem)--}}
{{--                                        <div class="card-body text-primary col-md-3">--}}
{{--                                            <h5 class="card-title">--}}
{{--                                                <input id="{{$permissionsChildrenItem->id}}" class="checkbox_children" type="checkbox" name="permission_id[]" value="{{$permissionsChildrenItem->id}}">--}}
{{--                                                <label for="{{$permissionsChildrenItem->id}}">--}}
{{--                                                    {{ str_replace("delete","Xóa",str_replace("edit","Chỉnh sửa",str_replace("add","Thêm mới",str_replace("list",'Xem',$permissionsChildrenItem->name)))) }}--}}
{{--                                                </label>--}}
{{--                                            </h5>--}}
{{--                                        </div>--}}
{{--                                    @endforeach--}}
{{--                                </div>--}}

{{--                            </div>--}}
{{--                        @endforeach--}}

{{--                    </div>--}}

{{--                </div>--}}

{{--                <div class="col-md-12 mb-2">--}}
{{--                    <button type="submit" class="btn btn-primary">Xác nhận</button>--}}
{{--                </div>--}}
{{--            </form>--}}
{{--        </div>--}}
{{--    </div>--}}

    <div class="content-main">
        <form autocomplete="off">
            <div class="content-main__header">
                <h3 class="main-header__title fw-bold">Infomation admin group</h3>
                <hr>
                <div class="form-group">
                    <label for="name" class="fw-bold">Name</label>
                    <input type="text" name="name" id="name" placeholder="Staff Manager" class="form-control input__field">
                </div>
            </div>
            <div class="content-main__body layout__custom">
                <h3 class="main-header__title fw-bold">Role</h3>
                <hr>
                <div class="form-group option__choose">
                    <input type="checkbox" id="selectall">
                    <label for="selectall">All roles</label>
                </div>
                @foreach($premissionsParent as $premissionsParentItem)
                    <div class="form-group all__options">
                        <h4 class="title fw-bold" style="text-transform: capitalize">{{$premissionsParentItem->display_name}}</h4>
                        <div class="option__choose">
                            <input type="checkbox" class="selectedId" id="selectall{{ $premissionsParentItem->id }}">
                            <label for="selectall{{ $premissionsParentItem->id }}">All roles</label>
                        </div>
                        @foreach($premissionsParentItem->condition as $permissionsChildrenItem)
                            <div class="option__choose">
                                <input type="checkbox" class="selectedId selectedItem{{  $premissionsParentItem->id }}" id="permision{{ $permissionsChildrenItem->id }}" value="{{$permissionsChildrenItem->id}}" name="permission_id">
                                <label for="permision{{ $permissionsChildrenItem->id }}">
                                    {{ str_replace("delete","Delete",str_replace("edit","Update",str_replace("list","List",str_replace("add",'Add',str_ireplace("get", "Get",$permissionsChildrenItem->name))))) }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <script>
                        $('#selectall'+{{ $premissionsParentItem->id }}).click(function () {
                            $('.selectedItem'+ {{ $premissionsParentItem->id }}).prop('checked', this.checked);
                        });

                        $('.selectedItem'+{{ $premissionsParentItem->id }}).change(function () {
                            var check = ($('.selectedItem'+{{ $premissionsParentItem->id }}).filter(":checked").length == $('.selectedItem'+{{ $premissionsParentItem->id }}).length);
                            $('#selectall'+{{ $premissionsParentItem->id }}).prop("checked", check);
                        });
                    </script>

                @endforeach
            </div>
        </form>
    </div>

@endsection

@section('js')

    <script>
        function create{{ $prefixView }}() {
            var permission_id = [];
            $('input[name="permission_id"]:checked').each(function () {
                permission_id.push($(this).val());
            })
            callAjax(
                'POST',
                '{{ route('administrator.roles.store') }}',
                {
                    'name' : $('input[name="name"]').val(),
                    'permission_id' : permission_id,
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
