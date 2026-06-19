@extends('back.template.master')
@section('title', 'Thêm Bài viết nổi bật')
@section('heading', 'Thêm Bài viết nổi bật')
@section('featured', 'active')

@section('content')
<div class="col-md-12">
    <div class="card card-default">
        <div class="card-header">
            <a href="{{ url('admin/featured/list') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
        <form action="{{ url('admin/featured/add') }}" method="POST">
            @csrf
            <div class="card-body">

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Bài viết <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select name="news_id" id="news_id" class="form-control select2" data-placeholder="Chọn bài viết nổi bật" required>
                            <option value="">— Chọn bài viết —</option>
                            @if(isset($news) && count($news) > 0)
                            @foreach($news as $n)
                                <option value="{{ $n->RowID }}" {{ old('news_id') == $n->RowID ? 'selected' : '' }}>
                                    {{ $n->Name }}{{ $n->category ? ' - ' . $n->category->Name : '' }}{{ (int) $n->Status === 1 ? ' - Xuất bản' : ' - Nháp/Ẩn' }}
                                </option>
                            @endforeach
                            @endif
                        </select>
                        @error('news_id')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                        <small class="form-text text-muted">
                            Danh sách này hiển thị toàn bộ bài viết trong hệ thống. Bài nháp/ẩn vẫn có thể chọn trước,
                            nhưng chỉ hiện ra trang chủ khi bài đó đã xuất bản.
                        </small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Vị trí hiển thị <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select name="position" id="position" class="form-control" required>
                            <option value="1" {{ old('position', request('position', 1)) == 1 ? 'selected' : '' }}>
                                Tin chính (Hero lớn)
                            </option>
                            <option value="2" {{ old('position', request('position', 1)) == 2 ? 'selected' : '' }}>
                                Tin phụ (Sidebar)
                            </option>
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i>
                            <strong>Tin chính:</strong> Hiển thị trong carousel lớn ở đầu trang.<br>
                            <strong>Tin phụ:</strong> Hiển thị danh sách bài nhỏ bên cạnh, nếu nhiều bài có thể cuộn.
                        </small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Trạng thái</label>
                    <div class="col-sm-9">
                        <select name="Status" class="form-control">
                            <option value="1" {{ old('Status', 1) == 1 ? 'selected' : '' }}>Bật</option>
                            <option value="0" {{ old('Status', 1) == 0 ? 'selected' : '' }}>Tắt</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu
                </button>
                <a href="{{ url('admin/featured/list') }}" class="btn btn-default">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
@include('back.partials.select2-admin')
@endsection
