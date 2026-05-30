@extends('front.template.master')
@section('title', 'Đăng ký tài khoản')
@section('content')

<div class="contact_wrap">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="auth_card">
                    <div class="auth_card__header">
                        <h2 class="auth_card__title">Đăng ký tài khoản</h2>
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

                        <form action="{{ url('/dang-ky') }}" method="POST" novalidate>
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
                                       placeholder="Nhập mật khẩu (ít nhất 6 ký tự)" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                       name="password_confirmation"
                                       placeholder="Nhập lại mật khẩu" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
                        </form>
                    </div>
                    <div class="auth_card__footer">
                        Đã có tài khoản? <a href="{{ url('/dang-nhap') }}">Đăng nhập ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
