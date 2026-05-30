<nav class="vnx-nav">
    @php
        $brandName = $siteName ?? 'VNXpress';
        $brandLogoText = $logoText ?? 'VN●XPRESS';
        $useImageLogo = ($logoType ?? 'text') === 'image' && !empty($logo->Description);
        $brandLogoTextHtml = str_replace('●', '<span>●</span>', e($brandLogoText));
    @endphp

    {{-- Mobile toggle --}}
    <button class="vnx-nav-toggle" id="vnxMenuToggle" aria-label="Mở menu">
        <i class="fas fa-bars"></i>
    </button>

    {{-- Logo --}}
    @if($useImageLogo)
        <a href="{{ url('/') }}" class="vnx-nav-logo">
            <img src="{{ asset($logo->Description) }}" alt="{{ $brandName }}" style="height:40px; max-width:160px; object-fit:contain;">
        </a>
    @else
        <a href="{{ url('/') }}" class="vnx-nav-logo">{!! $brandLogoTextHtml !!}</a>
    @endif

    {{-- Navigation Links --}}
    <ul class="vnx-nav-links">
        @php
            $newsAliases = [];
            if (isset($NewsCategoriesGlobal)) {
                foreach ($NewsCategoriesGlobal as $nc) {
                    $newsAliases[] = $nc->Alias;
                }
            }
        @endphp

        @if(isset($Page) && count($Page) > 0)
            @foreach($Page as $v)
                @php
                    $mk = $v->menu_kind ?? \App\Models\Page::MENU_LINK;
                @endphp

                @if($mk === \App\Models\Page::MENU_HOME || ($mk === \App\Models\Page::MENU_LINK && $v->Alias === '/'))
                    <li>
                        <a href="{{ url('/') }}" title="{{ $v->Name }}" class="@yield('home')">
                            {!! $v->Font !== null && $v->Font !== '' ? $v->Font : e($v->Name) !!}
                        </a>
                    </li>

                @elseif($mk === \App\Models\Page::MENU_NEWS_CATEGORIES)
                    @if(isset($NewsCategoriesGlobal) && count($NewsCategoriesGlobal) > 0)
                        <li class="nav-dropdown">
                            <a href="javascript:void(0)" title="{{ $v->Name }}">
                                @if($v->Font !== null && $v->Font !== '')
                                    {!! $v->Font !!}
                                @endif
                                {{ $v->Name }}
                                <i class="fas fa-caret-down" style="margin-left:3px; font-size:12px;"></i>
                            </a>
                            <ul class="dropdown-menu-custom">
                                @foreach($NewsCategoriesGlobal as $cat)
                                    <li>
                                        <a href="{{ url('/' . $cat->Alias) }}" title="{{ $cat->Name }}" class="@yield($cat->Alias)">
                                            {{ $cat->Name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif

                @elseif($mk === \App\Models\Page::MENU_ROUTE && $v->Alias !== '' && $v->Alias !== '#')
                    <li>
                        <a href="{{ url('/' . ltrim($v->Alias, '/')) }}" title="{{ $v->Name }}" class="@yield($v->Alias)">
                            @if($v->Font !== null && $v->Font !== '')
                                {!! $v->Font !!}
                            @endif
                            {{ $v->Name }}
                        </a>
                    </li>

                @elseif($mk === \App\Models\Page::MENU_LINK && $v->Alias !== '/' && !in_array($v->Alias, $newsAliases, true))
                    <li>
                        <a href="{{ url('/' . ltrim($v->Alias, '/')) }}" title="{{ $v->Name }}" class="@yield($v->Alias)">
                            {{ $v->Name }}
                        </a>
                    </li>
                @endif
            @endforeach
        @endif
    </ul>

    {{-- Right Side: Search + User --}}
    <div class="vnx-nav-right">
        <form action="{{ url('tim-kiem') }}" method="GET" class="vnx-search-form">
            <div class="vnx-search-bar">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="6" cy="6" r="4.5" stroke="rgba(255,255,255,0.4)" stroke-width="1.2"/>
                    <line x1="9.5" y1="9.5" x2="13" y2="13" stroke="rgba(255,255,255,0.4)" stroke-width="1.2" stroke-linecap="round"/>
                </svg>
                <input type="text" placeholder="Tìm kiếm tin tức..." name="keyword" autocomplete="off"/>
            </div>
        </form>

        {{-- User area --}}
        <div class="vnx-nav-user">
            @guest
                <a href="{{ url('/dang-nhap') }}" class="vnx-nav-user-btn" title="Đăng nhập">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Đăng nhập</span>
                </a>
                <a href="{{ url('/dang-ky') }}" class="vnx-nav-user-btn vnx-nav-user-btn--accent" title="Đăng ký">
                    <i class="fas fa-user-plus"></i>
                    <span>Đăng ký</span>
                </a>
            @else
                @php
                    $unreadCount = \App\Models\Notification::where('user_id', Auth::id())
                        ->where('is_read', false)
                        ->count();
                @endphp
                <div class="vnx-notif-bell-wrapper" id="notifBell">
                    <button class="vnx-nav-user-btn vnx-notif-badge" id="notifBellBtn" title="Thông báo">
                        <i class="fas fa-bell"></i>
                        <span class="vnx-notif-count" id="notifCount" @if($unreadCount === 0) style="display:none" @endif>{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    </button>
                    <div class="vnx-notif-dropdown" id="notifDropdown">
                        <div class="vnx-notif-dropdown-header">
                            <span>Thông báo</span>
                            <a href="{{ url('/thong-bao') }}" class="vnx-notif-see-all">Xem tất cả</a>
                        </div>
                        <div class="vnx-notif-list" id="notifList">
                            <div class="vnx-notif-loading">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="position:relative;" id="userLoggedWrapper">
                    <a href="javascript:void(0)" class="vnx-nav-user-btn" id="userDropdownBtn" title="Tài khoản">
                        <i class="fas fa-user-circle"></i>
                        <span style="max-width:80px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ Auth::user()->username }}</span>
                    </a>

                    {{-- Dropdown overlay backdrop --}}
                    <div class="vnx-user-overlay" id="userOverlay"></div>

                    {{-- Dropdown menu --}}
                    <div class="vnx-user-dropdown" id="userDropdown" role="menu">
                        <div class="vnx-user-dropdown-header">
                            <div class="vnx-user-dropdown-name">
                                {{ Auth::user()->fullname ?? Auth::user()->username }}
                            </div>
                            <div class="vnx-user-dropdown-email">
                                {{ Auth::user()->email ?? '' }}
                            </div>
                        </div>
                        <nav class="vnx-user-dropdown-nav">
                            <a href="{{ url('/tai-khoan') }}" class="vnx-user-dropdown-item">
                                <i class="fas fa-user-cog"></i>
                                <span>Tài khoản của tôi</span>
                            </a>
                            <a href="{{ url('/bai-viet-yeu-thich') }}" class="vnx-user-dropdown-item">
                                <i class="fas fa-heart"></i>
                                <span>Bài viết đã lưu</span>
                            </a>
                            <a href="{{ url('/thong-bao') }}" class="vnx-user-dropdown-item">
                                <i class="fas fa-bell"></i>
                                <span>Thông báo</span>
                                @if($unreadCount > 0)
                                    <span style="margin-left:auto; background:var(--accent);color:var(--accent-dark);font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px;">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                                @endif
                            </a>
                        </nav>
                        <a href="{{ url('/dang-xuat') }}" class="vnx-user-dropdown-item vnx-user-dropdown-item--logout"
                           onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Đăng xuất</span>
                        </a>
                        <form id="logoutForm" action="{{ url('/dang-xuat') }}" method="GET" style="display:none;"></form>
                    </div>
                </div>
            @endguest
        </div>
    </div>
</nav>

<style>
/* ── Notification Bell Dropdown ── */
.vnx-notif-bell-wrapper {
    position: relative;
}
.vnx-notif-badge .vnx-notif-count {
    position: absolute;
    top: -4px;
    right: -6px;
    background: #e74c3c;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    min-width: 16px;
    height: 16px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 3px;
    line-height: 1;
}
.vnx-notif-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 340px;
    background: #1a2332;
    border: 0.5px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    z-index: 1000;
    overflow: hidden;
}
.vnx-notif-bell-wrapper.is-open .vnx-notif-dropdown {
    display: block;
}
.vnx-notif-dropdown-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 0.5px solid rgba(255,255,255,0.08);
    font-size: .85rem;
    font-weight: 700;
    color: rgba(255,255,255,0.9);
}
.vnx-notif-see-all {
    font-size: .75rem;
    font-weight: 600;
    color: var(--accent);
    text-decoration: none;
    transition: color .15s;
}
.vnx-notif-see-all:hover { color: #fff; }
.vnx-notif-list {
    max-height: 360px;
    overflow-y: auto;
}
.vnx-notif-loading {
    padding: 24px;
    text-align: center;
    color: rgba(255,255,255,0.4);
}
.vnx-notif-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 0.5px solid rgba(255,255,255,0.05);
    text-decoration: none;
    transition: background .15s;
    cursor: pointer;
}
.vnx-notif-item:hover { background: rgba(255,255,255,0.04); }
.vnx-notif-item:last-child { border-bottom: none; }
.vnx-notif-item.is-unread { background: rgba(201,168,76,.05); }
.vnx-notif-icon {
    width: 32px; height: 32px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: .8rem;
}
.vnx-notif-icon-info { background: rgba(59,130,246,.15); color: #60a5fa; }
.vnx-notif-icon-success { background: rgba(34,197,94,.15); color: #4ade80; }
.vnx-notif-icon-warning { background: rgba(245,158,11,.15); color: #fbbf24; }
.vnx-notif-icon-danger { background: rgba(239,68,68,.15); color: #f87171; }
.vnx-notif-icon-default { background: rgba(201,168,76,.15); color: var(--accent); }
.vnx-notif-icon-primary { background: rgba(99,102,241,.15); color: #a5b4fc; }
.vnx-notif-icon-secondary { background: rgba(107,114,128,.15); color: #9ca3af; }
.vnx-notif-body { flex: 1; min-width: 0; }
.vnx-notif-title {
    font-size: .8rem;
    font-weight: 600;
    color: rgba(255,255,255,.85);
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.vnx-notif-item.is-unread .vnx-notif-title { color: #fff; }
.vnx-notif-time {
    font-size: .7rem;
    color: rgba(255,255,255,.3);
    margin-top: 3px;
}
.vnx-notif-empty {
    padding: 28px 16px;
    text-align: center;
    color: rgba(255,255,255,.3);
    font-size: .8rem;
}
.vnx-notif-empty i { font-size: 1.4rem; display: block; margin-bottom: 8px; opacity: .5; }
.vnx-notif-item .vnx-notif-delete {
    background: none;
    border: none;
    color: rgba(255,255,255,.2);
    cursor: pointer;
    padding: 2px;
    font-size: .7rem;
    transition: color .15s;
    flex-shrink: 0;
}
.vnx-notif-item .vnx-notif-delete:hover { color: #f87171; }
</style>

<script>
(function() {
    var notificationUrls = {
        unread: @json(url('/api/notifications/unread')),
        read: @json(url('/api/notifications')),
        readAll: @json(url('/api/notifications/read-all'))
    };
    var bellWrapper = document.getElementById('notifBell');
    var bellBtn = document.getElementById('notifBellBtn');
    var notifList = document.getElementById('notifList');

    if (!bellWrapper || !bellBtn) return;

    var isOpen = false;
    var loaded = false;

    bellBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        isOpen = !isOpen;
        bellWrapper.classList.toggle('is-open', isOpen);
        if (isOpen && !loaded) {
            loaded = true;
            loadNotifications();
        }
    });

    document.addEventListener('pointerdown', function(e) {
        if (isOpen && !bellWrapper.contains(e.target)) {
            isOpen = false;
            bellWrapper.classList.remove('is-open');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
            isOpen = false;
            bellWrapper.classList.remove('is-open');
        }
    });

    function loadNotifications() {
        notifList.innerHTML = '<div class="vnx-notif-loading"><i class="fas fa-spinner fa-spin"></i></div>';
        fetch(notificationUrls.unread)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success || !data.notifications || data.notifications.length === 0) {
                    notifList.innerHTML = '<div class="vnx-notif-empty"><i class="fas fa-bell-slash"></i>Không có thông báo nào</div>';
                    updateBellBadge(data.success ? data.count : 0);
                    return;
                }
                var html = '';
                data.notifications.forEach(function(n) {
                    var unread = n.is_read ? '' : ' is-unread';
                    var link = n.link ? '<a href="' + escapeHtml(n.link) + '" class="vnx-notif-item' + unread + '" data-notif-id="' + n.id + '" data-link="' + escapeHtml(n.link) + '">' : '<div class="vnx-notif-item' + unread + '">';
                    var linkEnd = n.link ? '</a>' : '</div>';
                    html += link +
                        '<div class="vnx-notif-icon vnx-notif-icon-' + escapeHtml(n.color) + '">' +
                        '<i class="fas ' + escapeHtml(n.icon) + '"></i></div>' +
                        '<div class="vnx-notif-body">' +
                        '<div class="vnx-notif-title">' + escapeHtml(n.title) + '</div>' +
                        (n.content ? '<div style="font-size:.72rem;color:rgba(255,255,255,.4);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + escapeHtml(n.content) + '</div>' : '') +
                        '<div class="vnx-notif-time">' + escapeHtml(n.time) + '</div></div>' +
                        '<button class="vnx-notif-delete" title="Xóa" data-id="' + n.id + '" onclick="event.stopPropagation(); event.preventDefault(); deleteNotification(' + n.id + ', this);"><i class="fas fa-times"></i></button>' +
                        linkEnd;
                });
                notifList.innerHTML = html;

                notifList.querySelectorAll('.vnx-notif-item[data-notif-id]').forEach(function(item) {
                    item.addEventListener('click', function(e) {
                        var notifId = item.getAttribute('data-notif-id');
                        var link = item.getAttribute('data-link');
                        if (notifId && !item.classList.contains('is-unread')) {
                            window.location.href = link;
                            return;
                        }
                        markNotificationRead(notifId, function() {
                            window.location.href = link;
                        });
                    });
                });

                updateBellBadge(data.count);
            })
            .catch(function() {
                notifList.innerHTML = '<div class="vnx-notif-empty"><i class="fas fa-exclamation-triangle"></i>Lỗi tải thông báo</div>';
            });
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function markNotificationRead(notifId, callback) {
        fetch(notificationUrls.read + '/' + notifId + '/read', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Content-Type': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            updateBellBadgeFromServer();
            if (callback) callback();
        })
        .catch(function() {
            if (callback) callback();
        });
    }

    function deleteNotification(notifId, btn) {
        var item = btn.closest('.vnx-notif-item');
        fetch(notificationUrls.read + '/' + notifId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': getCsrfToken() }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                if (item) item.remove();
                updateBellBadge(data.count);
                var list = document.getElementById('notifList');
                if (list && list.children.length === 0) {
                    list.innerHTML = '<div class="vnx-notif-empty"><i class="fas fa-bell-slash"></i>Không có thông báo nào</div>';
                }
            }
        })
        .catch(function() {});
    }

    function updateBellBadge(count) {
        var badge = document.getElementById('notifCount');
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = '';
        } else {
            badge.style.display = 'none';
        }
    }

    function updateBellBadgeFromServer() {
        fetch(notificationUrls.unread)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) updateBellBadge(data.count);
            })
            .catch(function() {});
    }

    function getCsrfToken() {
        var el = document.querySelector('meta[name="csrf-token"]');
        return el ? el.getAttribute('content') : '';
    }

    function pollNotifications() {
        fetch(notificationUrls.unread)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    updateBellBadge(data.count);
                    if (isOpen) {
                        loadNotifications();
                    }
                }
            })
            .catch(function() {});
    }

    setInterval(pollNotifications, 15000);
})();
</script>

