@extends('front.template.master')
@section('title', 'Quên mật khẩu')
@section('content')

<div class="contact_wrap">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="auth_card">
                    <div class="auth_card__header">
                        <i class="fas fa-key mb-2" style="font-size:2rem;color:#667eea;"></i>
                        <h2 class="auth_card__title">Quên mật khẩu</h2>
                        <p class="text-muted small">Nhập email đã đăng ký để nhận link đặt lại mật khẩu.</p>
                    </div>
                    <div class="auth_card__body">
                        @if(session('status'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle mr-1"></i>
                                {{ session('status') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('password.email') }}" method="POST" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Địa chỉ email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{ old('email') }}"
                                       placeholder="Nhập email đã đăng ký" required autofocus>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane mr-2"></i>Gửi link đặt lại mật khẩu
                            </button>
                        </form>
                    </div>
                    <div class="auth_card__footer">
                        Nhớ mật khẩu rồi?
                        <a href="{{ url('/dang-nhap') }}">Đăng nhập ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
