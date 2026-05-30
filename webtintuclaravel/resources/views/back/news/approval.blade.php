@extends('back.template.master')

@section('title', 'Duyệt bài viết')
@section('heading', 'Hàng đợi duyệt bài')
@section('news', 'active')

@section('content')
<div class="col-md-12">
    {{-- Stats cards --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['pending'] }}</h3>
                    <p>Chờ duyệt</p>
                </div>
                <div class="icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['scheduled'] }}</h3>
                    <p>Hẹn giờ</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $stats['draft'] }}</h3>
                    <p>Nháp</p>
                </div>
                <div class="icon"><i class="fas fa-file"></i></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách bài chờ duyệt</h3>
        </div>
        <div class="card-body">
            @if(session('flash_message'))
                <div class="alert alert-{{ session('flash_level') }}">{{ session('flash_message') }}</div>
            @endif

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tiêu đề bài viết</th>
                        <th>Tác giả</th>
                        <th>Trạng thái</th>
                        <th>Ngày gửi</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($schedules) && count($schedules) > 0)
                    @foreach($schedules as $k => $s)
                    <tr>
                        <td>{{ $k + 1 }}</td>
                        <td>
                            <a href="{{ url('admin/news/edit/' . $s->news_id) }}" target="_blank">
                                {{ $s->news->Name ?? '—' }}
                            </a>
                            @if($s->status === 'scheduled' && $s->scheduled_at)
                                <br><small class="text-info">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Hẹn: {{ $s->scheduled_at->format('d/m/Y H:i') }}
                                </small>
                            @endif
                        </td>
                        <td>{{ $s->creator->fullname ?? $s->creator->username ?? '—' }}</td>
                        <td>
                            @if($s->status === 'pending')
                                <span class="badge badge-warning">Chờ duyệt</span>
                            @elseif($s->status === 'scheduled')
                                <span class="badge badge-info">Hẹn giờ</span>
                            @else
                                <span class="badge badge-secondary">{{ $s->status }}</span>
                            @endif
                        </td>
                        <td>{{ $s->updated_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <a href="{{ url('admin/news/preview/' . $s->news_id) }}" class="btn btn-sm btn-info" target="_blank" title="Xem trước">
                                <i class="fas fa-eye"></i>
                            </a>

                            @if($s->status === 'pending' || $s->status === 'scheduled')
                                <a href="{{ url('admin/news-approval/approve/' . $s->id) }}"
                                   class="btn btn-sm btn-success" title="Duyệt"
                                   onclick="return confirm('Duyệt bài viết này?');">
                                    <i class="fas fa-check"></i> Duyệt
                                </a>

                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                        data-target="#rejectModal{{ $s->id }}" title="Từ chối">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </td>
                    </tr>

                    {{-- Reject Modal --}}
                    @if($s->status === 'pending')
                    <tr>
                        <td colspan="6" class="p-0 border-0">
                            <div class="modal fade" id="rejectModal{{ $s->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <h5 class="modal-title">Từ chối bài viết</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form action="{{ url('admin/news-approval/reject/' . $s->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <p>Bạn sắp từ chối bài: <strong>{{ $s->news->Name ?? '' }}</strong></p>
                                                <div class="form-group">
                                                    <label>Lý do từ chối <span class="text-danger">*</span></label>
                                                    <textarea name="reject_reason" class="form-control" rows="3"
                                                              placeholder="Nhập lý do từ chối bài viết..." required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-paper-plane mr-1"></i> Gửi từ chối
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                    @else
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">Không có bài viết nào trong hàng đợi duyệt.</p>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
