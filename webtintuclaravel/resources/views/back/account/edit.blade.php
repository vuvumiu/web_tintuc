@extends('back.template.master')

@section('title', 'Chỉnh sửa thành viên')

@section('heading', 'Chỉnh sửa thành viên')

@section('member', 'menu-open')

@section('content')
<div class="col-md-12">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ url('admin/member/list') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
        <a href="{{ url('admin/member/view/'.$account->id) }}" class="btn btn-info btn-sm">
            <i class="fas fa-eye"></i> Xem chi tiết
        </a>
    </div>

    <div class="card card-primary card-outline">
        <!-- /.card-header -->
        <!-- form start -->
        <form role="form" action="{{ url('admin/member/edit/'.$account->id) }}" method="POST">
            <div class="card-body">
                {!! csrf_field() !!}

                <!-- Username (readonly) -->
                <div class="form-group">
                    <label for="username">Tài khoản <span class="text-red">*</span></label>
                    <input type="text" class="form-control" name="username"
                           value="{{ $account->username }}" readonly
                           title="Tên đăng nhập không thể thay đổi">
                    <small class="text-muted">Tên đăng nhập không thể thay đổi.</small>
                </div>

                <div class="row">
                    <!-- Fullname -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fullname">Họ và tên <span class="text-red">*</span></label>
                            <input type="text" class="form-control" name="fullname"
                                   value="{{ old('fullname', $account->fullname) }}"
                                   placeholder="Nhập họ và tên đầy đủ">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email <span class="text-red">*</span></label>
                            <input type="email" class="form-control" name="email"
                                   value="{{ old('email', $account->email) }}"
                                   placeholder="Nhập địa chỉ email">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Phone -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" class="form-control" name="phone"
                                   value="{{ old('phone', $account->phone) }}"
                                   placeholder="Nhập số điện thoại">
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address">Địa chỉ</label>
                            <input type="text" class="form-control" name="address"
                                   value="{{ old('address', $account->address) }}"
                                   placeholder="Nhập địa chỉ">
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Mật khẩu mới</label>
                            <input type="password" class="form-control" name="password"
                                   id="password" placeholder="Để trống nếu không đổi mật khẩu">
                            <small class="text-muted">
                                Để trống nếu không muốn thay đổi mật khẩu.
                                Mật khẩu phải có ít nhất 6 ký tự.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control" name="password_confirmation"
                                   id="password_confirmation"
                                   placeholder="Nhập lại mật khẩu mới">
                        </div>
                    </div>
                </div>

                <!-- Account Status Toggle -->
                <div class="card card-{{ $account->is_active == 1 ? 'success' : 'secondary' }} mt-3" style="border-left: 4px solid {{ $account->is_active == 1 ? '#28a745' : '#6c757d' }};">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-shield-alt"></i>
                            Trạng thái tài khoản
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    @if($account->is_active == 1)
                                        <span class="badge badge-success" style="font-size: 1rem;">
                                            <i class="fas fa-check-circle"></i> Tài khoản đang hoạt động
                                        </span>
                                        <p class="text-muted mb-0 mt-1" style="font-size: 0.875rem;">
                                            Người dùng có thể đăng nhập, bình luận và đánh giá trên website.
                                        </p>
                                    @else
                                        <span class="badge badge-secondary" style="font-size: 1rem;">
                                            <i class="fas fa-ban"></i> Tài khoản bị vô hiệu hóa
                                        </span>
                                        <p class="text-muted mb-0 mt-1" style="font-size: 0.875rem;">
                                            Người dùng <strong>không thể</strong> đăng nhập, bình luận hay đánh giá.
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                @if($account->is_active == 1)
                                    <a href="{{ url('admin/member/lock/'.$account->id) }}"
                                       class="btn btn-outline-warning btn-lg"
                                       onclick="return confirm('Bạn có chắc muốn khóa tài khoản &quot;{{ $account->username }}&quot;?')">
                                        <i class="fas fa-lock"></i> Khóa tài khoản ngay
                                    </a>
                                @else
                                    <a href="{{ url('admin/member/unlock/'.$account->id) }}"
                                       class="btn btn-outline-success btn-lg"
                                       onclick="return confirm('Bạn có chắc muốn mở khóa tài khoản &quot;{{ $account->username }}&quot;?')">
                                        <i class="fas fa-unlock"></i> Mở khóa tài khoản ngay
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Checkbox (kept for form save, but visually secondary) -->
                <div class="form-group mt-3">
                    <div class="icheck-primary d-inline">
                        <input type="checkbox" name="is_active" id="is_active"
                               value="1"
                               {{ old('is_active', $account->is_active) == 1 ? 'checked' : '' }}>
                        <label for="is_active">
                            <strong>Tài khoản hoạt động</strong>
                        </label>
                    </div>
                    <br>
                    <small class="text-muted">
                        Bỏ tick để vô hiệu hóa tài khoản. Tài khoản bị khóa sẽ không thể đăng nhập,
                        bình luận hay đánh giá trên website.
                    </small>
                </div>

                <!-- Account Info (read-only) -->
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Ngày đăng ký</label>
                            <input type="text" class="form-control"
                                   value="{{ $account->created_at ? date('d/m/Y H:i', strtotime($account->created_at)) : '—' }}"
                                   readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Số bình luận</label>
                            <input type="text" class="form-control"
                                   value="{{ $account->comments->count() ?? 0 }} bình luận"
                                   readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Số đánh giá</label>
                            <input type="text" class="form-control"
                                   value="{{ $account->ratings->count() ?? 0 }} đánh giá"
                                   readonly>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
                <a href="{{ url('admin/member/list') }}" class="btn btn-secondary">
                    Hủy
                </a>
                <form action="{{ url('admin/member/delete/'.$account->id) }}" method="POST" class="d-inline float-right">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Bạn có chắc muốn xóa tài khoản này? Hành động này không thể hoàn tác.')">
                        <i class="fas fa-trash"></i> Xóa tài khoản
                    </button>
                </form>
            </div>
        </form>
    </div>
    <!-- /.card -->
</div>
@stop

@section('javascript')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('form').addEventListener('submit', function(e) {
        var password = document.getElementById('password').value;
        var confirmPwd = document.getElementById('password_confirmation').value;

        if (password !== '' && password !== confirmPwd) {
            e.preventDefault();
            alert('Mật khẩu xác nhận không khớp!');
            document.getElementById('password_confirmation').focus();
            return false;
        }

        if (password !== '' && password.length < 6) {
            e.preventDefault();
            alert('Mật khẩu phải có ít nhất 6 ký tự!');
            document.getElementById('password').focus();
            return false;
        }

        return true;
    });
});
</script>
@endsection
