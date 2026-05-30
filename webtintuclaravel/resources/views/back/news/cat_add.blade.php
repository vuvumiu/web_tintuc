@extends('back.template.master')

@section('title', 'Thêm danh mục tin tức')

@section('heading', 'Thêm danh mục tin tức')
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
      <form role="form" action="{{ url('admin/news_cat/add') }}" method="POST">
        <div class="card-body">
            {!! csrf_field() !!}

            <div class="form-group">
              <select class="form-control" name="Status">
                  <option value="1">Trạng thái: Bật</option>
                  <option value="0">Trạng thái: Tắt</option>
              </select>
            </div>
          
            <div class="form-group">
              <label for="title">Tên danh mục <span class="color_red">*</span></label>
              <input type="text" class="form-control" name="Name" id="title" onkeyup="ChangeToSlug()">
            </div>
          
            <div class="form-group">
              <label for="slug">Đường dẫn </label>
              <div class="input-group">
                <input type="text" class="form-control" name="Alias" id="slug">
                <div class="input-group-append">
                  <button type="button" class="btn btn-secondary" onclick="ChangeToSlug()" title="Tạo lại slug">
                    <i class="fas fa-sync-alt"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="colorPicker">Màu danh mục</label>
              <div class="d-flex align-items-center gap-2">
                <input type="color" name="color" id="colorPicker" value="#6c757d" style="width:60px;height:34px;padding:2px;border-radius:4px;">
                <input type="text" class="form-control" id="colorText" value="#6c757d" placeholder="#6c757d" maxlength="20" style="max-width:120px;" oninput="document.getElementById('colorPicker').value=this.value;">
              </div>
            </div>

            <div class="form-group">
              <label for="image">Ảnh danh mục</label>
              <input type="file" name="image" class="form-control" accept="image/*">
              <small class="text-muted">Kích thước đề xuất: 64x64 px</small>
            </div>

            <div class="form-group">
              <label for="description">Mô tả danh mục</label>
              <textarea name="description" rows="3" class="form-control" placeholder="Mô tả ngắn cho danh mục..."></textarea>
            </div>

        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Thêm danh mục</button>
        </div>
      </form>
    </div>

  </div>
@stop
