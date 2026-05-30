@extends('front.template.master')
@section('title', 'Tài khoản của tôi')
@section('content')

<style>
/* ── Account page shared ── */
.acct-page {
    padding: 0 0 60px;
}

.acct-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 16px rgba(0,0,0,.07);
    overflow: hidden;
}

.acct-sidebar {
    background: linear-gradient(160deg, #24313d 0%, #1e2a36 100%);
    min-height: 100%;
    padding: 0;
}

.acct-sidebar__avatar {
    padding: 28px 24px 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,.08);
}

.acct-sidebar__img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(245,76,11,.5);
    margin-bottom: 10px;
}

.acct-sidebar__letter {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #f54c0b;
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 10px;
    border: 3px solid rgba(245,76,11,.5);
}

.acct-sidebar__name {
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 2px;
}

.acct-sidebar__email {
    color: rgba(255,255,255,.55);
    font-size: .78rem;
}

.acct-sidebar__nav {
    padding: 12px 0 20px;
}

.acct-sidebar__nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    color: rgba(255,255,255,.7);
    font-size: .9rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    background: none;
    width: 100%;
    cursor: pointer;
    transition: all .15s;
}

.acct-sidebar__nav-item:hover {
    background: rgba(245,76,11,.12);
    color: #f54c0b;
}

.acct-sidebar__nav-item.active {
    background: rgba(245,76,11,.15);
    color: #f54c0b;
    border-left: 3px solid #f54c0b;
}

.acct-sidebar__nav-item i {
    width: 18px;
    text-align: center;
    font-size: 14px;
}

.acct-sidebar__nav-divider {
    height: 1px;
    background: rgba(255,255,255,.07);
    margin: 6px 16px;
}

.acct-sidebar__nav-item--danger {
    color: rgba(255,120,120,.7);
}

.acct-sidebar__nav-item--danger:hover {
    background: rgba(239,68,68,.12);
    color: #fca5a5;
}

/* ── Account content area ── */
.acct-content-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
    padding: 24px 28px 20px;
    border-bottom: 2px solid #f0f0f0;
}

.acct-content-header h3 {
    font-size: 1.15rem;
    font-weight: 800;
    color: #1a1a2e;
    margin: 0;
}

.acct-content-header p {
    color: #888;
    font-size: .82rem;
    margin: 4px 0 0;
}

.acct-content-body {
    padding: 28px;
}

/* ── Form fields ── */
.acct-form-label {
    font-size: .82rem;
    font-weight: 700;
    color: #444;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: .04em;
}

.acct-form-control {
    border: 1.5px solid #e4e7eb;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: .9rem;
    transition: border-color .15s, box-shadow .15s;
}

.acct-form-control:focus {
    border-color: #f54c0b;
    box-shadow: 0 0 0 3px rgba(245,76,11,.12);
}

.acct-form-control:disabled {
    background: #f8f9fa;
    color: #aaa;
    cursor: not-allowed;
}

.acct-form-group {
    margin-bottom: 18px;
}

/* ── Avatar upload ── */
.acct-avatar-wrap {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 16px 0 8px;
}

.acct-avatar-preview {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #f0f0f0;
}

.acct-avatar-preview-lg {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #f0f0f0;
}

.acct-avatar-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f54c0b, #d44109);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    font-weight: 800;
}

/* ── Password change ── */
.acct-pw-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #aaa;
    cursor: pointer;
    padding: 4px;
}

.acct-pw-toggle:hover { color: #f54c0b; }

.acct-pw-group {
    position: relative;
}

.acct-pw-group .acct-form-control {
    padding-right: 42px;
}

/* ── Buttons ── */
.acct-btn-save {
    background: #f54c0b;
    border: none;
    border-radius: 10px;
    color: #fff;
    font-weight: 700;
    font-size: .88rem;
    padding: 10px 28px;
    transition: background .15s, transform .1s;
}

.acct-btn-save:hover {
    background: #e03e08;
    color: #fff;
    transform: translateY(-1px);
}

.acct-btn-cancel {
    background: #f0f0f0;
    border: none;
    border-radius: 10px;
    color: #555;
    font-weight: 600;
    font-size: .88rem;
    padding: 10px 20px;
    transition: background .15s;
}

.acct-btn-cancel:hover {
    background: #e0e0e0;
    color: #333;
}

/* ── Alerts ── */
.acct-alert {
    border-radius: 10px;
    padding: 12px 16px;
    font-size: .88rem;
}

/* ── Stats cards ── */
.acct-stat-card {
    border-radius: 12px;
    border: 1.5px solid #f0f0f0;
    padding: 18px 20px;
    text-align: center;
    transition: box-shadow .15s;
}

.acct-stat-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,.08);
}

