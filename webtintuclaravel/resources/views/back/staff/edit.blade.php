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
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="is_author">La tac gia</label>
                            @if(Auth::user()->isAdmin())
                                <select class="form-control" name="is_author">
                                    <option value="0" @if((int) ($User->is_author ?? 0) === 0) selected @endif>Khong</option>
                                    <option value="1" @if((int) ($User->is_author ?? 0) === 1) selected @endif>Co</option>
                                </select>
                                <small class="text-muted">
                                    Tat cờ nay khong xoa lich su tac gia tren cac bai cu.
                                </small>
                            @else
                                <input type="hidden" name="is_author" value="{{ (int) ($User->is_author ?? 0) }}">
                                <input type="text" class="form-control" value="{{ (int) ($User->is_author ?? 0) === 1 ? 'Dang la tac gia' : 'Khong phai tac gia' }}" disabled>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Vai trò và phạm vi quyền <span class="text-red">*</span></label>
                    @if(Auth::user()->isAdmin())
                        @php
                            $activeRoleIds = array_map('intval', old('role_ids', $selectedRoleIds ?? []));
                        @endphp
                        <div class="row">
                            @foreach($roles as $role)
                                @continue($role->name === 'super_admin' && (int) $User->level !== 1)
                                <div class="col-md-4 mb-2">
                                    <label class="d-flex align-items-start p-3"
                                           style="gap:10px;border:1px solid var(--border-subtle);border-radius:8px;background:var(--bg-soft);cursor:pointer;">
                                        <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"
                                               style="margin-top:3px;"
                                               {{ in_array((int) $role->id, $activeRoleIds, true) ? 'checked' : '' }}>
                                        <span>
                                            <strong style="display:block;color:var(--text-primary);">{{ $role->display_name }}</strong>
                                            <small class="text-muted">{{ $role->description ?: 'Quyền theo vai trò hệ thống.' }}</small>
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted">SEO được sửa mọi bài để tối ưu metadata nhưng không có quyền duyệt hoặc xóa bài. Phóng viên chỉ quản lý bài của mình.</small>
                    @else
                        <input type="text" class="form-control"
                               value="{{ $User->roles->pluck('display_name')->implode(', ') ?: 'Chưa gán vai trò' }}" disabled>
                    @endif
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
