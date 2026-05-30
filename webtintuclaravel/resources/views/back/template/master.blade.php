@php
    $brand = $adminShell['brand'] ?? [];
    $brandName = $brand['name'] ?? 'VUvumiu';
    $brandText = $brand['text'] ?? 'VN●XPRESS';
    $brandShort = $brand['short'] ?? 'VUMU';
    $brandInitial = $brand['initial'] ?? strtoupper(substr($brandShort, 0, 1));
    $brandLogoType = $brand['logo_type'] ?? 'text';
    $brandLogoUrl = $brand['logo_url'] ?? '';
    $brandFaviconUrl = $brand['favicon_url'] ?? asset('favicon.ico');
    $brandTextHtml = str_replace('●', '<span>●</span>', e($brandText));

    $decodeSection = fn ($value) => html_entity_decode(trim((string) $value), ENT_QUOTES, 'UTF-8');
    $title = $decodeSection($__env->yieldContent('title', 'Quản trị website'));
    $heading = $decodeSection($__env->yieldContent('heading', $title !== '' ? $title : 'Dashboard'));
    $breadcrumb = $decodeSection($__env->yieldContent('breadcrumb', $heading !== '' ? $heading : 'Tổng quan'));
    $subheading = $decodeSection($__env->yieldContent('subheading', 'Quản lý nội dung, cấu hình và phản hồi của toàn bộ hệ thống.'));
    $hideAdminHeading = trim($__env->yieldContent('hide_admin_heading', '0')) === '1';

    $user = auth()->user();
    $notificationCount = (int) data_get($adminShell, 'counts.notifications_unread', 0);
    $newsTotal = (int) data_get($adminShell, 'counts.news_total', 0);
    $newsletterCount = (int) data_get($adminShell, 'counts.newsletter_unreviewed', 0);
    $contactCount = (int) data_get($adminShell, 'counts.contacts_new', 0);
    $commentCount = (int) data_get($adminShell, 'counts.comments_pending', 0);
    $isAdmin = (bool) data_get($adminShell, 'user.is_admin', false);

    $adminDashboardCss = 'css/admin-dashboard.css';
    $adminDashboardCssVersion = @filemtime(public_path($adminDashboardCss)) ?: time();

    // Sidebar partial shared variables
    $welcomeName = data_get($adminShell, 'user.name', $user->fullname ?: $user->username ?: 'Admin');
    $initials = data_get($adminShell, 'user.initial', strtoupper(substr($welcomeName, 0, 1)));
    $userRole = data_get($adminShell, 'user.role_label', ($isAdmin ? 'ADMIN' : 'STAFF'));

    $is = fn (...$patterns) => request()->is(...$patterns);

    $dashboardActive = $is('admin/home');
    $adminManagerOpen = $is('admin/admin-manager', 'admin/admin-manager/*', 'admin/authors', 'admin/authors/*');
    $adminManagerListActive = $is('admin/admin-manager/list', 'admin/admin-manager/edit/*', 'admin/admin-manager/stats/*');
    $adminManagerAddActive = $is('admin/admin-manager/add');
    $authorListActive = $is('admin/authors', 'admin/authors/*');
    $memberOpen = $is('admin/member', 'admin/member/*');
    $memberListActive = $is('admin/member/list', 'admin/member/view/*', 'admin/member/edit/*');
    $systemActive = $is('admin/system');

    $pageActive = $is('admin/page', 'admin/page/*');
    $tagActive = $is('admin/tag', 'admin/tag/*');
    $newsOpen = $is('admin/news', 'admin/news/*', 'admin/news_cat', 'admin/news_cat/*', 'admin/news-approval', 'admin/news-approval/*');
    $newsListActive = $is('admin/news/list', 'admin/news/edit/*', 'admin/news/preview/*');
    $newsAddActive = $is('admin/news/add');
    $newsCategoryActive = $is('admin/news_cat', 'admin/news_cat/*');
    $newsApprovalActive = $is('admin/news-approval/queue', 'admin/news-approval/approve/*', 'admin/news-approval/reject/*');
    $newsDraftsActive = $is('admin/news-approval/drafts');
    $sliderActive = $is('admin/slider', 'admin/slider/*');
    $adsActive = $is('admin/ads', 'admin/ads/*', 'admin/slider', 'admin/slider/*');
    $featuredActive = $is('admin/featured', 'admin/featured/*');
    $tickerActive = $is('admin/ticker', 'admin/ticker/*');
    $socialActive = $is('admin/social', 'admin/social/*');

    $newsletterActive = $is('admin/newsletter', 'admin/newsletter/*');
    $contactActive = $is('admin/contact', 'admin/contact/*');
    $commentActive = $is('admin/comment', 'admin/comment/*');
    $ratingActive = $is('admin/rating', 'admin/rating/*');

    $searchKeyword = trim((string) request('keyword', ''));
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <link rel="icon" href="{{ $brandFaviconUrl }}">
    <link rel="shortcut icon" href="{{ $brandFaviconUrl }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}">
    <link rel="stylesheet" href="{{ asset($adminDashboardCss) }}?v={{ $adminDashboardCssVersion }}">
    @stack('styles')