<script>
(function() {
    var toggle = document.getElementById('vnxMenuToggle');
    var links = document.querySelector('.vnx-nav-links');
    if (toggle && links) {
        toggle.addEventListener('click', function() {
            links.classList.toggle('open');
            var icon = toggle.querySelector('i');
            if (links.classList.contains('open')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }

    var isDesktop = window.matchMedia('(min-width: 993px)').matches;

    function initDropdown() {
        var dropdowns = document.querySelectorAll('.nav-dropdown');
        dropdowns.forEach(function(dropdown) {
            if (!isDesktop) {
                var trigger = dropdown.querySelector('a');
                if (trigger) {
                    trigger.addEventListener('click', function(e) {
                        e.preventDefault();
                        dropdowns.forEach(function(d) {
                            if (d !== dropdown) d.classList.remove('is-open');
                        });
                        dropdown.classList.toggle('is-open');
                    });
                }
            } else {
                var showTimer, hideTimer;

                dropdown.addEventListener('mouseenter', function() {
                    clearTimeout(hideTimer);
                    showTimer = setTimeout(function() {
                        dropdown.classList.add('is-open');
                    }, 100);
                });

                dropdown.addEventListener('mouseleave', function() {
                    clearTimeout(showTimer);
                    hideTimer = setTimeout(function() {
                        dropdown.classList.remove('is-open');
                    }, 150);
                });
            }
        });
    }

    initDropdown();

    window.addEventListener('resize', function() {
        var wasDesktop = isDesktop;
        isDesktop = window.matchMedia('(min-width: 993px)').matches;
        if (wasDesktop !== isDesktop) {
            document.querySelectorAll('.nav-dropdown').forEach(function(d) {
                d.classList.remove('is-open');
            });
            initDropdown();
        }
    });
})();

(function() {
    var wrapper = document.getElementById('userLoggedWrapper');
    var button = document.getElementById('userDropdownBtn');
    var dropdown = document.getElementById('userDropdown');
    var overlay = document.getElementById('userOverlay');

    if (!wrapper || !button || !dropdown) {
        return;
    }

    button.setAttribute('aria-haspopup', 'menu');
    button.setAttribute('aria-expanded', 'false');

    function setOpenState(isOpen) {
        wrapper.classList.toggle('is-open', isOpen);
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    function within(target, node) {
        return !!(target && node && node.contains(target));
    }

    button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        setOpenState(!wrapper.classList.contains('is-open'));
    }, true);

    if (overlay) {
        overlay.addEventListener('pointerdown', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            setOpenState(false);
        }, true);
    }

    document.addEventListener('pointerdown', function(e) {
        if (!wrapper.classList.contains('is-open')) {
            return;
        }

        if (within(e.target, button) || within(e.target, dropdown)) {
            return;
        }

        setOpenState(false);
    }, true);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            setOpenState(false);
        }
    });
})();
</script>
