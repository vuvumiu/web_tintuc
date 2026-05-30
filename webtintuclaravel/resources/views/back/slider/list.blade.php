@extends('back.template.master')

@section('title', 'Quản lý slideshow')
@section('heading', 'Quản lý slideshow')
@section('slider', 'active')

@section('content')
<style>
.vu-page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.vu-page-header h1 { font-size: 20px; font-weight: 700; margin: 0; }

.vu-card-dark { background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); overflow: hidden; }
.vu-card-header { background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--border-subtle); padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; }

.btn-gold { background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); color: #000; border: none; border-radius: var(--radius-md); font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; text-decoration: none; transition: all 0.15s; }
.btn-gold:hover { box-shadow: var(--shadow-gold); transform: translateY(-1px); color: #000; }

.vu-table-dark { width: 100%; border-collapse: collapse; }
.vu-table-dark thead { background: rgba(0,0,0,0.3); }
.vu-table-dark th { padding: 11px 14px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); text-align: left; border-bottom: 1px solid var(--border-subtle); }
.vu-table-dark td { padding: 12px 14px; font-size: 13px; color: var(--text-secondary); border-bottom: 1px solid var(--border-subtle); vertical-align: middle; }
.vu-table-dark tbody tr { transition: background 0.15s; }
.vu-table-dark tbody tr:hover { background: rgba(255,255,255,0.02); }
.vu-table-dark tbody tr:last-child td { border-bottom: 1px solid var(--border-subtle); }

.vu-badge-sm { display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
.vu-badge-sm.success { background: var(--status-success-bg); color: var(--status-success); }
.vu-badge-sm.neutral { background: rgba(255,255,255,0.06); color: var(--text-secondary); }

.slider-img-thumb { width: 100px; height: 50px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle); }

.action-group { display: flex; gap: 4px; }
.action-btn-sm { width: 30px; height: 30px; border-radius: var(--radius-sm); background: var(--bg-secondary); border: 1px solid var(--border-subtle); color: var(--text-secondary); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; font-size: 12px; text-decoration: none; }
.action-btn-sm:hover { color: var(--accent-gold); border-color: rgba(201,168,76,0.4); }
.action-btn-sm.danger:hover { color: var(--status-danger); border-color: rgba(239,68,68,0.4); }
</style>

<div class="vu-page-header">
    <h1><i class="fas fa-images mr-2" style="color: var(--accent-gold);"></i>Quản lý slideshow</h1>
    <a href="{{ url('admin/slider/add') }}" class="btn-gold">
        <i class="fas fa-plus"></i> Thêm
    </a>
</div>

<div class="vu-card-dark">
    <div style="overflow-x: auto;">
        <table class="vu-table-dark">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">#</th>
                    <th>Tên slider</th>
                    <th style="width: 130px;">Ảnh đại diện</th>
                    <th style="width: 100px; text-align: center;">Trạng thái</th>
                    <th style="width: 80px; text-align: center;">Sắp xếp</th>
                    <th style="width: 100px; text-align: center;"><i class="fas fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($Slider) && count($Slider) > 0)
                @foreach($Slider as $k => $v)
                <tr>
                    <td style="text-align: center; color: var(--text-muted); font-size: 12px;">{{ $k + 1 }}</td>
                    <td><strong style="color: var(--text-primary);">{{ $v->Name }}</strong></td>
                    <td>
                        @if($v->Images)
                            <img src="{{ url('images/slider/'. $v->Images) }}" class="slider-img-thumb" alt="{{ $v->Name }}">
                        @else
                            <span style="color: var(--text-muted); font-size: 12px;">—</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($v->Status == 1)
                            <span class="vu-badge-sm success">Bật</span>
                        @else
                            <span class="vu-badge-sm neutral">Tắt</span>
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: 600; color: var(--text-primary);">{{ $v->Sort }}</td>
                    <td>
                        <div class="action-group" style="justify-content: center;">
                            <a href="{{ url('admin/slider/edit/'. $v->RowID) }}" class="action-btn-sm" title="Sửa"><i class="fas fa-edit"></i></a>
                            <form action="{{ url('admin/slider/delete/' . $v->RowID) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa slider này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn-sm danger" title="Xóa"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
