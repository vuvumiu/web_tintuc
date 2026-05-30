@extends('back.template.master')
@section('title', 'Thêm Tin nóng')
@section('heading', 'Thêm Tin nóng')
@section('ticker', 'active')

@section('content')
<div class="col-md-12">
    <div class="card card-default">
        <div class="card-header">
            <a href="{{ url('admin/ticker/list') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
        <form action="{{ url('admin/ticker/add') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Bài viết <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select name="news_id" id="news_id" class="form-control select2" data-placeholder="Chọn bài viết cho tin nóng">
                            <option value="">— Chọn bài viết (hoặc nhập tiêu đề tùy chỉnh bên dưới) —</option>
                            @if(isset($News) && count($News) > 0)
                            @foreach($News as $n)
                                <option value="{{ $n->RowID }}" {{ old('news_id') == $n->RowID ? 'selected' : '' }}>
                                    {{ $n->Name }}{{ $n->category ? ' - ' . $n->category->Name : '' }}{{ (int) $n->Status === 1 ? ' - Xuất bản' : ' - Nháp/Ẩn' }}
                                </option>
                            @endforeach
                            @endif
                        </select>
                        <small class="form-text text-muted">
                            Danh sách này hiển thị toàn bộ bài viết trong hệ thống. Nếu bài liên kết chưa xuất bản thì tin nóng sẽ chưa hiện ra ngoài trang chủ.
                            Bạn cũng có thể bỏ trống để nhập tiêu đề tùy chỉnh bên dưới.
                        </small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Tiêu đề tùy chỉnh</label>
                    <div class="col-sm-9">
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                               class="form-control @error('title') is-invalid @enderror"
                               placeholder="Nhập tiêu đề tùy chỉnh (nếu không chọn bài viết)">
                        @error('title')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Trạng thái</label>
                    <div class="col-sm-9">
                        <select name="Status" class="form-control">
                            <option value="1" {{ old('Status', 1) == 1 ? 'selected' : '' }}>Bật</option>
                            <option value="0" {{ old('Status') == '0' ? 'selected' : '' }}>Tắt</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label text-right">Sắp xếp</label>
                    <div class="col-sm-9">
                        <input type="number" name="Sort" value="{{ old('Sort', 0) }}"
                               class="form-control @error('Sort') is-invalid @enderror"
                               placeholder="0" min="0">
                        @error('Sort')
                            <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                        @enderror
                        <small class="form-text text-muted">Số nhỏ hơn hiển thị trước.</small>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Lưu
                </button>
                <a href="{{ url('admin/ticker/list') }}" class="btn btn-default">Hủy</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('script')
@include('back.partials.select2-admin')
@endsection
