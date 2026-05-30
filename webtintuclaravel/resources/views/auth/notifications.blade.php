@extends('front.template.master')
@section('title', 'Thông báo')
@section('content')

<style>
.notif-page {
    padding: 0 0 60px;
}

/* ── Hero banner ── */
.notif-hero {
    background: linear-gradient(135deg, #24313d 0%, #3a4a5c 100%);
    padding: 28px 24px;
    border-radius: 0 0 24px 24px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}
.notif-hero::before {
    content: "";
    position: absolute;
    top: -30px; right: -30px;
    width: 160px; height: 160px;
    background: rgba(201,168,76,.08);
    border-radius: 50%;
}
.notif-hero-inner {
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    z-index: 1;
}
.notif-hero-icon {
    width: 56px; height: 56px;
    background: rgba(201,168,76,.15);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 0 0 8px rgba(201,168,76,.07);
}
.notif-hero-icon i { font-size: 1.4rem; color: var(--accent); }
.notif-hero-title { color: #fff; font-size: 1.2rem; font-weight: 800; margin-bottom: 4px; }
.notif-hero-desc { color: rgba(255,255,255,.6); font-size: .82rem; margin: 0; }

/* ── Action bar ── */
.notif-action-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.notif-count-label {
    font-size: .85rem;
    color: rgba(255,255,255,0.5);
}
.notif-count-label strong { color: var(--accent); }
.notif-mark-all-btn {
    background: rgba(201,168,76,.1);
    border: 1px solid rgba(201,168,76,.3);
    color: var(--accent);
    padding: 7px 16px;
    border-radius: 8px;
    font-size: .82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.notif-mark-all-btn:hover {
    background: var(--accent);
    color: var(--accent-dark);
}

/* ── Notification list ── */
.notif-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.notif-item {
    background: rgba(255,255,255,0.03);
    border: 0.5px solid rgba(255,255,255,0.07);
    border-radius: 12px;
    padding: 18px 20px;
    transition: all .2s;
    border-left: 3px solid transparent;
}
.notif-item:hover {
    border-color: rgba(201,168,76,.25);
    border-left-color: var(--accent);
    background: rgba(255,255,255,0.05);
}
.notif-item.is-unread {
    border-left-color: var(--accent);
    background: rgba(201,168,76,.04);
    border-color: rgba(201,168,76,.15);
}
.notif-item.is-unread:hover {
    border-color: rgba(201,168,76,.3);
}

.notif-item-header {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}
.notif-dot-wrap {
    flex-shrink: 0;
    padding-top: 6px;
}
.notif-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: transparent;
    display: block;
}
.notif-item.is-unread .notif-dot {
    background: var(--accent);
    box-shadow: 0 0 0 3px rgba(201,168,76,.2);
}
.notif-icon-wrap {
    flex-shrink: 0;
    width: 38px; height: 38px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem;
}
.notif-icon-color-info { background: rgba(59,130,246,.15); color: #60a5fa; }
.notif-icon-color-success { background: rgba(34,197,94,.15); color: #4ade80; }
.notif-icon-color-warning { background: rgba(245,158,11,.15); color: #fbbf24; }
.notif-icon-color-danger { background: rgba(239,68,68,.15); color: #f87171; }
.notif-icon-color-default { background: rgba(201,168,76,.15); color: var(--accent); }

.notif-body {
    flex: 1;
    min-width: 0;
}
.notif-title {
    font-size: .9rem;
    font-weight: 700;
    color: rgba(255,255,255,.9);
    margin-bottom: 4px;
    line-height: 1.4;
}
.notif-item.is-unread .notif-title {
    color: #fff;
}
.notif-content {
    font-size: .82rem;
    color: rgba(255,255,255,.4);
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.notif-time {
    font-size: .75rem;
    color: rgba(255,255,255,.25);
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.notif-time i { font-size: .7rem; }

.notif-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    margin-top: 10px;
}
.notif-action-link {
    background: rgba(201,168,76,.12);
    color: var(--accent);
    border: none;
    padding: 5px 12px;
    border-radius: 6px;
    font-size: .75rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.notif-action-link:hover {
    background: var(--accent);
    color: var(--accent-dark);
}
.notif-action-mark {
    background: rgba(34,197,94,.1);
    color: #4ade80;
    border: 1px solid rgba(34,197,94,.2);
    padding: 5px 12px;
    border-radius: 6px;
    font-size: .75rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.notif-action-mark:hover {
    background: rgba(34,197,94,.2);
    color: #86efac;
}
.notif-action-delete {
    background: rgba(239,68,68,.1);
    color: #f87171;
    border: 1px solid rgba(239,68,68,.2);
    padding: 5px 12px;
    border-radius: 6px;
    font-size: .75rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.notif-action-delete:hover {
    background: rgba(239,68,68,.2);
    color: #fca5a5;
}

/* ── Empty state ── */
.notif-empty {
    text-align: center;
    padding: 60px 24px;
    background: rgba(255,255,255,0.02);
    border: 0.5px solid rgba(255,255,255,0.07);
    border-radius: 16px;
}
.notif-empty-icon {
    width: 80px; height: 80px;
    background: rgba(255,255,255,0.03);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    border: 1px solid rgba(255,255,255,0.06);
}
.notif-empty-icon i { font-size: 2rem; color: rgba(255,255,255,.2); }
.notif-empty h3 { font-size: 1.1rem; font-weight: 700; color: rgba(255,255,255,.6); margin-bottom: 8px; }
.notif-empty p { font-size: .85rem; color: rgba(255,255,255,.3); }

/* ── Pagination ── */
.notif-pagination {
    margin-top: 24px;
    display: flex;
    justify-content: center;
}

/* ── Responsive ── */
@media (max-width: 767px) {
    .notif-item-header { flex-direction: column; gap: 10px; }
    .notif-dot-wrap { display: none; }
    .notif-actions { flex-wrap: wrap; }
}
</style>

<div class="contact_wrap notif-page">

    {{-- Hero Header --}}
    <div class="notif-hero">
        <div class="container">
            <div class="notif-hero-inner">
                <div class="notif-hero-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <h1 class="notif-hero-title">Thông báo</h1>
                    <p class="notif-hero-desc">Theo dõi các hoạt động trên tài khoản của bạn</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        {{-- Alert --}}
        @if(session('flash_message'))
            <div class="alert alert-success" style="margin-bottom:20px;">
                <i class="fas fa-check-circle mr-2"></i>{{ session('flash_message') }}
            </div>
        @endif

        {{-- Filter Tabs --}}
        @php
            $currentFilter = request('type', 'all');
        @endphp
        <div class="notif-filter-tabs" style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
            <a href="{{ url('/thong-bao?type=all' . (request('unread_only') ? '&unread_only=1' : '')) }}"
               class="notif-tab-btn {{ $currentFilter === 'all' ? 'active' : '' }}">
                <i class="fas fa-list"></i> Tất cả
            </a>
            <a href="{{ url('/thong-bao?type=comment' . (request('unread_only') ? '&unread_only=1' : '')) }}"
               class="notif-tab-btn {{ $currentFilter === 'comment' ? 'active' : '' }}">
                <i class="fas fa-comment"></i> Bình luận
            </a>
            <a href="{{ url('/thong-bao?type=vote' . (request('unread_only') ? '&unread_only=1' : '')) }}"
               class="notif-tab-btn {{ $currentFilter === 'vote' ? 'active' : '' }}">
                <i class="fas fa-thumbs-up"></i> Đánh giá
            </a>
            <a href="{{ url('/thong-bao?type=news' . (request('unread_only') ? '&unread_only=1' : '')) }}"
               class="notif-tab-btn {{ $currentFilter === 'news' ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i> Bài viết
            </a>
            <a href="{{ url('/thong-bao?type=all&unread_only=1') }}"
               class="notif-tab-btn {{ request('unread_only') ? 'active' : '' }}"
               style="margin-left:auto;">
                <i class="fas fa-bell"></i> Chưa đọc
                @if($unreadCount > 0)
                    <span class="notif-tab-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </a>
        </div>

        <style>
        .notif-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 8px;
            background: rgba(255,255,255,0.04);
            border: 0.5px solid rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.5);
            font-size: .82rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .15s;
        }
        .notif-tab-btn:hover {
            background: rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.8);
        }
        .notif-tab-btn.active {
            background: rgba(201,168,76,.15);
            border-color: rgba(201,168,76,.4);
            color: var(--accent);
        }
        .notif-tab-badge {
            background: #e74c3c;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 999px;
            min-width: 16px;
            text-align: center;
        }
        </style>

        {{-- Action bar --}}
        @if(isset($notifications) && $notifications->count() > 0)
            <div class="notif-action-bar">
                <span class="notif-count-label">
                    <strong>{{ $notifications->total() ?? $notifications->count() }}</strong> thông báo
                    @if($unreadCount > 0)
                        &bull; <strong style="color:var(--accent);">{{ $unreadCount }}</strong> chưa đọc
                    @endif
                </span>
                @if($unreadCount > 0)
                    <a href="javascript:void(0)" id="notifMarkAllBtn" class="notif-mark-all-btn">
                        <i class="fas fa-check-double"></i> Đánh dấu tất cả đã đọc
                    </a>
                @endif
            </div>
        @endif

        @if(isset($notifications) && $notifications->count() > 0)
            <div class="notif-list">
                @foreach($notifications as $notif)
                @php
                    $isUnread = !$notif->is_read;
                    $typeIconClass = 'fas fa-bell';
                    $typeColor = 'default';
                    $notifType = $notif->type ?? '';
                    if ($notifType === 'comment_new') {
                        $typeIconClass = 'fas fa-comment-dots';
                        $typeColor = 'info';
                    } elseif ($notifType === 'comment_reply') {
                        $typeIconClass = 'fas fa-reply-all';
                        $typeColor = 'success';
                    } elseif ($notifType === 'news_approved') {
                        $typeIconClass = 'fas fa-check-circle';
                        $typeColor = 'success';
                    } elseif ($notifType === 'news_rejected') {
                        $typeIconClass = 'fas fa-times-circle';
                        $typeColor = 'danger';
                    } elseif ($notifType === 'system') {
                        $typeIconClass = 'fas fa-cog';
                        $typeColor = 'danger';
                    }
                @endphp
                <div class="notif-item {{ $isUnread ? 'is-unread' : '' }}"
                     id="notif-{{ $notif->id }}">
                    <div class="notif-item-header">
                        <div class="notif-dot-wrap">
                            <span class="notif-dot"></span>
                        </div>
                        <div class="notif-icon-wrap notif-icon-color-{{ $typeColor }}">
                            <i class="{{ $typeIconClass }}"></i>
                        </div>
                        <div class="notif-body">
                            <div class="notif-title">{{ $notif->title }}</div>
                            @if($notif->content)
                                <div class="notif-content">{{ Str::limit($notif->content, 120) }}</div>
                            @endif
                            <div class="notif-time">
                                <i class="fas fa-clock"></i>
                                {{ $notif->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    <div class="notif-actions">
                        @if($notif->link)
                            <a href="{{ $notif->link }}" class="notif-action-link">
                                <i class="fas fa-external-link-alt"></i> Xem
                            </a>
                        @endif
                        @if($isUnread)
                            <a href="{{ url('thong-bao/mark-read/' . $notif->id) }}" class="notif-action-mark" title="Đánh dấu đã đọc">
                                <i class="fas fa-check"></i> Đã đọc
                            </a>
                        @endif
                        <a href="{{ url('thong-bao/delete/' . $notif->id) }}" class="notif-action-delete"
                           onclick="return confirm('Xóa thông báo này?');" title="Xóa">
                            <i class="fas fa-trash-alt"></i> Xóa
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            @if(method_exists($notifications, 'links') && $notifications->lastPage() > 1)
                <div class="notif-pagination">
                    {{ $notifications->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <div class="notif-empty">
                <div class="notif-empty-icon">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <h3>Không có thông báo nào</h3>
                <p>Các thông báo về bình luận, đánh giá và hoạt động sẽ xuất hiện ở đây.</p>
            </div>
        @endif
    </div>
</div>

@stop

@section('scripts')
<script>
(function() {
    var btn = document.getElementById('notifMarkAllBtn');
    if (!btn) return;
    btn.addEventListener('click', function() {
        if (!confirm('Đánh dấu tất cả thông báo đã đọc?')) return;
        fetch('/api/notifications/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || '',
                'Content-Type': 'application/json'
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                document.querySelectorAll('.notif-item.is-unread').forEach(function(el) {
                    el.classList.remove('is-unread');
                    var markBtn = el.querySelector('.notif-action-mark');
                    if (markBtn) markBtn.remove();
                });
                var badgeCount = document.getElementById('notifCount');
                if (badgeCount) { badgeCount.style.display = 'none'; }
                btn.remove();
                var countLabel = document.querySelector('.notif-count-label');
                if (countLabel) {
                    countLabel.innerHTML = '<strong>{{ $notifications->total() }}</strong> thông báo';
                }
            }
        })
        .catch(function() { alert('Lỗi! Vui lòng thử lại.'); });
    });
})();
</script>
@endsection