.acct-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 1.1rem;
}

.acct-stat-num {
    font-size: 1.6rem;
    font-weight: 800;
    color: #1a1a2e;
    line-height: 1;
    margin-bottom: 4px;
}

.acct-stat-lbl {
    font-size: .78rem;
    color: #888;
    text-transform: uppercase;
    letter-spacing: .04em;
}

/* ── Activity list ── */
.acct-activity-item {
    display: flex;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid #f5f5f5;
}

.acct-activity-item:last-child { border-bottom: none; }

.acct-activity-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: .85rem;
}

.acct-activity-body { flex: 1; min-width: 0; }

.acct-activity-title {
    font-size: .85rem;
    font-weight: 600;
    color: #1a1a2e;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.acct-activity-meta {
    font-size: .75rem;
    color: #aaa;
}

/* ── Tab content ── */
.acct-tab-pane { display: none; }
.acct-tab-pane.active { display: block; }

/* ── Favorite cards (in account page mini view) ── */
.acct-fav-thumb {
    width: 56px;
    height: 56px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}

.acct-fav-thumb-placeholder {
    width: 56px;
    height: 56px;
    border-radius: 8px;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ccc;
    font-size: 1.2rem;
    flex-shrink: 0;
}

/* ── Responsive ── */
@media (max-width: 767px) {
    .acct-sidebar {
        border-radius: 16px 16px 0 0;
    }

    .acct-content-body {
        padding: 20px 16px;
    }
}
</style>

