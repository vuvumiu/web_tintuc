@extends('front.template.master')

@if(isset($PageInfo) && $PageInfo)
    @section('title', $PageInfo->MetaTitle ?? 'Tìm kiếm')
    @section('description', $PageInfo->MetaDescription ?? '')
    @section('keywords', $PageInfo->MetaKeyword ?? '')
    @section('url', url('/tim-kiem'))
    @if($PageInfo->Alias)
        @section($PageInfo->Alias, 'active')
    @endif
    @if($PageInfo->Images)
        @section('images', url('images/page/'.$PageInfo->Images))
    @endif
@else
    @section('title', 'Tìm kiếm')
    @section('description', '')
    @section('keywords', '')
    @section('url', url('/tim-kiem'))
@endif

@section('content')
<div class="section" style="padding-top:3rem;padding-bottom:3rem;">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="breadcrumb-sep">&rsaquo;</span>
            <span>{{ $PageInfo->Name ?? 'Tìm kiếm' }}</span>
        </nav>

        <div class="page-header">
            <div class="page-header-line"></div>
            <h1 class="page-header-title">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle;margin-right:8px;">
                    <circle cx="6" cy="6" r="4.5" stroke="#c9a84c" stroke-width="1.2"/>
                    <line x1="9.5" y1="9.5" x2="13" y2="13" stroke="#c9a84c" stroke-width="1.2" stroke-linecap="round"/>
                </svg>
                Kết quả tìm kiếm
            </h1>
            <div class="page-header-line"></div>
        </div>

        @if(!empty($searchList) && $searchList->count() > 0)
            <p class="text-muted mb-4">Tìm thấy {{ $searchList->total() ?? $searchList->count() }} kết quả</p>
            <div class="row g-3">
                @foreach($searchList as $v)
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
                                @if(!empty($v->CategoryName))
                                <span class="news-item-badge">{{ $v->CategoryName }}</span>
                                @endif
                                <span class="news-item-date">{{ $v->created_at ? date('d/m/Y', strtotime($v->created_at)) : '' }}</span>
                                <span class="news-item-read">Đọc tiếp &rarr;</span>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            @if(method_exists($searchList, 'links'))
                <div class="page_pagination mt-4">
                    {{ $searchList->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="32" cy="32" r="24" fill="none" stroke="#c9a84c" stroke-width="1" opacity="0.2"/>
                    <circle cx="26" cy="26" r="12" fill="none" stroke="#c9a84c" stroke-width="1" opacity="0.15"/>
                </svg>
                <p>Không tìm thấy kết quả nào phù hợp với từ khóa của bạn.</p>
            </div>
        @endif
    </div>
</div>
@endsection
