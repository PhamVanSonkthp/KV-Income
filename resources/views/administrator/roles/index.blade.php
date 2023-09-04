@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')

@endsection

@section('content')

        <div class="content-main">
            @include('administrator.'.$prefixView.'.search')
            <div class="content-main__body">
                @include('administrator.roles.total')
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead style="background-color: #ADB1B9;">
                        <th scope="col" width="3%">
                            <label class="control control--checkbox">
                                <input type="checkbox" class="js-check-all">
                                <div class="control__indicator"></div>
                            </label>
                        </th>
                        <th scope="col" width="7%">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Created time</th>
                        <th scope="col" width="45%">Role</th>
                        <th scope="col" width="1%">
                            @include('administrator.components.checkbox_delete_table')
                        </th>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr id="{{ $item->id }}">
                                <th scope="row">
                                    <label class="control control--checkbox">
                                        <input type="checkbox" value="{{ $item->id }}" class="checkbox-delete-item">
                                        <div class="control__indicator"></div>
                                    </label>
                                </th>
                                <td>
                                    <a href="{{ route('administrator.roles.detail', ['id' => $item->id]) }}">
                                        {{ $item->id }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('administrator.roles.detail', ['id' => $item->id]) }}">
                                        {{ $item->name }}
                                    </a>

                                </td>
                                <td>
                                    {{ \App\Models\Helper::convert_date_from_db($item->created_at) }}
                                </td>
                                <td>
                                    @foreach($premissionsParent as $premissionsParentItem)
                                        @if(optional($item->permissions)->contains('parent_id',$premissionsParentItem->id))
                                            <p>{{ $premissionsParentItem->display_name }}:
                                                @foreach($premissionsParentItem->permissionsChildren as $key => $permissionsChildrenItem)
                                                    @if(optional($item->permissions)->contains('id',$permissionsChildrenItem->id))
                                                        {{ $permissionsChildrenItem->name }} <span>/</span>
                                                    @endif
                                                @endforeach
                                            </p>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{route('administrator.'.$prefixView.'.delete' , ['id'=> $item->id])}}"
                                       data-url="{{route('administrator.'.$prefixView.'.delete' , ['id'=> $item->id])}}"
                                       class="custom_button btn__reset action_delete delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @include('administrator.roles.total')
            </div>
        </div>

@endsection

<style>
    td:nth-child(5) span:last-child{
        display: none;
    }
</style>

@section('js')

    <script>

        let items = @json($items);
        items = items['data'];

        for(let i = 0 ; i < items.length; i++){
            $('#lable_created_at_' + items[i]['id']).html(getFormattedDate(items[i]['created_at']), 'm/d/Y H:i:s')
        }

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