<div class="contact_wrap acct-page">
    {{-- Header banner --}}
    <div style="background: linear-gradient(135deg, #24313d 0%, #3a4a5c 100%); padding: 28px 0; margin-bottom: 32px; border-radius: 0 0 24px 24px;">
        <div class="container">
            <div class="d-flex align-items-center gap-3">
                <div style="width:50px; height:50px; background:rgba(245,76,11,.15); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-user-circle" style="font-size:1.4rem; color:#f54c0b;"></i>
                </div>
                <div>
                    <h2 style="color:#fff; font-size:1.15rem; font-weight:800; margin:0;">Tài khoản của tôi</h2>
                    <p style="color:rgba(255,255,255,.6); font-size:.8rem; margin:0;">Quản lý thông tin cá nhân và hoạt động</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            {{-- Sidebar --}}
            <div class="col-12 col-md-3 mb-4">
                <div class="acct-card acct-sidebar">
                    <div class="acct-sidebar__avatar">
                        @if($user->avatar && file_exists(public_path('images/users/' . $user->avatar)))
                            <img src="{{ asset('images/users/' . $user->avatar) }}" alt="Avatar" class="acct-sidebar__img">
                        @else
                            <div class="acct-sidebar__letter">{{ strtoupper(substr($user->username, 0, 1)) }}</div>
                        @endif
                        <div class="acct-sidebar__name">{{ $user->fullname ?? $user->username }}</div>
                        <div class="acct-sidebar__email">{{ $user->email }}</div>
                    </div>
                    <nav class="acct-sidebar__nav">
                        <button class="acct-sidebar__nav-item active" onclick="switchTab('profile')">
                            <i class="fas fa-user-edit"></i> Hồ sơ cá nhân
                        </button>
                        <button class="acct-sidebar__nav-item" onclick="switchTab('security')">
                            <i class="fas fa-shield-alt"></i> Bảo mật
                        </button>
                        <button class="acct-sidebar__nav-item" onclick="switchTab('activity')">
                            <i class="fas fa-history"></i> Hoạt động
                        </button>
                        <button class="acct-sidebar__nav-item" onclick="switchTab('favorites')">
                            <i class="fas fa-heart"></i> Yêu thích
                        </button>
                        <div class="acct-sidebar__nav-divider"></div>
                        <a href="{{ url('/dang-xuat') }}"
                           class="acct-sidebar__nav-item acct-sidebar__nav-item--danger"
                           onclick="event.preventDefault(); document.getElementById('logoutFormAcct').submit();">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                        <form id="logoutFormAcct" action="{{ url('/dang-xuat') }}" method="GET" style="display:none;"></form>
                    </nav>
                </div>
            </div>

            {{-- Content --}}
            <div class="col-12 col-md-9">
                {{-- Alert --}}
                @if(session('success'))
                    <div class="acct-alert alert alert-success bg-success text-white border-0 mb-3">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="acct-alert alert alert-danger mb-3">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif

                {{-- Tab: Hồ sơ cá nhân --}}
                <div id="tab-profile" class="acct-tab-pane active">
                    <div class="acct-card">
                        <div class="acct-content-header">
                            <h3><i class="fas fa-user-edit mr-2" style="color:#f54c0b;"></i>Hồ sơ cá nhân</h3>
                            <p>Cập nhật thông tin tài khoản của bạn</p>
                        </div>
                        <div class="acct-content-body">
                            <form action="{{ url('/tai-khoan') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                                @csrf

                                {{-- Avatar --}}
                                <div class="acct-form-group">
                                    <label class="acct-form-label">Ảnh đại diện</label>
                                    <div class="acct-avatar-wrap">
                                        <div class="acct-avatar-placeholder">
                                            {{ strtoupper(substr($user->username, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="custom-file" style="max-width:260px;">
                                                <input type="file" class="custom-file-input" id="avatar" name="avatar" accept="image/*" onchange="previewAccountAvatar(this);">
                                                <label class="custom-file-label" for="avatar">Chọn ảnh...</label>
                                            </div>
                                            <small class="text-muted d-block mt-1">JPG, PNG, GIF &bull; Tối đa 2MB</small>
                                        </div>
                                    </div>
                                    <div class="mt-2" id="acctAvatarPreview"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="acct-form-group">
                                            <label class="acct-form-label">Tên đăng nhập</label>
                                            <input type="text" class="acct-form-control" value="{{ $user->username }}" disabled>
                                            <small class="text-muted">Tên đăng nhập không thể thay đổi.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="acct-form-group">
                                            <label class="acct-form-label">Email</label>
                                            <input type="email" class="acct-form-control @error('email') is-invalid @enderror"
                                                   name="email" value="{{ old('email', $user->email) }}" placeholder="Nhập email">
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="acct-form-group">
                                            <label class="acct-form-label">Họ và tên</label>
                                            <input type="text" class="acct-form-control @error('fullname') is-invalid @enderror"
                                                   name="fullname" value="{{ old('fullname', $user->fullname) }}" placeholder="Nhập họ và tên">
                                            @error('fullname')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="acct-form-group">
                                            <label class="acct-form-label">Số điện thoại</label>
                                            <input type="text" class="acct-form-control @error('phone') is-invalid @enderror"
                                                   name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Nhập số điện thoại">
                                            @error('phone')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="acct-form-group">
                                    <label class="acct-form-label">Địa chỉ</label>
                                    <input type="text" class="acct-form-control @error('address') is-invalid @enderror"
                                           name="address" value="{{ old('address', $user->address) }}" placeholder="Nhập địa chỉ">
                                    @error('address')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex gap-2 mt-3">
                                    <button type="submit" class="acct-btn-save">
                                        <i class="fas fa-save mr-2"></i>Lưu thay đổi
                                    </button>
                                    <a href="{{ url('/') }}" class="acct-btn-cancel">
                                        <i class="fas fa-times mr-2"></i>Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Tab: Bảo mật --}}
                <div id="tab-security" class="acct-tab-pane">
                    <div class="acct-card">
                        <div class="acct-content-header">
                            <h3><i class="fas fa-shield-alt mr-2" style="color:#f54c0b;"></i>Bảo mật tài khoản</h3>
                            <p>Thay đổi mật khẩu để bảo vệ tài khoản của bạn</p>
                        </div>
                        <div class="acct-content-body">
                            <form action="{{ url('/tai-khoan/doi-mat-khau') }}" method="POST" id="passwordForm">
                                @csrf
                                <div class="acct-form-group">
                                    <label class="acct-form-label">Mật khẩu hiện tại <span style="color:#f54c0b;">*</span></label>
                                    <div class="acct-pw-group">
                                        <input type="password" class="acct-form-control" name="current_password" id="current_password" placeholder="Nhập mật khẩu hiện tại" required>
                                        <button type="button" class="acct-pw-toggle" onclick="togglePw('current_password', this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="acct-form-group">
                                            <label class="acct-form-label">Mật khẩu mới <span style="color:#f54c0b;">*</span></label>
                                            <div class="acct-pw-group">
                                                <input type="password" class="acct-form-control" name="new_password" id="new_password" placeholder="Ít nhất 6 ký tự" required minlength="6">
                                                <button type="button" class="acct-pw-toggle" onclick="togglePw('new_password', this)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="acct-form-group">
                                            <label class="acct-form-label">Xác nhận mật khẩu mới <span style="color:#f54c0b;">*</span></label>
                                            <div class="acct-pw-group">
                                                <input type="password" class="acct-form-control" name="new_password_confirmation" id="new_password_confirmation" placeholder="Nhập lại mật khẩu mới" required>
                                                <button type="button" class="acct-pw-toggle" onclick="togglePw('new_password_confirmation', this)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(session('pw_error'))
                                    <div class="alert alert-danger">{{ session('pw_error') }}</div>
                                @endif
                                @if(session('pw_success'))
                                    <div class="alert alert-success">{{ session('pw_success') }}</div>
                                @endif
                                <div class="d-flex gap-2 mt-3">
                                    <button type="submit" class="acct-btn-save">
                                        <i class="fas fa-key mr-2"></i>Đổi mật khẩu
                                    </button>
                                </div>
                            </form>

                            <hr style="margin: 32px 0;">

                            {{-- Sessions / Device info --}}
                            <div class="acct-content-header border-0 p-0 mb-3">
                                <h3><i class="fas fa-mobile-alt mr-2" style="color:#f54c0b;"></i>Phiên đăng nhập</h3>
                                <p>Thông tin thiết bị &amp; phiên hiện tại</p>
                            </div>
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8f9fa; border: 1.5px solid #e9ecef;">
                                <div style="width:44px; height:44px; background:#e8f4fd; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-desktop" style="color:#007bff;"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700; font-size:.88rem; color:#1a1a2e;">Thiết bị hiện tại</div>
                                    <div style="font-size:.78rem; color:#888;">{{ request()->ip() }} &bull; {{ now()->format('d/m/Y H:i') }}</div>
                                </div>
                                <span class="badge badge-success ml-auto">Online</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab: Hoạt động --}}
                <div id="tab-activity" class="acct-tab-pane">
                    <div class="acct-card">
                        <div class="acct-content-header">
                            <h3><i class="fas fa-history mr-2" style="color:#f54c0b;"></i>Hoạt động gần đây</h3>
                            <p>Lịch sử bình luận và đánh giá của bạn</p>
                        </div>
                        <div class="acct-content-body">
                            @php
                                $recentComments = \App\Models\NewsComment::where('user_id', $user->id)
                                    ->with('news')
                                    ->orderBy('created_at', 'DESC')
                                    ->limit(10)
                                    ->get();
                            @endphp

                            @if($recentComments->count() > 0)
                                <div>
                                    @foreach($recentComments as $comment)
                                        <div class="acct-activity-item">
                                            <div class="acct-activity-icon" style="background:#e8f4fd; color:#007bff;">
                                                <i class="fas fa-comment-dots"></i>
                                            </div>
                                            <div class="acct-activity-body">
                                                <a href="{{ url($comment->news->Alias . '.html') }}" class="acct-activity-title" target="_blank">
                                                    {{ $comment->news->Name ?? 'Bài viết đã bị xóa' }}
                                                </a>
                                                <div class="acct-activity-meta">
                                                    <i class="far fa-calendar mr-1"></i>
                                                    {{ $comment->created_at->format('d/m/Y H:i') }}
                                                    &bull;
                                                    @if($comment->is_active)
                                                        <span class="text-success"><i class="fas fa-check-circle mr-1"></i>Đã duyệt</span>
                                                    @else
                                                        <span class="text-muted"><i class="fas fa-clock mr-1"></i>Chờ duyệt</span>
                                                    @endif
                                                </div>
                                                <p class="mb-0 mt-1" style="font-size:.82rem; color:#555; line-height:1.4;">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($comment->content), 100) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ url('/thong-bao') }}" class="acct-btn-cancel">
                                        Xem tất cả hoạt động <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div style="width:70px; height:70px; background:#f8f9fa; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                                        <i class="fas fa-comment-dots" style="font-size:1.8rem; color:#ddd;"></i>
                                    </div>
                                    <h5 style="color:#888; font-weight:700;">Chưa có hoạt động</h5>
                                    <p style="color:#aaa; font-size:.85rem;">Bạn chưa bình luận hoặc đánh giá bài viết nào.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tab: Yêu thích --}}
                <div id="tab-favorites" class="acct-tab-pane">
                    <div class="acct-card">
                        <div class="acct-content-header">
                            <h3><i class="fas fa-heart mr-2" style="color:#f54c0b;"></i>Bài viết đã lưu</h3>
                            <p>Danh sách bài viết bạn đã yêu thích</p>
                        </div>
                        <div class="acct-content-body">
                            @php
                                $favList = \App\Models\UserFavorite::with(['news' => function($q){ $q->where('Status', 1); }, 'news.category'])
                                    ->where('user_id', $user->id)
                                    ->orderBy('created_at', 'DESC')
                                    ->limit(6)
                                    ->get();
                            @endphp

                            @if($favList->count() > 0)
                                <div class="row">
                                    @foreach($favList as $fav)
                                        @if($fav->news)
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex gap-3 p-3 rounded-3" style="background:#f8f9fa; border:1.5px solid #e9ecef; transition:all .15s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow='none'">
                                                @if($fav->news->Images && file_exists(public_path('images/news/' . $fav->news->Images)))
                                                    <img src="{{ asset('images/news/' . $fav->news->Images) }}" class="acct-fav-thumb" alt="{{ $fav->news->Name }}">
                                                @else
                                                    <div class="acct-fav-thumb-placeholder">
                                                        <i class="fas fa-newspaper"></i>
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1 min-width-0">
                                                    <a href="{{ url($fav->news->Alias . '.html') }}" class="acct-activity-title d-block" target="_blank">
                                                        {{ $fav->news->Name }}
                                                    </a>
                                                    <div class="acct-activity-meta">
                                                        <i class="far fa-calendar mr-1"></i>{{ $fav->news->created_at ? $fav->news->created_at->format('d/m/Y') : '' }}
                                                    </div>
                                                    <form action="{{ route('yeuthich') }}" method="POST" class="d-inline m-0 mt-1">
                                                        @csrf
                                                        <input type="hidden" name="news_id" value="{{ $fav->news->RowID }}">
                                                        <button type="submit" class="btn btn-sm" style="background:#fff0f0; color:#ff3b30; border:1px solid #ff3b30; border-radius:6px; font-size:.72rem; font-weight:700; padding:3px 10px;">
                                                            <i class="fas fa-heart-broken mr-1"></i>Bỏ thích
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ url('/bai-viet-yeu-thich') }}" class="acct-btn-save">
                                        <i class="fas fa-heart mr-2"></i>Xem tất cả ({{ $favList->count() }}+)
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div style="width:70px; height:70px; background:#f8f9fa; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                                        <i class="far fa-heart" style="font-size:1.8rem; color:#ddd;"></i>
                                    </div>
                                    <h5 style="color:#888; font-weight:700;">Chưa có bài viết yêu thích</h5>
                                    <p style="color:#aaa; font-size:.85rem;">Lưu lại những bài viết bạn thích để đọc lại sau nhé!</p>
                                    <a href="{{ url('/') }}" class="acct-btn-save">
                                        <i class="fas fa-compass mr-2"></i>Khám phá bài viết
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    // Hide all
    document.querySelectorAll('.acct-tab-pane').forEach(function(el) {
        el.classList.remove('active');
    });
    // Remove active from nav
    document.querySelectorAll('.acct-sidebar__nav-item').forEach(function(btn) {
        btn.classList.remove('active');
    });
    // Show target
    var target = document.getElementById('tab-' + tab);
    if (target) target.classList.add('active');
    // Active nav button
    var btns = document.querySelectorAll('.acct-sidebar__nav-item');
    var idx = ['profile','security','activity','favorites'].indexOf(tab);
    if (btns[idx]) btns[idx].classList.add('active');
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function previewAccountAvatar(input) {
    var preview = document.getElementById('acctAvatarPreview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" class="acct-avatar-preview-lg" style="margin-top:8px;">';
        };
        reader.readAsDataURL(input.files[0]);
        input.nextElementSibling.textContent = input.files[0].name;
    }
}

function togglePw(inputId, btn) {
    var inp = document.getElementById(inputId);
    var icon = btn.querySelector('i');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        inp.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

@stop
