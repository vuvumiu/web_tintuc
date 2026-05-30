@extends('back.template.master')

@section('title', 'Chi tiết thành viên')

@section('heading', 'Chi tiết thành viên')

@section('member', 'menu-open')

@section('content')
<div class="col-md-12">
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user fa-fw"></i>
                Thông tin thành viên: <strong>{{ $account->username }}</strong>
            </h3>
            <div class="card-tools">
                <a href="{{ url('admin/member/edit/'.$account->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Chỉnh sửa
                </a>
                <a href="{{ url('admin/member/list') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
                <!-- Left: Account Info -->
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 160px;">Tài khoản</th>
                            <td><strong>{{ $account->username }}</strong></td>
                        </tr>
                        <tr>
                            <th>Họ tên</th>
                            <td>{{ $account->fullname ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $account->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Số điện thoại</th>
                            <td>{{ $account->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Địa chỉ</th>
                            <td>{{ $account->address ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>
                                @if($account->is_active == 1)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Hoạt động
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-ban"></i> Bị vô hiệu hóa
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Ngày đăng ký</th>
                            <td>{{ $account->created_at ? date('d/m/Y H:i:s', strtotime($account->created_at)) : '—' }}</td>
                        </tr>
                        <tr>
                            <th>Đăng nhập gần nhất</th>
                            <td>{{ $account->updated_at ? date('d/m/Y H:i:s', strtotime($account->updated_at)) : '—' }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Right: Activity Stats -->
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $commentCount }}</h3>
                                    <p>Bình luận</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-comments"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $ratingCount }}</h3>
                                    <p>Đánh giá sao</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Comments -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-comment-dots fa-fw"></i>
                                Bình luận gần nhất
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            @if($recentComments->count() > 0)
                                <table class="table table-sm table-hover">
                                    @foreach($recentComments as $comment)
                                        <tr>
                                            <td class="text-truncate" style="max-width: 200px;">
                                                {{ $comment->content }}
                                            </td>
                                            <td class="text-right text-muted" style="width: 90px;">
                                                {{ date('d/m', strtotime($comment->created_at)) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            @else
                                <p class="text-muted text-center py-3 mb-0">Chưa có bình luận nào.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-body -->

        <div class="card-footer">
            @if($account->is_active == 1)
                <a href="{{ url('admin/member/lock/'.$account->id) }}"
                   class="btn btn-warning"
                   onclick="return confirm('Bạn có chắc muốn khóa tài khoản &quot;{{ $account->username }}&quot;?')">
                    <i class="fas fa-lock"></i> Khóa tài khoản
                </a>
            @else
                <a href="{{ url('admin/member/unlock/'.$account->id) }}"
                   class="btn btn-success"
                   onclick="return confirm('Bạn có chắc muốn mở khóa tài khoản &quot;{{ $account->username }}&quot;?')">
                    <i class="fas fa-unlock"></i> Mở khóa tài khoản
                </a>
            @endif
            <a href="{{ url('admin/member/edit/'.$account->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Chỉnh sửa tài khoản
            </a>
            <form action="{{ url('admin/member/delete/'.$account->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger float-right"
                        onclick="return confirm('Bạn có chắc muốn xóa tài khoản này? Tất cả bình luận và đánh giá sẽ bị xóa.')">
                    <i class="fas fa-trash-alt"></i> Xóa tài khoản
                </button>
            </form>
        </div>
    </div>
    <!-- /.card -->
</div>
@stop
