@extends('back.template.master')

@section('title', 'Quản lý tin tức')
@section('news', 'active')

@section('heading', 'Quản lý tin tức')

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
}

.btn-outline:hover {
    border-color: var(--accent-gold);
    color: var(--text-primary);
}

.vu-news-stats {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.news-stat-chip {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-xl);
    font-size: 13px;
    color: var(--text-secondary);
}

.news-stat-chip .count {
    font-weight: 700;
    color: var(--text-primary);
}

.news-stat-chip.gold {
    border-color: rgba(201, 168, 76, 0.3);
    background: rgba(201, 168, 76, 0.08);
}

.news-stat-chip.gold .count {
    color: var(--accent-gold);
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

.vu-filter-group.search .vu-input {
    width: 100%;
}

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
    border-bottom: 1px solid var(--border-subtle);
}

.vu-table-dark .news-title-cell {
    max-width: 280px;
}

.vu-table-dark .news-title-cell a {
    color: var(--text-primary);
    font-weight: 600;
    font-size: 13px;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.vu-table-dark .news-title-cell a:hover {
    color: var(--accent-gold);
}

.vu-table-dark .news-excerpt {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.vu-table-dark .vu-checkbox {
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

.action-btn-sm.success:hover {
    color: var(--status-success);
    border-color: rgba(34, 197, 94, 0.4);
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
    background: var(--bg-card-hover);
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

.author-chip-sm {
    display: flex;
    align-items: center;
    gap: 6px;
}

.author-avatar-sm {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light));
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000;
    font-weight: 700;
    font-size: 10px;
    flex-shrink: 0;
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

.vu-card-dark {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    overflow: hidden;
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
    <h1><i class="fas fa-newspaper mr-2" style="color: var(--accent-gold);"></i>Quản lý tin tức</h1>
    <div class="d-flex gap-2">
        <a href="{{ url('admin/news/add') }}" class="btn btn-gold">
            <i class="fas fa-plus"></i> Thêm bài viết
        </a>
        <a href="{{ url('admin/news-approval/queue') }}" class="btn btn-outline">
            <i class="fas fa-check-circle"></i> Duyệt bài
        </a>
    </div>
</div>

<!-- Stats Chips -->
<div class="vu-news-stats">
    <div class="news-stat-chip gold">
        <i class="fas fa-file-alt"></i>
        Tổng: <span class="count">{{ number_format($stats['news_total'] ?? 0) }}</span>
    </div>
    <div class="news-stat-chip">
        <i class="fas fa-eye" style="color: var(--status-success);"></i>
        Xuất bản: <span class="count">{{ number_format($stats['news_published'] ?? 0) }}</span>
    </div>
    <div class="news-stat-chip">
        <i class="fas fa-save" style="color: var(--status-warning);"></i>
        Nháp: <span class="count">{{ number_format($stats['news_draft'] ?? 0) }}</span>
    </div>
    <div class="news-stat-chip">
        <i class="fas fa-clock" style="color: var(--status-info);"></i>
        Chờ duyệt: <span class="count">{{ number_format($stats['news_pending'] ?? 0) }}</span>
    </div>
</div>

<!-- Table Card -->
<div class="vu-card-dark">
    <!-- Filters -->
    <div class="vu-filters-dark">
        <form method="GET" action="{{ url('admin/news/list') }}" class="w-full">
            <div class="vu-filter-row">
                <div class="vu-filter-group search">
                    <label>Tìm kiếm</label>
                    <input type="text" name="keyword" class="vu-input" placeholder="Tìm tiêu đề..." value="{{ request('keyword') }}">
                </div>
                <div class="vu-filter-group">
                    <label>Danh mục</label>
                    <select name="cat" class="vu-select">
                        <option value="">-- Tất cả --</option>
                        @if(isset($NewsCategory))
                            @foreach($NewsCategory as $v)
                                <option value="{{ $v->RowID }}" {{ request('cat') == $v->RowID ? 'selected' : '' }}>{{ $v->Name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="vu-filter-group">
                    <label>Tác giả</label>
                    <select name="author" class="vu-select">
                        <option value="">-- Tất cả --</option>
                        @if(isset($authors))
                            @foreach($authors as $a)
                                <option value="{{ $a->id }}" {{ request('author') == $a->id ? 'selected' : '' }}>
                                    {{ $a->fullname ?? $a->username }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="vu-filter-group">
                    <label>Trạng thái</label>
                    <select name="status" class="vu-select">
                        <option value="">-- Tất cả --</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Xuất bản</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nháp</option>
                    </select>
                </div>
                <div class="vu-filter-group">
                    <label>Workflow</label>
                    <select name="schedule_status" class="vu-select">
                        <option value="">-- Tất cả --</option>
                        <option value="draft" {{ request('schedule_status') === 'draft' ? 'selected' : '' }}>Nháp</option>
                        <option value="pending" {{ request('schedule_status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('schedule_status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="scheduled" {{ request('schedule_status') === 'scheduled' ? 'selected' : '' }}>Hẹn giờ</option>
                    </select>
                </div>
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> Tìm
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Action Bar -->
    <div style="padding: 12px 20px; border-bottom: 1px solid var(--border-subtle);">
        <form id="bulk-form" method="POST" action="{{ url('admin/news/bulk-action') }}">
            @csrf
            <input type="hidden" name="ids" id="bulk-ids" value="">
            <div class="bulk-bar-dark" id="bulk-bar">
                <i class="fas fa-check-square"></i>
                <strong id="bulk-count">0</strong> bài viết được chọn
                <select name="action" id="bulk-action-select" class="vu-select">
                    <option value="">-- Chọn thao tác --</option>
                    <option value="submit_review">Gửi duyệt</option>
                    @if(Auth::user()->isAdmin())
                        <option value="show">Hiển thị</option>
                        <option value="hide">Ẩn</option>
                        <option value="delete">Xóa</option>
                    @endif
                </select>
                <button type="button" class="btn-bulk execute" onclick="submitBulk()" id="bulk-submit-btn" disabled>
                    <i class="fas fa-play"></i> Thực hiện
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="vu-table-dark">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">
                        <input type="checkbox" id="checkAll" onclick="toggleAll(this)" class="vu-checkbox">
                    </th>
                    <th style="width: 40px; text-align: center;">#</th>
                    <th>Tiêu đề</th>
                    <th style="width: 130px;">Danh mục</th>
                    <th style="width: 130px;">Tác giả</th>
                    <th style="width: 70px; text-align: center;">Tags</th>
                    <th style="width: 100px;">Hiển thị</th>
                    <th style="width: 100px;">Workflow</th>
                    <th style="width: 120px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($News) && count($News) > 0)
                @foreach($News as $k => $v)
                <tr>
                    <td style="text-align: center;">
                        <input type="checkbox" class="news-check vu-checkbox" value="{{ $v->RowID }}" onclick="updateBulkBar()">
                    </td>
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">
                        {{ ($News->currentPage() - 1) * $News->perPage() + $loop->iteration }}
                    </td>
                    <td class="news-title-cell">
                        <a href="{{ url('admin/news/edit/' . $v->RowID) }}" title="{{ $v->Name }}">
                            {{ $v->Name }}
                        </a>
                        @if($v->SmallDescription)
                            <div class="news-excerpt">{{ Str::limit($v->SmallDescription, 55) }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="vu-badge-sm neutral">{{ $v->CategoryName ?? '—' }}</span>
                    </td>
                    <td>
                        <div class="author-chip-sm">
                            <div class="author-avatar-sm">{{ strtoupper(substr($v->AuthorName ?? 'U', 0, 1)) }}</div>
                            <span>{{ $v->AuthorName ?? '—' }}</span>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        @php
                            $tagCount = DB::table('news_tags')->where('news_id', $v->RowID)->count();
                        @endphp
                        <span class="vu-badge-sm info">{{ $tagCount }}</span>
                    </td>
                    <td>
                        @if($v->Status == 1)
                            <span class="vu-badge-sm success"><i class="fas fa-eye mr-1"></i>Xuất bản</span>
                        @else
                            <span class="vu-badge-sm warning"><i class="fas fa-eye-slash mr-1"></i>Nháp</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusLabels = [
                                'draft'     => ['label' => 'Nháp',       'class' => 'neutral'],
                                'pending'   => ['label' => 'Chờ duyệt',   'class' => 'warning'],
                                'approved'  => ['label' => 'Đã duyệt',    'class' => 'success'],
                                'rejected'  => ['label' => 'Từ chối',    'class' => 'danger'],
                                'scheduled' => ['label' => 'Hẹn giờ',    'class' => 'info'],
                                'published' => ['label' => 'Xuất bản',   'class' => 'success'],
                            ];
                            $s = $v->ScheduleStatus ?? 'draft';
                            $sInfo = $statusLabels[$s] ?? ['label' => $s, 'class' => 'neutral'];
                        @endphp
                        <span class="vu-badge-sm {{ $sInfo['class'] }}">{{ $sInfo['label'] }}</span>
                    </td>
                    <td>
                        <div class="action-group" style="justify-content: center;">
                            <a href="{{ url('admin/news/edit/' . $v->RowID) }}" class="action-btn-sm" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ url('admin/news/preview/' . $v->RowID) }}" class="action-btn-sm" title="Xem trước" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ url('admin/news/duplicate/' . $v->RowID) }}" class="action-btn-sm" title="Sao chép">
                                <i class="fas fa-copy"></i>
                            </a>
                            @if(Auth::user()->hasPermission('news.delete'))
                                <form action="{{ url('admin/news/delete/' . $v->RowID) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa bài viết này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn-sm danger" title="Xóa">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="9">
                        <div class="empty-state-dark">
                            <div class="empty-icon"><i class="fas fa-newspaper"></i></div>
                            <h3>Chưa có bài viết nào</h3>
                            <p>Bắt đầu bằng cách tạo bài viết đầu tiên của bạn.</p>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($News) && method_exists($News, 'links') && $News->hasPages())
    <div class="pagination-dark">
        <div class="pagination-info-dark">
            Hiển thị {{ $News->firstItem() ?? 0 }} - {{ $News->lastItem() ?? 0 }} trong {{ $News->total() }} bài viết
        </div>
        <div class="pagination-links-dark">
            @if($News->onFirstPage())
                <span class="pagination-link disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $News->previousPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach($News->getUrlRange(max(1, $News->currentPage() - 2), min($News->lastPage(), $News->currentPage() + 2)) as $page => $url)
                @if($page == $News->currentPage())
                    <span class="pagination-link active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                @endif
            @endforeach

            @if($News->hasMorePages())
                <a href="{{ $News->nextPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="pagination-link disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
function toggleAll(cb) {
    document.querySelectorAll('.news-check').forEach(function(el) {
        el.checked = cb.checked;
    });
    updateBulkBar();
}

function updateBulkBar() {
    var checked = Array.from(document.querySelectorAll('.news-check:checked'));
    var count = checked.length;
    var ids = checked.map(function(el) { return el.value; });

    document.getElementById('bulk-count').textContent = count;
    document.getElementById('bulk-ids').value = ids.join(',');

    var bulkBar = document.getElementById('bulk-bar');
    var submitBtn = document.getElementById('bulk-submit-btn');

    if (count > 0) {
        bulkBar.classList.add('visible');
    } else {
        bulkBar.classList.remove('visible');
    }

    submitBtn.disabled = count === 0 || document.getElementById('bulk-action-select').value === '';
}

document.getElementById('bulk-action-select').addEventListener('change', function() {
    document.getElementById('bulk-submit-btn').disabled = this.value === '' || document.getElementById('bulk-ids').value === '';
});

function submitBulk() {
    var action = document.getElementById('bulk-action-select').value;
    var count = parseInt(document.getElementById('bulk-count').textContent);

    if (action === '') {
        alert('Vui lòng chọn thao tác.');
        return;
    }
    if (count === 0) {
        alert('Vui lòng chọn ít nhất một bài viết.');
        return;
    }

    var confirmMsg = {
        'delete': 'Bạn chắc chắn muốn XÓA ' + count + ' bài viết đã chọn? Hành động này không thể hoàn tác.',
        'show': 'Hiển thị ' + count + ' bài viết đã chọn?',
        'hide': 'Ẩn ' + count + ' bài viết đã chọn?',
        'submit_review': 'Gửi ' + count + ' bài viết đã chọn để duyệt?',
    };

    if (confirm(confirmMsg[action] || 'Xác nhận thao tác?')) {
        document.getElementById('bulk-form').submit();
    }
}

document.addEventListener('DOMContentLoaded', updateBulkBar);
</script>
@endsection
