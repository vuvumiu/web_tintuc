@extends('back.template.master')

@section('title', 'Thong ke bai viet nhan vien')
@section('heading', 'Thong ke bai viet nhan vien')
@section('admin-manager', 'menu-open')
@section('admin-manager-list', 'active')

@section('content')
<div class="col-md-12">
    <div class="mb-3 d-flex flex-wrap align-items-center">
        <a href="{{ url('admin/admin-manager/list') }}" class="btn btn-secondary btn-sm mr-2 mb-2">
            <i class="fas fa-arrow-left"></i> Quay lai danh sach nhan vien
        </a>
        <a href="{{ url('admin/admin-manager/edit/' . $author->id) }}" class="btn btn-primary btn-sm mr-2 mb-2">
            <i class="fas fa-user-edit"></i> Sua tai khoan noi bo
        </a>
        @if(Auth::user()->isAdmin())
            <form action="{{ url('admin/admin-manager/author-toggle/' . $author->id) }}" method="POST" class="mb-2">
                @csrf
                <button type="submit" class="btn btn-{{ (int) ($author->is_author ?? 0) === 1 ? 'warning' : 'success' }} btn-sm">
                    <i class="fas {{ (int) ($author->is_author ?? 0) === 1 ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                    {{ (int) ($author->is_author ?? 0) === 1 ? 'Tat tu cach tac gia' : 'Bat tu cach tac gia' }}
                </button>
            </form>
        @endif
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-1">{{ $author->fullname ?: $author->username }}</h3>
                    <div class="text-muted mb-2">{{ $author->email ?: $author->username }}</div>
                    <div>
                        @if((int) ($author->is_author ?? 0) === 1)
                            <span class="badge badge-success">Dang bat tac gia</span>
                        @else
                            <span class="badge badge-secondary">Tac gia lich su</span>
                        @endif

                        @if((int) ($author->is_active ?? 1) === 1)
                            <span class="badge badge-light">Tai khoan hoat dong</span>
                        @else
                            <span class="badge badge-dark">Tai khoan khoa</span>
                        @endif

                        <span class="badge badge-info">{{ $author->level == 1 ? 'Quan tri vien' : 'Nhan vien' }}</span>
                    </div>
                </div>
                <div class="col-md-4 text-md-right mt-3 mt-md-0">
                    <div class="text-muted small">Thong tin lien he</div>
                    <div>{{ $author->phone ?: '-' }}</div>
                    <div>{{ $author->address ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($stats['total_articles'] ?? 0) }}</h3>
                    <p>Tong bai viet</p>
                </div>
                <div class="icon"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($stats['published_articles'] ?? 0) }}</h3>
                    <p>Da xuat ban</p>
                </div>
                <div class="icon"><i class="fas fa-globe"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($stats['workflow_articles'] ?? 0) }}</h3>
                    <p>Dang workflow</p>
                </div>
                <div class="icon"><i class="fas fa-stream"></i></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-dark">
                <div class="inner">
                    <h3>{{ number_format($stats['total_views'] ?? 0) }}</h3>
                    <p>Tong luot xem</p>
                </div>
                <div class="icon"><i class="fas fa-eye"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ number_format($stats['active_comments'] ?? 0) }}</h3>
                    <p>Binh luan dang hoat dong</p>
                </div>
                <div class="icon"><i class="fas fa-comments"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format($stats['rating_count'] ?? 0) }}</h3>
                    <p>Luot rating</p>
                </div>
                <div class="icon"><i class="fas fa-star-half-alt"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-light">
                <div class="inner">
                    <h3>{{ number_format((float) ($stats['rating_avg'] ?? 0), 1) }}</h3>
                    <p>Diem trung binh</p>
                </div>
                <div class="icon"><i class="fas fa-star"></i></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ url('admin/admin-manager/stats/' . $author->id) }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label>Tu ngay</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Den ngay</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Danh muc</label>
                        <select name="category_id" class="form-control">
                            <option value="">-- Tat ca danh muc --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->RowID }}" {{ request('category_id') == $category->RowID ? 'selected' : '' }}>
                                    {{ $category->Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Ap dung bo loc
                        </button>
                        <a href="{{ url('admin/admin-manager/stats/' . $author->id) }}" class="btn btn-secondary">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bai moi nhat</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($latestArticles as $news)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="font-weight-bold">{{ $news->Name }}</div>
                                        <small class="text-muted">
                                            {{ optional($news->created_at)->format('d/m/Y H:i') }} · {{ number_format((int) ($news->Views ?? 0)) }} views
                                        </small>
                                    </div>
                                    <a href="{{ url('admin/news/edit/' . $news->RowID) }}" class="btn btn-xs btn-outline-primary">Mo</a>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Chua co bai viet nao trong bo loc hien tai.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bai xem nhieu nhat</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($topViewedArticles as $news)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="font-weight-bold">{{ $news->Name }}</div>
                                        <small class="text-muted">
                                            {{ number_format((int) ($news->Views ?? 0)) }} views · {{ optional($news->created_at)->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <a href="{{ url('admin/news/edit/' . $news->RowID) }}" class="btn btn-xs btn-outline-primary">Mo</a>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Chua co bai viet nao trong bo loc hien tai.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dau moc gan nhat</h3>
                </div>
                <div class="card-body">
                    @if($latestArticle)
                        <div class="font-weight-bold">{{ $latestArticle->Name }}</div>
                        <div class="text-muted">{{ optional($latestArticle->created_at)->format('d/m/Y H:i') }}</div>
                    @else
                        <div class="text-muted">Chua co bai viet.</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bai top views</h3>
                </div>
                <div class="card-body">
                    @if($topViewedArticle)
                        <div class="font-weight-bold">{{ $topViewedArticle->Name }}</div>
                        <div class="text-muted">{{ number_format((int) ($topViewedArticle->Views ?? 0)) }} views</div>
                    @else
                        <div class="text-muted">Chua co bai viet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sach bai viet cua tac gia</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Tieu de</th>
                            <th>Danh muc</th>
                            <th class="text-center">Trang thai</th>
                            <th class="text-center">Views</th>
                            <th class="text-center">Binh luan</th>
                            <th class="text-center">Rating</th>
                            <th>Ngay tao</th>
                            <th class="text-center"><i class="fas fa-wrench"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $news)
                            <tr>
                                <td>{{ $news->Name }}</td>
                                <td>{{ $news->category_name ?: '-' }}</td>
                                <td class="text-center">
                                    @if((int) $news->Status === 1)
                                        <span class="badge badge-success">Da xuat ban</span>
                                    @else
                                        <span class="badge badge-secondary">Dang workflow</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ number_format((int) ($news->Views ?? 0)) }}</td>
                                <td class="text-center">{{ number_format((int) ($news->active_comments ?? 0)) }}</td>
                                <td class="text-center">
                                    {{ number_format((int) ($news->rating_count ?? 0)) }}
                                    <small class="text-muted d-block">{{ number_format((float) ($news->rating_avg ?? 0), 1) }}/5</small>
                                </td>
                                <td>{{ optional($news->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <a href="{{ url('admin/news/edit/' . $news->RowID) }}" class="btn btn-xs btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Khong co bai viet nao phu hop voi bo loc hien tai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($articles, 'links'))
                <div class="mt-3 d-flex justify-content-center">
                    {{ $articles->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@stop
