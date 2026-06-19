@extends('back.template.master')
@section('title', 'Quản lý Bài viết nổi bật')
@section('heading', 'Bài viết nổi bật - Hero Section')
@section('featured', 'active')

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

.vu-card-dark {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 20px;
}

.vu-section-header {
    padding: 14px 20px;
    background: rgba(0, 0, 0, 0.3);
    border-bottom: 1px solid var(--border-subtle);
    display: flex;
    align-items: center;
    gap: 10px;
}

.vu-section-title {
    font-size: 14px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}

.vu-section-subtitle {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 400;
    margin-left: auto;
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
    <h1><i class="fas fa-star mr-2" style="color: var(--accent-gold);"></i>Bài viết nổi bật</h1>
    <a href="{{ url('admin/featured/add') }}" class="btn-gold">
        <i class="fas fa-plus"></i> Thêm bài viết nổi bật
    </a>
</div>

<!-- POSITION 1: Tin chính (Hero lớn) -->
<div class="vu-card-dark">
    <div class="vu-section-header">
        <div class="vu-section-title">
            <i class="fas fa-star" style="color: var(--accent-gold);"></i> Tin chính (Hero lớn)
        </div>
        <div class="vu-section-subtitle">Hiển thị các bài trong carousel lớn ở đầu trang</div>
    </div>
    @if($grouped['main']->count() > 0)
    <div style="overflow-x: auto;">
        <table class="vu-table-dark">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">STT</th>
                    <th>Tiêu đề bài viết</th>
                    <th style="width: 120px; text-align: center;">Trạng thái</th>
                    <th style="width: 80px; text-align: center;">Sắp xếp</th>
                    <th style="width: 120px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach($grouped['main'] as $k => $v)
                <tr>
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">{{ $k + 1 }}</td>
                    <td>
                        <strong style="color: var(--text-primary);">{{ $v->news->Name ?? '—' }}</strong>
                        @if($v->news)
                        <br>
                        <small style="color: var(--text-muted); font-size: 11px;">
                            <a href="{{ url($v->news->Alias . '.html') }}" target="_blank" style="color: var(--accent-gold);">
                                {{ url($v->news->Alias . '.html') }}
                            </a>
                        </small>
                        @if((int) $v->news->Status !== 1)
                        <br>
                        <small style="color: var(--status-warning); font-size: 11px;">
                            <i class="fas fa-exclamation-triangle"></i> Bài viết chưa xuất bản nên không hiện ở trang chính
                        </small>
                        @endif
                        @else
                        <br>
                        <small style="color: var(--status-danger); font-size: 11px;">
                            <i class="fas fa-exclamation-triangle"></i> Bài viết không còn tồn tại
                        </small>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ url('admin/featured/toggle/' . $v->RowID) }}">
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
                            <a href="{{ url('admin/featured/edit/' . $v->RowID) }}" class="action-btn-sm" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ url('admin/featured/delete/' . $v->RowID) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn-sm danger" title="Xóa"
                                        onclick="return confirm('Bạn có chắc muốn xóa?');">
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
    @else
    <div class="empty-state-dark">
        <div class="empty-icon"><i class="fas fa-star"></i></div>
        <h3>Chưa có bài viết nào</h3>
        <p>Chưa có bài viết nào cho vị trí này. <a href="{{ url('admin/featured/add?position=1') }}">Thêm ngay</a></p>
    </div>
    @endif
</div>

<!-- POSITION 2: Tin phụ (Sidebar) -->
<div class="vu-card-dark">
    <div class="vu-section-header">
        <div class="vu-section-title">
            <i class="fas fa-list" style="color: var(--text-muted);"></i> Tin phụ (Sidebar)
        </div>
        <div class="vu-section-subtitle">Hiển thị các bài nhỏ bên cạnh tin chính, nhiều bài sẽ cuộn trong khung sidebar</div>
    </div>
    @if($grouped['sidebar']->count() > 0)
    <div style="overflow-x: auto;">
        <table class="vu-table-dark">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">STT</th>
                    <th>Tiêu đề bài viết</th>
                    <th style="width: 120px; text-align: center;">Trạng thái</th>
                    <th style="width: 80px; text-align: center;">Sắp xếp</th>
                    <th style="width: 120px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach($grouped['sidebar'] as $k => $v)
                <tr>
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">{{ $k + 1 }}</td>
                    <td>
                        <strong style="color: var(--text-primary);">{{ $v->news->Name ?? '—' }}</strong>
                        @if($v->news)
                        <br>
                        <small style="color: var(--text-muted); font-size: 11px;">
                            <a href="{{ url($v->news->Alias . '.html') }}" target="_blank" style="color: var(--accent-gold);">
                                {{ url($v->news->Alias . '.html') }}
                            </a>
                        </small>
                        @if((int) $v->news->Status !== 1)
                        <br>
                        <small style="color: var(--status-warning); font-size: 11px;">
                            <i class="fas fa-exclamation-triangle"></i> Bài viết chưa xuất bản nên không hiện ở trang chính
                        </small>
                        @endif
                        @else
                        <br>
                        <small style="color: var(--status-danger); font-size: 11px;">
                            <i class="fas fa-exclamation-triangle"></i> Bài viết không còn tồn tại
                        </small>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ url('admin/featured/toggle/' . $v->RowID) }}">
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
                            <a href="{{ url('admin/featured/edit/' . $v->RowID) }}" class="action-btn-sm" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ url('admin/featured/delete/' . $v->RowID) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn-sm danger" title="Xóa"
                                        onclick="return confirm('Bạn có chắc muốn xóa?');">
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
    @else
    <div class="empty-state-dark">
        <div class="empty-icon"><i class="fas fa-list"></i></div>
        <h3>Chưa có bài viết nào</h3>
        <p>Chưa có bài viết nào cho vị trí này. <a href="{{ url('admin/featured/add?position=2') }}">Thêm ngay</a></p>
    </div>
    @endif
</div>
@endsection
