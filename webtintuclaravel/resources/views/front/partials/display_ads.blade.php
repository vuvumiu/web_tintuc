@if(isset($ads) && $ads->count() > 0)
<div class="vu-display-ads vu-display-ads--{{ $variant ?? 'default' }}">
    @foreach($ads as $ad)
        <div class="vu-display-ad vu-display-ad--{{ $ad->banner_align ?? 'center' }}">
            <a href="{{ $ad->link ?? '#' }}"
               target="_blank"
               rel="noopener"
               onclick="trackDisplayAdClick({{ $ad->id }})"
               class="vu-display-ad__link">
                @if($ad->image)
                    <img src="{{ $ad->image_url }}"
                         alt="{{ $ad->name }}"
                         style="width: {{ $ad->banner_width ?? '100%' }}; height: {{ $ad->banner_height ?? '90px' }};"
                         loading="lazy">
                @else
                    <div class="vu-display-ad__fallback" style="width: {{ $ad->banner_width ?? '100%' }}; height: {{ $ad->banner_height ?? '90px' }};">
                        {{ $ad->name }}
                    </div>
                @endif
            </a>
        </div>
    @endforeach
</div>

@once
<style>
    .vu-display-ads {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin: 22px 0;
    }

    .vu-display-ads--sidebar {
        margin-top: 0;
    }

    .vu-display-ad {
        display: flex;
    }

    .vu-display-ad--left { justify-content: flex-start; }
    .vu-display-ad--center { justify-content: center; }
    .vu-display-ad--right { justify-content: flex-end; }

    .vu-display-ad__link {
        display: block;
        max-width: 100%;
        border-radius: 8px;
        overflow: hidden;
        text-decoration: none;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .vu-display-ad__link:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,.16);
    }

    .vu-display-ad__link img {
        display: block;
        max-width: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    .vu-display-ad__fallback {
        background: linear-gradient(135deg, #24313d, #c9a84c);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
    }
</style>

<script>
    window.trackDisplayAdClick = function(adId) {
        fetch(@json(url('/ads/track-click')) + '/' + adId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).catch(function() {});
    };
</script>
@endonce
@endif
