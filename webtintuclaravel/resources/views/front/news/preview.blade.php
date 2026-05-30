@extends('front.template.master')
@section('title', 'Xem trước bài viết')
@section('content')

<style>
.preview-banner {
    background: linear-gradient(135deg, #c9a84c 0%, #b8941f 100%);
    color: #0a0a0f;
    padding: 12px 24px;
    text-align: center;
    font-weight: 700;
    position: sticky;
    top: 0;
    z-index: 100;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}
.preview-banner-text {
    flex: 1;
    text-align: center;
}
.preview-banner a.btn-sm {
    background: rgba(0,0,0,0.2);
    color: #0a0a0f;
    border: none;
    padding: 4px 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: background .15s;
    white-space: nowrap;
}
.preview-banner a.btn-sm:hover {
    background: rgba(0,0,0,0.35);
}
</style>

<div class="preview-banner">
    <span class="preview-banner-text">
        <i class="fas fa-eye mr-1"></i>
        CHẾ ĐỘ XEM TRƯỚC — Bài viết chưa được xuất bản
    </span>
    <a href="{{ url('admin/news/edit/' . $news->RowID) }}" class="btn btn-sm">
        <i class="fas fa-edit mr-1"></i> Chỉnh sửa
    </a>
</div>

<div class="contact_wrap">
    <div class="contact_page">
        <h2 class="heading" style="font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:20px;">{{ $news->Name }}</h2>
        <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:16px;font-size:13px;color:var(--text-secondary);">
            @if($news->author)
                <span><i class="fas fa-user-edit" style="color:var(--accent);"></i> Tác giả: <strong>{{ $news->author->fullname ?? $news->author->username }}</strong></span>
            @endif
            <span><i class="fas fa-calendar-alt" style="color:var(--accent);"></i> {{ $news->created_at ? date('d/m/Y H:i:s', strtotime($news->created_at)) : '—' }}</span>
            <span><i class="fas fa-eye" style="color:var(--accent);"></i> {{ number_format($news->Views ?? 0) }} lượt xem</span>
        </div>
        @if($news->tags && $news->tags->count() > 0)
            <div class="mb-3 d-flex flex-wrap gap-2">
                @foreach($news->tags as $tag)
                    <a href="{{ url('tag/' . $tag->slug) }}" style="background:rgba(201,168,76,0.15);color:#b8941f;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(201,168,76,0.3);display:inline-flex;align-items:center;gap:5px;">
                        <i class="fas fa-tag"></i>{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif
        <div class="contact_description" style="color:var(--text-secondary);line-height:1.9;font-size:16px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:32px;">
            {!! $news->Description !!}
        </div>
        <div class="clearfix"></div>
    </div>
</div>

{{-- Bài viết liên quan --}}
@if(isset($relatedNews) && $relatedNews->count() > 0)
<div class="container pb-5">
    <div class="section-header" style="margin-bottom:24px;">
        <div class="section-label">
            <div class="section-line"></div>
            <span class="section-name">Bài viết liên quan</span>
        </div>
    </div>
    <div class="row g-4">
        @foreach($relatedNews as $rel)
        <div class="col-6 col-md-3">
            <a href="{{ url($rel->Alias . '.html') }}" style="text-decoration:none;display:block;height:100%;">
                <div style="background:rgba(255,255,255,0.04);border:0.5px solid rgba(255,255,255,0.08);border-radius:10px;overflow:hidden;height:100%;transition:all .25s;" onmouseover="this.style.borderColor='var(--accent)';this.style.boxShadow='0 4px 20px rgba(201,168,76,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.08)';this.style.boxShadow='none'">
                    @if($rel->Images && file_exists(public_path('images/news/' . $rel->Images)))
                        <img src="{{ asset('images/news/' . $rel->Images) }}" style="width:100%;height:140px;object-fit:cover;" alt="{{ $rel->Name }}">
                    @else
                        <div style="width:100%;height:140px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-newspaper" style="color:rgba(255,255,255,0.2);font-size:2rem;"></i>
                        </div>
                    @endif
                    <div style="padding:14px;">
                        <div style="font-size:.88rem;font-weight:700;color:rgba(255,255,255,0.85);line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:8px;">
                            {{ Str::limit($rel->Name, 70) }}
                        </div>
                        <div style="font-size:11px;color:rgba(255,255,255,0.3);">
                            <i class="fas fa-calendar-alt" style="color:var(--accent);margin-right:4px;"></i>
                            {{ $rel->created_at ? date('d/m/Y', strtotime($rel->created_at)) : '' }}
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

@stop