</head>
<body class="vu-admin-body{{ request()->is('admin/home') ? ' is-dashboard' : '' }}">
<div class="vu-overlay" data-sidebar-close></div>

<div class="vu-admin-shell">
    @include('back.template.sidebar')

    <div class="main">
        <div class="topbar">
            <div class="topbar-left">
                <button class="icon-btn vu-mobile-trigger" type="button" data-sidebar-toggle aria-label="Mở menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="topbar-title-wrap">
                    <div class="topbar-title-line">
                        <div class="topbar-title">{{ $heading }}</div>
                        <div class="topbar-date">{{ data_get($adminShell, 'current_date', now()->locale('vi')->isoFormat('dddd, D [Tháng] M, YYYY')) }}</div>
                    </div>
                    <div class="topbar-breadcrumb">Trang chủ › <span>{{ $breadcrumb }}</span></div>
                </div>
            </div>

            <div class="topbar-right">
                <form class="t-search" action="{{ data_get($adminShell, 'search_action', url('admin/news/list')) }}" method="GET">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><circle cx="5.5" cy="5.5" r="4" stroke="rgba(255,255,255,0.3)" stroke-width="1.2"/><line x1="8.5" y1="8.5" x2="12" y2="12" stroke="rgba(255,255,255,0.3)" stroke-width="1.2" stroke-linecap="round"/></svg>
                    <input type="text" name="keyword" value="{{ $searchKeyword }}" placeholder="Tìm kiếm nội dung...">
                </form>

                <div class="topbar-dropdown">
                    <button class="icon-btn" type="button" data-topbar-toggle="notifications" aria-label="Thông báo">
                        <i class="far fa-bell"></i>
                        <span id="adminNotifDot" class="notif-dot" style="{{ $notificationCount > 0 ? '' : 'display:none;' }}"></span>
                    </button>

                    <div class="topbar-menu-dropdown" data-topbar-menu="notifications">
                        <div class="topbar-menu-dropdown__head">
                            <strong>Thong bao</strong>
                            <div class="topbar-breadcrumb"><span id="adminNotifUnreadText">{{ $notificationCount }}</span> chua doc</div>
                        </div>
                        <div class="topbar-menu-dropdown__body" id="adminNotifList">
                            @forelse(($adminShell['notifications'] ?? []) as $notification)
                                <a class="topbar-menu-item" href="{{ $notification->link ?: url('admin/notifications/mark-read/' . $notification->id) }}">
                                    <div class="topbar-menu-item__icon">
                                        <i class="fas {{ \App\Models\Notification::typeIcon($notification->type) }}"></i>
                                    </div>
                                    <div>
                                        <div class="topbar-menu-item__title">{{ $notification->title }}</div>
                                        <div class="topbar-menu-item__meta">{{ optional($notification->created_at)->diffForHumans() }}</div>
                                    </div>
                                </a>
                            @empty
                                <div class="topbar-menu-item">
                                    <div class="topbar-menu-item__icon"><i class="fas fa-inbox"></i></div>
                                    <div>
                                        <div class="topbar-menu-item__title">Chua co thong bao nao</div>
                                        <div class="topbar-menu-item__meta">He thong se hien thi thong bao moi tai day.</div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <div class="topbar-menu-dropdown__foot">
                            <a class="topbar-menu-link" href="{{ url('admin/notifications') }}">Xem tat ca thong bao</a>
                        </div>
                    </div>
                </div>

                <a class="btn btn-outline topbar-action" href="{{ url('/') }}" target="_blank" rel="noopener">
                    <i class="fas fa-globe"></i>
                    <span>Xem website</span>
                </a>

                <a class="btn btn-primary topbar-action topbar-action-primary" href="{{ url('admin/news/add') }}">
                    <i class="fas fa-plus"></i>
                    <span>Thêm bài viết</span>
                </a>

                <div class="topbar-dropdown">
                    <button class="user-chip" type="button" data-topbar-toggle="user" aria-label="Tài khoản">
                        <div class="uc-avatar">{{ data_get($adminShell, 'user.initial', 'A') }}</div>
                        <span class="uc-name">{{ data_get($adminShell, 'user.name', 'Quản trị viên') }}</span>
                        @if($isAdmin)
                            <span class="admin-badge">ADMIN</span>
                        @endif
                    </button>

                    <div class="topbar-menu-dropdown topbar-menu-dropdown--user" data-topbar-menu="user">
                        <div class="topbar-menu-dropdown__head">
                            <strong>{{ data_get($adminShell, 'user.name', 'Quản trị viên') }}</strong>
                            <div class="topbar-breadcrumb">{{ data_get($adminShell, 'user.role_label', 'Quản trị viên') }}</div>
                            <div class="topbar-menu-item__meta">{{ data_get($adminShell, 'user.email', '') }}</div>
                        </div>
                        <div class="topbar-menu-dropdown__body">
                            <a class="topbar-menu-item" href="{{ url('admin/admin-manager/profile') }}">
                                <div class="topbar-menu-item__icon"><i class="fas fa-user-edit"></i></div>
                                <div>
                                    <div class="topbar-menu-item__title">Thông tin tài khoản</div>
                                    <div class="topbar-menu-item__meta">Cập nhật hồ sơ và thông tin đăng nhập.</div>
                                </div>
                            </a>
                            <a class="topbar-menu-item" href="{{ url('/') }}" target="_blank" rel="noopener">
                                <div class="topbar-menu-item__icon"><i class="fas fa-globe"></i></div>
                                <div>
                                    <div class="topbar-menu-item__title">Xem website</div>
                                    <div class="topbar-menu-item__meta">Mở trang tin chính ở tab mới.</div>
                                </div>
                            </a>
                            <a class="topbar-menu-item" href="{{ url('admin/logout') }}">
                                <div class="topbar-menu-item__icon"><i class="fas fa-sign-out-alt"></i></div>
                                <div>
                                    <div class="topbar-menu-item__title">Đăng xuất</div>
                                    <div class="topbar-menu-item__meta">Kết thúc phiên làm việc hiện tại.</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            @if(!$hideAdminHeading)
                <div class="vu-page-heading">
                    <div>
                        <h1>{{ $heading }}</h1>
                        <p>{{ $subheading }}</p>
                    </div>
                    @hasSection('page_actions')
                        <div>
                            @yield('page_actions')
                        </div>
                    @endif
                </div>
            @endif

            @php
                $flashMessage = session('success') ?: session('flash_message');
                $flashLevel = session('success') ? 'success' : session('flash_level', 'info');
            @endphp

            @if($flashMessage)
                <div class="vu-alert vu-alert-{{ $flashLevel === 'error' ? 'danger' : $flashLevel }}">
                    <i class="fas fa-info-circle"></i>
                    <span>{{ $flashMessage }}</span>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="vu-alert vu-alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Dữ liệu chưa hợp lệ.</strong>
                        <div>{{ $errors->first() }}</div>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>

        <footer class="main-footer">
            <strong>&copy; {{ now()->year }} {{ $brandName }}</strong>
            <span>Quản trị nội dung và vận hành hệ thống.</span>
        </footer>
    </div>
