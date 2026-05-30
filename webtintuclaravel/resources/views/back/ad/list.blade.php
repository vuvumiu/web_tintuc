@extends('back.template.master')

@section('title', 'Quản lý popup quảng cáo')
@section('heading', 'Quản lý popup quảng cáo')
@section('ads', 'active')

@section('content')
<style>
    .vu-page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
    .vu-page-header h1 { font-size: 20px; font-weight: 700; margin: 0; }
    .filter-bar { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; align-items: center; }
    .filter-bar input, .filter-bar select { background: var(--bg-secondary); border: 1px solid var(--border-subtle); color: var(--text-primary); padding: 8px 12px; border-radius: var(--radius-md); font-size: 13px; }
    .filter-bar .btn-filter { background: var(--accent-gold); color: #000; border: none; padding: 8px 16px; border-radius: var(--radius-md); font-weight: 600; cursor: pointer; font-size: 13px; text-decoration: none; }
    .vu-card-dark { background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); overflow: hidden; }
    .btn-gold { background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); color: #000; border: none; border-radius: var(--radius-md); font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; text-decoration: none; transition: all 0.15s; }
    .btn-gold:hover { box-shadow: var(--shadow-gold); transform: translateY(-1px); color: #000; }
    .vu-table-dark { width: 100%; border-collapse: collapse; }
    .vu-table-dark thead { background: rgba(0,0,0,0.3); }
    .vu-table-dark th { padding: 11px 14px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); text-align: left; border-bottom: 1px solid var(--border-subtle); }
    .vu-table-dark td { padding: 12px 14px; font-size: 13px; color: var(--text-secondary); border-bottom: 1px solid var(--border-subtle); vertical-align: middle; }
    .vu-table-dark tbody tr:hover { background: rgba(255,255,255,0.02); }
    .vu-badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
    .vu-badge-success { background: var(--status-success-bg); color: var(--status-success); }
    .vu-badge-danger { background: var(--status-danger-bg); color: var(--status-danger); }
    .vu-badge-neutral { background: rgba(255,255,255,0.06); color: var(--text-secondary); }
    .ad-img-thumb { width: 90px; height: 58px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle); background: var(--bg-secondary); display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .ad-img-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .ad-img-placeholder { color: var(--text-muted); font-size: 20px; }
    .action-group { display: flex; gap: 4px; }
    .action-btn-sm { width: 30px; height: 30px; border-radius: var(--radius-sm); background: var(--bg-secondary); border: 1px solid var(--border-subtle); color: var(--text-secondary); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; font-size: 12px; text-decoration: none; }
    .action-btn-sm:hover { color: var(--accent-gold); border-color: rgba(201,168,76,0.4); }
    .action-btn-sm.danger:hover { color: var(--status-danger); border-color: rgba(239,68,68,0.4); }
    .stats-row { display: grid; grid-template-columns: repeat(5, minmax(120px, 1fr)); gap: 12px; margin-bottom: 16px; }
    .stat-card { background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 12px 16px; }
    .stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .stat-value { font-size: 20px; font-weight: 700; color: var(--text-primary); }
    .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
    .empty-state i { font-size: 48px; margin-bottom: 16px; opacity: 0.3; }
    @media (max-width: 900px) { .stats-row { grid-template-columns: repeat(2, minmax(120px, 1fr)); } }
