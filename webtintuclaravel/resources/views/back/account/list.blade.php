@extends('back.template.master')

@section('title', 'Quản lý thành viên')
@section('heading', 'Danh sách thành viên')
@section('member-list', 'active')

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
    font-size: 12px;
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
.vu-badge-sm.neutral { background: rgba(255,255,255,0.06); color: var(--text-secondary); }

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

.action-btn-sm.success:hover {
    color: var(--status-success);
    border-color: rgba(34, 197, 94, 0.4);
}

.action-btn-sm.warning:hover {
    color: var(--status-warning);
    border-color: rgba(224, 186, 96, 0.4);
}

.member-avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: #000;
    flex-shrink: 0;
}

.member-cell {
    display: flex;
    align-items: center;
    gap: 8px;
}

.member-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 13px;
}

.page-hint {
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 20px;
}

.page-hint a {
    color: var(--accent-gold);
}

.account-count {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    background: rgba(201, 168, 76, 0.1);
    border: 1px solid rgba(201, 168, 76, 0.2);
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
    color: var(--accent-gold);
}

.pagination-dark {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-top: 1px solid var(--border-subtle);
    flex-wrap: wrap;
    gap: 12px;
}

.pagination-info-dark {
    font-size: 12px;
    color: var(--text-muted);
}

.pagination-links-dark {
    display: flex;
    gap: 4px;
}

.pagination-link {
    min-width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 500;
    color: var(--text-secondary);
    background: var(--bg-secondary);
    border: 1px solid var(--border-subtle);
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
    padding: 0 8px;
}

.pagination-link:hover {
    background: var(--bg-hover);
    color: var(--text-primary);
}

.pagination-link.active {
    background: var(--accent-gold);
    color: #000;
    border-color: var(--accent-gold);
}

.pagination-link.disabled {
    opacity: 0.3;
    cursor: not-allowed;
    pointer-events: none;
}

@media (max-width: 767px) {
    .vu-filter-row { flex-direction: column; }
    .vu-filter-group { width: 100%; }
    .vu-filter-group.search { min-width: unset; }
    .action-group { flex-wrap: wrap; }
}
</style>

<!-- Page Header -->
<div class="vu-page-header">
    <h1><i class="fas fa-users mr-2" style="color: var(--accent-gold);"></i>Quản lý thành viên</h1>
    <span class="account-count">
        <i class="fas fa-user"></i> {{ $accounts->total() }} tài khoản
    </span>
</div>

<p class="page-hint">
    Tài khoản quản trị viên xem trong
    <a href="{{ url('admin/admin-manager/list') }}">Quản lý nhân viên nội bộ</a>.
</p>

<!-- Table Card -->
<div class="vu-card-dark">
    <!-- Filters -->
    <div class="vu-filters-dark">
        <form action="{{ url('admin/member/list') }}" method="GET" class="vu-filter-row">
            <div class="vu-filter-group search">
                <label>Tìm kiếm</label>
                <input type="text" name="keyword" class="vu-input" placeholder="Tên đăng nhập, họ tên, email..." value="{{ $keyword ?? '' }}">
            </div>
            <div class="vu-filter-group">
                <label>Trạng thái</label>
                <select name="is_active" class="vu-select">
                    <option value="">-- Tất cả --</option>
                    <option value="1" {{ ($is_active ?? '') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="0" {{ ($is_active ?? '') === '0' ? 'selected' : '' }}>Bị vô hiệu hóa</option>
                </select>
            </div>
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i> Lọc
            </button>
            <a href="{{ url('admin/member/list') }}" class="btn-outline" style="height: 36px;">
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
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                    <th style="width: 140px; text-align: center;">Trạng thái</th>
                    <th style="width: 150px; text-align: center;">Ngày đăng ký</th>
                    <th style="width: 200px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($accounts) && count($accounts) > 0)
                    @foreach($accounts as $k => $v)
                        <tr>
                            <td style="text-align: center; color: var(--text-muted); font-size: 12px;">{{ $k + 1 }}</td>
                            <td>
                                <div class="member-cell">
                                    <div class="member-avatar-sm">{{ strtoupper(substr($v->username, 0, 1)) }}</div>
                                    <span class="member-name">{{ $v->username }}</span>
                                    @if($v->is_active == 0)
                                        <span class="vu-badge-sm danger">Khóa</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $v->fullname ?? '—' }}</td>
                            <td>{{ $v->email ?? '—' }}</td>
                            <td>{{ $v->phone ?? '—' }}</td>
                            <td style="text-align: center;">
                                @if($v->is_active == 1)
                                    <span class="vu-badge-sm success"><i class="fas fa-check mr-1"></i>Hoạt động</span>
                                @else
                                    <span class="vu-badge-sm neutral"><i class="fas fa-ban mr-1"></i>Bị khóa</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span style="font-size: 12px; color: var(--text-muted);">
                                    {{ $v->created_at ? date('d/m/Y H:i', strtotime($v->created_at)) : '—' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-group" style="justify-content: center;">
                                    <a href="{{ url('admin/member/view/' . $v->id) }}" class="action-btn-sm info" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ url('admin/member/edit/' . $v->id) }}" class="action-btn-sm" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($v->is_active == 1)
                                        <a href="{{ url('admin/member/lock/' . $v->id) }}" class="action-btn-sm warning"
                                           onclick="return confirm('Khóa tài khoản &quot;{{ $v->username }}&quot;?')"
                                           title="Khóa tài khoản">
                                            <i class="fas fa-lock"></i>
                                        </a>
                                    @else
                                        <a href="{{ url('admin/member/unlock/' . $v->id) }}" class="action-btn-sm success"
                                           onclick="return confirm('Mở khóa tài khoản &quot;{{ $v->username }}&quot;?')"
                                           title="Mở khóa tài khoản">
                                            <i class="fas fa-unlock"></i>
                                        </a>
                                    @endif
                                    <form action="{{ url('admin/member/delete/' . $v->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn-sm danger"
                                                onclick="return confirm('Xóa tài khoản này?');"
                                                title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 60px 20px;">
                            <div style="color: var(--text-muted);">
                                <div style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"><i class="fas fa-users"></i></div>
                                <h3 style="font-size: 16px; color: var(--text-primary); margin-bottom: 6px;">Chưa có thành viên nào</h3>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($accounts) && $accounts->hasPages())
    <div class="pagination-dark">
        <div class="pagination-info-dark">
            Hiển thị {{ $accounts->firstItem() ?? 0 }} - {{ $accounts->lastItem() ?? 0 }} trong {{ $accounts->total() }} tài khoản
        </div>
        <div class="pagination-links-dark">
            @if($accounts->onFirstPage())
                <span class="pagination-link disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $accounts->previousPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach($accounts->getUrlRange(max(1, $accounts->currentPage() - 2), min($accounts->lastPage(), $accounts->currentPage() + 2)) as $page => $url)
                @if($page == $accounts->currentPage())
                    <span class="pagination-link active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                @endif
            @endforeach

            @if($accounts->hasMorePages())
                <a href="{{ $accounts->nextPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="pagination-link disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
