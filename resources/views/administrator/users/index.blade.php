@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')

@endsection

@section('content')

    @can('users-list')
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
                        <th scope="col" width="25%">Admin groups</th>
                        <th scope="col" width="25%">Branch</th>
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
                                    <a href="{{ route('administrator.'.$prefixView.'.detail', ['id' => $item->id]) }}">
                                        {{ $item->id }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('administrator.'.$prefixView.'.detail', ['id' => $item->id]) }}">
                                        {{ $item->name }}
                                    </a>

                                </td>
                                <td>{{ \App\Models\Helper::convert_date_from_db($item->created_at) }}</td>
                                <td>
                                    @php
                                        $arr_role = json_decode(isset($item) && !empty($item) ? $item->role_id : '');
                                    @endphp
                                    @foreach($roles as $role)
                                        @if(isset($arr_role) && !empty($arr_role))
                                            <p>{{ in_array($role->id, $arr_role) ? $role->name : ''  }}</p>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @php
                                        $arr_branch = json_decode(isset($item) && !empty($item) ? $item->branch_id : '');
                                    @endphp
                                    @foreach($branchs as $branch)
                                        @if(isset($arr_branch) && !empty($arr_branch))
                                        <p>{{ in_array($branch->id, $arr_branch) ? $branch->name : ''  }}</p>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @if(auth()->id() != $item->id)
                                    <a href="{{route('administrator.'.$prefixView.'.delete' , ['id'=> $item->id])}}"
                                       data-url="{{route('administrator.'.$prefixView.'.delete' , ['id'=> $item->id])}}"
                                       class="custom_button btn__reset action_delete delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @include('administrator.roles.total')
            </div>
        </div>
    @endcan

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
