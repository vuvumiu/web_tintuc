@extends('back.template.master')

@section('title', 'Quản lý Tags')
@section('heading', 'Danh sách Tags')
@section('tag', 'active')

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

.tag-slug {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    color: var(--text-muted);
    background: rgba(255, 255, 255, 0.04);
    padding: 2px 6px;
    border-radius: 3px;
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

@media (max-width: 767px) {
    .vu-filter-row { flex-direction: column; }
    .vu-filter-group { width: 100%; }
    .vu-filter-group.search { min-width: unset; }
    .action-group { flex-wrap: wrap; }
}
</style>

<!-- Page Header -->
<div class="vu-page-header">
    <h1><i class="fas fa-tags mr-2" style="color: var(--accent-gold);"></i>Quản lý Tags</h1>
    <a href="{{ url('admin/tag/add') }}" class="btn-gold">
        <i class="fas fa-plus"></i> Thêm Tag
    </a>
</div>

<!-- Table Card -->
<div class="vu-card-dark">
    <!-- Filters -->
    <div class="vu-filters-dark">
        <form method="GET" action="{{ url('admin/tag/list') }}" class="vu-filter-row">
            <div class="vu-filter-group search">
                <label>Tìm kiếm</label>
                <input type="text" name="keyword" class="vu-input" placeholder="Tìm tag..." value="{{ request('keyword') }}">
            </div>
            <div class="vu-filter-group">
                <label>Trạng thái</label>
                <select name="status" class="vu-select">
                    <option value="">-- Tất cả --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hiển thị</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ẩn</option>
                </select>
            </div>
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i> Tìm
            </button>
            <a href="{{ url('admin/tag/list') }}" class="btn-outline" style="height: 36px;">
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
                    <th>Tên Tag</th>
                    <th>Slug</th>
                    <th style="width: 130px; text-align: center;">Lượt sử dụng</th>
                    <th style="width: 120px; text-align: center;">Trạng thái</th>
                    <th style="width: 100px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tags) && count($tags) > 0)
                @foreach($tags as $k => $t)
                <tr>
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">
                        {{ $k + 1 }}
                    </td>
                    <td>
                        <a href="{{ url('tag/' . $t->slug) }}" target="_blank"
                           style="color: var(--text-primary); font-weight: 600; text-decoration: none;">
                            {{ $t->name }}
                        </a>
                    </td>
                    <td>
                        <code class="tag-slug">{{ $t->slug }}</code>
                    </td>
                    <td style="text-align: center;">
                        <span class="vu-badge-sm info">{{ $t->popular_count }}</span>
                    </td>
                    <td style="text-align: center;">
                        @if($t->status == 1)
                            <span class="vu-badge-sm success"><i class="fas fa-eye mr-1"></i>Hiển thị</span>
                        @else
                            <span class="vu-badge-sm neutral"><i class="fas fa-eye-slash mr-1"></i>Ẩn</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-group" style="justify-content: center;">
                            <a href="{{ url('admin/tag/edit/' . $t->id) }}" class="action-btn-sm" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ url('admin/tag/delete/' . $t->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn-sm danger" title="Xóa"
                                        onclick="return confirm('Xóa tag này?');">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="6">
                        <div class="empty-state-dark">
                            <div class="empty-icon"><i class="fas fa-tags"></i></div>
                            <h3>Chưa có tag nào</h3>
                            <p>Tạo tag đầu tiên để phân loại bài viết.</p>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($tags) && method_exists($tags, 'links') && $tags->hasPages())
    <div class="pagination-dark">
        <div class="pagination-info-dark">
            Hiển thị {{ $tags->firstItem() ?? 0 }} - {{ $tags->lastItem() ?? 0 }} trong {{ $tags->total() }} tags
        </div>
        <div class="pagination-links-dark">
            @if($tags->onFirstPage())
                <span class="pagination-link disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $tags->previousPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach($tags->getUrlRange(max(1, $tags->currentPage() - 2), min($tags->lastPage(), $tags->currentPage() + 2)) as $page => $url)
                @if($page == $tags->currentPage())
                    <span class="pagination-link active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                @endif
            @endforeach

            @if($tags->hasMorePages())
                <a href="{{ $tags->nextPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="pagination-link disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
