@extends('back.template.master')

@section('title', 'Quản lý nhân viên nội bộ')
@section('heading', 'Quản lý nhân viên nội bộ')
@section('admin-manager', 'menu-open')
@section('admin-manager-list', 'active')

@section('content')
<style>
.vu-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
}

.vu-page-header h1 {
    font-size: 20px;
    font-weight: 700;
    margin: 0;
}

.btn-gold {
    background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light));
    color: #000;
    border: none;
    font-weight: 600;
    border-radius: var(--radius-md);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
}

.btn-gold:hover {
    box-shadow: var(--shadow-gold);
    transform: translateY(-1px);
}

.btn-outline {
    background: var(--bg-card);
    color: var(--text-secondary);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-md);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
}

.btn-outline:hover {
    border-color: var(--accent-gold);
    color: var(--text-primary);
}

.vu-filters-dark {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    padding: 16px 20px;
}

.vu-filter-row {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    flex-wrap: wrap;
}

.vu-filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 160px;
}

.vu-filter-group label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
}

.vu-filter-group .vu-input,
.vu-filter-group .vu-select {
    height: 36px;
    padding: 0 12px;
    font-size: 13px;
}

.vu-filter-group.search {
    flex: 1;
    min-width: 200px;
}

.vu-filter-group .vu-input { width: 100%; }

.btn-search {
    height: 36px;
    padding: 0 16px;
    background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light));
    color: #000;
    border: none;
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-search:hover {
    box-shadow: var(--shadow-gold);
}

