@extends('back.template.master')

@section('title', 'Quản lý nhận tin khuyến mại')
@section('heading', 'Danh sách nhận tin khuyến mại')
@section('newsletter', 'active')

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
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}

.news-stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    padding: 18px;
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
.news-stat-card.red::before { background: var(--accent-red); }
.news-stat-card.gold::before { background: var(--accent-gold); }

.news-stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}

.news-stat-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
}

.news-stat-icon {
    width: 32px;
    height: 32px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.news-stat-card.blue .news-stat-icon { background: rgba(74, 158, 255, 0.12); color: var(--accent-blue); }
.news-stat-card.green .news-stat-icon { background: rgba(92, 185, 123, 0.12); color: var(--accent-green); }
.news-stat-card.red .news-stat-icon { background: rgba(229, 115, 115, 0.12); color: var(--accent-red); }
.news-stat-card.gold .news-stat-icon { background: rgba(201, 168, 76, 0.12); color: var(--accent-gold); }

.news-stat-value {
    font-size: 36px;
    font-weight: 800;
    line-height: 1;
    font-family: 'JetBrains Mono', monospace;
}

.news-stat-card.blue .news-stat-value { color: var(--accent-blue); }
.news-stat-card.green .news-stat-value { color: var(--accent-green); }
.news-stat-card.red .news-stat-value { color: var(--accent-red); }
.news-stat-card.gold .news-stat-value { color: var(--accent-gold); }

.news-stat-desc {
    font-size: 12px;
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

@media (max-width: 991px) {
    .vu-news-stats { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 767px) {
    .vu-news-stats { grid-template-columns: 1fr; }
    .vu-filter-row { flex-direction: column; }
    .vu-filter-group { width: 100%; }
    .vu-filter-group.search { min-width: unset; }
}
</style>

<!-- Page Header -->
<div class="vu-page-header">
    <h1><i class="fas fa-envelope-open-text mr-2" style="color: var(--accent-gold);"></i>Nhận tin khuyến mãi</h1>
    <a href="{{ url('admin/newsletter/export') }}" class="btn-outline">
        <i class="fas fa-file-csv"></i> Export CSV
    </a>
</div>

<!-- Stats Cards -->
<div class="vu-news-stats">
    <div class="news-stat-card blue">
        <div class="news-stat-header">
            <div class="news-stat-label">Tổng email</div>
            <div class="news-stat-icon"><i class="fas fa-envelope"></i></div>
        </div>
        <div class="news-stat-value">{{ $stats['total'] }}</div>
        <div class="news-stat-desc">Tổng số đăng ký nhận tin</div>
    </div>
    <div class="news-stat-card green">
        <div class="news-stat-header">
            <div class="news-stat-label">Đang hoạt động</div>
            <div class="news-stat-icon"><i class="fas fa-check-circle"></i></div>
        </div>
        <div class="news-stat-value">{{ $stats['active'] }}</div>
        <div class="news-stat-desc">Email đang theo dõi</div>
    </div>
    <div class="news-stat-card red">
        <div class="news-stat-header">
            <div class="news-stat-label">Đã hủy</div>
            <div class="news-stat-icon"><i class="fas fa-ban"></i></div>
        </div>
        <div class="news-stat-value">{{ $stats['unsub'] }}</div>
        <div class="news-stat-desc">Không muốn nhận tin nữa</div>
    </div>
    <div class="news-stat-card gold">
        <div class="news-stat-header">
            <div class="news-stat-label">Chưa xem</div>
            <div class="news-stat-icon"><i class="fas fa-eye-slash"></i></div>
        </div>
        <div class="news-stat-value">{{ $stats['unreviewed'] }}</div>
        <div class="news-stat-desc">Email mới chưa duyệt</div>
    </div>
</div>

<!-- Table Card -->
<div class="vu-card-dark">
    <!-- Filters -->
    <div class="vu-filters-dark">
        <form method="GET" action="{{ url('admin/newsletter/list') }}" class="vu-filter-row">
            <div class="vu-filter-group search">
                <label>Tìm kiếm</label>
                <input type="text" name="keyword" class="vu-input" placeholder="Tìm theo email..." value="{{ request('keyword') }}">
            </div>
            <div class="vu-filter-group">
                <label>Trạng thái</label>
                <select name="status" class="vu-select">
                    <option value="">-- Tất cả --</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i> Lọc
            </button>
            <a href="{{ url('admin/newsletter/list') }}" class="btn-outline" style="height: 36px;">
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
                    <th>Email</th>
                    <th style="width: 150px;">Trạng thái</th>
                    <th style="width: 150px;">Ngày đăng ký</th>
                    <th style="width: 150px;">Ngày hủy</th>
                    <th style="width: 100px;">IP</th>
                    <th style="width: 100px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($Newsletter) && count($Newsletter) > 0)
                @foreach($Newsletter as $k => $v)
                <tr>
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">
                        {{ $k + 1 }}
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-weight: 600; color: var(--text-primary);">{{ $v->Email }}</span>
                            @if($v->is_reviewed == false)
                                <span class="vu-badge-sm warning">Mới</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($v->is_active && !$v->unsubscribed_at)
                            <span class="vu-badge-sm success"><i class="fas fa-check mr-1"></i>Đang hoạt động</span>
                        @else
                            <span class="vu-badge-sm neutral"><i class="fas fa-ban mr-1"></i>Đã hủy</span>
                        @endif
                    </td>
                    <td>
                        @if($v->subscribed_at)
                            <span style="font-size: 12px; color: var(--text-muted);">
                                {{ $v->subscribed_at->format('d/m/Y H:i') }}
                            </span>
                        @else
                            <span style="color: var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($v->unsubscribed_at)
                            <span style="font-size: 12px; color: var(--text-muted);">
                                {{ $v->unsubscribed_at->format('d/m/Y H:i') }}
                            </span>
                        @else
                            <span style="color: var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td>
                        <code style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--text-muted); background: rgba(255,255,255,0.04); padding: 2px 6px; border-radius: 3px;">
                            {{ $v->ip_address ?? '—' }}
                        </code>
                    </td>
                    <td>
                        <div class="action-group" style="justify-content: center;">
                            <a href="{{ url('admin/newsletter/edit/' . $v->RowID) }}" class="action-btn-sm" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ url('admin/newsletter/delete/' . $v->RowID) }}" class="action-btn-sm danger" title="Xóa"
                               onclick="return confirm('Bạn có chắc muốn xóa email này?');">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="7">
                        <div class="empty-state-dark">
                            <div class="empty-icon"><i class="fas fa-envelope-open-text"></i></div>
                            <h3>Chưa có email nào</h3>
                            <p>Chưa có ai đăng ký nhận tin khuyến mãi.</p>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($Newsletter) && $Newsletter->hasPages())
    <div class="pagination-dark">
        <div class="pagination-info-dark">
            Hiển thị {{ $Newsletter->firstItem() ?? 0 }} - {{ $Newsletter->lastItem() ?? 0 }} trong {{ $Newsletter->total() }} emails
        </div>
        <div class="pagination-links-dark">
            @if($Newsletter->onFirstPage())
                <span class="pagination-link disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $Newsletter->previousPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach($Newsletter->getUrlRange(max(1, $Newsletter->currentPage() - 2), min($Newsletter->lastPage(), $Newsletter->currentPage() + 2)) as $page => $url)
                @if($page == $Newsletter->currentPage())
                    <span class="pagination-link active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                @endif
            @endforeach

            @if($Newsletter->hasMorePages())
                <a href="{{ $Newsletter->nextPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="pagination-link disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
