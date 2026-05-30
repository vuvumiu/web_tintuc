@extends('back.template.master')

@section('title', 'Quản lý danh mục tin tức')

@section('heading', 'Danh sách danh mục tin tức')
@section('news', 'active')

@section('content')
<div class="col-md-12">
  <div class="card-header">
    <a class="btn btn-primary" href="{{ url('admin/news_cat/list') }}" title="Quay lại">
      Quay lại
    </a>      
  </div>
 
    <!-- general form elements -->
    <div class="card card-primary">
      <!-- /.card-header -->
      <!-- form start -->
      <form role="form" action="{{ url('admin/news_cat/edit/'.$NewsCategory->RowID) }}" method="POST">
        <div class="card-body">
            {!! csrf_field() !!}

            <div class="form-group">
              <select class="form-control" name="Status">
                  <option value="1" @if($NewsCategory->Status == 1) selected="" @endif> 
                      Trạng thái: Bật
                  </option>
                  <option value="0" @if($NewsCategory->Status == 0) selected="" @endif> 
                      Trạng thái: Tắt
                  </option>
              </select>
          </div>
          
          <div class="form-group">
              <label for="exampleInputEmail1">Tên danh mục <span class="color_red">*</span></label>
              <input type="text" class="form-control" name="Name" value="{{$NewsCategory->Name}}" id="title" onkeyup="ChangeToSlug()">
          </div>
          
          <label for="exampleInputEmail1">Đường dẫn </label>
          <div class="input-group">
            <input type="text" class="form-control" name="Alias" id="slug" value="{{ $NewsCategory->Alias }}">
            <div class="input-group-append">
              <button type="button" class="btn btn-secondary" onclick="ChangeToSlug()" title="Tạo lại slug">
                <i class="fas fa-sync-alt"></i>
              </button>
            </div>
          </div>
      </div>

      <div class="form-group">
          <label>Màu danh mục</label>
          <div class="d-flex align-items-center gap-2">
              <input type="color" name="color" id="colorPicker"
                     value="{{ $NewsCategory->color ?? '#6c757d' }}"
                     style="width:60px;height:34px;padding:2px;border-radius:4px;">
              <input type="text" class="form-control" id="colorText"
                     value="{{ $NewsCategory->color ?? '#6c757d' }}"
                     placeholder="#6c757d" maxlength="20" style="max-width:120px;"
                     oninput="document.getElementById('colorPicker').value=this.value;">
          </div>
      </div>

      <div class="form-group">
          <label>Ảnh danh mục</label>
          @if($NewsCategory->image)
              <div class="mb-2">
                  <img src="{{ url('images/category/' . $NewsCategory->image) }}"
                       width="64" height="64" class="img-thumbnail"
                       style="object-fit:cover;background:{{ $NewsCategory->color ?? '#6c757d' }}20;">
                  <small class="d-block text-muted">Ảnh hiện tại</small>
              </div>
          @endif
          <input type="file" name="image" class="form-control" accept="image/*">
          <small class="text-muted">Kích thước đề xuất: 64x64 px</small>
      </div>

      <div class="form-group">
          <label>Mô tả danh mục</label>
          <textarea name="description" rows="3" class="form-control"
                    placeholder="Mô tả ngắn cho danh mục...">{{ $NewsCategory->description ?? '' }}</textarea>
      </div>

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
