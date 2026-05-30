@extends('front.template.master')
@section('title', $PageInfo->MetaTitle ?? 'Trang chủ')
@section('description', $PageInfo->MetaDescription ?? '')
@section('keywords', $PageInfo->MetaKeyword ?? '')
@section('url', url('/'))
@section('home', 'active')
@if($PageInfo && $PageInfo->Images)
    @section('images', url('images/page/'.$PageInfo->Images))
@endif

@section('content')

{{-- ======================== TICKER ======================== --}}
@if(isset($Tickers) && $Tickers->count() > 0)
<div class="ticker">
    <span class="ticker-label">
        <i class="fas fa-bolt"></i> Tin nóng
    </span>
    <div class="ticker-wrapper">
        <div class="ticker-content">
            @foreach($Tickers as $ticker)
                <a href="{{ $ticker->getDisplayUrl() }}" class="ticker-item">
                    {{ $ticker->getDisplayTitle() }}
                </a>
            @endforeach
            @foreach($Tickers as $ticker)
                <a href="{{ $ticker->getDisplayUrl() }}" class="ticker-item">
                    {{ $ticker->getDisplayTitle() }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ======================== HERO SECTION ======================== --}}
@php
    // === LOGIC SẠCH: Tách biệt carousel và sidebar ===

    // 1. Carousel: hero chính + sidebar items (tối đa 5 items, unique theo RowID)
    $carouselSlides = collect();

    if ($FeaturedMain && $FeaturedMain->news) {
        $carouselSlides->push($FeaturedMain->news);
    }

    foreach ($FeaturedSidebar as $fs) {
        if ($fs && $fs->news) {
            $carouselSlides->push($fs->news);
        }
    }

    $heroSlides = $carouselSlides->unique('RowID')->take(5)->values();
    $hasCarousel = $heroSlides->count() > 1;

    // 2. Sidebar: CHỈ lấy items KHÔNG trùng với hero main (tránh duplicate)
    $heroSidebar = collect($FeaturedSidebar)
        ->filter(fn($fs) => $fs && $fs->news)
        ->pluck('news')
        ->reject(fn($news) =>
            $FeaturedMain &&
            $FeaturedMain->news &&
            $news->RowID === $FeaturedMain->news->RowID
        )
        ->take(4)
        ->values();

    // 3. Nếu sidebar trống, lấy thêm tin từ DB (tránh trùng với carousel)
    if ($heroSidebar->count() < 4) {
        $excludedIds = $heroSlides->pluck('RowID')->filter()->toArray();
        $needed = 4 - $heroSidebar->count();
        $sidebarExtra = \DB::table('news as a')
            ->join('news_cat as b', 'a.RowIDCat', '=', 'b.RowID')
            ->selectRaw('a.RowID, a.Name, a.Alias, a.Images, a.SmallDescription, a.created_at, b.Name as CategoryName')
            ->where('a.Status', 1)
            ->when(!empty($excludedIds), fn($q) => $q->whereNotIn('a.RowID', $excludedIds))
            ->orderBy('a.RowID', 'DESC')
            ->limit($needed)
            ->get();
        $heroSidebar = $heroSidebar->merge($sidebarExtra);
    }

    // 4. Hero main cho fallback
    $heroMain = $FeaturedMain->news ?? null;
@endphp

