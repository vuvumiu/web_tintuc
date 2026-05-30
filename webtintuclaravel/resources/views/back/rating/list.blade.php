@extends('back.template.master')

@section('title', 'Quản lý đánh giá sao')
@section('heading', 'Quản lý đánh giá sao')
@section('rating', 'active')

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
.news-stat-card.gold::before { background: var(--accent-gold); }

.news-stat-value {
    font-size: 32px;
    font-weight: 800;
    line-height: 1;
    font-family: 'JetBrains Mono', monospace;
}

.news-stat-card.blue .news-stat-value { color: var(--accent-blue); }
.news-stat-card.gold .news-stat-value { color: var(--accent-gold); }

.news-stat-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    margin-top: 6px;
}

.news-stat-sub {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 2px;
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

.bulk-bar-dark {
    display: none;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    background: rgba(201, 168, 76, 0.08);
    border: 1px solid rgba(201, 168, 76, 0.3);
    border-radius: var(--radius-md);
    margin-bottom: 12px;
    font-size: 13px;
    color: var(--accent-gold);
}

.bulk-bar-dark.visible {
    display: flex;
}

.bulk-bar-dark .vu-select {
    height: 32px;
    padding: 0 10px;
    font-size: 12px;
    min-width: 140px;
}

.bulk-bar-dark .btn-bulk {
    height: 32px;
    padding: 0 14px;
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
    border: none;
    cursor: pointer;
}

.bulk-bar-dark .btn-bulk.execute {
    background: var(--accent-gold);
    color: #000;
}

.bulk-bar-dark .btn-bulk.execute:disabled {
    opacity: 0.4;
    cursor: not-allowed;
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

.vu-checkbox {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: var(--accent-gold);
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

.star-display {
    display: flex;
    align-items: center;
    gap: 6px;
}

.stars {
    color: var(--accent-gold);
    font-size: 14px;
    letter-spacing: -1px;
}

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

<!-- Page Header -->
<div class="vu-page-header">
    <h1><i class="fas fa-star mr-2" style="color: var(--accent-gold);"></i>Quản lý đánh giá sao</h1>
</div>

<!-- Stats Cards -->
@if(isset($stats) && $stats['total'] > 0)
<div class="vu-news-stats">
    <div class="news-stat-card blue">
        <div class="news-stat-value">{{ $stats['total'] }}</div>
        <div class="news-stat-label">Tổng đánh giá</div>
    </div>
    <div class="news-stat-card gold">
        <div class="news-stat-value">{{ $stats['avg'] }}/5</div>
        <div class="news-stat-label">Điểm TB</div>
    </div>
    @foreach($stats['byScore'] as $item)
    <div class="news-stat-card gold">
        <div class="news-stat-value">{{ $item->score }}<span style="font-size: 16px;">★</span></div>
        <div class="news-stat-sub">{{ $item->total }} đánh giá</div>
    </div>
    @endforeach
</div>
@endif

<!-- Table Card -->
<div class="vu-card-dark">
    <!-- Filters -->
    <div class="vu-filters-dark">
        <form method="GET" action="{{ url('admin/rating/list') }}" class="vu-filter-row">
            <div class="vu-filter-group search">
                <label>Tìm kiếm</label>
                <input type="text" name="keyword" class="vu-input" placeholder="Username, email, họ tên..." value="{{ request('keyword') }}">
            </div>
            <div class="vu-filter-group">
                <label>Bài viết</label>
                <select name="news_id" class="vu-select">
                    <option value="">-- Tất cả bài viết --</option>
                    @if(isset($news) && count($news) > 0)
                        @foreach($news as $n)
                            <option value="{{ $n->RowID }}" {{ request('news_id') == $n->RowID ? 'selected' : '' }}>
                                {{ $n->Name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="vu-filter-group">
                <label>Số sao</label>
                <select name="score" class="vu-select">
                    <option value="">-- Tất cả --</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('score') == $i ? 'selected' : '' }}>
                            {{ $i }} sao
                        </option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i> Lọc
            </button>
            <a href="{{ url('admin/rating/list') }}" class="btn-outline" style="height: 36px;">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>
    </div>

    <!-- Bulk Action Bar -->
    <div style="padding: 12px 20px; border-bottom: 1px solid var(--border-subtle);">
        <form id="bulk-form" method="POST" action="{{ url('admin/rating/bulk-delete') }}">
            @csrf
            <input type="hidden" name="ids" id="bulk-ids" value="">
            <div class="bulk-bar-dark" id="bulk-bar">
                <i class="fas fa-check-square"></i>
                <strong id="bulk-count">0</strong> đánh giá được chọn
                <button type="button" class="btn-bulk execute" onclick="submitBulk()" id="bulk-submit-btn" disabled>
                    <i class="fas fa-trash"></i> Xóa đã chọn
                </button>
            </div>
        </form>
    </div>

    @if(isset($ratings) && count($ratings) > 0)
    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="vu-table-dark">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">
                        <input type="checkbox" id="checkAll" onclick="toggleAll(this)" class="vu-checkbox">
                    </th>
                    <th style="width: 50px; text-align: center;">#</th>
                    <th>Người dùng</th>
                    <th>Bài viết</th>
                    <th style="width: 140px; text-align: center;">Số sao</th>
                    <th style="width: 150px; text-align: center;">Ngày đánh giá</th>
                    <th style="width: 80px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach($ratings as $r)
                <tr>
                    <td style="text-align: center;">
                        <input type="checkbox" class="rate-check vu-checkbox" value="{{ $r->id }}" onclick="updateBulkBar()">
                    </td>
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">
                        {{ $loop->index + 1 + ($ratings->currentPage() - 1) * $ratings->perPage() }}
                    </td>
                    <td>
                        <span class="vu-badge-sm neutral">{{ $r->user->username ?? 'N/A' }}</span>
                    </td>
                    <td>
                        @php $alias = trim((string) ($r->news->Alias ?? '')); @endphp
                        <a href="{{ $alias !== '' ? url($alias.'.html') : '#' }}"
                           target="_blank"
                           style="color: var(--accent-gold); font-size: 12px; text-decoration: none; font-weight: 600;"
                           @if($alias === '') onclick="return false;" @endif>
                            {{ Str::limit($r->news->Name ?? 'N/A', 50) }}
                        </a>
                    </td>
                    <td>
                        <div class="star-display" style="justify-content: center;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star" style="font-size: 13px; color: {{ $i <= ($r->score ?? 0) ? 'var(--accent-gold)' : 'var(--bg-tertiary)' }};"></i>
                            @endfor
                            <span class="vu-badge-sm warning" style="margin-left: 4px;">{{ $r->score ?? 0 }}</span>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <span style="font-size: 12px; color: var(--text-muted);">
                            @if($r->created_at)
                                {{ $r->created_at instanceof \Carbon\Carbon ? $r->created_at->format('d/m/Y H:i') : $r->created_at }}
                            @else
                                —
                            @endif
                        </span>
                    </td>
                    <td>
                        <div class="action-group" style="justify-content: center;">
                            <form action="{{ url('admin/rating/delete/'.$r->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Xóa đánh giá này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn-sm danger" title="Xóa">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($ratings->hasPages())
    <div class="pagination-dark">
        <div class="pagination-info-dark">
            Hiển thị {{ $ratings->firstItem() ?? 0 }} - {{ $ratings->lastItem() ?? 0 }} trong {{ $ratings->total() }} đánh giá
        </div>
        <div class="pagination-links-dark">
            @if($ratings->onFirstPage())
                <span class="pagination-link disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $ratings->previousPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach($ratings->getUrlRange(max(1, $ratings->currentPage() - 2), min($ratings->lastPage(), $ratings->currentPage() + 2)) as $page => $url)
                @if($page == $ratings->currentPage())
                    <span class="pagination-link active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                @endif
            @endforeach

            @if($ratings->hasMorePages())
                <a href="{{ $ratings->nextPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="pagination-link disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
    @else
    <div style="overflow-x: auto;">
        <table class="vu-table-dark">
            <tbody>
                <tr>
                    <td colspan="7">
                        <div class="empty-state-dark">
                            <div class="empty-icon"><i class="fas fa-star"></i></div>
                            <h3>Chưa có đánh giá nào</h3>
                            <p>Bài viết chưa nhận được đánh giá sao nào.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
function toggleAll(cb) {
    document.querySelectorAll('.rate-check').forEach(function(el) { el.checked = cb.checked; });
    updateBulkBar();
}

function updateBulkBar() {
    var checked = Array.from(document.querySelectorAll('.rate-check:checked'));
    var count = checked.length;
    document.getElementById('bulk-count').textContent = count;
    document.getElementById('bulk-ids').value = checked.map(function(el) { return el.value; }).join(',');
    document.getElementById('bulk-bar').style.display = count > 0 ? 'flex' : 'none';
    document.getElementById('bulk-submit-btn').disabled = count === 0;
}

function submitBulk() {
    var count = parseInt(document.getElementById('bulk-count').textContent);
    if (!count) { alert('Vui lòng chọn ít nhất một đánh giá.'); return; }
    if (confirm('XÓA ' + count + ' đánh giá đã chọn? Hành động này không thể hoàn tác.')) {
        document.getElementById('bulk-form').submit();
    }
}
document.addEventListener('DOMContentLoaded', updateBulkBar);
</script>
@endsection
