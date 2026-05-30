@extends('back.template.master')

@section('title', 'Quản lý tác giả')
@section('heading', 'Quản lý tác giả')
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

.vu-news-stats {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.news-stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    padding: 16px;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: border-color 0.2s;
}

.news-stat-card:hover {
    border-color: rgba(255, 255, 255, 0.1);
}

.news-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    opacity: 0.8;
}

.news-stat-card.blue::before { background: var(--accent-blue); }
.news-stat-card.green::before { background: var(--accent-green); }
.news-stat-card.gold::before { background: var(--accent-gold); }
.news-stat-card.red::before { background: var(--accent-red); }
.news-stat-card.purple::before { background: var(--accent-purple); }

.news-stat-value {
    font-size: 28px;
    font-weight: 800;
    line-height: 1;
    font-family: 'JetBrains Mono', monospace;
}

.news-stat-card.blue .news-stat-value { color: var(--accent-blue); }
.news-stat-card.green .news-stat-value { color: var(--accent-green); }
.news-stat-card.gold .news-stat-value { color: var(--accent-gold); }
.news-stat-card.red .news-stat-value { color: var(--accent-red); }
.news-stat-card.purple .news-stat-value { color: var(--accent-purple); }

.news-stat-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    margin-top: 6px;
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
    min-width: 140px;
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

.action-btn-sm.success:hover {
    color: var(--status-success);
    border-color: rgba(34, 197, 94, 0.4);
}

