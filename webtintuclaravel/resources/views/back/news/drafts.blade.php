@extends('back.template.master')

@section('title', 'Bài nháp của tôi')
@section('heading', 'Bài nháp của tôi')
@section('news', 'active')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Bài viết nháp</h3>
            <a href="{{ url('admin/news/add') }}" class="btn btn-primary btn-sm float-right">
                <i class="fas fa-plus mr-1"></i> Viết bài mới
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tiêu đề</th>
                        <th>Trạng thái</th>
                        <th>Cập nhật lần cuối</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($drafts) && count($drafts) > 0)
                    @foreach($drafts as $k => $d)
                    <tr>
                        <td>{{ $k + 1 }}</td>
                        <td>
                            <a href="{{ url('admin/news/edit/' . $d->news_id) }}">
                                {{ $d->news->Name ?? '—' }}
                            </a>
                        </td>
                        <td>
                            @if($d->status === 'draft')
                                <span class="badge badge-secondary">Nháp</span>
                            @elseif($d->status === 'pending')
                                <span class="badge badge-warning">Chờ duyệt</span>
                            @elseif($d->status === 'rejected')
                                <span class="badge badge-danger">Từ chối
                                    @if($d->reject_reason)
                                        <i class="fas fa-info-circle" title="{{ $d->reject_reason }}"></i>
                                    @endif
                                </span>
                            @else
                                <span class="badge badge-{{ ['approved'=>'success','scheduled'=>'info','published'=>'success'][$d->status] ?? 'secondary' }}">
                                    {{ $d->status }}
                                </span>
                            @endif
                        </td>
                        <td>{{ $d->updated_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <a href="{{ url('admin/news/edit/' . $d->news_id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <a href="{{ url('admin/news/preview/' . $d->news_id) }}" class="btn btn-sm btn-info" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($d->status === 'draft' || $d->status === 'rejected')
                                <a href="{{ url('admin/news/submit-review/' . $d->news_id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-paper-plane"></i> Gửi duyệt
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-file-alt fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">Bạn chưa có bài nháp nào.</p>
                            <a href="{{ url('admin/news/add') }}" class="btn btn-primary btn-sm mt-2">Viết bài mới</a>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
