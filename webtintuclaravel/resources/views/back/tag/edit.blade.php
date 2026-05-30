@extends('back.template.master')

@section('title', 'Sửa Tag')
@section('heading', 'Chỉnh sửa Tag')
@section('news', 'active')

@section('content')
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <a class="btn btn-secondary" href="{{ url('admin/tag/list') }}">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>
        <form action="{{ url('admin/tag/edit/' . $tag->id) }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Tên Tag <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ $tag->name }}" required>
                </div>
                <div class="form-group">
                    <label>Slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" class="form-control" value="{{ $tag->slug }}" required>
                    <small class="text-muted">Slug phải là duy nhất và không có dấu. Có thể sửa tự động từ tên tag.</small>
                </div>
                <div class="form-group">
                    <label>Meta Title</label>
                    <input type="text" name="meta_title" class="form-control" value="{{ $tag->meta_title }}">
                </div>
                <div class="form-group">
                    <label>Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="3">{{ $tag->meta_description }}</textarea>
                </div>
                <div class="form-group">
                    <label>Lượt sử dụng</label>
                    <input type="text" class="form-control" value="{{ $tag->popular_count }}" readonly>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="statusActive" name="status" value="1"
                            {{ $tag->status ? 'checked' : '' }}>
                        <label class="custom-control-label" for="statusActive">Hiển thị tag</label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@stop
