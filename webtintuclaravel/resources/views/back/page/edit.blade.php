@extends('back.template.master')

@section('title', 'Quản lý trang')

@section('heading', 'Chỉnh sửa trang')

@section('page', 'active')

@section('content')
<div class="col-md-12">
  <div class="card-header">
    <a class="btn btn-primary" href="{{ url('admin/page/list') }}" title="Quay lại">
      Quay lại
    </a>      
  </div>
 
    <!-- general form elements -->
    <div class="card card-primary">
      <!-- /.card-header -->
      <!-- form start -->
      <form role="form" action="{{ url('admin/page/edit/'.$page->RowID) }}" method="POST" enctype="multipart/form-data">
        <div class="card-body">
            {!! csrf_field() !!}

            <div class="form-group">
              <select class="form-control" name="Status">
                  <option value="1" @if($page->Status == 1) selected="" @endif> 
                      Trạng thái: Bật
                  </option>
                  <option value="0" @if($page->Status == 0) selected="" @endif> 
                      Trạng thái: Tắt
                  </option>
              </select>
          </div>

          @php
              $currentKind = $page->menu_kind ?? \App\Models\Page::MENU_LINK;
          @endphp
          <div class="form-group">
              <label for="menu_kind">Loại mục trên menu website</label>
              <select class="form-control" name="menu_kind" id="menu_kind">
                  @foreach(\App\Models\Page::menuKindLabels() as $val => $label)
                      <option value="{{ $val }}" @if($currentKind === $val) selected @endif>{{ $label }}</option>
                  @endforeach
              </select>
              <small class="form-text text-muted">
                  Thứ tự hiển thị menu = cột <strong>Sắp xếp</strong>. Chỉ trang <strong>Bật</strong> mới lên menu.
                  <strong>Route</strong>: điền đúng slug đã có route (vd. <code>tin-moi-nhat</code>, <code>tin-noi-bat</code>, <code>tim-kiem</code>).
                  <strong>Dropdown Tin tức</strong>: thường để Alias <code>#</code>; nhãn hiển thị = Tên trang.
              </small>
          </div>
          
          <div class="form-group">
              <label for="title">Tên trang <span class="color_red">*</span></label>
              <input type="text" class="form-control" name="Name" value="{{$page->Name}}" id="title" onkeyup="ChangeToSlug()">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">Ảnh đại diện </label>
            @if($page->Images != NULL)
            <br/>
            <img src="{{ url('images/page/' . $page->Images) }}" alt="Avatar" />
            @endif
            <input type="file" class="form-control" name="Images" >
        </div>

        <div class="form-group">
          <label for="slug">Đường dẫn (Alias)</label>
          <input type="text" class="form-control" name="Alias" id="slug" value="{{ $page->Alias }}">
          @php
              if (($page->menu_kind ?? \App\Models\Page::MENU_LINK) === \App\Models\Page::MENU_NEWS_CATEGORIES) {
                  $previewUrl = null;
              } elseif ($page->Alias === '/' || $page->Alias === '') {
                  $previewUrl = url('/');
              } else {
                  $previewUrl = url('/' . ltrim($page->Alias, '/'));
              }
          @endphp
          <small class="form-text text-muted">
            Với <strong>Trang tĩnh / Route</strong>: slug = URL trên site. <strong>Trang chủ</strong> sẽ lưu Alias <code>/</code>.
            @if($previewUrl)
            @if($page->Status == 1)
              <a href="{{ $previewUrl }}" target="_blank" rel="noopener noreferrer">
                <i class="fas fa-external-link-alt"></i> Xem trên website
              </a>
            @else
              <span class="text-warning">Đang tắt — vẫn có thể
                <a href="{{ $previewUrl }}" target="_blank" rel="noopener noreferrer">mở URL</a>
              </span>
            @endif
            @else
              <span class="text-muted">Mục dropdown không có một URL riêng — xem menu trên trang chủ.</span>
            @endif
          </small>
      </div>
          
          <div class="form-group">
              <label for="exampleInputEmail1">Font </label>
              <input type="text" class="form-control" name="Font" value="{{$page->Font}}">
          </div>
          
          <div class="form-group">
              <label for="exampleInputEmail1">Sắp xếp</label>
              <input type="number" class="form-control" name="Sort" value="{{$page->Sort}}">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">Thẻ meta title</label>
            <textarea name="MetaTitle" rows="2" class="form-control">{{ $page->MetaTitle }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="exampleInputEmail1">Thẻ meta description</label>
            <textarea name="MetaDescription" rows="4" class="form-control">{{ $page->MetaDescription }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="exampleInputEmail1">Thẻ meta keyword</label>
            <textarea name="MetaKeyword" rows="2" class="form-control">{{ $page->MetaKeyword }}</textarea>
        </div>
        <div class="form-group">
          <label for="exampleInputEmail1">Mô tả tin tức <span class="color_red">*</span></label>
          <textarea id="ckeditor" name="Description" rows="8" class="form-control">{{ $page->Description }}</textarea>
      </div> 
        </div>
          
        <!-- /.card-body -->

        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Chỉnh sửa</button>
        </div>
      </form>
    </div>
    <!-- /.card -->

  </div>
@stop
