@extends('back.template.master')

@section('title', 'Sửa popup quảng cáo')
@section('heading', 'Sửa popup quảng cáo')
@section('ads', 'active')

@section('content')
<div class="card card-primary">
    <div class="card-header">
        <a href="{{ url('admin/ads/list') }}" class="btn btn-outline-light btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <form role="form" action="{{ url('admin/ads/edit/' . $Ad->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <h5 class="mb-3" style="color: var(--accent-gold);"><i class="fas fa-window-restore"></i> Thông tin popup</h5>

            <div class="form-group">
                <label for="name">Tên popup <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $Ad->name) }}" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="image">Ảnh popup</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        @error('image') <small class="text-danger">{{ $message }}</small> @enderror

                        @if($Ad->image)
                            <div class="mt-2">
                                <img src="{{ url('images/ads/' . $Ad->image) }}" alt="Preview" style="max-width: 220px; max-height: 140px; border-radius: 8px; border: 1px solid var(--border-subtle);">
                                <p class="mt-1 mb-0"><small class="text-muted">Ảnh hiện tại</small></p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="link">Link khi click popup</label>
                        <input type="url" class="form-control" id="link" name="link" value="{{ old('link', $Ad->link) }}" placeholder="https://example.com">
                        @error('link') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>

            <hr class="mt-4 mb-4">

            <h5 class="mb-3" style="color: var(--accent-gold);"><i class="fas fa-cog"></i> Cài đặt hiển thị</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="location">Hiển thị ở đâu</label>
                        <select class="form-control" id="location" name="location">
                            <option value="all" {{ old('location', $Ad->location) == 'all' ? 'selected' : '' }}>Tất cả trang public</option>
                            <option value="homepage" {{ old('location', $Ad->location) == 'homepage' ? 'selected' : '' }}>Chỉ trang chủ</option>
                            <option value="article" {{ old('location', $Ad->location) == 'article' ? 'selected' : '' }}>Chỉ trang bài viết</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="popup_position">Vị trí popup</label>
                        <select class="form-control" id="popup_position" name="popup_position">
                            <option value="center" {{ old('popup_position', $Ad->popup_position) == 'center' ? 'selected' : '' }}>Giữa màn hình</option>
                            <option value="bottom_right" {{ old('popup_position', $Ad->popup_position) == 'bottom_right' ? 'selected' : '' }}>Góc dưới phải</option>
                            <option value="bottom_left" {{ old('popup_position', $Ad->popup_position) == 'bottom_left' ? 'selected' : '' }}>Góc dưới trái</option>
                            <option value="top_right" {{ old('popup_position', $Ad->popup_position) == 'top_right' ? 'selected' : '' }}>Góc trên phải</option>
                            <option value="top_left" {{ old('popup_position', $Ad->popup_position) == 'top_left' ? 'selected' : '' }}>Góc trên trái</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="show_delay_seconds">Hiện sau (giây)</label>
                        <input type="number" class="form-control" id="show_delay_seconds" name="show_delay_seconds" value="{{ old('show_delay_seconds', $Ad->show_delay_seconds) }}" min="0" max="300">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="impression_limit">Số lần hiện tối đa</label>
                        <input type="number" class="form-control" id="impression_limit" name="impression_limit" value="{{ old('impression_limit', $Ad->impression_limit) }}" min="0" max="999">
                        <small class="form-text text-muted">Tính trên mỗi trình duyệt. 0 = không giới hạn.</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cooldown_minutes">Nghỉ giữa các lần hiện (phút)</label>
                        <input type="number" class="form-control" id="cooldown_minutes" name="cooldown_minutes" value="{{ old('cooldown_minutes', $Ad->cooldown_minutes) }}" min="0" max="525600">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="auto_close_seconds">Tự đóng sau (giây)</label>
                        <input type="number" class="form-control" id="auto_close_seconds" name="auto_close_seconds" value="{{ old('auto_close_seconds', $Ad->auto_close_seconds) }}" min="0" max="300">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select class="form-control" id="status" name="status">
                            <option value="1" {{ old('status', $Ad->status) == '1' ? 'selected' : '' }}>Bật</option>
                            <option value="0" {{ old('status', $Ad->status) == '0' ? 'selected' : '' }}>Tắt</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="priority">Độ ưu tiên</label>
                        <input type="number" class="form-control" id="priority" name="priority" value="{{ old('priority', $Ad->priority) }}" min="0">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sort">Thứ tự</label>
                        <input type="number" class="form-control" id="sort" name="sort" value="{{ old('sort', $Ad->sort) }}" min="0">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tuỳ chọn</label>
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="show_close_button" value="0">
                            <input type="checkbox" class="custom-control-input" id="show_close_button" name="show_close_button" value="1" {{ old('show_close_button', $Ad->show_close_button) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="show_close_button">Hiển thị nút đóng</label>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="mt-4 mb-4">

            <h5 class="mb-3" style="color: var(--accent-gold);"><i class="fas fa-clock"></i> Thời gian hiệu lực</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="start_date">Ngày bắt đầu</label>
                        <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $Ad->start_date ? $Ad->start_date->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="end_date">Ngày kết thúc</label>
                        <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $Ad->end_date ? $Ad->end_date->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>
            </div>

            <hr class="mt-4 mb-4">
            <h5 class="mb-3" style="color: var(--text-muted);"><i class="fas fa-chart-bar"></i> Thống kê</h5>
            <div class="row">
                <div class="col-md-3">
                    <label>Lượt hiển thị</label>
                    <p class="form-control-static" style="font-size: 18px; font-weight: 700;">{{ number_format($Ad->view_count) }}</p>
                </div>
                <div class="col-md-3">
                    <label>Lượt click</label>
                    <p class="form-control-static" style="font-size: 18px; font-weight: 700; color: var(--accent-gold);">{{ number_format($Ad->click_count) }}</p>
                </div>
                <div class="col-md-3">
                    <label>Ngày tạo</label>
                    <p class="form-control-static">{{ $Ad->created_at ? $Ad->created_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
                <div class="col-md-3">
                    <label>Cập nhật cuối</label>
                    <p class="form-control-static">{{ $Ad->updated_at ? $Ad->updated_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu thay đổi
            </button>
            <a href="{{ url('admin/ads/list') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-times"></i> Hủy
            </a>
        </div>
    </form>
</div>
@endsection
