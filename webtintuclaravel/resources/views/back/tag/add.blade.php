@extends('back.template.master')

@section('title', 'Thêm Tag')
@section('heading', 'Thêm Tag mới')
@section('news', 'active')

@section('content')
<div class="col-md-6">
    <div class="card">
        <div class="card-header">
            <a class="btn btn-secondary" href="{{ url('admin/tag/list') }}">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>
        <form action="{{ url('admin/tag/add') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Tên Tag <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="VD: Tin tức, Công nghệ..." required>
                </div>
                <div class="form-group">
                    <label>Meta Title</label>
                    <input type="text" name="meta_title" class="form-control" placeholder="SEO Title...">
                </div>
                <div class="form-group">
                    <label>Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="3" placeholder="SEO Description..."></textarea>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="statusActive" name="status" value="1" checked>
                        <label class="custom-control-label" for="statusActive">Hiển thị tag</label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Lưu Tag
                </button>
            </div>
        </form>
    </div>
</div>
@stop
