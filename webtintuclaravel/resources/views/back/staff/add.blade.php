@extends('back.template.master')

@section('title', 'Them tai khoan noi bo')
@section('heading', 'Them tai khoan noi bo')
@section('admin-manager', 'menu-open')
@section('admin-manager-add', 'active')

@section('content')
<div class="col-md-12">
    <div class="mb-3">
        <a href="{{ url('admin/admin-manager/list') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lai danh sach
        </a>
    </div>

    <div class="card card-primary">
        <form role="form" action="{{ url('admin/admin-manager/add') }}" method="POST">
            <div class="card-body">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="level">Cap bac <span class="text-red">*</span></label>
                            <select class="form-control" name="level">
                                @foreach($UserLevel as $level)
                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Administrator co toan quyen. Seo Content chi quan ly bai viet cua chinh minh va gui duyet.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="is_active">Trang thai</label>
                            <select class="form-control" name="is_active">
                                <option value="1">Hoat dong</option>
                                <option value="0">Bi khoa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    Quyen truy cap duoc gan tu dong theo cap bac, khong can chon them vai tro.
                </div>

                <div class="form-group">
                    <label for="fullname">Ho va ten <span class="text-red">*</span></label>
                    <input type="text" class="form-control" name="fullname" placeholder="Nhap ho va ten day du" value="{{ old('fullname') }}">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email <span class="text-red">*</span></label>
                            <input type="email" class="form-control" name="email" placeholder="Nhap dia chi email" value="{{ old('email') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">So dien thoai <span class="text-red">*</span></label>
                            <input type="text" class="form-control" name="phone" placeholder="Nhap so dien thoai" value="{{ old('phone') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Dia chi</label>
                    <input type="text" class="form-control" name="address" placeholder="Nhap dia chi" value="{{ old('address') }}">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="username">Tai khoan <span class="text-red">*</span></label>
                            <input type="text" class="form-control" name="username" placeholder="Ten dang nhap duy nhat" value="{{ old('username') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password">Mat khau <span class="text-red">*</span></label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Mat khau it nhat 6 ky tu">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Tao tai khoan
                </button>
            </div>
        </form>
    </div>
</div>
@stop