<section class="hero">
    <div class="hero-accent-top"></div>

    @if($heroMain)
    {{-- Main Story with Carousel --}}
    <div class="hero-main" id="heroCarousel">
        {{-- Carousel Slides --}}
        <div class="hero-carousel-track">
            @foreach($heroSlides as $index => $slide)
            <div class="hero-carousel-slide {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">
                <div class="hero-visual">
                    <div class="hero-visual-bg"></div>
                    @if(!empty(data_get($slide, 'Images')))
                    <div class="hero-image" style="background-image: url('{{ url('images/news/'.data_get($slide, 'Images')) }}')"></div>
                    @endif
                    <div class="hero-gradient"></div>
                    <svg class="hero-circles" style="position:absolute;top:40px;left:100px;opacity:0.12;" width="300" height="300" viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="150" cy="150" r="140" fill="none" stroke="#c9a84c" stroke-width="0.5"/>
                        <circle cx="150" cy="150" r="100" fill="none" stroke="#c9a84c" stroke-width="0.5"/>
                        <circle cx="150" cy="150" r="60" fill="none" stroke="#c9a84c" stroke-width="0.5"/>
                    </svg>
                </div>

                <div class="hero-content">
                    @if(!empty(data_get($slide, 'CategoryName')))
                    <div class="hero-tag">
                        <div class="hero-tag-dot"></div>
                        <span class="hero-tag-text">{{ data_get($slide, 'CategoryName') }}</span>
                    </div>
                    @endif

                    <h1 class="hero-title">
                        <a href="{{ url('/'.data_get($slide, 'Alias').'.html') }}" style="color:#fff;text-decoration:none;">
                            {{ data_get($slide, 'Name', 'Không có tiêu đề') }}
                        </a>
                    </h1>

                    <p class="hero-desc">
                        {{ Str::limit(strip_tags(data_get($slide, 'SmallDescription', '')), 160, '...') }}
                    </p>

                    <div class="hero-meta">
                        @if(!empty(data_get($slide, 'AuthorName')))
                        <div class="hero-author">
                            <div class="hero-avatar">
                                {{ strtoupper(substr(data_get($slide, 'AuthorName'), 0, 2)) }}
                            </div>
                            <span class="hero-author-name">{{ data_get($slide, 'AuthorName') }}</span>
                        </div>
                        @endif
                        <span class="hero-date">
                            {{ !empty(data_get($slide, 'created_at')) ? date('d/m/Y', strtotime(data_get($slide, 'created_at'))) : '' }}
                        </span>
                        <a href="{{ url('/'.data_get($slide, 'Alias').'.html') }}" class="read-btn">
                            Đọc tiếp &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Carousel Indicators --}}
        @if($hasCarousel)
        <div class="hero-carousel-indicators">
            @foreach($heroSlides as $index => $slide)
            <button class="carousel-indicator {{ $index === 0 ? 'active' : '' }}" 
                    data-slide="{{ $index }}"
                    aria-label="Slide {{ $index + 1 }}">
            </button>
            @endforeach
        </div>

        @endif
    </div>

    {{-- Sidebar Stories --}}
    @if(count($heroSidebar) > 0)
    <div class="hero-sidebar">
        <div class="sidebar-title">Tin mới nhất</div>

        @foreach($heroSidebar as $sideNews)
        <div class="side-article">
            <div class="side-img">
                @if(!empty(data_get($sideNews, 'Images')))
                    <img src="{{ url('images/news/'.data_get($sideNews, 'Images')) }}" alt="{{ data_get($sideNews, 'Name') }}" loading="lazy"/>
                @else
                    <svg width="36" height="36" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="18" cy="18" r="12" fill="none" stroke="#c9a84c" stroke-width="0.8" opacity="0.5"/>
                        <rect x="12" y="13" width="12" height="9" rx="2" fill="#c9a84c" opacity="0.25"/>
                    </svg>
                @endif
            </div>
            <div>
                @if(!empty(data_get($sideNews, 'CategoryName')))
                <div class="side-cat">{{ data_get($sideNews, 'CategoryName') }}</div>
                @endif
                <a href="{{ url('/'.data_get($sideNews, 'Alias').'.html') }}" class="side-headline" style="text-decoration:none;display:block;">
                    {{ data_get($sideNews, 'Name', 'Không có tiêu đề') }}
                </a>
                <div class="side-time">
                    {{ !empty(data_get($sideNews, 'created_at')) ? \Carbon\Carbon::parse(data_get($sideNews, 'created_at'))->diffForHumans() : '' }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @else
    {{-- Fallback hero if no news --}}
    <div class="hero-main">
        <div class="hero-visual">
            <div class="hero-visual-bg"></div>
        </div>
        <div class="hero-content">
            <div class="hero-tag">
                <div class="hero-tag-dot"></div>
                <span class="hero-tag-text">Tin tức hàng đầu</span>
            </div>
            <h1 class="hero-title">Chào mừng đến với {{ $siteName ?? 'VNXpress' }}</h1>
            <p class="hero-desc">Cập nhật tin tức nóng hổi, chính xác và đáng tin cậy mỗi ngày.</p>
            <a href="{{ url('/') }}" class="read-btn">Khám phá ngay &rarr;</a>
        </div>
    </div>
    @endif
</section>

{{-- ======================== CATEGORY SECTIONS ======================== --}}
@if(!empty($CategoriesWithNews))
    @foreach($CategoriesWithNews as $index => $item)
        @php
            $cat = $item['category'] ?? null;
            $newsList = $item['news'] ?? collect();
            if(!$cat || $newsList->count() == 0) {
                continue;
            }
            $isDark = ($index == 1);
            $sectionClass = $isDark ? 'section-dark' : (($index == 0) ? 'section-alt' : 'section');
        @endphp

        <section class="{{ $sectionClass }}">
            <div class="container">
                <div class="section-header">
                    <div class="section-label">
                        <div class="section-line"></div>
                        <span class="section-name">{{ $cat->Name }}</span>
                    </div>
                    <a href="{{ url('/' . $cat->Alias) }}" class="view-all">Xem tất cả</a>
                </div>

                @if($index == 0 && $newsList->count() >= 1)
                    {{-- FEATURED ROW cho section đầu tiên --}}
                    <div class="featured-row">
                        @php $featured = $newsList->first(); @endphp
                        <a href="{{ url('/'.$featured->Alias) }}.html" class="featured-main" style="text-decoration:none;">
                            <div class="featured-img">
                                @if(!empty($featured->Images))
                                    <img src="{{ url('images/news/'.$featured->Images) }}" alt="{{ $featured->Name }}" loading="lazy"/>
                                @else
                                    <svg width="96" height="96" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="48" cy="48" r="36" fill="none" stroke="#c9a84c" stroke-width="1" opacity="0.28"/>
                                        <circle cx="48" cy="48" r="24" fill="none" stroke="#c9a84c" stroke-width="0.8" opacity="0.2"/>
                                        <path d="M36 64 L48 30 L60 64" fill="none" stroke="#c9a84c" stroke-width="2" opacity="0.5"/>
                                        <circle cx="48" cy="30" r="5" fill="#c9a84c" opacity="0.55"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="featured-body">
                                <div class="card-cat">{{ $cat->Name }}</div>
                                <div class="featured-title">{{ $featured->Name }}</div>
                                <div class="featured-excerpt">
                                    {{ Str::limit(strip_tags($featured->SmallDescription ?? ''), 180, '...') }}
                                </div>
                                <div class="card-footer">
                                    <span class="card-date">{{ $featured->created_at ? date('d/m/Y', strtotime($featured->created_at)) : '' }}</span>
                                    <span class="card-readmore">Đọc tiếp &rarr;</span>
                                </div>
                            </div>
                        </a>

                        <div class="featured-stack">
                            @php
                                $stackItems = $newsList->skip(1)->take(4)->values();
                                $stackCount = $stackItems->count();
                                // Nếu chưa đủ 4 bài, lấy thêm từ danh mục tiếp theo
                                if (false && $stackCount < 4) {
                                    $needed = 4 - $stackCount;
                                    $fillNews = collect();
                                    for ($nextIdx = $index + 1; $nextIdx < count($CategoriesWithNews) && $fillNews->count() < $needed; $nextIdx++) {
                                        $nextCat = $CategoriesWithNews[$nextIdx]['category'] ?? null;
                                        $nextNews = $CategoriesWithNews[$nextIdx]['news'] ?? collect();
                                        if ($nextCat && $nextNews->count() > 0) {
                                            foreach ($nextNews->take($needed - $fillNews->count()) as $n) {
                                                $n->_fillCatName = $nextCat->Name;
                                                $n->_fillCatAlias = $nextCat->Alias;
                                                $fillNews->push($n);
                                            }
                                        }
                                    }
                                    $stackItems = $stackItems->merge($fillNews);
                                }
                                $stackItems = $newsList->skip(1)->take(4)->values();
                            @endphp
                            @foreach($stackItems as $mini)
                            <a href="{{ url('/'.$mini->Alias) }}.html" class="mini-card" style="text-decoration:none;">
                                <div class="mini-img">
                                    @if(!empty($mini->Images))
                                        <img src="{{ url('images/news/'.$mini->Images) }}" alt="{{ $mini->Name }}" loading="lazy"/>
                                    @else
                                        <svg width="36" height="36" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="18" cy="18" r="12" fill="none" stroke="#c9a84c" stroke-width="0.8" opacity="0.35"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="mini-body">
                                    <div class="card-cat">{{ $cat->Name }}</div>
                                    <div class="mini-title">{{ $mini->Name }}</div>
                                    <div class="mini-date">{{ $mini->created_at ? date('d/m/Y', strtotime($mini->created_at)) : '' }}</div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- CARD GRID thường --}}
                    <div class="row g-4">
                        @foreach($newsList as $card)
                        <div class="col-12 col-md-6 col-lg-3">
                            <a href="{{ url('/'.$card->Alias) }}.html" class="{{ $isDark ? 'card-dark' : 'card' }}" style="text-decoration:none;display:flex;flex-direction:column;">
                                <div class="{{ $isDark ? 'card-img-dark' : 'card-img' }}">
                                    @if(!empty($card->Images))
                                        <img src="{{ url('images/news/'.$card->Images) }}" alt="{{ $card->Name }}" loading="lazy"/>
                                    @else
                                        <svg width="72" height="72" viewBox="0 0 72 72" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="36" cy="36" r="22" fill="none" stroke="#c9a84c" stroke-width="1" opacity="0.35"/>
                                            <rect x="24" y="24" width="24" height="18" rx="2" fill="#c9a84c" opacity="0.18"/>
                                            <circle cx="36" cy="33" r="5" fill="#c9a84c" opacity="0.45"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="card-cat">{{ $cat->Name }}</div>
                                    <div class="{{ $isDark ? 'card-title-dark' : 'card-title' }}">{{ $card->Name }}</div>
                                    <div class="{{ $isDark ? 'card-excerpt-dark' : 'card-excerpt' }}">
                                        {{ Str::limit(strip_tags($card->SmallDescription ?? ''), 90, '...') }}
                                    </div>
                                    <div class="{{ $isDark ? 'card-footer-dark' : 'card-footer' }}">
                                        <span class="{{ $isDark ? 'card-date-dark' : 'card-date' }}">
                                            {{ $card->created_at ? date('d/m/Y', strtotime($card->created_at)) : '' }}
                                        </span>
                                        <span class="card-readmore">Đọc tiếp &rarr;</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endforeach
