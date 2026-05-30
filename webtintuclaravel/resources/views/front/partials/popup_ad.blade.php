@if(isset($popupAd) && $popupAd)
<style>
    .vu-popup-overlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(0, 0, 0, 0.62);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
        backdrop-filter: blur(3px);
    }

    .vu-popup-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .vu-popup-box {
        position: relative;
        width: min(92vw, 620px);
        max-height: 88vh;
        overflow: hidden;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.38);
        transform: translateY(16px) scale(0.96);
        transition: transform 0.25s ease;
    }

    .vu-popup-overlay.show .vu-popup-box {
        transform: translateY(0) scale(1);
    }

    .vu-popup-position-bottom_right,
    .vu-popup-position-bottom_left,
    .vu-popup-position-top_right,
    .vu-popup-position-top_left {
        background: rgba(0, 0, 0, 0.35);
    }

    .vu-popup-position-bottom_right {
        align-items: flex-end;
        justify-content: flex-end;
    }

    .vu-popup-position-bottom_left {
        align-items: flex-end;
        justify-content: flex-start;
    }

    .vu-popup-position-top_right {
        align-items: flex-start;
        justify-content: flex-end;
    }

    .vu-popup-position-top_left {
        align-items: flex-start;
        justify-content: flex-start;
    }

    .vu-popup-position-bottom_right .vu-popup-box,
    .vu-popup-position-bottom_left .vu-popup-box,
    .vu-popup-position-top_right .vu-popup-box,
    .vu-popup-position-top_left .vu-popup-box {
        width: min(92vw, 420px);
    }

    .vu-popup-image {
        display: block;
        width: 100%;
        max-height: 82vh;
        object-fit: contain;
        background: #fff;
    }

    .vu-popup-fallback {
        min-height: 260px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px;
        background: #111827;
        color: #fff;
        font-size: 22px;
        font-weight: 700;
        text-align: center;
    }

    .vu-popup-close {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 2;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.68);
        color: #fff;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    .vu-popup-close:hover {
        background: rgba(0, 0, 0, 0.86);
    }

    .vu-popup-countdown {
        position: absolute;
        left: 50%;
        bottom: 12px;
        transform: translateX(-50%);
        z-index: 2;
        background: rgba(0, 0, 0, 0.72);
        color: #fff;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
    }

    @media (max-width: 575px) {
        .vu-popup-overlay {
            padding: 12px;
        }

        .vu-popup-box {
            width: 96vw;
        }
    }
</style>

<div class="vu-popup-overlay vu-popup-position-{{ $popupAd->popup_position ?: 'center' }}"
     id="vuPopupOverlay"
     data-popup-id="{{ $popupAd->id }}"
     data-auto-close="{{ (int) $popupAd->auto_close_seconds }}"
     data-show-once-per-session="{{ $popupAd->show_once_per_session ? 1 : 0 }}"
     data-impression-limit="{{ (int) ($popupAd->impression_limit ?? 1) }}"
     data-cooldown-minutes="{{ (int) ($popupAd->cooldown_minutes ?? 30) }}"
     data-show-delay-seconds="{{ (int) ($popupAd->show_delay_seconds ?? 2) }}">
    <div class="vu-popup-box">
        @if($popupAd->show_close_button)
            <button type="button" class="vu-popup-close" id="vuPopupClose" title="Dong">
                <i class="fas fa-times"></i>
            </button>
        @endif

        @if((int) $popupAd->auto_close_seconds > 0)
            <div class="vu-popup-countdown" id="vuPopupCountdown">
                Dong sau <span id="vuCountdownNumber">{{ (int) $popupAd->auto_close_seconds }}</span>s
            </div>
        @endif

        <a href="{{ $popupAd->link ?: '#' }}"
           target="_blank"
           rel="noopener"
           id="vuPopupLink"
           onclick="trackPopupClick({{ $popupAd->id }})">
            @if($popupAd->image)
                <img src="{{ url('images/ads/' . $popupAd->image) }}"
                     alt="{{ $popupAd->name }}"
                     class="vu-popup-image"
                     loading="eager">
            @else
                <div class="vu-popup-fallback">{{ $popupAd->name }}</div>
            @endif
        </a>
    </div>
