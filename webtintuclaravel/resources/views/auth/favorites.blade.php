@extends('front.template.master')
@section('title', 'Bài viết yêu thích')
@section('content')

<style>
.fav-page { padding: 0 0 60px; }

/* ── Hero banner ── */
.fav-hero {
    background: linear-gradient(135deg, #24313d 0%, #3a4a5c 100%);
    padding: 32px 24px;
    border-radius: 0 0 24px 24px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}

.fav-hero::before {
    content: "";
    position: absolute;
    top: -40px; right: -40px;
    width: 180px; height: 180px;
    background: rgba(245,76,11,.08);
    border-radius: 50%;
}

.fav-hero::after {
    content: "";
    position: absolute;
    bottom: -60px; right: 60px;
    width: 220px; height: 220px;
    background: rgba(245,76,11,.06);
    border-radius: 50%;
}

.fav-hero-inner {
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    z-index: 1;
}

.fav-hero-icon {
    width: 64px; height: 64px;
    background: rgba(245,76,11,.15);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 0 0 8px rgba(245,76,11,.07);
}

.fav-hero-icon i { font-size: 1.6rem; color: #f54c0b; }

.fav-hero-title { color: #fff; font-size: 1.3rem; font-weight: 800; margin-bottom: 4px; }
.fav-hero-title span { color: #f54c0b; }

.fav-hero-desc { color: rgba(255,255,255,.6); font-size: .85rem; margin: 0; }

/* ── Filter bar ── */
.fav-filter-bar {
    background: #fff;
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.05);
    flex-wrap: wrap;
}

.fav-filter-label {
    font-size: .82rem;
    font-weight: 700;
    color: #888;
    text-transform: uppercase;
    letter-spacing: .04em;
    white-space: nowrap;
    margin: 0;
}

.fav-filter-chip {
    background: #f4f4f4;
    color: #555;
    border: 1.5px solid #e4e7eb;
    border-radius: 999px;
    padding: 5px 14px;
    font-size: .8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
}

.fav-filter-chip:hover,
.fav-filter-chip.active {
    background: #f54c0b;
    color: #fff;
    border-color: #f54c0b;
}

/* ── Card ── */
.fav-card {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 14px rgba(0,0,0,.07);
    transition: box-shadow .2s, transform .2s;
    height: 100%;
    display: flex;
    flex-direction: column;
    border: 1.5px solid #f0f0f0;
}

.fav-card:hover {
    box-shadow: 0 8px 28px rgba(0,0,0,.12);
    transform: translateY(-3px);
    border-color: #e4e7eb;
}

.fav-card-img-wrap {
    height: 180px;
    overflow: hidden;
    background: #f5f5f5;
    position: relative;
    flex-shrink: 0;
}

.fav-card-img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .4s;
}

.fav-card:hover .fav-card-img-wrap img { transform: scale(1.05); }

.fav-card-img-placeholder {
    height: 100%;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    color: #ccc; font-size: 2.8rem;
}

.fav-card-cat-badge {
    position: absolute;
    top: 12px; left: 12px;
    background: rgba(36,49,61,.85);
    color: #fff;
    font-size: .7rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 999px;
    text-transform: uppercase;
    letter-spacing: .04em;
    backdrop-filter: blur(4px);
}

.fav-card-body {
    padding: 18px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.fav-card-title {
    font-size: .92rem;
    font-weight: 700;
    color: #1a1a2e;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 8px;
    text-decoration: none;
    transition: color .15s;
}

.fav-card-title:hover { color: #f54c0b; }

.fav-card-desc {
    font-size: .8rem;
    color: #777;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
    margin-bottom: 12px;
}

.fav-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 12px;
    border-top: 1px solid #f5f5f5;
    gap: 8px;
}

.fav-card-date {
    font-size: .72rem;
    color: #aaa;
}

.fav-card-meta-icons {
    display: flex;
    gap: 10px;
    font-size: .72rem;
    color: #aaa;
}

.fav-card-remove {
    background: none;
    border: 1.5px solid #ff3b30;
    color: #ff3b30;
    border-radius: 8px;
    font-size: .75rem;
    font-weight: 700;
    padding: 5px 12px;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
    flex-shrink: 0;
}

.fav-card-remove:hover {
    background: #ff3b30;
    color: #fff;
    transform: scale(1.03);
}

/* ── Empty state ── */
.fav-empty {
    text-align: center;
    padding: 60px 24px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,.05);
}

.fav-empty-icon {
    width: 90px; height: 90px;
    background: #fff5f5;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 22px;
    box-shadow: 0 4px 16px rgba(255,59,48,.12);
}

.fav-empty-icon i { font-size: 2.2rem; color: #ff3b30; }
.fav-empty h3 { font-size: 1.15rem; font-weight: 700; color: #333; margin-bottom: 8px; }
.fav-empty p { font-size: .88rem; color: #888; margin-bottom: 24px; }

.fav-empty-btn {
    background: #f54c0b;
    color: #fff;
    padding: 11px 28px;
    border-radius: 10px;
    font-weight: 700;
    font-size: .9rem;
    text-decoration: none;
    transition: background .15s, transform .1s;
    display: inline-block;
}

.fav-empty-btn:hover {
    background: #d63808;
    color: #fff;
    transform: translateY(-1px);
}

/* ── Stats summary bar ── */
.fav-stats-bar {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.fav-stat-item {
    background: #fff;
    border-radius: 12px;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,.05);
    border: 1.5px solid #f0f0f0;
    min-width: 160px;
    flex: 1;
}

.fav-stat-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
}

.fav-stat-num {
    font-size: 1.4rem;
    font-weight: 800;
    color: #1a1a2e;
    line-height: 1;
}

.fav-stat-lbl {
    font-size: .72rem;
    color: #888;
    text-transform: uppercase;
    letter-spacing: .04em;
}

/* ── Responsive ── */
@media (max-width: 767px) {
    .fav-card-img-wrap { height: 160px; }
    .fav-hero-inner { flex-direction: column; text-align: center; }
    .fav-filter-bar { flex-direction: column; align-items: flex-start; }
}

@media (max-width: 575px) {
    .fav-card-footer { flex-direction: column; align-items: flex-start; gap: 8px; }
    .fav-card-remove { width: 100%; text-align: center; }
}
</style>

<div class="contact_wrap fav-page">

    {{-- Hero Header --}}
    <div class="fav-hero">
        <div class="container">
            <div class="fav-hero-inner">
                <div class="fav-hero-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div>
                    <h1 class="fav-hero-title">Bài viết <span>Yêu thích</span></h1>
                    <p class="fav-hero-desc">Tất cả bài viết bạn đã lưu lại để đọc lại sau</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">

        @if(isset($favorites) && $favorites->count() > 0)

            {{-- Stats summary --}}
            <div class="fav-stats-bar">
                <div class="fav-stat-item">
                    <div class="fav-stat-icon" style="background:#fff0f0; color:#ff3b30;">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div>
                        <div class="fav-stat-num">{{ $favorites->total() }}</div>
                        <div class="fav-stat-lbl">Tổng bài đã lưu</div>
                    </div>
                </div>
                <div class="fav-stat-item">
                    <div class="fav-stat-icon" style="background:#e8f4fd; color:#007bff;">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div>
                        <div class="fav-stat-num">{{ $favorites->total() }}</div>
                        <div class="fav-stat-lbl">Đang theo dõi</div>
                    </div>
                </div>
            </div>

            {{-- Cards grid --}}
            <div class="row g-4">
                @foreach($favorites as $fav)
                @if($fav && $fav->news)
                <div class="col-md-6 col-lg-4">
                    <div class="fav-card">

                        {{-- Image --}}
                        <div class="fav-card-img-wrap">
                            @if($fav->news->Images && file_exists(public_path('images/news/' . $fav->news->Images)))
                                <img src="{{ asset('images/news/' . $fav->news->Images) }}"
                                     alt="{{ $fav->news->Name }}"
                                     loading="lazy">
                            @else
                                <div class="fav-card-img-placeholder">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                            @endif

                            {{-- Category badge --}}
                            @if($fav->news->category)
                                <span class="fav-card-cat-badge">
                                    {{ $fav->news->category->Name }}
                                </span>
                            @endif
                        </div>

                        {{-- Body --}}
                        <div class="fav-card-body">
                            <a href="{{ url($fav->news->Alias . '.html') }}"
                               class="fav-card-title"
                               title="{{ $fav->news->Name }}">
                                {{ $fav->news->Name }}
                            </a>

                            @if($fav->news->SmallDescription)
                                <p class="fav-card-desc">{{ $fav->news->SmallDescription }}</p>
                            @endif

                            {{-- Footer --}}
                            <div class="fav-card-footer">
                                <div>
                                    <div class="fav-card-date">
                                        <i class="far fa-calendar me-1"></i>
                                        {{ $fav->news->created_at ? date('d/m/Y', strtotime($fav->news->created_at)) : '' }}
                                    </div>
                                    @if($fav->news->Views > 0)
                                        <div class="fav-card-meta-icons mt-1">
                                            <span><i class="fas fa-eye me-1"></i>{{ number_format($fav->news->Views) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <form action="{{ route('yeuthich') }}" method="POST" class="d-inline m-0">
                                    @csrf
                                    <input type="hidden" name="news_id" value="{{ $fav->news->RowID }}">
                                    <button type="submit" class="fav-card-remove" title="Bỏ khỏi yêu thích">
                                        <i class="fas fa-heart-broken me-1"></i>Bỏ thích
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            {{-- Pagination --}}
            @if(method_exists($favorites, 'links') && $favorites->lastPage() > 1)
                <div class="mt-5 d-flex justify-content-center">
                    {{ $favorites->links('pagination::bootstrap-5') }}
                </div>
            @endif

        @else
            {{-- Empty state --}}
            <div class="fav-empty">
                <div class="fav-empty-icon">
                    <i class="far fa-heart"></i>
                </div>
                <h3>Chưa có bài viết yêu thích</h3>
                <p>Lưu lại những bài viết bạn thích để đọc lại sau nhé!</p>
                <a href="{{ url('/') }}" class="fav-empty-btn">
                    <i class="fas fa-compass me-2"></i>Khám phá bài viết
                </a>
            </div>
        @endif
    </div>
</div>

@stop
