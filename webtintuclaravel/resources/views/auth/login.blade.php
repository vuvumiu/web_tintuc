@extends('front.template.master')
@section('title', 'Đăng nhập tài khoản')
@section('content')

<div class="contact_wrap">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="auth_card">
                    <div class="auth_card__header">
                        <h2 class="auth_card__title">Đăng nhập</h2>
                    </div>
                    <div class="auth_card__body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ url('/dang-nhap') }}" method="POST" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label for="username" class="form-label">Tên đăng nhập</label>
                                <input type="text" class="form-control" id="username" name="username"
                                       value="{{ old('username') }}"
                                       placeholder="Nhập tên đăng nhập" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Nhập mật khẩu" required>
                            </div>
                            <div class="mb-3 text-end">
                                <a href="{{ route('password.request') }}" class="small text-muted" style="text-decoration:none;">
                                    <i class="fas fa-key me-1"></i>Quên mật khẩu?
                                </a>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                                    <label class="form-check-label" for="remember">
                                        Ghi nhớ đăng nhập
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                        </form>
                    </div>
                    <div class="auth_card__footer">
                        Chưa có tài khoản? <a href="{{ url('/dang-ky') }}">Đăng ký ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
