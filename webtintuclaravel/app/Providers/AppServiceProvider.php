<?php

namespace App\Providers;

use App\Models\Contact;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsComment;
use App\Models\Newsletter;
use App\Models\Notification;
use App\Models\Page;
use App\Models\Social;
use App\Models\System;
use App\Models\User;
use App\Models\Ad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Biến dùng chung cho layout front: tên website, logo, favicon, menu và dữ liệu phụ trợ.
        View::composer('front.template.master', function ($view) {
            $systemSettings = System::query()
                ->whereIn('Code', ['name', 'logo_text', 'logo', 'logo_type', 'favicon', 'copyright'])
                ->pluck('Description', 'Code');

            $siteName = trim((string) ($systemSettings->get('name') ?? ''));
            if ($siteName === '') {
                $siteName = 'VNXpress';
            }

            $logoRaw = trim((string) ($systemSettings->get('logo') ?? ''));
            $logoText = trim((string) ($systemSettings->get('logo_text') ?? ''));
            $logoType = trim((string) ($systemSettings->get('logo_type') ?? ''));
            $faviconRaw = trim((string) ($systemSettings->get('favicon') ?? ''));
            $copyrightValue = trim((string) ($systemSettings->get('copyright') ?? ''));

            if ($logoText === '') {
                $logoText = 'VN●XPRESS';
            }

            if (!in_array($logoType, ['text', 'image'], true)) {
                $logoType = $logoRaw !== '' ? 'image' : 'text';
            }

            $logo = (object) [
                'Description' => $logoRaw !== '' ? 'images/logo/' . $logoRaw : '',
            ];
            $favicon = (object) [
                'Description' => $faviconRaw !== '' ? 'images/favicon/' . $faviconRaw : '',
            ];
            $faviconUrl = $faviconRaw !== ''
                ? asset('images/favicon/' . $faviconRaw)
                : asset('favicon.ico');
            $copyright = $copyrightValue !== ''
                ? (object) ['Description' => $copyrightValue]
                : null;

            $Social = Social::where('Status', 1)
                ->selectRaw('Name, Font, Alias')
                ->orderBy('Sort', 'ASC')
                ->get();

            $Page = Page::where('Status', 1)
                ->orderBy('Sort', 'ASC')
                ->orderBy('RowID', 'ASC')
                ->get();

            $NewsCategoriesGlobal = NewsCategory::where('Status', 1)
                ->selectRaw('Name, Alias')
                ->orderBy('RowID', 'ASC')
                ->get();

            $LatestNewsGlobal = DB::table('news')
                ->where('Status', 1)
                ->selectRaw('RowID, Name, Alias, Images')
                ->orderBy('RowID', 'DESC')
                ->limit(5)
                ->get();

            $TopViewedGlobal = DB::table('news')
                ->where('Status', 1)
                ->selectRaw('RowID, Name, Alias, Images')
                ->orderBy('Views', 'DESC')
                ->limit(5)
                ->get();

            $popupAd = null;
            if (Schema::hasTable('ads')) {
                $path = request()->path();
                $popupLocation = Ad::LOC_ALL;

                if ($path === '/' || $path === '') {
                    $popupLocation = Ad::LOC_HOME;
                } elseif (Str::endsWith($path, '.html')) {
                    $popupLocation = Ad::LOC_ARTICLE;
                }

                $popupAd = Ad::getRandomPopup($popupLocation);
            }

            $view->with(compact(
                'siteName',
                'logo',
                'logoText',
                'logoType',
                'favicon',
                'faviconUrl',
                'copyright',
                'Social',
                'Page',
                'NewsCategoriesGlobal',
                'LatestNewsGlobal',
                'TopViewedGlobal',
                'popupAd'
            ));
        });

        // Biến dùng chung cho layout admin: thương hiệu, badge menu, thông báo và thông tin người dùng.
        View::composer('back.template.master', function ($view) {
            if (!auth()->check()) {
                return;
            }

            /** @var User $user */
            $user = auth()->user();
            $displayName = $user->fullname ?: $user->username;

            $systemSettings = System::query()
                ->whereIn('Code', ['name', 'logo_text', 'logo', 'logo_type', 'favicon'])
                ->pluck('Description', 'Code');

            $brandName = trim((string) ($systemSettings->get('name') ?? ''));
            if ($brandName === '') {
                $brandName = 'VUvumiu';
            }

            $brandText = trim((string) ($systemSettings->get('logo_text') ?? ''));
            if ($brandText === '') {
                $brandText = 'VN●XPRESS';
            }

            $brandLogo = trim((string) ($systemSettings->get('logo') ?? ''));
            $brandLogoType = trim((string) ($systemSettings->get('logo_type') ?? ''));
            $brandFavicon = trim((string) ($systemSettings->get('favicon') ?? ''));
            if (!in_array($brandLogoType, ['text', 'image'], true)) {
                $brandLogoType = $brandLogo !== '' ? 'image' : 'text';
            }

            $brandShort = collect(preg_split('/\s+/', $brandText))
                ->filter()
                ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
                ->implode('');

            if ($brandShort === '') {
                $brandShort = Str::upper(Str::substr(preg_replace('/\s+/', '', $brandText), 0, 6));
            }

            if ($brandShort === '') {
                $brandShort = 'VUMU';
            }

            $brandInitialSource = preg_replace('/[^A-Za-z0-9]/', '', $brandText);
            $brandInitial = Str::upper(Str::substr($brandInitialSource !== '' ? $brandInitialSource : $brandShort, 0, 2));
            if ($brandInitial === '') {
                $brandInitial = Str::upper(Str::substr($brandShort, 0, 1));
            }

            $adminShell = [
                'brand' => [
                    'name' => $brandName,
                    'text' => $brandText,
                    'short' => $brandShort,
                    'initial' => $brandInitial,
                    'logo_type' => $brandLogoType,
                    'logo_url' => $brandLogo !== '' ? asset('images/logo/' . $brandLogo) : '',
                    'favicon_url' => $brandFavicon !== '' ? asset('images/favicon/' . $brandFavicon) : asset('favicon.ico'),
                ],
                'current_date' => now()->locale('vi')->isoFormat('dddd, D [Tháng] M, YYYY'),
                'search_action' => url('admin/news/list'),
                'counts' => [
                    'news_total' => News::count(),
                    'newsletter_unreviewed' => Newsletter::query()->unreviewed()->count(),
                    'contacts_new' => Contact::query()->unread()->count(),
                    'comments_pending' => NewsComment::query()->where('is_active', false)->count(),
                    'notifications_unread' => Notification::unreadCount($user->id),
                ],
                'user' => [
                    'name' => $displayName,
                    'email' => $user->email,
                    'initial' => $user->initials ?? strtoupper(substr($displayName, 0, 1)),
                    'role_label' => $user->isAdmin() ? 'Quản trị viên' : ($user->isStaff() ? 'Nhân viên' : 'Tác giả'),
                    'is_admin' => $user->isAdmin(),
                ],
                'notifications' => Notification::query()
                    ->where('user_id', $user->id)
                    ->latest('created_at')
                    ->limit(5)
                    ->get(),
            ];

            $view->with(compact('adminShell'));
        });
    }
}