</div>

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
<script src="{{ asset('ckeditor4.14/ckeditor.js') }}"></script>

<script>
    if (window.jQuery && $.widget && $.ui && $.ui.button) {
        $.widget.bridge('uibutton', $.ui.button);
    }

    (function () {
        var body = document.body;
        var sidebarToggle = document.querySelector('[data-sidebar-toggle]');
        var sidebarClose = document.querySelector('[data-sidebar-close]');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function () {
                body.classList.toggle('vu-sidebar-open');
            });
        }

        if (sidebarClose) {
            sidebarClose.addEventListener('click', function () {
                body.classList.remove('vu-sidebar-open');
            });
        }

        document.querySelectorAll('[data-sidebar-group]').forEach(function (button) {
            button.addEventListener('click', function () {
                var key = button.getAttribute('data-sidebar-group');
                var panel = document.querySelector('[data-sidebar-panel="' + key + '"]');
                if (!panel) {
                    return;
                }

                button.classList.toggle('is-open');
                button.classList.toggle('active');
                panel.classList.toggle('is-open');
                button.setAttribute('aria-expanded', panel.classList.contains('is-open') ? 'true' : 'false');
            });
        });

        // Submenu toggle for sidebar items with submenus
        document.querySelectorAll('.nav-item.has-submenu[data-submenu]').forEach(function (toggle) {
            toggle.addEventListener('click', function (e) {
                var key = toggle.getAttribute('data-submenu');
                var submenu = document.getElementById('submenu-' + key);
                if (!submenu) return;
                e.preventDefault();
                toggle.classList.toggle('is-open');
                submenu.classList.toggle('is-open');
            });
        });

        document.querySelectorAll('[data-topbar-toggle]').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.stopPropagation();
                var key = button.getAttribute('data-topbar-toggle');
                var target = document.querySelector('[data-topbar-menu="' + key + '"]');
                if (!target) {
                    return;
                }

                document.querySelectorAll('[data-topbar-menu]').forEach(function (menu) {
                    if (menu !== target) {
                        menu.classList.remove('is-open');
                    }
                });

                target.classList.toggle('is-open');
            });
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.topbar-dropdown')) {
                document.querySelectorAll('[data-topbar-menu]').forEach(function (menu) {
                    menu.classList.remove('is-open');
                });
            }
        });

        function escapeAdminNotif(value) {
            var div = document.createElement('div');
            div.textContent = value || '';
            return div.innerHTML;
        }

        function updateAdminNotifications() {
            fetch('{{ url('admin/api/notifications') }}')
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (!data.success) {
                        return;
                    }

                    var dot = document.getElementById('adminNotifDot');
                    var countText = document.getElementById('adminNotifUnreadText');
                    var list = document.getElementById('adminNotifList');

                    if (dot) {
                        dot.style.display = data.count > 0 ? '' : 'none';
                    }
                    if (countText) {
                        countText.textContent = data.count || 0;
                    }
                    if (!list) {
                        return;
                    }

                    if (!data.notifications || data.notifications.length === 0) {
                        list.innerHTML = '<div class="topbar-menu-item"><div class="topbar-menu-item__icon"><i class="fas fa-inbox"></i></div><div><div class="topbar-menu-item__title">Chua co thong bao nao</div><div class="topbar-menu-item__meta">He thong se hien thi thong bao moi tai day.</div></div></div>';
                        return;
                    }

                    list.innerHTML = data.notifications.map(function (notification) {
                        return '<a class="topbar-menu-item" href="' + escapeAdminNotif(notification.link) + '" data-admin-notif-id="' + notification.id + '">' +
                            '<div class="topbar-menu-item__icon"><i class="fas ' + escapeAdminNotif(notification.icon) + '"></i></div>' +
                            '<div><div class="topbar-menu-item__title">' + escapeAdminNotif(notification.title) + '</div>' +
                            '<div class="topbar-menu-item__meta">' + escapeAdminNotif(notification.time) + '</div></div>' +
                            '</a>';
                    }).join('');
                })
                .catch(function () {});
        }

        updateAdminNotifications();
        setInterval(updateAdminNotifications, 15000);
    })();

    function ChangeToSlug() {
        var titleInput = document.getElementById('title');
        var slugInput = document.getElementById('slug');

        if (!titleInput || !slugInput) {
            return;
        }

        var slug = titleInput.value.toLowerCase();
        slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
        slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
        slug = slug.replace(/í|ì|ỉ|ĩ|ị/gi, 'i');
        slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
        slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
        slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
        slug = slug.replace(/đ/gi, 'd');
        slug = slug.replace(/[^a-z0-9\s-]/g, '');
        slug = slug.replace(/\s+/g, '-');
        slug = slug.replace(/-+/g, '-').replace(/^-|-$/g, '');
        slugInput.value = slug;
    }

    if (document.getElementById('ckeditor') && window.CKEDITOR) {
        if (CKEDITOR.instances.ckeditor) {
            CKEDITOR.instances.ckeditor.destroy(true);
        }

        CKEDITOR.replace('ckeditor', {
            language: 'vi',
            height: 420,
            allowedContent: true,
            extraPlugins: 'colorbutton,font,justify,pastefromword',
            removeButtons: '',
            font_names: 'Arial/Arial, Helvetica, sans-serif;' +
                'Times New Roman/Times New Roman, Times, serif;' +
                'Tahoma/Tahoma, Geneva, sans-serif;' +
                'Verdana/Verdana, Geneva, sans-serif;' +
                'Roboto/Roboto, Arial, sans-serif;' +
                'Georgia/Georgia, serif;' +
                'Courier New/Courier New, Courier, monospace',
            fontSize_sizes: '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;24/24px;28/28px;32/32px;48/48px;72/72px',
            toolbar: [
                { name: 'document', items: ['Source', '-', 'Preview', 'Maximize'] },
                { name: 'clipboard', items: ['Undo', 'Redo', '-', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'Smiley'] },
                { name: 'tools', items: ['ShowBlocks'] }
            ],
            filebrowserBrowseUrl: '{!! url("responsive_filemanager/filemanager/dialog.php?type=1&editor=ckeditor&fldr=") !!}',
            filebrowserUploadUrl: '{!! url("responsive_filemanager/filemanager/dialog.php?type=1&editor=ckeditor&fldr=") !!}',
            filebrowserImageBrowseUrl: '{!! url("responsive_filemanager/filemanager/dialog.php?type=1&editor=ckeditor&fldr=") !!}'
        });
    }

    $(function () {
        if ($.fn.DataTable && $('#example1').length) {
            $('#example1').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    search: 'Tìm kiếm:',
                    lengthMenu: 'Hiển thị _MENU_ dòng',
                    info: 'Hiển thị _START_ đến _END_ trong _TOTAL_ mục',
                    infoEmpty: 'Không có dữ liệu',
                    zeroRecords: 'Không tìm thấy dữ liệu phù hợp',
                    paginate: {
                        first: 'Đầu',
                        last: 'Cuối',
                        next: 'Sau',
                        previous: 'Trước'
                    }
                }
            });
        }

        if ($.fn.DataTable && $('#example2').length) {
            $('#example2').DataTable({
                paging: true,
                lengthChange: false,
                searching: false,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                language: {
                    info: 'Hiển thị _START_ đến _END_ trong _TOTAL_ mục',
                    infoEmpty: 'Không có dữ liệu',
                    zeroRecords: 'Không tìm thấy dữ liệu phù hợp',
                    paginate: {
                        next: 'Sau',
                        previous: 'Trước'
                    }
                }
            });
        }
    });
</script>

@yield('script')
@stack('scripts')
</body>
</html>