</div>

<script>
(function() {
    'use strict';

    var overlay = document.getElementById('vuPopupOverlay');
    if (!overlay) return;

    var closeBtn = document.getElementById('vuPopupClose');
    var countdownNumber = document.getElementById('vuCountdownNumber');
    var popupId = overlay.dataset.popupId;
    var autoCloseSeconds = parseInt(overlay.dataset.autoClose, 10) || 0;
    var showOncePerSession = parseInt(overlay.dataset.showOncePerSession, 10) === 1;
    var impressionLimit = parseInt(overlay.dataset.impressionLimit, 10);
    var cooldownMinutes = parseInt(overlay.dataset.cooldownMinutes, 10);
    var showDelaySeconds = parseInt(overlay.dataset.showDelaySeconds, 10);
    var countdownInterval = null;

    if (isNaN(impressionLimit) || impressionLimit < 0) impressionLimit = 1;
    if (isNaN(cooldownMinutes) || cooldownMinutes < 0) cooldownMinutes = 30;
    if (isNaN(showDelaySeconds) || showDelaySeconds < 0) showDelaySeconds = 2;

    var storageKey = 'vu_popup_ad_' + popupId;
    var sessionKey = storageKey + '_session';

    function getState() {
        try {
            return JSON.parse(localStorage.getItem(storageKey) || '{}') || {};
        } catch (e) {
            return {};
        }
    }

    function saveState(state) {
        try {
            localStorage.setItem(storageKey, JSON.stringify(state));
        } catch (e) {}
    }

    function shouldShowPopup() {
        var state = getState();
        var shownCount = parseInt(state.count, 10) || 0;
        var lastShownAt = parseInt(state.lastShownAt, 10) || 0;
        var now = Date.now();

        if (showOncePerSession) {
            try {
                if (sessionStorage.getItem(sessionKey) === '1') {
                    return false;
                }
            } catch (e) {}
        }

        if (impressionLimit > 0 && shownCount >= impressionLimit) {
            return false;
        }

        if (cooldownMinutes > 0 && lastShownAt > 0) {
            var cooldownMs = cooldownMinutes * 60 * 1000;
            if (now - lastShownAt < cooldownMs) {
                return false;
            }
        }

        return true;
    }

    function markPopupShown() {
        var state = getState();
        state.count = (parseInt(state.count, 10) || 0) + 1;
        state.lastShownAt = Date.now();
        saveState(state);

        if (showOncePerSession) {
            try {
                sessionStorage.setItem(sessionKey, '1');
            } catch (e) {}
        }
    }

    function trackPopupView(adId) {
        fetch(@json(url('/ads/track-view')) + '/' + adId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).catch(function() {});
    }

    function startAutoCloseCountdown() {
        if (autoCloseSeconds <= 0) {
            return;
        }

        var remaining = autoCloseSeconds;
        if (countdownNumber) {
            countdownNumber.textContent = remaining;
        }

        countdownInterval = setInterval(function() {
            remaining--;
            if (remaining <= 0) {
                clearInterval(countdownInterval);
                closePopup();
            } else if (countdownNumber) {
                countdownNumber.textContent = remaining;
            }
        }, 1000);
    }

    if (!shouldShowPopup()) {
        overlay.remove();
        return;
    }

    setTimeout(function() {
        markPopupShown();
        overlay.classList.add('show');
        trackPopupView(popupId);
        startAutoCloseCountdown();
    }, showDelaySeconds * 1000);

    function closePopup() {
        overlay.classList.remove('show');

        if (countdownInterval) {
            clearInterval(countdownInterval);
        }

        setTimeout(function() {
            if (overlay && overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
        }, 260);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closePopup);
    }

    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closePopup();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.classList.contains('show')) {
            closePopup();
        }
    });

    window.trackPopupClick = function(adId) {
        fetch(@json(url('/ads/track-click')) + '/' + adId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).catch(function() {});
    };
})();
</script>
@endif
