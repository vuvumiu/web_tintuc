<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentAdminController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NewsInteractionController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\NewsScheduleController;
use App\Http\Controllers\TickerController;
use App\Http\Controllers\FeaturedNewsController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\Admin\AIController as AdminAIController;
use App\Http\Controllers\Front\AIController as FrontAIController;

/*
|--------------------------------------------------------------------------
| Route cho người dùng (front-end)
|--------------------------------------------------------------------------
*/
// Đăng ký / Đăng nhập / Đăng xuất người dùng
Route::get('/dang-ky', [AuthController::class, 'getRegister'])->name('dangky');
Route::post('/dang-ky', [AuthController::class, 'postRegister']);
Route::get('/dang-nhap', [AuthController::class, 'getLogin'])->name('dangnhap');
Route::post('/dang-nhap', [AuthController::class, 'postLogin']);
Route::get('/dang-xuat', [AuthController::class, 'logout'])->name('dangxuat');

// Quên mật khẩu
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Tài khoản người dùng (frontend - yêu cầu đăng nhập)
Route::middleware('auth')->group(function () {
    Route::get('/tai-khoan', [AuthController::class, 'myAccount'])->name('taikhoan');
    Route::post('/tai-khoan', [AuthController::class, 'myAccountPost']);
    Route::post('/tai-khoan/doi-mat-khau', [AuthController::class, 'changePassword'])->name('taikhoan.doimatkhau');
    Route::get('/thong-bao', [NotificationController::class, 'index'])->name('thongbao');
    Route::get('/thong-bao/mark-read/{id}', [NotificationController::class, 'markAsRead']);
    Route::get('/thong-bao/mark-all-read', [NotificationController::class, 'markAllRead']);
    Route::get('/thong-bao/delete/{id}', [NotificationController::class, 'delete']);
    Route::get('/thong-bao/cai-dat', [AuthController::class, 'notificationSettings'])->name('thongbao.settings');
    Route::post('/thong-bao/cai-dat', [AuthController::class, 'notificationSettingsPost']);
    Route::get('/bai-viet-yeu-thich', [NewsInteractionController::class, 'favoriteList'])->name('yeuthich.list');

    // API: Notifications
    Route::get('/api/notifications/unread', [NotificationController::class, 'apiUnreadCount']);
    Route::post('/api/notifications/{id}/read', [NotificationController::class, 'apiMarkRead']);
    Route::post('/api/notifications/read-all', [NotificationController::class, 'apiMarkAllRead']);
    Route::delete('/api/notifications/{id}', [NotificationController::class, 'apiDelete']);
    Route::get('/api/notifications', [NotificationController::class, 'apiList']);
});

Route::get('/', [FrontController::class, 'home']);
Route::get('lien-he', [FrontController::class, 'contact']);
Route::get('ve-chung-toi', [FrontController::class, 'about']);
Route::get('tim-kiem', [FrontController::class, 'search']);

// Tags - trang danh sách bài viết theo tag
Route::get('tag/{slug}', [TagController::class, 'show'])->name('tag.show');

Route::post('dang-ky-nhan-tin-khuyen-mai', [FrontController::class, 'subEmail']);
Route::post('gui-email-lien-he', [FrontController::class, 'contactSendEmail']);

// Newsletter - xác nhận & hủy
Route::get('newsletter/confirm/{token}', [FrontController::class, 'confirmNewsletter'])->name('newsletter.confirm');
Route::get('newsletter/unsubscribe', [FrontController::class, 'unsubscribeNewsletter'])->name('newsletter.unsubscribe');

// AI Chatbot (public)
Route::post('ai/chat', [FrontAIController::class, 'chat']);
Route::post('ai/chat/clear', [FrontAIController::class, 'clearChat']);

// Tin tức
Route::get('tin-moi-nhat', [FrontController::class, 'latestNews']);
Route::get('tin-noi-bat', [FrontController::class, 'topViewedNews']);

// Bình luận & Đánh giá sao & Vote (yêu cầu đăng nhập)
Route::post('binh-luan', [CommentController::class, 'store'])->middleware('auth')->name('binhluan');
Route::post('binh-luan/phan-hoi', [CommentController::class, 'reply'])->middleware('auth')->name('binhluan.phanhoi');
Route::post('binh-luan/sua/{id}', [CommentController::class, 'update'])->middleware('auth')->name('binhluan.sua');
Route::post('binh-luan/xoa/{id}', [CommentController::class, 'destroy'])->middleware('auth')->name('binhluan.xoa');
Route::delete('binh-luan/xoa/{id}', [CommentController::class, 'destroy'])->middleware('auth')->name('binhluan.xoa');
Route::post('binh-luan/vote', [CommentController::class, 'vote'])->middleware('auth')->name('binhluan.vote');
Route::post('binh-luan/load-more', [CommentController::class, 'loadMore'])->middleware('auth')->name('binhluan.loadmore');
Route::post('danh-gia-sao', [CommentController::class, 'rate'])->middleware('auth')->name('danhgiasao');