@endif

@php
    $hasCategoryNews = collect($CategoriesWithNews ?? [])->contains(function ($item) {
        return collect($item['news'] ?? [])->count() > 0;
    });
@endphp

@if(!$heroMain && !$hasCategoryNews && (!isset($NewsViews) || $NewsViews->count() === 0))
<section class="section">
    <div class="container">
        <div class="card" style="padding:32px; text-align:center; min-height:auto;">
            <div class="section-label" style="justify-content:center; margin-bottom:14px;">
                <div class="section-line"></div>
                <span class="section-name">Dang cap nhat noi dung</span>
            </div>
            <h2 class="card-title" style="margin-bottom:12px;">Chua co bai viet nao dang xuat ban</h2>
            <div class="card-excerpt" style="max-width:680px; margin:0 auto;">
                Trang hien dang khong co du lieu tin tuc de hien thi. Hay kiem tra lai danh muc, bai viet va cac ban ghi noi bat trong trang quan tri.
            </div>
        </div>
    </div>
</section>
@endif

{{-- ======================== XEM NHIEU NHAT ======================== --}}
@if(!empty($NewsViews) && $NewsViews->count() > 0)
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-label">
                <div class="section-line"></div>
                <span class="section-name">Xem nhiều nhất</span>
            </div>
            <a href="{{ url('/tin-noi-bat') }}" class="view-all">Xem tất cả</a>
        </div>
        <div class="row g-4">
            @foreach($NewsViews as $v)
            <div class="col-12 col-md-6 col-lg-3">
                <a href="{{ url('/'.$v->Alias) }}.html" class="card" style="text-decoration:none;display:flex;flex-direction:column;">
                    <div class="card-img">
                        @if(!empty($v->Images))
                            <img src="{{ url('images/news/'.$v->Images) }}" alt="{{ $v->Name }}" loading="lazy"/>
                        @else
                            <svg width="72" height="72" viewBox="0 0 72 72" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="36" cy="36" r="22" fill="none" stroke="#c9a84c" stroke-width="1" opacity="0.35"/>
                            </svg>
                        @endif
                    </div>
                    <div class="card-body">
                        @if(!empty($v->CategoryName))
                        <div class="card-cat">{{ $v->CategoryName }}</div>
                        @endif
                        <div class="card-title">{{ $v->Name }}</div>
                        <div class="card-excerpt">
                            {{ Str::limit(strip_tags($v->SmallDescription ?? ''), 90, '...') }}
                        </div>
                        <div class="card-footer">
                            <span class="card-date">{{ $v->created_at ? date('d/m/Y', strtotime($v->created_at)) : '' }}</span>
                            <span class="card-readmore">Đọc tiếp &rarr;</span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($hasCarousel)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('heroCarousel');
    if (!carousel) return;

    const slides = carousel.querySelectorAll('.hero-carousel-slide');
    const indicators = carousel.querySelectorAll('.carousel-indicator');
    const prevBtn = carousel.querySelector('.carousel-control-prev');
    const nextBtn = carousel.querySelector('.carousel-control-next');
    const progressBar = document.getElementById('carouselProgressBar');

    if (slides.length <= 1) return;

    let currentSlide = 0;
    let autoPlayInterval;
    let progressInterval;
    let isPaused = false;
    const slideCount = slides.length;
    const autoPlayDelay = 5000; // 5 giây

    function goToSlide(index) {
        slides[currentSlide].classList.remove('active');
        indicators[currentSlide].classList.remove('active');

        currentSlide = (index + slideCount) % slideCount;

        slides[currentSlide].classList.add('active');
        indicators[currentSlide].classList.add('active');

        resetProgress();
    }

    function nextSlide() {
        goToSlide(currentSlide + 1);
    }

    function prevSlide() {
        goToSlide(currentSlide - 1);
    }

    function startAutoPlay() {
        stopAutoPlay();
        autoPlayInterval = setInterval(function() {
            if (!isPaused) nextSlide();
        }, autoPlayDelay);

        startProgress();
    }

    function stopAutoPlay() {
        clearInterval(autoPlayInterval);
        clearInterval(progressInterval);
    }

    function startProgress() {
        if (!progressBar) return;
        var progress = 0;
        var step = 100 / (autoPlayDelay / 50);

        progressInterval = setInterval(function() {
            if (!isPaused) {
                progress += step;
                if (progress > 100) progress = 100;
                progressBar.style.width = progress + '%';
            }
        }, 50);
    }

    function resetProgress() {
        if (!progressBar) return;
        progressBar.style.transition = 'none';
        progressBar.style.width = '0%';
        setTimeout(function() {
            progressBar.style.transition = 'width 0.1s linear';
        }, 10);
    }

    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);

    indicators.forEach(function(indicator, index) {
        indicator.addEventListener('click', function() { goToSlide(index); });
    });

    carousel.addEventListener('mouseenter', function() { isPaused = true; });
    carousel.addEventListener('mouseleave', function() { isPaused = false; });

    var touchStartX = 0;
    var touchEndX = 0;

    carousel.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
        isPaused = true;
    }, { passive: true });

    carousel.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        isPaused = false;
        if (touchStartX - touchEndX > 50) nextSlide();
        if (touchEndX - touchStartX > 50) prevSlide();
    }, { passive: true });

    startAutoPlay();
});
</script>
@endif

@endsection