.vu-card-dark {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.vu-table-dark {
    width: 100%;
    border-collapse: collapse;
}

.vu-table-dark thead {
    background: rgba(0, 0, 0, 0.3);
}

.vu-table-dark th {
    padding: 12px 14px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    text-align: left;
    border-bottom: 1px solid var(--border-subtle);
    white-space: nowrap;
}

.vu-table-dark td {
    padding: 12px 14px;
    font-size: 13px;
    color: var(--text-secondary);
    border-bottom: 1px solid var(--border-subtle);
    vertical-align: middle;
}

.vu-table-dark tbody tr {
    transition: background 0.15s;
}

.vu-table-dark tbody tr:hover {
    background: rgba(255, 255, 255, 0.02);
}

.vu-table-dark tbody tr:last-child td {
    border-bottom: none;
}

.vu-badge-sm {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.vu-badge-sm.success { background: var(--status-success-bg); color: var(--status-success); }
.vu-badge-sm.warning { background: var(--status-warning-bg); color: var(--status-warning); }
.vu-badge-sm.danger { background: var(--status-danger-bg); color: var(--status-danger); }
.vu-badge-sm.info { background: var(--status-info-bg); color: var(--status-info); }
.vu-badge-sm.neutral { background: rgba(255,255,255,0.06); color: var(--text-secondary); }
.vu-badge-sm.gold { background: var(--accent-gold-glow); color: var(--accent-gold); }

.action-group {
    display: flex;
    gap: 4px;
}

.action-btn-sm {
    width: 30px;
    height: 30px;
    border-radius: var(--radius-sm);
    background: var(--bg-secondary);
    border: 1px solid var(--border-subtle);
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s;
    font-size: 12px;
    text-decoration: none;
}

.action-btn-sm:hover {
    color: var(--accent-gold);
    border-color: rgba(201, 168, 76, 0.4);
}

.action-btn-sm.danger:hover {
    color: var(--status-danger);
    border-color: rgba(239, 68, 68, 0.4);
}

.action-btn-sm.info:hover {
    color: var(--accent-blue);
    border-color: rgba(74, 158, 255, 0.4);
}

.empty-state-dark {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: var(--bg-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    color: var(--text-muted);
    font-size: 24px;
}

.empty-state-dark h3 {
    font-size: 16px;
    color: var(--text-primary);
    margin-bottom: 6px;
}

.empty-state-dark p {
    font-size: 13px;
    color: var(--text-muted);
}

.page-hint {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 4px;
}

@media (max-width: 767px) {
    .vu-filter-row { flex-direction: column; }
    .vu-filter-group { width: 100%; }
    .vu-filter-group.search { min-width: unset; }
}
</style>

<!-- Page Header -->
<div class="vu-page-header">
    <h1><i class="fas fa-user-shield mr-2" style="color: var(--accent-gold);"></i>Quản lý nhân viên nội bộ</h1>
    <div class="d-flex gap-2" style="flex-wrap: wrap;">
        @if(Auth::user()->hasPermission('admin-manager.create'))
        <a href="{{ url('admin/admin-manager/add') }}" class="btn-gold">
            <i class="fas fa-user-plus"></i> Thêm tài khoản
        </a>
        @endif
        @if(Auth::user()->hasAnyPermission(['author.list', 'author.manage']))
            <a href="{{ url('admin/authors/list') }}" class="btn-outline">
                <i class="fas fa-feather-alt"></i> Quản lý tác giả
            </a>
        @endif
    </div>
</div>

<p class="page-hint">
    Tài khoản đăng ký trên website được quản lý tại
    <a href="{{ url('admin/member/list') }}" style="color: var(--accent-gold);">thành viên</a>.
</p>

<!-- Table Card -->
<div class="vu-card-dark" style="margin-top: 16px;">
    <!-- Filters -->
    <div class="vu-filters-dark">
        <form action="{{ url('admin/admin-manager/list') }}" method="GET" class="vu-filter-row">
            <div class="vu-filter-group search">
                <label>Tìm kiếm</label>
                <input type="text" name="keyword" class="vu-input" placeholder="Tài khoản, họ tên, email..." value="{{ $keyword ?? '' }}">
            </div>
            <div class="vu-filter-group">
                <label>Cấp bậc</label>
                <select name="level" class="vu-select">
                    <option value="">-- Tất cả --</option>
                    <option value="1" {{ ($level ?? '') === '1' ? 'selected' : '' }}>Quản trị viên</option>
                    <option value="2" {{ ($level ?? '') === '2' ? 'selected' : '' }}>Seo Content</option>
                </select>
            </div>
            <div class="vu-filter-group">
                <label>Trạng thái</label>
                <select name="status" class="vu-select">
                    <option value="">-- Tất cả --</option>
                    <option value="1" {{ ($status ?? '') === '1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ ($status ?? '') === '0' ? 'selected' : '' }}>Khóa</option>
                </select>
            </div>
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i> Lọc
            </button>
            <a href="{{ url('admin/admin-manager/list') }}" class="btn-outline" style="height: 36px;">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="vu-table-dark">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">STT</th>
                    <th>Tài khoản</th>
                    <th>Họ và tên</th>
                    <th style="width: 150px;">Cấp bậc</th>
                    <th style="width: 100px; text-align: center;">Bài đã đăng</th>
                    <th>Email</th>
                    <th style="width: 120px;">Trạng thái</th>
                    <th style="width: 140px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @forelse($user as $index => $item)
                <tr>
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">
                        {{ $index + 1 }}
                    </td>
                    <td>
                        <strong style="color: var(--text-primary);">{{ $item->username ?? '-' }}</strong>
                    </td>
                    <td>{{ $item->fullname ?? '-' }}</td>
                    <td>
                        @if($item->level == 1)
                            <span class="vu-badge-sm danger"><i class="fas fa-crown mr-1"></i>Quản trị viên</span>
                        @else
                            <span class="vu-badge-sm warning"><i class="fas fa-user mr-1"></i>Seo Content</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="vu-badge-sm info">{{ $item->authored_news_count ?? 0 }}</span>
                    </td>
                    <td>{{ $item->email ?? '-' }}</td>
                    <td>
                        @if((int) ($item->is_active ?? 1) === 1)
                            <span class="vu-badge-sm success"><i class="fas fa-check mr-1"></i>Hoạt động</span>
                        @else
                            <span class="vu-badge-sm neutral"><i class="fas fa-lock mr-1"></i>Bị khóa</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-group" style="justify-content: center;">
                            @if(Auth::user()->hasAnyPermission(['author.list', 'author.manage']) && ((int) ($item->is_author ?? 0) === 1 || ($item->authored_news_count ?? 0) > 0))
                                <a href="{{ url('admin/authors/detail/' . $item->id) }}" class="action-btn-sm info" title="Dashboard tác giả">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                            @endif

                            @if(Auth::user()->hasPermission('admin-manager.edit'))
                                <a href="{{ url('admin/admin-manager/edit/' . $item->id) }}" class="action-btn-sm" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif

                            @if(Auth::user()->hasPermission('admin-manager.delete'))
                                <form action="{{ url('admin/admin-manager/delete/' . $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn-sm danger" title="Xóa"
                                            onclick="return confirm('Bạn có chắc muốn xóa tài khoản nội bộ này?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state-dark">
                            <div class="empty-icon"><i class="fas fa-users"></i></div>
                            <h3>Chưa có tài khoản nào</h3>
                            <p>Tạo tài khoản nội bộ đầu tiên.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