</style>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-label">Tổng popup</div>
        <div class="stat-value">{{ number_format($adStats['total'] ?? 0) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Đang bật</div>
        <div class="stat-value" style="color: var(--status-success);">{{ number_format($adStats['active'] ?? 0) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Đang tắt</div>
        <div class="stat-value">{{ number_format($adStats['inactive'] ?? 0) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Lượt xem</div>
        <div class="stat-value">{{ number_format($adStats['views'] ?? 0) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Lượt click</div>
        <div class="stat-value" style="color: var(--accent-gold);">{{ number_format($adStats['clicks'] ?? 0) }}</div>
    </div>
</div>

<div class="vu-page-header">
    <h1><i class="fas fa-window-restore" style="color: var(--accent-gold);"></i> Quản lý popup quảng cáo</h1>
    <a href="{{ url('admin/ads/add') }}" class="btn-gold">
        <i class="fas fa-plus"></i> Thêm popup
    </a>
</div>

<form method="GET" action="{{ url('admin/ads/list') }}" class="filter-bar">
    <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm theo tên popup..." style="flex: 1; min-width: 220px;">
    <select name="status">
        <option value="">-- Trạng thái --</option>
        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Bật</option>
        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tắt</option>
    </select>
    <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Lọc</button>
    <a href="{{ url('admin/ads/list') }}" class="btn-filter" style="background: var(--bg-secondary); color: var(--text-secondary);"><i class="fas fa-times"></i> Xóa lọc</a>
</form>

<div class="vu-card-dark">
    @if($Ads->count() > 0)
        <div style="overflow-x: auto;">
            <table class="vu-table-dark">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th style="width: 110px;">Ảnh</th>
                        <th>Tên popup</th>
                        <th style="width: 125px;">Nơi hiển thị</th>
                        <th style="width: 110px;">Tần suất</th>
                        <th style="width: 110px;">Trạng thái</th>
                        <th style="width: 90px; text-align: center;">Ưu tiên</th>
                        <th style="width: 120px; text-align: center;">Xem / Click</th>
                        <th style="width: 100px; text-align: center;"><i class="fas fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($Ads as $k => $v)
                        <tr>
                            <td style="text-align: center; color: var(--text-muted); font-size: 12px;">{{ $Ads->firstItem() + $k }}</td>
                            <td>
                                <div class="ad-img-thumb">
                                    @if($v->image)
                                        <img src="{{ url('images/ads/' . $v->image) }}" alt="{{ $v->name }}">
                                    @else
                                        <i class="fas fa-image ad-img-placeholder"></i>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong style="color: var(--text-primary);">{{ $v->name }}</strong>
                                @if($v->link)
                                    <br><small style="color: var(--text-muted);">{{ Str::limit($v->link, 56) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($v->location === 'homepage')
                                    <span class="vu-badge vu-badge-neutral">Trang chủ</span>
                                @elseif($v->location === 'article')
                                    <span class="vu-badge vu-badge-neutral">Bài viết</span>
                                @else
                                    <span class="vu-badge vu-badge-neutral">Tất cả</span>
                                @endif
                            </td>
                            <td style="font-size: 12px;">
                                {{ (int) $v->impression_limit === 0 ? 'Không giới hạn' : ((int) $v->impression_limit . ' lần') }}
                                <br><small style="color: var(--text-muted);">nghỉ {{ (int) $v->cooldown_minutes }} phút</small>
                            </td>
                            <td>
                                @if($v->status)
                                    <span class="vu-badge vu-badge-success">Bật</span>
                                @else
                                    <span class="vu-badge vu-badge-danger">Tắt</span>
                                @endif
                            </td>
                            <td style="text-align: center; font-weight: 700; color: var(--text-primary);">{{ $v->priority }}</td>
                            <td style="text-align: center; font-size: 12px;">
                                <span style="color: var(--text-muted);">{{ number_format($v->view_count) }}</span> /
                                <span style="color: var(--accent-gold);">{{ number_format($v->click_count) }}</span>
                            </td>
                            <td>
                                <div class="action-group" style="justify-content: center;">
                                    <a href="{{ url('admin/ads/edit/' . $v->id) }}" class="action-btn-sm" title="Sửa"><i class="fas fa-edit"></i></a>
                                    <form action="{{ url('admin/ads/delete/' . $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa popup này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn-sm danger" title="Xóa"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding: 16px 20px; border-top: 1px solid var(--border-subtle);">
            {{ $Ads->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-window-restore"></i>
            <p>Chưa có popup quảng cáo nào.</p>
            <p><a href="{{ url('admin/ads/add') }}" class="btn-gold" style="margin-top: 12px;"><i class="fas fa-plus"></i> Thêm popup đầu tiên</a></p>
        </div>
    @endif
</div>
@endsection
