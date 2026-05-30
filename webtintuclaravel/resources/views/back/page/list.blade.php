@extends('back.template.master')

@section('title', 'Quản lý trang')
@section('heading', 'Danh sách trang')
@section('page', 'active')

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

.action-btn-sm.info:hover {
    color: var(--accent-blue);
    border-color: rgba(74, 158, 255, 0.4);
}

.page-slug {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    color: var(--text-muted);
    background: rgba(255, 255, 255, 0.04);
    padding: 2px 6px;
    border-radius: 3px;
}

.page-hint {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 12px 16px;
    background: rgba(201, 168, 76, 0.06);
    border: 1px solid rgba(201, 168, 76, 0.15);
    border-radius: var(--radius-md);
    margin-bottom: 20px;
    font-size: 12px;
    color: var(--text-muted);
}

.page-hint i {
    color: var(--accent-gold);
    margin-top: 2px;
    flex-shrink: 0;
}

.page-hint strong {
    color: var(--text-primary);
}
</style>

<!-- Page Header -->
<div class="vu-page-header">
    <h1><i class="fas fa-file-alt mr-2" style="color: var(--accent-gold);"></i>Quản lý trang</h1>
</div>

<!-- Hint -->
<div class="page-hint">
    <i class="fas fa-info-circle"></i>
    <div>
        <strong>Menu website</strong> lấy từ đây: chỉ các trang <strong style="color: var(--accent-gold);">Bật</strong>, sắp xếp theo <strong>Trường sắp xếp</strong>.
        Chọn <strong>Loại menu</strong> khi sửa: Trang chủ, Trang tĩnh, Dropdown Tin tức (danh mục), hoặc Route có sẵn (vd. <code>tin-moi-nhat</code>).
    </div>
</div>

<!-- Table Card -->
<div class="vu-card-dark">
    @if(isset($page) && count($page) > 0)
    <div style="overflow-x: auto;">
        <table class="vu-table-dark">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">STT</th>
                    <th>Tên trang</th>
                    <th style="width: 150px;">Loại menu</th>
                    <th>Đường dẫn (Alias)</th>
                    <th style="width: 100px; text-align: center;">Trạng thái</th>
                    <th style="width: 80px; text-align: center;">Sắp xếp</th>
                    <th style="width: 80px; text-align: center;">Web</th>
                    <th style="width: 80px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @foreach($page as $k => $v)
                @php
                    $mk = $v->menu_kind ?? \App\Models\Page::MENU_LINK;
                    $kindLabels = \App\Models\Page::menuKindLabels();
                    $kindLabel = $kindLabels[$mk] ?? $mk;
                    if ($mk === \App\Models\Page::MENU_NEWS_CATEGORIES) {
                        $publicUrl = null;
                    } elseif ($v->Alias === '/' || $v->Alias === '') {
                        $publicUrl = url('/');
                    } else {
                        $publicUrl = url('/' . ltrim($v->Alias, '/'));
                    }
                @endphp
                <tr>
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">{{ $k + 1 }}</td>
                    <td>
                        <strong style="color: var(--text-primary);">{{ $v->Name }}</strong>
                    </td>
                    <td>
                        <span style="font-size: 11px; color: var(--text-muted);">{{ $kindLabel }}</span>
                    </td>
                    <td>
                        <code class="page-slug">{{ $v->Alias }}</code>
                    </td>
                    <td style="text-align: center;">
                        @if($v->Status == 1)
                            <span class="vu-badge-sm success"><i class="fas fa-check mr-1"></i>Bật</span>
                        @else
                            <span class="vu-badge-sm neutral"><i class="fas fa-times mr-1"></i>Tắt</span>
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: 600;">{{ $v->Sort }}</td>
                    <td style="text-align: center;">
                        @if($publicUrl)
                            <a href="{{ $publicUrl }}" target="_blank" rel="noopener noreferrer"
                               class="action-btn-sm info" title="Mở trên website"
                               style="width: 28px; height: 28px; font-size: 11px;">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        @else
                            <span style="color: var(--text-muted); font-size: 12px;" title="Dropdown menu">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-group" style="justify-content: center;">
                            <a href="{{ url('admin/page/edit/' . $v->RowID) }}" class="action-btn-sm" title="Chỉnh sửa">
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
        <div style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"><i class="fas fa-file-alt"></i></div>
        <h3 style="font-size: 16px; color: var(--text-primary); margin-bottom: 6px;">Chưa có trang nào</h3>
        <p style="font-size: 13px;">Tạo trang tĩnh đầu tiên cho website.</p>
    </div>
    @endif
</div>
@endsection
