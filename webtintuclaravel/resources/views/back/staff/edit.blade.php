@extends('back.template.master')

@section('title', 'Chinh sua tai khoan noi bo')
@section('heading', 'Chinh sua tai khoan noi bo')
@section('admin-manager', 'menu-open')

@section('content')
<div class="col-md-12">
    <div class="mb-3 d-flex flex-wrap align-items-center">
        <a href="{{ url('admin/admin-manager/list') }}" class="btn btn-secondary btn-sm mr-2 mb-2">
            <i class="fas fa-arrow-left"></i> Quay lai danh sach
        </a>
        @if((int) ($User->is_author ?? 0) === 1 || $User->authoredNews()->exists())
            <a href="{{ url('admin/admin-manager/stats/' . $User->id) }}" class="btn btn-info btn-sm mb-2">
                <i class="fas fa-chart-line"></i> Xem thong ke bai viet
            </a>
        @endif
    </div>

    <div class="card card-primary">
        <form role="form" action="{{ url('admin/admin-manager/edit/' . $User->id) }}" method="POST">
            <div class="card-body">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="level">Cap bac <span class="text-red">*</span></label>
                            <select class="form-control" name="level">
                                @foreach($UserLevel as $level)
                                    <option value="{{ $level->id }}" @if($level->id == $User->level) selected @endif>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if(Auth::id() == $User->id)
                                <small class="text-warning">
                                    Ban dang chinh sua tai khoan cua chinh minh.
                                </small>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="is_active">Trang thai</label>
                            <select class="form-control" name="is_active">
                                <option value="1" @if((int) ($User->is_active ?? 1) === 1) selected @endif>Hoat dong</option>
                                <option value="0" @if((int) ($User->is_active ?? 1) === 0) selected @endif>Bi khoa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    Administrator co toan quyen. Seo Content chi quan ly bai viet cua chinh minh, xem truoc va gui duyet.
                </div>

                <div class="form-group">
                    <label for="fullname">Ho va ten <span class="text-red">*</span></label>
                    <input type="text" class="form-control" name="fullname" value="{{ $User->fullname ?? '' }}">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email <span class="text-red">*</span></label>
                            <input type="email" class="form-control" name="email" value="{{ $User->email ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">So dien thoai <span class="text-red">*</span></label>
                            <input type="text" class="form-control" name="phone" value="{{ $User->phone ?? '' }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Dia chi</label>
                    <input type="text" class="form-control" name="address" value="{{ $User->address ?? '' }}">
                </div>

                <div class="form-group">
                    <label for="username">Tai khoan</label>
                    <input type="text" class="form-control" value="{{ $User->username }}" disabled>
                    <small class="text-muted">Ten dang nhap khong the thay doi.</small>
                </div>

                <div class="form-group">
                    <label for="password">Mat khau moi</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="De trong neu khong muon doi mat khau">
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Luu thay doi
                </button>
            </div>
        </form>
    </div>
</div>
@stop
