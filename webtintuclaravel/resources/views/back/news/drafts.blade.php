@extends('back.template.master')

@section('title', ($isGlobalDrafts ?? false) ? 'Bài nháp' : 'Bài nháp của tôi')
@section('heading', ($isGlobalDrafts ?? false) ? 'Bài nháp' : 'Bài nháp của tôi')
@section('news', 'active')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Bài viết nháp
                @if(isset($drafts))
                    <small class="text-muted ml-2">({{ number_format($drafts->total()) }} bài)</small>
                @endif
            </h3>
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
                        <th>Danh mục</th>
                        <th>Tác giả</th>
                        <th>Trạng thái</th>
                        <th>Cập nhật lần cuối</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($drafts) && count($drafts) > 0)
                    @foreach($drafts as $k => $d)
                    @php
                        $status = $d->status ?? 'draft';
                        $updatedAt = $d->schedule_updated_at ?? $d->news_updated_at ?? $d->news_created_at;
                    @endphp
                    <tr>
                        <td>{{ ($drafts->currentPage() - 1) * $drafts->perPage() + $loop->iteration }}</td>
                        <td>
                            <a href="{{ url('admin/news/edit/' . $d->news_id) }}">
                                {{ $d->Name ?? '—' }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-light">{{ $d->CategoryName ?? '—' }}</span>
                        </td>
                        <td>{{ $d->AuthorName ?? '—' }}</td>
                        <td>
                            @if($status === 'draft')
                                <span class="badge badge-secondary">Nháp</span>
                            @elseif($status === 'rejected')
                                <span class="badge badge-danger">Từ chối
                                    @if($d->reject_reason)
                                        <i class="fas fa-info-circle" title="{{ $d->reject_reason }}"></i>
                                    @endif
                                </span>
                            @else
                                <span class="badge badge-secondary">Nháp</span>
                            @endif
                        </td>
                        <td>{{ $updatedAt ? \Carbon\Carbon::parse($updatedAt)->format('d/m/Y H:i') : '—' }}</td>
                        <td class="text-center">
                            <a href="{{ url('admin/news/edit/' . $d->news_id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <a href="{{ url('admin/news/preview/' . $d->news_id) }}" class="btn btn-sm btn-info" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($status === 'draft' || $status === 'rejected')
                                <a href="{{ url('admin/news/submit-review/' . $d->news_id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-paper-plane"></i> Gửi duyệt
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-file-alt fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">Bạn chưa có bài nháp nào.</p>
                            <a href="{{ url('admin/news/add') }}" class="btn btn-primary btn-sm mt-2">Viết bài mới</a>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
            @if(isset($drafts) && method_exists($drafts, 'links') && $drafts->hasPages())
                <div class="d-flex justify-content-between align-items-center flex-wrap mt-3">
                    <div class="text-muted small">
                        Hiển thị {{ $drafts->firstItem() ?? 0 }} - {{ $drafts->lastItem() ?? 0 }} trong {{ $drafts->total() }} bài nháp
                    </div>
                    <div>
                        {{ $drafts->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@stop
