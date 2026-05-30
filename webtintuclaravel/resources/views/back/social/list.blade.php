@extends('back.template.master')

@section('title', 'Quản lý mạng xã hội')
@section('heading', 'Danh sách mạng xã hội')
@section('social', 'active')

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
.vu-badge-sm.neutral { background: rgba(255,255,255,0.06); color: var(--text-secondary); }

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

.social-name-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.social-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--bg-secondary);
    border: 1px solid var(--border-subtle);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
</style>

<!-- Page Header -->
<div class="vu-page-header">
    <h1><i class="fas fa-share-alt mr-2" style="color: var(--accent-gold);"></i>Quản lý mạng xã hội</h1>
</div>

<!-- Table Card -->
<div class="vu-card-dark">
    @if(isset($Social) && count($Social) > 0)
    <div style="overflow-x: auto;">
        <table class="vu-table-dark">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">STT</th>
                    <th>Tên mạng xã hội</th>
                    <th style="width: 120px; text-align: center;">Trạng thái</th>
                    <th style="width: 80px; text-align: center;">Sắp xếp</th>
                    <th style="width: 100px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach($Social as $k => $v)
                <tr>
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">{{ $k + 1 }}</td>
                    <td>
                        <div class="social-name-cell">
                            <div class="social-icon">
                                @if(stripos($v->Name, 'facebook') !== false)
                                    <i class="fab fa-facebook-f" style="color: #1877f2;"></i>
                                @elseif(stripos($v->Name, 'youtube') !== false)
                                    <i class="fab fa-youtube" style="color: #ff0000;"></i>
                                @elseif(stripos($v->Name, 'instagram') !== false)
                                    <i class="fab fa-instagram" style="color: #e4405f;"></i>
                                @elseif(stripos($v->Name, 'twitter') !== false || stripos($v->Name, 'x') !== false)
                                    <i class="fab fa-x-twitter" style="color: #000;"></i>
                                @elseif(stripos($v->Name, 'tiktok') !== false)
                                    <i class="fab fa-tiktok" style="color: #fff;"></i>
                                @else
                                    <i class="fas fa-globe" style="color: var(--text-muted);"></i>
                                @endif
                            </div>
                            <strong style="color: var(--text-primary);">{{ $v->Name }}</strong>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        @if($v->Status == 1)
                            <span class="vu-badge-sm success"><i class="fas fa-check mr-1"></i>Bật</span>
                        @else
                            <span class="vu-badge-sm neutral"><i class="fas fa-times mr-1"></i>Tắt</span>
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: 600;">{{ $v->Sort }}</td>
                    <td>
                        <div class="action-group" style="justify-content: center;">
                            <a href="{{ url('admin/social/edit/' . $v->RowID) }}" class="action-btn-sm" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
        <div style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"><i class="fas fa-share-alt"></i></div>
        <h3 style="font-size: 16px; color: var(--text-primary); margin-bottom: 6px;">Chưa có mạng xã hội nào</h3>
        <p style="font-size: 13px;">Thêm kênh mạng xã hội để hiển thị trên website.</p>
    </div>
    @endif
</div>
@endsection