.action-btn-sm.warning:hover {
    color: var(--status-warning);
    border-color: rgba(224, 186, 96, 0.4);
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

.author-cell {
    display: flex;
    align-items: center;
    gap: 8px;
}

.author-avatar {
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

.author-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 13px;
}

.author-username {
    font-size: 11px;
    color: var(--text-muted);
}

@media (max-width: 1199px) {
    .vu-news-stats { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 991px) {
    .vu-news-stats { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 767px) {
    .vu-news-stats { grid-template-columns: 1fr 1fr; }
    .vu-filter-row { flex-direction: column; }
    .vu-filter-group { width: 100%; }
    .vu-filter-group.search { min-width: unset; }
}
</style>

<!-- Back Button -->
<div style="margin-bottom: 16px;">
    <a href="{{ url('admin/admin-manager/list') }}" class="btn-outline" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; font-size: 12px;">
        <i class="fas fa-arrow-left"></i> Quay lại danh sách nhân viên
    </a>
</div>

<!-- Page Header -->
<div class="vu-page-header">
    <h1><i class="fas fa-feather-alt mr-2" style="color: var(--accent-gold);"></i>Quản lý tác giả</h1>
    @if(Auth::user()->hasAnyPermission(['admin-manager.create']))
        <a href="{{ url('admin/admin-manager/add') }}" class="btn-gold">
            <i class="fas fa-user-plus"></i> Thêm tài khoản
        </a>
    @endif
</div>

<!-- Stats Cards -->
<div class="vu-news-stats">
    <div class="news-stat-card blue">
        <div class="news-stat-value">{{ number_format($summary['total_authors'] ?? 0) }}</div>
        <div class="news-stat-label">Tổng tác giả</div>
    </div>
    <div class="news-stat-card green">
        <div class="news-stat-value">{{ number_format($summary['active_authors'] ?? 0) }}</div>
        <div class="news-stat-label">Đang bật tác giả</div>
    </div>
    <div class="news-stat-card gold">
        <div class="news-stat-value">{{ number_format($summary['total_articles'] ?? 0) }}</div>
        <div class="news-stat-label">Tổng bài viết</div>
    </div>
    <div class="news-stat-card purple">
        <div class="news-stat-value">{{ number_format($summary['total_views'] ?? 0) }}</div>
        <div class="news-stat-label">Tổng lượt xem</div>
    </div>
    <div class="news-stat-card red">
        <div class="news-stat-value">{{ number_format($summary['published_articles'] ?? 0) }}</div>
        <div class="news-stat-label">Bài đã xuất bản</div>
    </div>
    <div class="news-stat-card gold">
        <div class="news-stat-value">{{ number_format((float) ($summary['rating_avg'] ?? 0), 1) }}</div>
        <div class="news-stat-label">Điểm TB</div>
    </div>
</div>

<!-- Table Card -->
<div class="vu-card-dark">
    <!-- Filters -->
    <div class="vu-filters-dark">
        <form action="{{ url('admin/authors/list') }}" method="GET" class="vu-filter-row">
            <div class="vu-filter-group search">
                <label>Tìm kiếm</label>
                <input type="text" name="keyword" class="vu-input" placeholder="Tài khoản, họ tên, email..." value="{{ $keyword ?? '' }}">
            </div>
            <div class="vu-filter-group">
                <label>Tư cách tác giả</label>
                <select name="author_status" class="vu-select">
                    <option value="">-- Tất cả --</option>
                    <option value="active" {{ ($authorStatus ?? '') === 'active' ? 'selected' : '' }}>Đang bật</option>
                    <option value="historical" {{ ($authorStatus ?? '') === 'historical' ? 'selected' : '' }}>Tác giả lịch sử</option>
                </select>
            </div>
            <div class="vu-filter-group">
                <label>Trạng thái TK</label>
                <select name="account_status" class="vu-select">
                    <option value="">-- Tất cả --</option>
                    <option value="1" {{ ($accountStatus ?? '') === '1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ ($accountStatus ?? '') === '0' ? 'selected' : '' }}>Khóa</option>
                </select>
            </div>
            <div class="vu-filter-group">
                <label>Danh mục</label>
                <select name="category_id" class="vu-select">
                    <option value="">-- Tất cả --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->RowID }}" {{ request('category_id') == $category->RowID ? 'selected' : '' }}>
                            {{ $category->Name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="vu-filter-group">
                <label>Sắp xếp</label>
                <select name="sort" class="vu-select">
                    <option value="views" {{ ($sort ?? '') === 'views' ? 'selected' : '' }}>Lượt xem cao nhất</option>
                    <option value="articles" {{ ($sort ?? '') === 'articles' ? 'selected' : '' }}>Tổng bài viết</option>
                    <option value="published" {{ ($sort ?? '') === 'published' ? 'selected' : '' }}>Bài đã xuất bản</option>
                    <option value="comments" {{ ($sort ?? '') === 'comments' ? 'selected' : '' }}>Bình luận</option>
                    <option value="rating" {{ ($sort ?? '') === 'rating' ? 'selected' : '' }}>Điểm đánh giá</option>
                    <option value="name" {{ ($sort ?? '') === 'name' ? 'selected' : '' }}>Tên tác giả</option>
                </select>
            </div>
            <button type="submit" class="btn-search">
                <i class="fas fa-filter"></i> Áp dụng
            </button>
            <a href="{{ url('admin/authors/list') }}" class="btn-outline" style="height: 36px;">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>
        <form action="{{ url('admin/authors/list') }}" method="GET" class="vu-filter-row" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border-subtle);">
            <div class="vu-filter-group">
                <label>Từ ngày</label>
                <input type="date" name="from_date" class="vu-input" value="{{ request('from_date') }}">
            </div>
            <div class="vu-filter-group">
                <label>Đến ngày</label>
                <input type="date" name="to_date" class="vu-input" value="{{ request('to_date') }}">
            </div>
            @if(request('keyword'))<input type="hidden" name="keyword" value="{{ request('keyword') }}">@endif
            @if(request('author_status'))<input type="hidden" name="author_status" value="{{ request('author_status') }}">@endif
            @if(request('account_status'))<input type="hidden" name="account_status" value="{{ request('account_status') }}">@endif
            @if(request('category_id'))<input type="hidden" name="category_id" value="{{ request('category_id') }}">@endif
            @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
            <button type="submit" class="btn-search">
                <i class="fas fa-calendar"></i> Lọc ngày
            </button>
        </form>
    </div>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="vu-table-dark">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">STT</th>
                    <th>Tác giả</th>
                    <th>Email</th>
                    <th style="width: 130px; text-align: center;">Tư cách</th>
                    <th style="width: 80px; text-align: center;">Bài viết</th>
                    <th style="width: 80px; text-align: center;">Xuất bản</th>
                    <th style="width: 80px; text-align: center;">Workflow</th>
                    <th style="width: 90px; text-align: center;">Lượt xem</th>
                    <th style="width: 80px; text-align: center;">Bình luận</th>
                    <th style="width: 80px; text-align: center;">Đánh giá</th>
                    <th style="width: 90px; text-align: center;">Điểm TB</th>
                    <th style="width: 160px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $startIndex = method_exists($authors, 'currentPage') ? (($authors->currentPage() - 1) * $authors->perPage()) : 0;
                @endphp
                @forelse($authors as $index => $author)
                <tr>
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">
                        {{ $startIndex + $index + 1 }}
                    </td>
                    <td>
                        <div class="author-cell">
                            <div class="author-avatar">
                                {{ strtoupper(substr($author->fullname ?: ($author->username ?: 'U'), 0, 1)) }}
                            </div>
                            <div>
                                <div class="author-name">{{ $author->fullname ?: $author->username }}</div>
                                @if($author->username)
                                    <div class="author-username">{{ $author->username }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $author->email ?: '-' }}</td>
                    <td style="text-align: center;">
                        @if((int) ($author->is_author ?? 0) === 1)
                            <span class="vu-badge-sm success">Đang bật</span>
                        @else
                            <span class="vu-badge-sm neutral">Lịch sử</span>
                        @endif
                        @if((int) ($author->is_active ?? 1) !== 1)
                            <div style="margin-top: 4px;"><span class="vu-badge-sm danger">TK khóa</span></div>
                        @endif
                    </td>
                    <td style="text-align: center;"><span class="vu-badge-sm info">{{ number_format((int) ($author->total_articles ?? 0)) }}</span></td>
                    <td style="text-align: center;">{{ number_format((int) ($author->published_articles ?? 0)) }}</td>
                    <td style="text-align: center;">{{ number_format((int) ($author->workflow_articles ?? 0)) }}</td>
                    <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 600; color: var(--accent-gold);">
                        {{ number_format((int) ($author->total_views ?? 0)) }}
                    </td>
                    <td style="text-align: center;">{{ number_format((int) ($author->active_comments ?? 0)) }}</td>
                    <td style="text-align: center;">{{ number_format((int) ($author->rating_count ?? 0)) }}</td>
                    <td style="text-align: center;">
                        @php $ra = (float) ($author->rating_avg ?? 0); @endphp
                        <span class="vu-badge-sm {{ $ra >= 4 ? 'success' : ($ra >= 3 ? 'warning' : 'danger') }}">
                            {{ number_format($ra, 1) }}/5
                        </span>
                    </td>
                    <td>
                        <div class="action-group" style="justify-content: center;">
                            <a href="{{ url('admin/authors/detail/' . $author->id) }}" class="action-btn-sm info" title="Thống kê tác giả">
                                <i class="fas fa-chart-line"></i>
                            </a>
                            @if(Auth::user()->hasAnyPermission(['admin-manager.edit']))
                                <a href="{{ url('admin/admin-manager/edit/' . $author->id) }}" class="action-btn-sm" title="Chỉnh sửa tài khoản">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                            @if(Auth::user()->hasAnyPermission(['author.manage', 'admin-manager.edit']))
                                <form action="{{ url('admin/authors/toggle/' . $author->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="action-btn-sm {{ (int) ($author->is_author ?? 0) === 1 ? 'warning' : 'success' }}"
                                            title="{{ (int) ($author->is_author ?? 0) === 1 ? 'Tắt tác giả' : 'Bật tác giả' }}">
                                        <i class="fas {{ (int) ($author->is_author ?? 0) === 1 ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12">
                        <div class="empty-state-dark">
                            <div class="empty-icon"><i class="fas fa-feather-alt"></i></div>
                            <h3>Chưa có tác giả nào</h3>
                            <p>Không có tác giả phù hợp với bộ lọc hiện tại.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(method_exists($authors, 'links') && $authors->hasPages())
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 12px;">
        <div style="font-size: 12px; color: var(--text-muted);">
            Hiển thị {{ $authors->firstItem() ?? 0 }} - {{ $authors->lastItem() ?? 0 }} trong {{ $authors->total() }} tác giả
        </div>
        <div style="display: flex; gap: 4px;">
            @if($authors->onFirstPage())
                <span style="min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; color: var(--text-secondary); background: var(--bg-secondary); border: 1px solid var(--border-subtle); opacity: 0.3; cursor: not-allowed;"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $authors->previousPageUrl() }}" style="min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; color: var(--text-secondary); background: var(--bg-secondary); border: 1px solid var(--border-subtle); text-decoration: none;"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach($authors->getUrlRange(max(1, $authors->currentPage() - 2), min($authors->lastPage(), $authors->currentPage() + 2)) as $page => $url)
                @if($page == $authors->currentPage())
                    <span style="min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; background: var(--accent-gold); color: #000; border: 1px solid var(--accent-gold); font-weight: 600;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; color: var(--text-secondary); background: var(--bg-secondary); border: 1px solid var(--border-subtle); text-decoration: none;">{{ $page }}</a>
                @endif
            @endforeach

            @if($authors->hasMorePages())
                <a href="{{ $authors->nextPageUrl() }}" style="min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; color: var(--text-secondary); background: var(--bg-secondary); border: 1px solid var(--border-subtle); text-decoration: none;"><i class="fas fa-chevron-right"></i></a>
            @else
                <span style="min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; color: var(--text-secondary); background: var(--bg-secondary); border: 1px solid var(--border-subtle); opacity: 0.3; cursor: not-allowed;"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
