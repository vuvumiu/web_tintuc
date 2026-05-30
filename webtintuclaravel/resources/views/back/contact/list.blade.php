@extends('back.template.master')

@section('title', 'Quản lý liên hệ')

@section('heading', 'Quản lý liên hệ')
@section('contact', 'active')

@section('content')
<style>
.vu-page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.vu-page-header h1 { font-size: 20px; font-weight: 700; margin: 0; }

.stats-contact-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 20px; }
.stat-contact-card { background: var(--bg-tertiary); border: 1px solid var(--border-subtle); border-radius: 12px; padding: 18px; cursor: pointer; transition: border-color 0.2s ease, transform 0.15s ease; position: relative; overflow: hidden; }
.stat-contact-card:hover { border-color: rgba(201,168,76,0.3); transform: translateY(-1px); }
.stat-contact-card::before { content: ""; position: absolute; top: 0; right: 0; width: 80px; height: 80px; border-radius: 50%; opacity: 0.04; transform: translate(20px, -20px); }
.stat-contact-card.total::before { background: #fff; }
.stat-contact-card.new::before { background: var(--accent-red); }
.stat-contact-card.read::before { background: var(--accent-blue); }
.stat-contact-card.replied::before { background: var(--accent-green); }
.stat-contact-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px; margin-bottom: 14px; }
.stat-contact-card.total .stat-contact-icon { background: rgba(201,168,76,0.14); color: var(--accent-gold); }
.stat-contact-card.new .stat-contact-icon { background: rgba(229,115,115,0.14); color: var(--accent-red); }
.stat-contact-card.read .stat-contact-icon { background: rgba(73,158,255,0.14); color: var(--accent-blue); }
.stat-contact-card.replied .stat-contact-icon { background: rgba(92,185,123,0.14); color: var(--accent-green); }
.stat-contact-value { font-size: 30px; font-weight: 700; color: #fff; letter-spacing: -0.5px; margin-bottom: 4px; }
.stat-contact-label { font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; }
.stat-contact-bar { height: 3px; background: rgba(255,255,255,0.07); border-radius: 2px; margin-top: 14px; overflow: hidden; }
.stat-contact-bar-inner { height: 100%; border-radius: 2px; }
.stat-contact-card.total .stat-contact-bar-inner { background: linear-gradient(90deg, var(--accent-gold), var(--accent-gold-light)); }
.stat-contact-card.new .stat-contact-bar-inner { background: linear-gradient(90deg, #e05555, #f08080); }
.stat-contact-card.read .stat-contact-bar-inner { background: linear-gradient(90deg, #4a9eff, #7bc4ff); }
.stat-contact-card.replied .stat-contact-bar-inner { background: linear-gradient(90deg, #4eb87a, #7dd4a4); }

.vu-card-dark { background: var(--bg-card, var(--bg-tertiary)); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); overflow: hidden; }
.vu-card-header { background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--border-subtle); padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
.vu-card-title { font-size: 15px; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
.vu-card-title i { color: var(--accent-gold); }

.vu-filter-row { display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap; padding: 16px 20px; background: rgba(0,0,0,0.2); }
.vu-filter-group { display: flex; flex-direction: column; gap: 6px; min-width: 150px; }
.vu-filter-group label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
.vu-filter-group .vu-input,
.vu-filter-group .vu-select { height: 36px; padding: 0 12px; font-size: 13px; background: var(--bg-input, rgba(255,255,255,0.04)); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); color: var(--text-primary); transition: all 0.15s; }
.vu-filter-group .vu-input:focus,
.vu-filter-group .vu-select:focus { outline: none; border-color: rgba(201,168,76,0.45); box-shadow: 0 0 0 3px rgba(201,168,76,0.15); }
.vu-filter-group.search { flex: 1; min-width: 200px; }

.btn-search { height: 36px; padding: 0 16px; background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); color: #000; border: none; border-radius: var(--radius-md); font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.15s; }
.btn-search:hover { box-shadow: var(--shadow-gold); }

.btn-outline-sm { background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; text-decoration: none; transition: all 0.15s; }
.btn-outline-sm:hover { border-color: var(--accent-gold); color: var(--accent-gold); }
.btn-outline-sm.active { background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); color: #000; border-color: var(--accent-gold); }

.vu-table-dark { width: 100%; border-collapse: collapse; }
.vu-table-dark thead { background: rgba(0,0,0,0.3); }
.vu-table-dark th { padding: 11px 14px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); text-align: left; border-bottom: 1px solid var(--border-subtle); white-space: nowrap; }
.vu-table-dark td { padding: 12px 14px; font-size: 13px; color: var(--text-secondary); border-bottom: 1px solid var(--border-subtle); vertical-align: middle; }
.vu-table-dark tbody tr { transition: background 0.15s; }
.vu-table-dark tbody tr:hover { background: rgba(255,255,255,0.02); }
.vu-table-dark tbody tr:last-child td { border-bottom: 1px solid var(--border-subtle); }
.vu-table-dark tbody tr.row-new { background: rgba(229,115,115,0.04); }
.vu-table-dark tbody tr.row-new:hover { background: rgba(229,115,115,0.07); }

.vu-checkbox { width: 16px; height: 16px; cursor: pointer; accent-color: var(--accent-gold); }

.vu-badge-sm { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 999px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
.vu-badge-sm.success { background: rgba(92,185,123,0.14); color: var(--status-success); }
.vu-badge-sm.warning { background: rgba(201,168,76,0.14); color: var(--status-warning); }
.vu-badge-sm.danger { background: rgba(229,115,115,0.14); color: var(--status-danger); }
.vu-badge-sm.info { background: rgba(73,158,255,0.14); color: var(--status-info); }
.vu-badge-sm.neutral { background: rgba(255,255,255,0.06); color: var(--text-secondary); }
.vu-badge-sm.gold { background: rgba(201,168,76,0.12); color: var(--accent-gold); }
.vu-badge-sm.purple { background: rgba(180,80,220,0.14); color: var(--accent-purple); }
.vu-badge-sm.cyan { background: rgba(80,200,180,0.14); color: #50c8b4; }
.vu-badge-sm.orange { background: rgba(220,140,80,0.14); color: #dc8c50; }

.action-group { display: flex; gap: 4px; }
.action-btn-sm { width: 30px; height: 30px; border-radius: var(--radius-sm); background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); color: var(--text-secondary); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; font-size: 11px; text-decoration: none; }
.action-btn-sm:hover { color: var(--accent-gold); border-color: rgba(201,168,76,0.4); }
.action-btn-sm.danger:hover { color: var(--status-danger); border-color: rgba(229,115,115,0.4); }
.action-btn-sm.reply:hover { color: var(--status-success); border-color: rgba(92,185,123,0.4); }

.contact-avatar { width: 36px; height: 36px; border-radius: 50%; background: rgba(201,168,76,0.12); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: var(--accent-gold); flex-shrink: 0; }

.pagination-dark { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 12px; }
.pagination-info-dark { font-size: 12px; color: var(--text-muted); }
.pagination-links-dark { display: flex; gap: 4px; }
.pagination-link { min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm); font-size: 12px; font-weight: 500; color: var(--text-secondary); background: var(--bg-tertiary); border: 1px solid var(--border-subtle); cursor: pointer; transition: all 0.15s; text-decoration: none; padding: 0 8px; }
.pagination-link:hover { background: rgba(255,255,255,0.06); color: var(--text-primary); }
.pagination-link.active { background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); color: #000; border-color: var(--accent-gold); }
.pagination-link.disabled { opacity: 0.3; cursor: not-allowed; pointer-events: none; }

.empty-state-dark { text-align: center; padding: 60px 20px; }
.empty-state-dark .empty-icon { width: 64px; height: 64px; border-radius: 50%; background: rgba(201,168,76,0.08); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: var(--accent-gold); font-size: 24px; }
.empty-state-dark h3 { font-size: 16px; color: var(--text-primary); margin-bottom: 6px; }
.empty-state-dark p { font-size: 13px; color: var(--text-muted); }

.tab-filter-group { display: flex; gap: 4px; padding: 14px 20px 0; }
.tab-filter-btn { padding: 7px 16px; border-radius: var(--radius-md); font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; text-decoration: none; background: transparent; border: 1px solid transparent; color: var(--text-muted); transition: all 0.15s; }
.tab-filter-btn:hover { color: var(--text-primary); border-color: var(--border-subtle); }
.tab-filter-btn.active { background: rgba(201,168,76,0.12); color: var(--accent-gold); border-color: rgba(201,168,76,0.25); }
.tab-filter-btn .count-badge { background: rgba(255,255,255,0.08); padding: 1px 7px; border-radius: 10px; font-size: 10px; }
.tab-filter-btn.active .count-badge { background: rgba(201,168,76,0.2); color: var(--accent-gold); }

@media (max-width: 767px) {
    .stats-contact-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .vu-filter-row { flex-direction: column; }
    .vu-filter-group { width: 100%; }
    .action-group { flex-wrap: wrap; }
    .tab-filter-group { overflow-x: auto; }
}
@media (max-width: 480px) {
    .stats-contact-grid { grid-template-columns: 1fr; }
}
</style>

<div class="vu-page-header">
    <h1><i class="fas fa-envelope-open-text mr-2" style="color: var(--accent-gold);"></i>Quản lý liên hệ</h1>
</div>

{{-- Stats Cards --}}
<div class="stats-contact-grid">
    <div class="stat-contact-card total" onclick="window.location.href='{{ url('admin/contact/list') }}'" title="Tất cả liên hệ">
        <div class="stat-contact-icon"><i class="fas fa-inbox"></i></div>
        <div class="stat-contact-value">{{ $stats['total'] }}</div>
        <div class="stat-contact-label">Tổng liên hệ</div>
        <div class="stat-contact-bar"><div class="stat-contact-bar-inner" style="width: 100%;"></div></div>
    </div>
    <div class="stat-contact-card new" onclick="window.location.href='{{ url('admin/contact/list?status=new') }}'" title="Liên hệ mới chưa xem">
        <div class="stat-contact-icon"><i class="fas fa-bell"></i></div>
        <div class="stat-contact-value">{{ $stats['new'] }}</div>
        <div class="stat-contact-label">Liên hệ mới</div>
        <div class="stat-contact-bar"><div class="stat-contact-bar-inner" style="width: {{ $stats['total'] > 0 ? round($stats['new'] / $stats['total'] * 100) : 0 }}%;"></div></div>
    </div>
    <div class="stat-contact-card read" onclick="window.location.href='{{ url('admin/contact/list?status=read') }}'" title="Đã xem">
        <div class="stat-contact-icon"><i class="fas fa-eye"></i></div>
        <div class="stat-contact-value">{{ $stats['read'] }}</div>
        <div class="stat-contact-label">Đã xem</div>
        <div class="stat-contact-bar"><div class="stat-contact-bar-inner" style="width: {{ $stats['total'] > 0 ? round($stats['read'] / $stats['total'] * 100) : 0 }}%;"></div></div>
    </div>
    <div class="stat-contact-card replied" onclick="window.location.href='{{ url('admin/contact/list?status=replied') }}'" title="Đã phản hồi">
        <div class="stat-contact-icon"><i class="fas fa-check-double"></i></div>
        <div class="stat-contact-value">{{ $stats['replied'] }}</div>
        <div class="stat-contact-label">Đã phản hồi</div>
        <div class="stat-contact-bar"><div class="stat-contact-bar-inner" style="width: {{ $stats['total'] > 0 ? round($stats['replied'] / $stats['total'] * 100) : 0 }}%;"></div></div>
    </div>
</div>

<div class="vu-card-dark">
    {{-- Tab Filter --}}
    <div class="tab-filter-group">
        <a href="{{ url('admin/contact/list') }}" class="tab-filter-btn {{ !request('status') ? 'active' : '' }}">
            Tất cả <span class="count-badge">{{ $stats['total'] }}</span>
        </a>
        <a href="{{ url('admin/contact/list?status=new') }}" class="tab-filter-btn {{ request('status') == 'new' ? 'active' : '' }}">
            <i class="fas fa-bell" style="font-size: 10px;"></i> Mới <span class="count-badge">{{ $stats['new'] }}</span>
        </a>
        <a href="{{ url('admin/contact/list?status=read') }}" class="tab-filter-btn {{ request('status') == 'read' ? 'active' : '' }}">
            <i class="fas fa-eye" style="font-size: 10px;"></i> Đã xem <span class="count-badge">{{ $stats['read'] }}</span>
        </a>
        <a href="{{ url('admin/contact/list?status=replied') }}" class="tab-filter-btn {{ request('status') == 'replied' ? 'active' : '' }}">
            <i class="fas fa-reply" style="font-size: 10px;"></i> Đã phản hồi <span class="count-badge">{{ $stats['replied'] }}</span>
        </a>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ url('admin/contact/list') }}">
        @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        <div class="vu-filter-row">
            <div class="vu-filter-group search">
                <label>Tìm kiếm</label>
                <input type="text" name="keyword" class="vu-input" placeholder="Tên, email, SĐT, tiêu đề..." value="{{ request('keyword') }}">
            </div>
            <div class="vu-filter-group">
                <label>Phân loại</label>
                <select name="category" class="vu-select">
                    <option value="">-- Tất cả --</option>
                    @foreach($categoryLabels as $key => $label)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="vu-filter-group">
                <label>Ưu tiên</label>
                <select name="priority" class="vu-select">
                    <option value="">-- Tất cả --</option>
                    @foreach($priorityLabels as $key => $label)
                        <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-search"><i class="fas fa-search"></i> Lọc</button>
            <a href="{{ url('admin/contact/list') }}" class="action-btn-sm" title="Reset lọc" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-redo"></i></a>
        </div>
    </form>

    {{-- Table --}}
    @if(isset($Contact) && count($Contact) > 0)
    <div style="overflow-x: auto;">
        <table class="vu-table-dark">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">#</th>
                    <th style="min-width: 180px;">Người liên hệ</th>
                    <th style="width: 120px;">Phân loại</th>
                    <th style="width: 100px;">Ưu tiên</th>
                    <th style="width: 130px;">Trạng thái</th>
                    <th style="width: 120px; text-align: center;">Ngày gửi</th>
                    <th style="width: 110px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach($Contact as $v)
                <tr class="{{ !$v->is_reviewed ? 'row-new' : '' }}">
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">{{ $loop->index + 1 + ($Contact->currentPage() - 1) * $Contact->perPage() }}</td>
                    <td>
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <div class="contact-avatar">
                                {{ strtoupper(substr($v->Name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                    <strong style="color: var(--text-primary); font-size: 13px;">{{ $v->Name }}</strong>
                                    @if(!$v->is_reviewed)
                                        <span class="vu-badge-sm danger"><i class="fas fa-bell" style="font-size: 8px;"></i> Mới</span>
                                    @endif
                                </div>
                                <div style="font-size: 12px; color: var(--accent-gold); margin-top: 3px;">{{ $v->Email }}</div>
                                @if($v->Phone)
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;"><i class="fas fa-phone" style="font-size: 9px;"></i> {{ $v->Phone }}</div>
                                @endif
                                @if($v->subject)
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 3px; font-style: italic;">{{ Str::limit($v->subject, 45) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @if(isset($categoryLabels[$v->category]))
                            <span class="vu-badge-sm {{ $categoryColors[$v->category] ?? 'neutral' }}">{{ $categoryLabels[$v->category] }}</span>
                        @else
                            <span class="vu-badge-sm neutral">{{ $v->category ?? '-' }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="vu-badge-sm {{ $priorityColors[$v->priority] ?? 'neutral' }}">{{ $priorityLabels[$v->priority] ?? 'Trung bình' }}</span>
                    </td>
                    <td>
                        @if($v->replied_at)
                            <span class="vu-badge-sm success"><i class="fas fa-check" style="font-size: 9px;"></i> Đã phản hồi</span>
                            <br><small style="font-size: 10px; color: var(--text-muted);">{{ $v->replied_at->format('d/m/Y H:i') }}</small>
                        @elseif($v->is_reviewed)
                            <span class="vu-badge-sm info"><i class="fas fa-eye" style="font-size: 9px;"></i> Đã xem</span>
                        @else
                            <span class="vu-badge-sm danger"><i class="fas fa-bell" style="font-size: 9px;"></i> Chưa xem</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span style="font-size: 12px; color: var(--text-muted);">{{ $v->created_at->format('d/m/Y') }}</span>
                        <br><small style="font-size: 10px; color: var(--text-muted);">{{ $v->created_at->format('H:i') }}</small>
                    </td>
                    <td>
                        <div class="action-group" style="justify-content: center;">
                            @if(!$v->is_reviewed)
                                <a href="{{ url('admin/contact/mark-read/' . $v->RowID) }}" class="action-btn-sm" title="Đánh dấu đã xem"
                                   onclick="return confirm('Đánh dấu là đã xem?')" style="background: rgba(73,158,255,0.1); border-color: rgba(73,158,255,0.2); color: var(--accent-blue);">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                            @if(!$v->replied_at)
                                <a href="{{ url('admin/contact/edit/' . $v->RowID) }}#reply-form" class="action-btn-sm reply" title="Phản hồi">
                                    <i class="fas fa-paper-plane"></i>
                                </a>
                            @endif
                            <a href="{{ url('admin/contact/edit/' . $v->RowID) }}" class="action-btn-sm" title="Chi tiết">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ url('admin/contact/delete/' . $v->RowID) }}" class="action-btn-sm danger" title="Xóa"
                               onclick="return confirm('Bạn có chắc muốn xóa liên hệ này?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-dark">
        <div class="pagination-info-dark">Hiển thị {{ $Contact->firstItem() ?? 0 }} - {{ $Contact->lastItem() ?? 0 }} trong {{ $Contact->total() }} liên hệ</div>
        <div class="pagination-links-dark">
            @if($Contact->onFirstPage())
                <span class="pagination-link disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $Contact->previousPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-left"></i></a>
            @endif
            @foreach($Contact->getUrlRange(max(1, $Contact->currentPage()-2), min($Contact->lastPage(), $Contact->currentPage()+2)) as $page => $url)
                @if($page == $Contact->currentPage())
                    <span class="pagination-link active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
                @endif
            @endforeach
            @if($Contact->hasMorePages())
                <a href="{{ $Contact->nextPageUrl() }}" class="pagination-link"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="pagination-link disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @else
    <div class="empty-state-dark">
        <div class="empty-icon"><i class="fas fa-envelope-open"></i></div>
        <h3>Chưa có liên hệ nào</h3>
        <p>Danh sách liên hệ sẽ xuất hiện khi có khách hàng gửi yêu cầu liên hệ qua website.</p>
    </div>
    @endif
</div>
@stop
