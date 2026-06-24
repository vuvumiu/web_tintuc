@php
    $menuBrand = $adminShell['brand'] ?? [];
    $menuBrandName = $menuBrand['name'] ?? ($brandName ?? 'SCF corporation');
    $menuBrandText = $menuBrand['text'] ?? ($brandText ?? 'VN•XPRESS');
    $menuBrandText = str_replace(['●', '•'], '•', $menuBrandText);
    $menuBrandInitial = $menuBrand['initial'] ?? ($brandInitial ?? 'VN');
    $menuBrandLogoType = $menuBrand['logo_type'] ?? ($brandLogoType ?? 'text');
    $menuBrandLogoUrl = $menuBrand['logo_url'] ?? ($brandLogoUrl ?? '');
    $menuBrandTextHtml = str_replace('•', '<span>•</span>', e($menuBrandText));

    $menuUser = auth()->user();
    $menuUserName = $welcomeName ?? data_get($adminShell, 'user.name', $menuUser?->fullname ?: $menuUser?->username ?: 'Quản trị viên');
    $menuUserInitial = $initials ?? data_get($adminShell, 'user.initial', mb_strtoupper(mb_substr($menuUserName, 0, 1)));
    $menuUserRole = $userRole ?? data_get($adminShell, 'user.role_label', ($isAdmin ?? false) ? 'ADMIN' : 'STAFF');
    $menuIsAdmin = isset($isAdmin) ? (bool) $isAdmin : (bool) data_get($adminShell, 'user.is_admin', false);

    $badge = fn ($count) => $count > 99 ? '99+' : $count;
    $canAny = fn (array $permissions) => $menuUser && $menuUser->hasAnyPermission($permissions);

    $canAdminManager = $canAny(['admin-manager.list', 'admin-manager.create', 'admin-manager.edit', 'admin-manager.delete', 'author.list', 'author.manage']);
    $canMember = $canAny(['member.list', 'member.edit', 'member.delete', 'member.lock']);
    $canPage = $canAny(['page.manage']);
    $canTag = $canAny(['tag.list', 'tag.create', 'tag.edit', 'tag.delete']);
    $canNews = $canAny(['news.list', 'news.create', 'news.edit', 'news.delete', 'news.preview', 'news.approve', 'category.list', 'category.create', 'category.edit', 'category.delete']);
    $canAds = $canAny(['ads.manage', 'slider.manage']);
    $canFeatured = $canAny(['featured.manage']);
    $canSocial = $canAny(['social.manage']);
    $canNewsletter = $canAny(['newsletter.list', 'newsletter.export']);
    $canContact = $canAny(['contact.list', 'contact.reply']);
    $canComment = $canAny(['comment.list', 'comment.delete', 'comment.hide', 'comment.moderate']);
    $canAi = $menuIsAdmin;
    $canTicker = $menuIsAdmin || $menuUser?->isStaff();

    $aiActive = request()->is('admin/ai', 'admin/ai/*');
@endphp

