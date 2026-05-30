@extends('back.template.guest')

@section('title', 'Đăng nhập quản trị')

@section('body_class', '')

@section('content')
<div class="login-wrap">
  <div class="login-blobs" aria-hidden="true">
    <span></span>
    <span></span>
    <span></span>
  </div>

  <div class="login-card">
    <div class="login-card__brand">
      <div class="login-logo-wrap">
        <div class="login-logo-icon">VN</div>
        <div class="login-logo-text">
          <div class="login-logo-brand">VN&bull;XPRESS</div>
          <div class="login-logo-sub">Hệ thống quản trị SCF</div>
        </div>
      </div>
      <h1>Chào mừng trở lại</h1>
      <p>Đăng nhập để truy cập bảng điều khiển quản trị viên</p>
    </div>

    <div class="login-card__body">
      @if(session('notice'))
        <div class="login-alert login-alert--danger" role="alert">
          <i class="fas fa-exclamation-circle" aria-hidden="true"></i>{{ session('notice') }}
        </div>
      @endif

      <form action="{{ url('/admin/login') }}" method="POST" autocomplete="off" novalidate>
        @csrf
        <div class="login-field">
          <label for="admin-username" class="login-label">Tên đăng nhập</label>
          <div class="login-input-wrap">
            <div class="login-input-icon"><i class="fas fa-user" aria-hidden="true"></i></div>
            <input type="text" class="login-input" id="admin-username" name="username"
                   placeholder="Nhập tài khoản quản trị"
                   required autofocus
                   value="{{ old('username') }}">
          </div>
        </div>

        <div class="login-field">
          <label for="admin-password" class="login-label">Mật khẩu</label>
          <div class="login-input-wrap">
            <div class="login-input-icon"><i class="fas fa-lock" aria-hidden="true"></i></div>
            <input type="password" class="login-input" id="admin-password" name="password"
                   placeholder="••••••••" required>
            <button type="button" class="login-toggle-pw" onclick="togglePassword(this)" tabindex="-1" aria-label="Hiện/ẩn mật khẩu">
              <i class="fas fa-eye" aria-hidden="true"></i>
            </button>
          </div>
        </div>

        <div class="login-options">
          <label class="login-remember">
            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <span class="login-checkbox-custom"></span>
            Ghi nhớ đăng nhập
          </label>
        </div>

        <button type="submit" class="login-btn">
          <span class="login-btn-inner">
            <i class="fas fa-sign-in-alt" aria-hidden="true"></i>Đăng nhập
          </span>
        </button>
      </form>

      <div class="login-links">
        <a href="{{ url('/') }}"><i class="fas fa-globe" aria-hidden="true"></i>Về trang chủ</a>
        <a href="{{ url('/dang-nhap') }}"><i class="fas fa-user" aria-hidden="true"></i>Đăng nhập khách</a>
      </div>
    </div>
  </div>

  <p class="login-foot">
    Tài khoản người đọc tin chỉ đăng nhập tại trang <strong>/dang-nhap</strong>
  </p>
</div>
@endsection

@section('script')
<script>
function togglePassword(btn) {
  var input = btn.closest('.login-input-wrap').querySelector('.login-input');
  var icon = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'fas fa-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'fas fa-eye';
  }
}
</script>
@endsection
