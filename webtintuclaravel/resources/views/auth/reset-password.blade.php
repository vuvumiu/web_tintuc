@extends('front.template.master')
@section('title', 'Đặt lại mật khẩu')
@section('content')

<div class="contact_wrap">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="auth_card">
                    <div class="auth_card__header">
                        <i class="fas fa-lock mb-2" style="font-size:2rem;color:#667eea;"></i>
                        <h2 class="auth_card__title">Đặt lại mật khẩu</h2>
                    </div>
                    <div class="auth_card__body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('password.update') }}" method="POST" novalidate>
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email }}">

                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password-confirm" class="form-label">Xác nhận mật khẩu</label>
                                <input type="password" class="form-control" id="password-confirm" name="password_confirmation"
                                       placeholder="Nhập lại mật khẩu mới" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-key mr-2"></i>Đặt lại mật khẩu
                            </button>
                        </form>
                    </div>
                    <div class="auth_card__footer">
                        <a href="{{ url('/dang-nhap') }}">Quay lại đăng nhập</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
