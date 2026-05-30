@extends('back.template.master')

@section('title', 'Quản lý nhận tin khuyến mại')

@section('heading', 'Chỉnh sửa nhận tin khuyến mại')

@section('newsletter', 'active')

@section('content')
<div class="col-md-12">
  <div class="card-header">
    <a class="btn btn-primary" href="{{ url('admin/newsletter/list') }}" title="Quay lại">
      <i class="fas fa-arrow-left"></i> Quay lại
    </a>
  </div>

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Thông tin đăng ký</h3>
        </div>
        <form role="form" action="{{ url('admin/newsletter/edit/'.$Newsletter->RowID) }}" method="POST">
            <div class="card-body">
                {!! csrf_field() !!}

                <div class="form-group">
                    <label for="Email">Email <span class="color_red">*</span></label>
                    <input type="email" class="form-control" name="Email" value="{{ $Newsletter->Email }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Trạng thái hoạt động</label>
                            <select name="is_active" class="form-control">
                                <option value="1" {{ $Newsletter->is_active ? 'selected' : '' }}>
                                    Đang hoạt động
                                </option>
                                <option value="0" {{ !$Newsletter->is_active ? 'selected' : '' }}>
                                    Đã hủy
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Trạng thái xem</label>
                            <select name="is_reviewed" class="form-control">
                                <option value="1" {{ $Newsletter->is_reviewed ? 'selected' : '' }}>
                                    Đã xem
                                </option>
                                <option value="0" {{ !$Newsletter->is_reviewed ? 'selected' : '' }}>
                                    Chưa xem
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ngày đăng ký</label>
                            <input type="text" class="form-control"
                                   value="{{ $Newsletter->subscribed_at ? $Newsletter->subscribed_at->format('d/m/Y H:i') : '-' }}"
                                   readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ngày hủy</label>
                            <input type="text" class="form-control"
                                   value="{{ $Newsletter->unsubscribed_at ? $Newsletter->unsubscribed_at->format('d/m/Y H:i') : 'Chưa hủy' }}"
                                   readonly>
                        </div>
                    </div>
                </div>

                @if($Newsletter->ip_address)
                <div class="form-group">
                    <label>IP đăng ký</label>
                    <input type="text" class="form-control" value="{{ $Newsletter->ip_address }}" readonly>
                </div>
                @endif

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu chỉnh sửa
                </button>
            </div>
        </form>
    </div>
</div>
@stop
