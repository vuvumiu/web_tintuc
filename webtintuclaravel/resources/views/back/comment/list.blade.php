@extends('back.template.master')

@section('title', 'Quản lý bình luận')
@section('heading', 'Quản lý bình luận')
@section('comment', 'active')

@section('content')
<style>
.vu-page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.vu-page-header h1 { font-size: 20px; font-weight: 700; margin: 0; }

.vu-card-dark { background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); overflow: hidden; }
.vu-card-header { background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--border-subtle); padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
.vu-card-title { font-size: 15px; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
.vu-card-title i { color: var(--accent-gold); }

.btn-gold { background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); color: #000; border: none; border-radius: var(--radius-md); font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; text-decoration: none; transition: all 0.15s; }
.btn-gold:hover { box-shadow: var(--shadow-gold); transform: translateY(-1px); color: #000; }

.btn-outline-sm { background: var(--bg-card); color: var(--text-secondary); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; text-decoration: none; transition: all 0.15s; }
.btn-outline-sm:hover { border-color: var(--accent-gold); color: var(--accent-gold); }
.btn-outline-sm.active { background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); color: #000; border-color: var(--accent-gold); }

.vu-filter-row { display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap; padding: 16px 20px; background: rgba(0,0,0,0.2); }
.vu-filter-group { display: flex; flex-direction: column; gap: 6px; min-width: 150px; }
.vu-filter-group label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
.vu-filter-group .vu-input,
.vu-filter-group .vu-select { height: 36px; padding: 0 12px; font-size: 13px; background: var(--bg-input); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); color: var(--text-primary); transition: all 0.15s; }
.vu-filter-group .vu-input:focus,
.vu-filter-group .vu-select:focus { outline: none; border-color: var(--accent-gold); box-shadow: 0 0 0 3px rgba(201,168,76,0.15); }
.vu-filter-group.search { flex: 1; min-width: 200px; }

.btn-search { height: 36px; padding: 0 16px; background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); color: #000; border: none; border-radius: var(--radius-md); font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; }
.btn-search:hover { box-shadow: var(--shadow-gold); }

.bulk-bar-dark { display: none; align-items: center; gap: 12px; padding: 10px 16px; background: rgba(201,168,76,0.08); border: 1px solid rgba(201,168,76,0.3); border-radius: var(--radius-md); margin: 0 20px 12px; font-size: 13px; color: var(--accent-gold); }
.bulk-bar-dark.visible { display: flex; }
.bulk-bar-dark .vu-select { height: 32px; padding: 0 10px; font-size: 12px; min-width: 140px; }
.bulk-bar-dark .btn-bulk { height: 32px; padding: 0 14px; border-radius: var(--radius-sm); font-size: 12px; font-weight: 600; border: none; cursor: pointer; }
.bulk-bar-dark .btn-bulk.execute { background: var(--accent-gold); color: #000; }
.bulk-bar-dark .btn-bulk.execute:disabled { opacity: 0.4; cursor: not-allowed; }

.vu-table-dark { width: 100%; border-collapse: collapse; }
.vu-table-dark thead { background: rgba(0,0,0,0.3); }
.vu-table-dark th { padding: 11px 14px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); text-align: left; border-bottom: 1px solid var(--border-subtle); white-space: nowrap; }
.vu-table-dark td { padding: 12px 14px; font-size: 13px; color: var(--text-secondary); border-bottom: 1px solid var(--border-subtle); vertical-align: middle; }
.vu-table-dark tbody tr { transition: background 0.15s; }
.vu-table-dark tbody tr:hover { background: rgba(255,255,255,0.02); }
.vu-table-dark tbody tr:last-child td { border-bottom: 1px solid var(--border-subtle); }
.vu-table-dark tbody tr.is-reply { opacity: 0.85; }

.vu-checkbox { width: 16px; height: 16px; cursor: pointer; accent-color: var(--accent-gold); }

.vu-badge-sm { display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
.vu-badge-sm.success { background: var(--status-success-bg); color: var(--status-success); }
.vu-badge-sm.warning { background: var(--status-warning-bg); color: var(--status-warning); }
.vu-badge-sm.danger { background: var(--status-danger-bg); color: var(--status-danger); }
.vu-badge-sm.info { background: var(--status-info-bg); color: var(--status-info); }
.vu-badge-sm.neutral { background: rgba(255,255,255,0.06); color: var(--text-secondary); }
.vu-badge-sm.gold { background: rgba(201,168,76,0.12); color: var(--accent-gold); }

.cmt-user-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; }

.action-group { display: flex; gap: 4px; }
.action-btn-sm { width: 28px; height: 28px; border-radius: var(--radius-sm); background: var(--bg-secondary); border: 1px solid var(--border-subtle); color: var(--text-secondary); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; font-size: 11px; text-decoration: none; }
.action-btn-sm:hover { color: var(--accent-gold); border-color: rgba(201,168,76,0.4); }
.action-btn-sm.danger:hover { color: var(--status-danger); border-color: rgba(239,68,68,0.4); }

.pagination-dark { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 12px; }
.pagination-info-dark { font-size: 12px; color: var(--text-muted); }
.pagination-links-dark { display: flex; gap: 4px; }
.pagination-link { min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; font-weight: 500; color: var(--text-secondary); background: var(--bg-secondary); border: 1px solid var(--border-subtle); cursor: pointer; transition: all 0.15s; text-decoration: none; padding: 0 8px; }
.pagination-link:hover { background: var(--bg-card-hover); color: var(--text-primary); }
.pagination-link.active { background: var(--accent-gold); color: #000; border-color: var(--accent-gold); }
.pagination-link.disabled { opacity: 0.3; cursor: not-allowed; pointer-events: none; }

.empty-state-dark { text-align: center; padding: 60px 20px; }
.empty-state-dark .empty-icon { width: 64px; height: 64px; border-radius: 50%; background: var(--bg-secondary); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: var(--text-muted); font-size: 24px; }
.empty-state-dark h3 { font-size: 16px; color: var(--text-primary); margin-bottom: 6px; }
.empty-state-dark p { font-size: 13px; color: var(--text-muted); }

@media (max-width: 767px) { .vu-filter-row { flex-direction: column; } .vu-filter-group { width: 100%; } .action-group { flex-wrap: wrap; } }
</style>

<div class="vu-page-header">
    <h1><i class="fas fa-comments mr-2" style="color: var(--accent-gold);"></i>Quản lý bình luận</h1>
    <div class="d-flex gap-2">
        <a href="{{ url('admin/comment/list?type=root') }}" class="btn-outline-sm {{ request('type') == 'root' ? 'active' : '' }}">Bình luận gốc</a>
        <a href="{{ url('admin/comment/list?type=reply') }}" class="btn-outline-sm {{ request('type') == 'reply' ? 'active' : '' }}">Phản hồi</a>
        <a href="{{ url('admin/comment/list') }}" class="btn-outline-sm {{ !request('type') ? 'active' : '' }}">Tất cả</a>
    </div>
</div>

<div class="vu-card-dark">
    <!-- Filter -->
    <form method="GET" action="{{ url('admin/comment/list') }}">
        @if(request('type'))<input type="hidden" name="type" value="{{ request('type') }}">@endif
        <div class="vu-filter-row">
            <div class="vu-filter-group search">
                <label>Tìm kiếm</label>
                <input type="text" name="keyword" class="vu-input" placeholder="Nhập nội dung bình luận..." value="{{ request('keyword') }}">
            </div>
            <div class="vu-filter-group">
                <label>Bài viết</label>
                <select name="news_id" class="vu-select">
                    <option value="">-- Tất cả --</option>
                    @if(isset($news) && count($news) > 0)
                        @foreach($news as $n)
                            <option value="{{ $n->RowID }}" {{ request('news_id') == $n->RowID ? 'selected' : '' }}>{{ $n->Name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <button type="submit" class="btn-search"><i class="fas fa-search"></i> Lọc</button>
            <a href="{{ url('admin/comment/list') }}" class="action-btn-sm" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-md);" title="Reset"><i class="fas fa-redo"></i></a>
        </div>
    </form>

    @if(isset($comments) && count($comments) > 0)
    <!-- Bulk Bar -->
    <form id="bulk-form" method="POST" action="{{ url('admin/comment/bulk-action') }}">
        @csrf
        <input type="hidden" name="ids" id="bulk-ids" value="">
        <div class="bulk-bar-dark" id="bulk-bar">
            <i class="fas fa-check-square"></i>
            <strong id="bulk-count">0</strong> bình luận được chọn
            <select name="action" id="bulk-action-select" class="vu-select">
                <option value="">-- Chọn thao tác --</option>
                <option value="show">Hiển thị</option>
                <option value="hide">Ẩn</option>
                <option value="delete">Xóa</option>
            </select>
            <button type="button" class="btn-bulk execute" onclick="submitBulk()" id="bulk-submit-btn" disabled><i class="fas fa-play"></i> Thực hiện</button>
        </div>
    </form>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table class="vu-table-dark">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;"><input type="checkbox" id="checkAll" onclick="toggleAll(this)" class="vu-checkbox"></th>
                    <th style="width: 40px; text-align: center;">#</th>
                    <th style="width: 110px;">Người dùng</th>
                    <th style="width: 100px;">Loại</th>
                    <th>Bài viết</th>
                    <th style="width: 200px;">Nội dung</th>
                    <th style="width: 90px; text-align: center;">Trạng thái</th>
                    <th style="width: 100px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach($comments as $c)
                @php $isReply = !is_null($c->parent_id); @endphp
                <tr class="{{ $isReply ? 'is-reply' : '' }}">
                    <td style="text-align: center;"><input type="checkbox" class="cmt-check vu-checkbox" value="{{ $c->id }}" onclick="updateBulkBar()"></td>
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">{{ $loop->index + 1 + ($comments->currentPage() - 1) * $comments->perPage() }}</td>
                    <td><span class="vu-badge-sm neutral">{{ $c->user?->username ?? 'N/A' }}</span></td>
                    <td>
                        @if($isReply)
                            <span class="vu-badge-sm warning"><i class="fas fa-reply mr-1"></i>Phản hồi</span>
                            @if($c->parent && $c->parent->user)
                                <br><small style="font-size: 10px; color: var(--text-muted);">→ {{ Str::limit($c->parent->user->username, 12) }}</small>
                            @endif
                        @else
                            <span class="vu-badge-sm gold"><i class="fas fa-comment mr-1"></i>Gốc</span>
                        @endif
                    </td>
                    <td>
                        @if($c->news)
                            @php $alias = trim((string) $c->news->Alias); @endphp
                            <a href="{{ $alias !== '' ? url($alias.'.html') : '#' }}" target="_blank" style="color: var(--accent-gold); font-size: 12px; text-decoration: none;" @if($alias === '') onclick="return false;" @endif>
                                {{ Str::limit($c->news->Name ?? 'N/A', 40) }}
                            </a>
                        @else
                            <span style="color: var(--text-muted); font-size: 12px;"><i class="fas fa-unlink"></i> Bài đã xóa @if($c->news_id)#{{ $c->news_id }}@endif</span>
                        @endif
                    </td>
                    <td>
                        <span style="color: var(--text-secondary); font-size: 12px;">{{ Str::limit($c->content, 80) }}</span>
                        <br><small style="color: var(--text-muted); font-size: 10px;"><i class="far fa-clock mr-1"></i>{{ $c->created_at->format('d/m/Y H:i') }}</small>
                    </td>
                    <td style="text-align: center;">
                        @if($c->is_active)
                            <span class="vu-badge-sm success"><i class="fas fa-check mr-1"></i>Hiển thị</span>
                        @else
                            <span class="vu-badge-sm neutral"><i class="fas fa-eye-slash mr-1"></i>Ẩn</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-group" style="justify-content: center;">
                            <button type="button" class="action-btn-sm" title="Kiểm duyệt AI" onclick="showAIModModal(); window.checkAIComment({{ $c->id }}, this.closest('tr'));"
                                style="background: rgba(155,89,182,0.15); border-color: rgba(155,89,182,0.3); color: #9b59b6;"
                                data-comment-id="{{ $c->id }}">
                                <i class="fas fa-robot"></i>
                            </button>
                            <form method="POST" action="{{ url('admin/comment/toggle/'.$c->id) }}" class="d-inline" onsubmit="return confirm('Cập nhật trạng thái?');">
                                @csrf
                                <input type="hidden" name="is_active" value="{{ $c->is_active ? 0 : 1 }}">
                                <button type="submit" class="action-btn-sm" title="{{ $c->is_active ? 'Ẩn' : 'Hiện' }}"
                                        style="background: {{ $c->is_active ? 'rgba(108,117,125,0.2)' : 'rgba(40,167,69,0.2)' }}; border-color: {{ $c->is_active ? 'rgba(108,117,125,0.3)' : 'rgba(40,167,69,0.3)' }}; color: {{ $c->is_active ? 'var(--text-muted)' : 'var(--status-success)' }};">
                                    <i class="fas {{ $c->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            <form action="{{ url('admin/comment/delete/'.$c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa bình luận?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn-sm danger" title="Xóa"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-dark">
        <div class="pagination-info-dark">Hiển thị {{ $comments->firstItem() ?? 0 }} - {{ $comments->lastItem() ?? 0 }} trong {{ $comments->total() }}</div>
        <div class="pagination-links-dark">
            @if($comments->onFirstPage())
                <span class="pagination-link disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $comments->previousPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-left"></i></a>
            @endif
            @foreach($comments->getUrlRange(max(1, $comments->currentPage()-2), min($comments->lastPage(), $comments->currentPage()+2)) as $page => $url)
                @if($page == $comments->currentPage())
                    <span class="pagination-link active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                @endif
            @endforeach
            @if($comments->hasMorePages())
                <a href="{{ $comments->nextPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="pagination-link disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @else
    <div class="empty-state-dark">
        <div class="empty-icon"><i class="fas fa-comment-dots"></i></div>
        <h3>Chưa có bình luận nào</h3>
        <p>Danh sách bình luận sẽ xuất hiện khi có người dùng bình luận trên website.</p>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
function toggleAll(cb) {
    document.querySelectorAll('.cmt-check').forEach(function(el) { el.checked = cb.checked; });
    updateBulkBar();
}

function updateBulkBar() {
    var checked = Array.from(document.querySelectorAll('.cmt-check:checked'));
    var count = checked.length;
    document.getElementById('bulk-count').textContent = count;
    document.getElementById('bulk-ids').value = checked.map(function(el) { return el.value; }).join(',');
    var bulkBar = document.getElementById('bulk-bar');
    if (count > 0) { bulkBar.classList.add('visible'); } else { bulkBar.classList.remove('visible'); }
    document.getElementById('bulk-submit-btn').disabled = count === 0 || document.getElementById('bulk-action-select').value === '';
}

document.getElementById('bulk-action-select').addEventListener('change', function() {
    document.getElementById('bulk-submit-btn').disabled = this.value === '' || document.getElementById('bulk-ids').value === '';
});

function submitBulk() {
    var action = document.getElementById('bulk-action-select').value;
    var count = parseInt(document.getElementById('bulk-count').textContent);
    if (!action) { alert('Vui lòng chọn thao tác.'); return; }
    if (!count) { alert('Vui lòng chọn ít nhất một bình luận.'); return; }
    var confirmMsg = {
        'delete': 'XÓA ' + count + ' bình luận? Hành động không thể hoàn tác.',
        'show': 'Hiển thị ' + count + ' bình luận?',
        'hide': 'Ẩn ' + count + ' bình luận?',
    };
    if (confirm(confirmMsg[action])) {
        document.getElementById('bulk-form').submit();
    }
}

document.addEventListener('DOMContentLoaded', updateBulkBar);
</script>
@endsection