<aside class="sidebar admin-unified-sidebar" id="adminSidebar">
    <a class="sidebar-logo" href="{{ url('admin/home') }}" aria-label="{{ $menuBrandName }}">
        @if($menuBrandLogoType === 'image' && $menuBrandLogoUrl !== '')
            <div class="logo-icon logo-icon-image">
                <img src="{{ $menuBrandLogoUrl }}" alt="{{ $menuBrandName }}">
            </div>
        @else
            <div class="logo-icon">{{ $menuBrandInitial }}</div>
        @endif
        <div class="logo-text">
            <div class="brand">{!! $menuBrandTextHtml !!}</div>
            <div class="sub">{{ $menuBrandName }}</div>
        </div>
    </a>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Hệ thống</div>
        <a class="nav-item {{ $dashboardActive ? 'active' : '' }}" href="{{ url('admin/home') }}">
            <span class="icon"><i class="fas fa-th-large"></i></span>
            <span>Dashboard</span>
        </a>

        @if($canAdminManager)
            <button class="nav-item has-submenu {{ $adminManagerOpen ? 'active is-open' : '' }}" type="button" data-submenu="admin-manager" aria-expanded="{{ $adminManagerOpen ? 'true' : 'false' }}">
                <span class="icon"><i class="fas fa-user-tie"></i></span>
                <span>Nhân viên &amp; Tác giả</span>
                <span class="submenu-arrow"><i class="fas fa-chevron-down"></i></span>
            </button>
            <div class="submenu {{ $adminManagerOpen ? 'is-open' : '' }}" id="submenu-admin-manager">
                @if($canAny(['author.list', 'author.manage', 'admin-manager.list', 'admin-manager.edit']))
                    <a class="submenu-item {{ $authorListActive ? 'active' : '' }}" href="{{ url('admin/authors/list') }}">
                        <span class="submenu-dot"></span>
                        <span>Danh sách tác giả</span>
                    </a>
                @endif
                @if($canAny(['admin-manager.list', 'admin-manager.edit', 'admin-manager.delete']))
                    <a class="submenu-item {{ $adminManagerListActive ? 'active' : '' }}" href="{{ url('admin/admin-manager/list') }}">
                        <span class="submenu-dot"></span>
                        <span>Nhân viên nội bộ</span>
                    </a>
                @endif
                @if($canAny(['admin-manager.create']))
                    <a class="submenu-item {{ $adminManagerAddActive ? 'active' : '' }}" href="{{ url('admin/admin-manager/add') }}">
                        <span class="submenu-dot"></span>
                        <span>Thêm nhân viên</span>
                    </a>
                @endif
            </div>
        @endif

        @if($canMember)
            <a class="nav-item {{ $memberOpen ? 'active' : '' }}" href="{{ url('admin/member/list') }}">
                <span class="icon"><i class="fas fa-users"></i></span>
                <span>Quản lý thành viên</span>
            </a>
        @endif

        @if($menuIsAdmin)
            <a class="nav-item {{ $systemActive ? 'active' : '' }}" href="{{ url('admin/system') }}">
                <span class="icon"><i class="fas fa-cogs"></i></span>
                <span>Cấu hình hệ thống</span>
            </a>
        @endif

        @if($canAi)
            <a class="nav-item {{ $aiActive ? 'active' : '' }}" href="{{ url('admin/ai/dashboard') }}">
                <span class="icon"><i class="fas fa-robot"></i></span>
                <span>AI Tools</span>
            </a>
        @endif
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Nội dung</div>

        @if($canPage)
            <a class="nav-item {{ $pageActive ? 'active' : '' }}" href="{{ url('admin/page/list') }}">
                <span class="icon"><i class="far fa-file-alt"></i></span>
                <span>Quản lý trang</span>
            </a>
        @endif

        @if($canTag)
            <a class="nav-item {{ $tagActive ? 'active' : '' }}" href="{{ url('admin/tag/list') }}">
                <span class="icon"><i class="fas fa-tags"></i></span>
                <span>Quản lý Tags</span>
            </a>
        @endif

        @if($canNews)
            <button class="nav-item has-submenu {{ $newsOpen ? 'active is-open' : '' }}" type="button" data-submenu="news" aria-expanded="{{ $newsOpen ? 'true' : 'false' }}">
                <span class="icon"><i class="far fa-newspaper"></i></span>
                <span>Quản lý tin tức</span>
                @if($newsTotal > 0)
                    <span class="nav-badge">{{ $badge($newsTotal) }}</span>
                @endif
                <span class="submenu-arrow"><i class="fas fa-chevron-down"></i></span>
            </button>
            <div class="submenu {{ $newsOpen ? 'is-open' : '' }}" id="submenu-news">
                @if($canAny(['news.list', 'news.edit', 'news.delete', 'news.preview']))
                    <a class="submenu-item {{ $newsListActive ? 'active' : '' }}" href="{{ url('admin/news/list') }}">
                        <span class="submenu-dot"></span>
                        <span>Danh sách bài viết</span>
                    </a>
                @endif
                @if($canAny(['news.create']))
                    <a class="submenu-item {{ $newsAddActive ? 'active' : '' }}" href="{{ url('admin/news/add') }}">
                        <span class="submenu-dot"></span>
                        <span>Thêm bài viết</span>
                    </a>
                @endif
                @if($canAny(['category.list', 'category.create', 'category.edit', 'category.delete']))
                    <a class="submenu-item {{ $newsCategoryActive ? 'active' : '' }}" href="{{ url('admin/news_cat/list') }}">
                        <span class="submenu-dot"></span>
                        <span>Danh mục tin</span>
                    </a>
                @endif
                @if($canAny(['news.approve']))
                    <a class="submenu-item {{ $newsApprovalActive ? 'active' : '' }}" href="{{ url('admin/news-approval/queue') }}">
                        <span class="submenu-dot"></span>
                        <span>Chờ duyệt</span>
                    </a>
                @endif
                @if($canAny(['news.list', 'news.create', 'news.edit', 'news.approve']))
                    <a class="submenu-item {{ $newsDraftsActive ? 'active' : '' }}" href="{{ url('admin/news-approval/drafts') }}">
                        <span class="submenu-dot"></span>
                        <span>Bản nháp</span>
                    </a>
                @endif
            </div>
        @endif

        @if($canAds)
            <a class="nav-item {{ $adsActive ? 'active' : '' }}" href="{{ url('admin/ads/list') }}">
                <span class="icon"><i class="fas fa-bullhorn"></i></span>
                <span>Quản lý popup QC</span>
            </a>
        @endif

        @if($canFeatured)
            <a class="nav-item {{ $featuredActive ? 'active' : '' }}" href="{{ url('admin/featured/list') }}">
                <span class="icon"><i class="fas fa-star"></i></span>
                <span>Bài viết nổi bật</span>
            </a>
        @endif

        @if($canTicker)
            <a class="nav-item {{ $tickerActive ? 'active' : '' }}" href="{{ url('admin/ticker/list') }}">
                <span class="icon"><i class="fas fa-fire"></i></span>
                <span>Quản lý Tin nóng</span>
            </a>
        @endif

        @if($canSocial)
            <a class="nav-item {{ $socialActive ? 'active' : '' }}" href="{{ url('admin/social/list') }}">
                <span class="icon"><i class="fas fa-share-alt"></i></span>
                <span>Quản lý mạng XH</span>
            </a>
        @endif
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-label">Phản hồi</div>

        @if($canNewsletter)
            <a class="nav-item {{ $newsletterActive ? 'active' : '' }}" href="{{ url('admin/newsletter/list') }}">
                <span class="icon"><i class="far fa-envelope"></i></span>
                <span>Nhận tin KM</span>
                @if($newsletterCount > 0)
                    <span class="nav-badge">{{ $badge($newsletterCount) }}</span>
                @endif
            </a>
        @endif

        @if($canContact)
            <a class="nav-item {{ $contactActive ? 'active' : '' }}" href="{{ url('admin/contact/list') }}">
                <span class="icon"><i class="far fa-address-book"></i></span>
                <span>Quản lý liên hệ</span>
                @if($contactCount > 0)
                    <span class="nav-badge">{{ $badge($contactCount) }}</span>
                @endif
            </a>
        @endif

        @if($canComment)
            <a class="nav-item {{ $commentActive ? 'active' : '' }}" href="{{ url('admin/comment/list') }}">
                <span class="icon"><i class="far fa-comments"></i></span>
                <span>Quản lý bình luận</span>
                @if($commentCount > 0)
                    <span class="nav-badge">{{ $badge($commentCount) }}</span>
                @endif
            </a>

            <a class="nav-item {{ $ratingActive ? 'active' : '' }}" href="{{ url('admin/rating/list') }}">
                <span class="icon"><i class="fas fa-star-half-alt"></i></span>
                <span>Đánh giá sao</span>
            </a>
        @endif
    </div>

    <div class="sidebar-bottom">
        <div class="avatar">{{ $menuUserInitial }}</div>
        <div class="user-info">
            <div class="name">{{ $menuUserName }}</div>
            <div class="role">{{ $menuUserRole }}</div>
        </div>
    </div>
</aside>
