@extends('administrator.layouts.master')

@include('administrator.'.$prefixView.'.header')

@section('css')

@endsection

@section('content')

    <div class="container-fluid list-products">
        <div class="row">

            <form action="{{route('administrator.'.$prefixView.'.update', ['id'=> $item->id]) }}" method="post"
                  enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="col-md-12">
                    <div class="form-group mt-3">
                        <label>Tiêu đề</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{$item->title}}" required>
                        @error('name')
                        <div class="alert alert-danger">{{$message}}</div>
                        @enderror
                    </div>

                    @include('administrator.components.select_category' , ['name' => 'category_id' ,'html_category' => \App\Models\CategoryNew::getCategory(isset($item) ? optional($item)->category_id : ''), 'can_create' => true])

                    @if($isSingleImage)
                        <div class="mt-3 mb-3">
                            @include('administrator.components.upload_image', ['post_api' => route('ajax,administrator.upload_image.store'), 'table' => 'news' , 'image' => optional($item->image)->image_path, 'relate_id' => $item->id])
                        </div>
                    @endif

                    @if($isMultipleImages)
                        <div class="mt-3 mb-3">
                            @include('administrator.components.upload_multiple_images', ['post_api' => route('ajax,administrator.upload_multiple_images.store'), 'delete_api' => route('ajax,administrator.upload_multiple_images.delete') , 'sort_api' => route('ajax,administrator.upload_multiple_images.sort'), 'table' => 'news' , 'images' => $item->images, 'relate_id' => $item->id])
                        </div>
                    @endif

                    <div class="form-group mt-3">
                        <label>Nhập nội dung</label>
                        <textarea style="min-height: 400px;" name="contents"
                                  class="form-control tinymce_editor_init @error('contents') is-invalid @enderror"
                                  rows="8">{{$item->content}}</textarea>
                        @error('contents')
                        <div class="alert alert-danger">{{$message}}</div>
                        @enderror
                    </div>


                    <button type="submit" class="btn btn-primary mt-3">Lưu thay đổi</button>

                </div>
            </form>

        </div>
    </div>
@endsection

@section('js')

@endsection
