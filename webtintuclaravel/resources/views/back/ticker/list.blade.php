@extends('back.template.master')
@section('title', 'Quản lý Tin nóng')
@section('heading', 'Danh sách Tin nóng')
@section('ticker', 'active')

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
    padding: 11px 14px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    text-align: left;
    border-bottom: 1px solid var(--border-subtle);
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

.empty-state-dark a {
    color: var(--accent-gold);
    font-weight: 600;
}
</style>

<!-- Page Header -->
<div class="vu-page-header">
    <h1><i class="fas fa-bolt mr-2" style="color: var(--accent-gold);"></i>Quản lý Tin nóng</h1>
    <a href="{{ url('admin/ticker/add') }}" class="btn-gold">
        <i class="fas fa-plus"></i> Thêm tin nóng
    </a>
</div>

<!-- Table Card -->
<div class="vu-card-dark">
    @if($Ticker->count() > 0)
    <div style="overflow-x: auto;">
        <table class="vu-table-dark">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">STT</th>
                    <th>Tiêu đề</th>
                    <th>Bài viết liên kết</th>
                    <th style="width: 120px; text-align: center;">Trạng thái</th>
                    <th style="width: 80px; text-align: center;">Sắp xếp</th>
                    <th style="width: 120px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach($Ticker as $k => $v)
                <tr>
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">{{ $k + 1 }}</td>
                    <td>
                        <strong style="color: var(--text-primary);">{{ $v->title ?? '—' }}</strong>
                    </td>
                    <td>
                        @if($v->news)
                            <a href="{{ url($v->news->Alias . '.html') }}" target="_blank" style="color: var(--accent-gold); font-weight: 600; text-decoration: none; font-size: 13px;">
                                {{ $v->news->Name }}
                            </a>
                        @else
                            <span style="color: var(--text-muted); font-style: italic;">Tùy chỉnh</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="javascript:void(0)" onclick="toggleTicker({{ $v->RowID }})" id="ticker-status-{{ $v->RowID }}">
                            @if($v->Status == 1)
                                <span class="vu-badge-sm success"><i class="fas fa-check mr-1"></i>Bật</span>
                            @else
                                <span class="vu-badge-sm neutral"><i class="fas fa-times mr-1"></i>Tắt</span>
                            @endif
                        </a>
                    </td>
                    <td style="text-align: center; font-weight: 600;">{{ $v->Sort }}</td>
                    <td>
                        <div class="action-group" style="justify-content: center;">
                            <a href="{{ url('admin/ticker/edit/' . $v->RowID) }}" class="action-btn-sm" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ url('admin/ticker/delete/' . $v->RowID) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn-sm danger" title="Xóa"
                                        onclick="return confirm('Bạn có chắc muốn xóa tin nóng này?');">
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

    @if($Ticker->hasPages())
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 12px;">
        <div style="font-size: 12px; color: var(--text-muted);">
            Trang {{ $Ticker->currentPage() }} / {{ $Ticker->lastPage() }}
        </div>
        <div style="display: flex; gap: 4px;">
            @if($Ticker->onFirstPage())
                <span style="min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; color: var(--text-secondary); background: var(--bg-secondary); border: 1px solid var(--border-subtle); opacity: 0.3; cursor: not-allowed;"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $Ticker->previousPageUrl() }}" style="min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; color: var(--text-secondary); background: var(--bg-secondary); border: 1px solid var(--border-subtle); text-decoration: none;"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach($Ticker->getUrlRange(max(1, $Ticker->currentPage() - 2), min($Ticker->lastPage(), $Ticker->currentPage() + 2)) as $page => $url)
                @if($page == $Ticker->currentPage())
                    <span style="min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; background: var(--accent-gold); color: #000; border: 1px solid var(--accent-gold); font-weight: 600;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; color: var(--text-secondary); background: var(--bg-secondary); border: 1px solid var(--border-subtle); text-decoration: none;">{{ $page }}</a>
                @endif
            @endforeach

            @if($Ticker->hasMorePages())
                <a href="{{ $Ticker->nextPageUrl() }}" style="min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; color: var(--text-secondary); background: var(--bg-secondary); border: 1px solid var(--border-subtle); text-decoration: none;"><i class="fas fa-chevron-right"></i></a>
            @else
                <span style="min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; color: var(--text-secondary); background: var(--bg-secondary); border: 1px solid var(--border-subtle); opacity: 0.3; cursor: not-allowed;"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
    @else
    <div class="empty-state-dark">
        <div class="empty-icon"><i class="fas fa-bolt"></i></div>
        <h3>Chưa có tin nóng nào</h3>
        <p>Chưa có tin nóng nào. <a href="{{ url('admin/ticker/add') }}">Thêm ngay</a></p>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
function toggleTicker(id) {
    $.ajax({
        url: '{{ url("admin/ticker/toggle") }}/' + id,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(res) {
            if (res.success) {
                var badge = $('#ticker-status-' + id);
                if (res.status === 1) {
                    badge.html('<span class="vu-badge-sm success"><i class="fas fa-check mr-1"></i>Bật</span>');
                } else {
                    badge.html('<span class="vu-badge-sm neutral"><i class="fas fa-times mr-1"></i>Tắt</span>');
                }
            }
        },
        error: function() {
            alert('Có lỗi xảy ra!');
        }
    });
}
</script>
@endsection
