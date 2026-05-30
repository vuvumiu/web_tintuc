{{-- Banner Ads Component --}}
{{-- Sử dụng: @include('front.partials.banner_ads', ['bannerAds' => $bannerAds]) --}}

@if(isset($bannerAds) && $bannerAds->count() > 0)
<div class="vu-banner-ads">
    @foreach($bannerAds as $ad)
    <div class="vu-banner-item vu-banner-align-{{ $ad->banner_align ?? 'center' }}">
        <a href="{{ $ad->link ?? '#' }}"
           target="_blank"
           rel="noopener"
           onclick="trackBannerClick({{ $ad->id }})"
           class="vu-banner-link">
            @if($ad->image)
                <img src="{{ url('images/ads/' . $ad->image) }}"
                     alt="{{ $ad->name }}"
                     style="width: {{ $ad->banner_width ?? '100%' }}; height: {{ $ad->banner_height ?? '90px' }}; object-fit: cover;"
                     loading="lazy">
            @else
                <div style="width: {{ $ad->banner_width ?? '100%' }}; height: {{ $ad->banner_height ?? '90px' }}; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; border-radius: 8px;">
                    {{ $ad->name }}
                </div>
            @endif
        </a>
    </div>
    @endforeach
</div>

<style>
    .vu-banner-ads {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin: 16px 0;
    }

    .vu-banner-item {
        display: flex;
    }

    .vu-banner-align-left {
        justify-content: flex-start;
    }

    .vu-banner-align-center {
        justify-content: center;
    }

    .vu-banner-align-right {
        justify-content: flex-end;
    }

    .vu-banner-link {
        display: block;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 8px;
        overflow: hidden;
    }

    .vu-banner-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .vu-banner-link img {
        display: block;
        border-radius: 8px;
    }
</style>

<script>
    window.trackBannerClick = function(adId) {
        fetch(@json(url('/ads/track-click')) + '/' + adId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).catch(function() {});
    };
</script>
@endif
