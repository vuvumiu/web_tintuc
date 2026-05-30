@extends('front.template.master')

@if(isset($newsCat) && $newsCat)
    @section('title', $newsCat->MetaTitle ?? $newsCat->Name ?? 'Danh mục')
    @section('description', $newsCat->MetaDescription ?? '')
    @section('keywords', $newsCat->MetaKeyword ?? '')
    @section('url', url('/'.$newsCat->Alias))
    @if($newsCat->Alias)
        @section($newsCat->Alias, 'active')
    @endif
    @if($newsCat->Images)
        @section('images', url('images/page/'.$newsCat->Images))
    @endif
@else
    @section('title', 'Danh mục')
    @section('description', '')
    @section('keywords', '')
@endif

@section('content')
<div class="section" style="padding-top:3rem;padding-bottom:3rem;">
    <div class="container">
        {{-- Breadcrumb --}}
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="breadcrumb-sep">&rsaquo;</span>
            <span>{{ $newsCat->Name ?? 'Danh mục' }}</span>
        </nav>

        <div class="page-header">
            <div class="page-header-line"></div>
            <h1 class="page-header-title">{{ $newsCat->Name ?? 'Danh mục' }}</h1>
            <div class="page-header-line"></div>
        </div>

        @if(isset($sort))
        <div class="d-flex align-items-center gap-3 mb-4">
            <div style="flex:1;"></div>
            <select id="newSort" class="news_cat_sort">
                <option value="tinmoi" @if($sort == 'tinmoi') selected @endif>Sắp xếp theo tin mới</option>
                <option value="luotxem" @if($sort == 'luotxem') selected @endif>Sắp xếp theo lượt xem</option>
            </select>
            <input type="hidden" id="newsCat" value="{{ $newsCat->Alias ?? '' }}">
        </div>
        @endif

        @if(!empty($listNews) && $listNews->count() > 0)
            <div class="row g-3">
                @foreach($listNews as $v)
                <div class="col-12">
                    <a href="{{ url('/'.$v->Alias) }}.html" class="news-item-card" title="{{ $v->Name }}">
                        <div class="news-item-img">
                            @if(!empty($v->Images))
                                <img src="{{ url('images/news/'.$v->Images) }}" alt="{{ $v->Name }}" loading="lazy"/>
                            @else
                                <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="32" cy="32" r="22" fill="none" stroke="#c9a84c" stroke-width="1" opacity="0.28"/>
                                    <rect x="20" y="20" width="24" height="18" rx="2" fill="#c9a84c" opacity="0.18"/>
                                </svg>
                            @endif
                        </div>
                        <div class="news-item-body">
                            <div class="news-item-title">{{ $v->Name }}</div>
                            <p class="news-item-desc">
                                {{ Str::limit(strip_tags($v->SmallDescription ?? ''), 140, '...') }}
                            </p>
                            <div class="news-item-meta">
                                <span class="news-item-date">{{ $v->created_at ? date('d/m/Y', strtotime($v->created_at)) : '' }}</span>
                                <span class="news-item-read">Đọc tiếp &rarr;</span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            @if(method_exists($listNews, 'links'))
                <div class="page_pagination mt-4">
                    {{ $listNews->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="32" cy="32" r="24" fill="none" stroke="#c9a84c" stroke-width="1" opacity="0.2"/>
                    <path d="M22 32 Q32 20 42 32 Q32 44 22 32Z" fill="#c9a84c" opacity="0.15"/>
                </svg>
                <p>Đang cập nhật tin tức trong chuyên mục này.</p>
            </div>
        @endif
    </div>
</div>
@endsection
