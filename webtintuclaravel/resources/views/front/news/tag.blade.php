@extends('front.template.master')

@if(isset($tag) && $tag)
    @section('title', $tag->meta_title ?? ('Tag: ' . ($tag->name ?? '')))
    @section('description', $tag->meta_description ?? '')
@else
    @section('title', 'Tag')
    @section('description', '')
@endif

@section('content')
<div class="section" style="padding-top:3rem;padding-bottom:3rem;">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="breadcrumb-sep">&rsaquo;</span>
            <span>Tag</span>
        </nav>

        <div class="page-header">
            <div class="page-header-line"></div>
            <h1 class="page-header-title">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;vertical-align:middle;margin-right:8px;">
                    <path d="M7 1a4 4 0 00-4 4v.5H2.5a.5.5 0 000 1h.5V7a.5.5 0 001 0v-.5H5a4 4 0 104 0 4 4 0 00-4-4h-.5V1.5a.5.5 0 00-1 0v.5H5a.5.5 0 00.5.5V5a.5.5 0 001 0V3a4 4 0 00-2.5-3.77V1z" fill="#c9a84c" opacity="0.7"/>
                </svg>
                {{ $tag->name ?? 'Unknown' }}
            </h1>
            <div class="page-header-line"></div>
        </div>

        @if(!empty($listNews) && $listNews->count() > 0)
            <div class="row g-3">
                @foreach($listNews as $news)
                <div class="col-12">
                    <a href="{{ url(($news->Alias ?? '') . '.html') }}" class="news-item-card">
                        <div class="news-item-img">
                            @if(!empty($news->Images))
                                <img src="{{ url('images/news/' . $news->Images) }}" alt="{{ $news->Name ?? '' }}" loading="lazy"/>
                            @else
                                <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="32" cy="32" r="22" fill="none" stroke="#c9a84c" stroke-width="1" opacity="0.28"/>
                                    <rect x="20" y="20" width="24" height="18" rx="2" fill="#c9a84c" opacity="0.18"/>
                                </svg>
                            @endif
                        </div>
                        <div class="news-item-body">
                            <div class="news-item-title">{{ $news->Name ?? '' }}</div>
                            <p class="news-item-desc">
                                {{ Str::limit(strip_tags($news->SmallDescription ?? ''), 140, '...') }}
                            </p>
                            <div class="news-item-meta">
                                <span class="news-item-date">{{ $news->created_at ? date('d/m/Y', strtotime($news->created_at)) : date('d/m/Y') }}</span>
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
                </svg>
                <p>Không có bài viết nào cho tag "{{ $tag->name ?? '' }}"</p>
            </div>
        @endif
    </div>
</div>
@endsection