// Yêu thích bài viết
Route::post('yeu-thich', [NewsInteractionController::class, 'favorite'])->middleware('auth')->name('yeuthich');

// Chi tiết bài viết
Route::get('{slug}.html', [FrontController::class, 'slugHtml']);
Route::get('{slug}', [FrontController::class, 'slug']);

// Track ad clicks (public)
Route::post('ads/track-view/{id}', [FrontController::class, 'trackAdView']);
Route::post('ads/track-click/{id}', [FrontController::class, 'trackAdClick']);

/*
|--------------------------------------------------------------------------
| Route đăng nhập / đăng xuất admin (ĐẶT NGOÀI group auth)
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [UserController::class, 'getLogin'])->name('admin.login');
Route::post('/admin/login', [UserController::class, 'postLogin']);
Route::get('/admin/logout', [UserController::class, 'getLogout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Route cho quản trị (back-end)
| Yêu cầu: đăng nhập (auth) + phải là admin/staff (admin middleware)
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/home', [BackController::class, 'home']);

    // AJAX API Endpoints
    Route::get('/api/stats', [BackController::class, 'api_stats']);
    Route::get('/api/notifications', [BackController::class, 'api_notifications']);
    Route::post('/api/mark-notif-read', [BackController::class, 'api_mark_notif_read']);
    Route::post('/api/bulk-action', [BackController::class, 'api_bulk_action']);

    // Redirect old URLs → new URLs (so bookmarks don't break)
    Route::get('staff/list', fn () => redirect()->to(url('admin/admin-manager/list')), 301);
    Route::get('staff/add', fn () => redirect()->to(url('admin/admin-manager/add')), 301);
    Route::get('staff/edit/{id}', fn ($id) => redirect()->to(url('admin/admin-manager/edit/' . $id)), 301);
    Route::get('staff/delete/{id}', fn ($id) => redirect()->to(url('admin/admin-manager/delete/' . $id)), 301);
    Route::match(['GET', 'POST'], 'staff/filter', fn () => redirect()->to(url('admin/admin-manager/list')), 301);
    Route::get('author/list', fn () => redirect()->to(url('admin/authors/list')), 301);
    Route::get('authors/list', [AuthorController::class, 'index'])->middleware('permission:author.list|author.manage|admin-manager.list|admin-manager.edit');
    Route::get('authors/detail/{id}', [AuthorController::class, 'show'])->middleware('permission:admin-manager.list|admin-manager.edit|author.list|author.manage');
    Route::post('authors/toggle/{id}', [AuthorController::class, 'toggle'])->middleware('permission:author.manage|admin-manager.edit');
    Route::get('authors/admin-manager/list', [AuthorController::class, 'index'])->middleware('permission:author.list|author.manage|admin-manager.list|admin-manager.edit');
    Route::get('authors/admin-manager/stats/{id}', [AuthorController::class, 'show'])->middleware('permission:admin-manager.list|admin-manager.edit|author.list|author.manage');
    Route::get('account/list', fn () => redirect()->to(url('admin/member/list')), 301);
    Route::get('account/view/{id}', fn ($id) => redirect()->to(url('admin/member/view/' . $id)), 301);
    Route::get('account/edit/{id}', fn ($id) => redirect()->to(url('admin/member/edit/' . $id)), 301);
    Route::get('account/delete/{id}', fn ($id) => redirect()->to(url('admin/member/delete/' . $id)), 301);
    Route::get('account/lock/{id}', fn ($id) => redirect()->to(url('admin/member/lock/' . $id)), 301);
    Route::get('account/unlock/{id}', fn ($id) => redirect()->to(url('admin/member/unlock/' . $id)), 301);

    // ====================== QUẢN LÝ QUẢN TRỊ VIÊN ======================
    Route::group(['prefix' => 'admin-manager'], function () {
        Route::get('profile', [BackController::class, 'staff_profile']);
        Route::post('profile', [BackController::class, 'staff_profile_post']);
        Route::get('list', [BackController::class, 'staff_list'])->middleware('permission:admin-manager.list|admin-manager.create|admin-manager.edit|admin-manager.delete');
        Route::get('add', [BackController::class, 'staff_add'])->middleware('permission:admin-manager.create');
        Route::post('add', [BackController::class, 'staff_add_post'])->middleware('permission:admin-manager.create');
        Route::get('edit/{id}', [BackController::class, 'staff_edit'])->middleware('permission:admin-manager.edit');
        Route::post('edit/{id}', [BackController::class, 'staff_edit_post'])->middleware('permission:admin-manager.edit');
        Route::get('stats/{id}', [AuthorController::class, 'show'])->middleware('permission:admin-manager.list|admin-manager.edit|author.list|author.manage');
        Route::post('author-toggle/{id}', [AuthorController::class, 'toggle'])->middleware('permission:admin-manager.edit|author.manage');
        Route::delete('delete/{id}', [BackController::class, 'staff_delete'])->middleware('permission:admin-manager.delete');
        Route::post('filter', [BackController::class, 'staff_filter'])->middleware('permission:admin-manager.list|admin-manager.edit');
    });

    // ====================== QUẢN LÝ THÀNH VIÊN ======================
    Route::group(['prefix' => 'member'], function () {
        Route::get('list', [AccountController::class, 'account_list'])->middleware('permission:member.list');
        Route::get('view/{id}', [AccountController::class, 'account_view'])->middleware('permission:member.list');
        Route::get('edit/{id}', [AccountController::class, 'account_edit'])->middleware('permission:member.edit');
        Route::post('edit/{id}', [AccountController::class, 'account_edit_post'])->middleware('permission:member.edit');
        Route::delete('delete/{id}', [AccountController::class, 'account_delete'])->middleware('permission:member.delete');
        Route::get('lock/{id}', [AccountController::class, 'account_lock'])->middleware('permission:member.lock');
        Route::get('unlock/{id}', [AccountController::class, 'account_unlock'])->middleware('permission:member.lock');
        Route::post('toggle/{id}', [AccountController::class, 'account_toggle']);
    });

    Route::get('/system', [BackController::class, 'system'])->middleware('admin');
    Route::post('/system', [BackController::class, 'system_post'])->middleware('admin');

    // ====================== QUẢN LÝ TAGS ======================
    Route::group(['prefix' => 'tag'], function () {
        Route::get('list', [BackController::class, 'tag_list'])->middleware('permission:tag.list');
        Route::get('add', [BackController::class, 'tag_getadd'])->middleware('permission:tag.create');
        Route::post('add', [BackController::class, 'tag_add'])->middleware('permission:tag.create');
        Route::get('edit/{id}', [BackController::class, 'tag_getedit'])->middleware('permission:tag.edit');
        Route::post('edit/{id}', [BackController::class, 'tag_edit'])->middleware('permission:tag.edit');
        Route::delete('delete/{id}', [BackController::class, 'tag_delete'])->middleware('permission:tag.delete');
    });

    // page management
    Route::group(['prefix' => 'page'], function () {
        Route::get('list', [BackController::class, 'page_list'])->middleware('permission:page.manage');
        Route::get('edit/{id}', [BackController::class, 'page_edit'])->middleware('permission:page.manage');
        Route::post('edit/{id}', [BackController::class, 'page_edit_post'])->middleware('permission:page.manage');
    });

    // social management
    Route::group(['prefix' => 'social'], function () {
        Route::get('list', [BackController::class, 'social_list'])->middleware('permission:social.manage');
        Route::get('edit/{id}', [BackController::class, 'social_edit'])->middleware('permission:social.manage');
        Route::post('edit/{id}', [BackController::class, 'social_edit_post'])->middleware('permission:social.manage');
    });

    // quản lý nhận tin khuyến mại
    Route::group(['prefix' => 'newsletter'], function () {
        Route::get('list', [BackController::class, 'newsletter_list'])->middleware('permission:newsletter.list');
        Route::get('export', [BackController::class, 'newsletter_export'])->middleware('permission:newsletter.export');
        Route::get('edit/{id}', [BackController::class, 'newsletter_edit'])->middleware('permission:newsletter.list');
        Route::post('edit/{id}', [BackController::class, 'newsletter_edit_post'])->middleware('permission:newsletter.list');
        Route::delete('delete/{id}', [BackController::class, 'newsletter_delete'])->middleware('permission:newsletter.list');
    });

    // quản lý liên hệ
    Route::group(['prefix' => 'contact'], function () {
        Route::get('list', [BackController::class, 'contact_list'])->middleware('permission:contact.list');
        Route::get('edit/{id}', [BackController::class, 'contact_edit'])->middleware('permission:contact.list');
        Route::post('edit/{id}', [BackController::class, 'contact_edit_post'])->middleware('permission:contact.list');
        Route::delete('delete/{id}', [BackController::class, 'contact_delete'])->middleware('permission:contact.list');
        Route::get('mark-read/{id}', [BackController::class, 'contact_mark_read'])->middleware('permission:contact.list');
        Route::get('mark-replied/{id}', [BackController::class, 'contact_mark_replied'])->middleware('permission:contact.reply');
        Route::post('reply/{id}', [BackController::class, 'contactReply'])->middleware('permission:contact.reply');
    });

    // quản lý tin tức
    Route::group(['prefix' => 'news_cat'], function () {
        Route::get('list', [BackController::class, 'news_cat_list'])->middleware('permission:category.list');
        Route::get('add', [BackController::class, 'news_cat_getadd'])->middleware('permission:category.create');
        Route::post('add', [BackController::class, 'news_cat_add'])->middleware('permission:category.create');
        Route::get('edit/{id}', [BackController::class, 'news_cat_getedit'])->middleware('permission:category.edit');
        Route::post('edit/{id}', [BackController::class, 'news_cat_edit'])->middleware('permission:category.edit');
        Route::delete('delete/{id}', [BackController::class, 'news_cat_delete'])->middleware('permission:category.delete');
        Route::post('sort/{id}', [BackController::class, 'news_cat_update_sort']);
    });

    // News - đầy đủ workflow
    Route::group(['prefix' => 'news'], function () {
        Route::get('list', [BackController::class, 'news_list'])->middleware('permission:news.list');
        Route::get('add', [BackController::class, 'news_getAdd'])->middleware('permission:news.create');
        Route::post('add', [BackController::class, 'news_add'])->middleware('permission:news.create');
        Route::get('edit/{RowID}', [BackController::class, 'news_getedit'])->middleware('permission:news.edit');
        Route::post('edit/{RowID}', [BackController::class, 'news_edit'])->middleware('permission:news.edit');
        Route::delete('delete/{RowID}', [BackController::class, 'news_delete'])->middleware('permission:news.delete');
        Route::get('duplicate/{RowID}', [BackController::class, 'news_duplicate'])->middleware('permission:news.create');
        Route::get('preview/{id}', [NewsInteractionController::class, 'preview'])->middleware('permission:news.preview');
        Route::get('submit-review/{newsId}', [NewsScheduleController::class, 'submitReview'])->middleware('permission:news.create');
        Route::post('bulk-action', [BackController::class, 'news_bulk_action'])->middleware('permission:news.delete|news.edit');
    });

    // Approval Workflow
    Route::group(['prefix' => 'news-approval'], function () {
        Route::get('queue', [NewsScheduleController::class, 'approvalQueue'])->middleware('permission:news.approve');
        Route::get('approve/{id}', [NewsScheduleController::class, 'approve'])->middleware('permission:news.approve');
        Route::post('reject/{id}', [NewsScheduleController::class, 'reject'])->middleware('permission:news.approve');
        Route::get('drafts', [NewsScheduleController::class, 'drafts']);
    });

    // Slider (legacy - giữ lại để tương thích ngược)
    Route::group(['prefix' => 'slider'], function () {
        Route::get('list', fn () => redirect('admin/ads/list'))->middleware('permission:slider.manage|ads.manage');
        Route::get('add', fn () => redirect('admin/ads/add'))->middleware('permission:slider.manage|ads.manage');
        Route::post('add', fn () => redirect('admin/ads/add'))->middleware('permission:slider.manage|ads.manage');
        Route::get('edit/{RowID}', fn () => redirect('admin/ads/list'))->middleware('permission:slider.manage|ads.manage');
        Route::post('edit/{RowID}', fn () => redirect('admin/ads/list'))->middleware('permission:slider.manage|ads.manage');
        Route::delete('delete/{RowID}', fn () => redirect('admin/ads/list'))->middleware('permission:slider.manage|ads.manage');
    });

    // Quản lý Quảng cáo (Ads - popup, banner, sidebar)
    Route::group(['prefix' => 'ads'], function () {
        Route::get('list', [BackController::class, 'ad_list'])->middleware('permission:ads.manage');
        Route::get('add', [BackController::class, 'ad_getAdd'])->middleware('permission:ads.manage');
        Route::post('add', [BackController::class, 'ad_add'])->middleware('permission:ads.manage');
        Route::get('edit/{id}', [BackController::class, 'ad_getedit'])->middleware('permission:ads.manage');
        Route::post('edit/{id}', [BackController::class, 'ad_edit'])->middleware('permission:ads.manage');
        Route::delete('delete/{id}', [BackController::class, 'ad_delete'])->middleware('permission:ads.manage');
    });

    // Tin nóng (Ticker)
    Route::group(['prefix' => 'ticker'], function () {
        Route::get('list', [TickerController::class, 'list']);
        Route::get('add', [TickerController::class, 'getAdd']);
        Route::post('add', [TickerController::class, 'postAdd']);
        Route::get('edit/{id}', [TickerController::class, 'getEdit']);
        Route::post('edit/{id}', [TickerController::class, 'postEdit']);
        Route::delete('delete/{id}', [TickerController::class, 'delete']);
        Route::post('toggle/{id}', [TickerController::class, 'toggle']);
    });

    // Bài viết nổi bật (Featured News)
    Route::group(['prefix' => 'featured'], function () {
        Route::get('list', [FeaturedNewsController::class, 'index'])->middleware('permission:featured.manage')->name('featured.list');
        Route::get('add', [FeaturedNewsController::class, 'create'])->middleware('permission:featured.manage')->name('featured.add');
        Route::post('add', [FeaturedNewsController::class, 'store'])->middleware('permission:featured.manage')->name('featured.store');
        Route::post('add-test', function() {
            $data = request()->all();
            file_put_contents(storage_path('logs/test_post.log'), "POST test hit!\n".print_r($data,true)."\n", FILE_APPEND);
            return response()->json(['ok' => true, 'data' => $data]);
        });
        Route::get('edit/{id}', [FeaturedNewsController::class, 'edit'])->middleware('permission:featured.manage')->name('featured.edit');
        Route::post('edit/{id}', [FeaturedNewsController::class, 'update'])->middleware('permission:featured.manage')->name('featured.update');
        Route::delete('delete/{id}', [FeaturedNewsController::class, 'destroy'])->middleware('permission:featured.manage')->name('featured.delete');
        Route::post('toggle/{id}', [FeaturedNewsController::class, 'toggle'])->middleware('permission:featured.manage')->name('featured.toggle');
        Route::post('bulk-action', [FeaturedNewsController::class, 'bulkAction'])->middleware('permission:featured.manage')->name('featured.bulk-action');
    });

    // Bình luận & Đánh giá sao (quản lý)
    Route::group(['prefix' => 'comment'], function () {
        Route::get('list', [CommentAdminController::class, 'index'])->middleware('permission:comment.list');
        Route::delete('delete/{id}', [CommentAdminController::class, 'destroy'])->middleware('permission:comment.delete');
        Route::post('toggle/{id}', [CommentAdminController::class, 'toggle'])->middleware('permission:comment.hide');
        Route::post('bulk-action', [CommentAdminController::class, 'bulkAction'])->middleware('permission:comment.delete|comment.hide');
    });

    // Quản lý đánh giá sao
    Route::group(['prefix' => 'rating'], function () {
        Route::get('list', [CommentAdminController::class, 'ratingList'])->middleware('permission:comment.list');
        Route::delete('delete/{id}', [CommentAdminController::class, 'ratingDelete'])->middleware('permission:comment.delete');
        Route::post('bulk-delete', [CommentAdminController::class, 'ratingBulkDelete'])->middleware('permission:comment.delete');
    });

    // Notifications
    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('mark-read/{id}', [NotificationController::class, 'markAsRead']);
        Route::get('mark-all-read', [NotificationController::class, 'markAllRead']);
        Route::get('delete/{id}', [NotificationController::class, 'delete']);
    });

    // AI Tools (admin only)
    Route::group(['prefix' => 'ai'], function () {
        Route::get('dashboard', [AdminAIController::class, 'dashboard'])->middleware('admin');
        Route::get('settings', [AdminAIController::class, 'settings'])->middleware('admin');
        Route::post('settings', [AdminAIController::class, 'settings'])->middleware('admin');
        Route::post('generate-meta', [AdminAIController::class, 'generateMeta']);
        Route::post('suggest-tags', [AdminAIController::class, 'suggestTags']);
        Route::post('moderate-comment', [AdminAIController::class, 'moderateComment']);
        Route::post('moderate-comment-bulk', [AdminAIController::class, 'moderateCommentBulk']);
    });
});
