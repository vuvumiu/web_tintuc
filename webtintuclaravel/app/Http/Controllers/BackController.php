<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Helpers\NotificationHelper;
use App\Services\NotificationService;
use App\Models\Contact;
use App\Models\ContactReply;
use App\Models\FeaturedNews;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Newsletter;
use App\Models\Notification;
use App\Models\NewsViewStat;
use App\Models\NewsRating;
use App\Models\NewsTicker;
use App\Models\Page;
use App\Models\Role;
use App\Models\Slider;
use App\Models\Social;
use App\Models\Ad;
use App\Models\System;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\NewsSchedule;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home(Request $request)
    {
        $user = Auth::user();
        $dateRange = $this->resolveDashboardDateRange($request);
        $rangeStart = $dateRange['start'];
        $rangeEnd = $dateRange['end'];

        $stats = [
            'news_total' => $this->applyDashboardNewsDateFilter(News::query(), $rangeStart, $rangeEnd)->count(),
            'news_published' => $this->applyDashboardNewsDateFilter(News::where('Status', 1), $rangeStart, $rangeEnd)->count(),
            'news_draft' => NewsSchedule::where('status', 'draft')->whereBetween('created_at', [$rangeStart, $rangeEnd])->count(),
            'news_pending' => NewsSchedule::where('status', 'pending')->whereBetween('created_at', [$rangeStart, $rangeEnd])->count(),
            'news_scheduled' => NewsSchedule::where('status', 'scheduled')->whereBetween('created_at', [$rangeStart, $rangeEnd])->count(),
            'comment_total' => \App\Models\NewsComment::whereBetween('created_at', [$rangeStart, $rangeEnd])->count(),
            'comment_pending' => Schema::hasColumn('news_comments', 'moderation_status')
                ? \App\Models\NewsComment::where('moderation_status', \App\Models\NewsComment::STATUS_PENDING)
                    ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->count()
                : \App\Models\NewsComment::where('is_active', false)
                    ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->count(),
            'contacts_new' => Contact::query()->unread()->whereBetween('created_at', [$rangeStart, $rangeEnd])->count(),
            'members_total' => User::query()->regularAccounts()->whereBetween('created_at', [$rangeStart, $rangeEnd])->count(),
            'newsletter_total' => Newsletter::whereBetween('subscribed_at', [$rangeStart, $rangeEnd])->count(),
            'notif_unread' => Notification::unreadCount($user->id),
        ];

        $weeklyViews = $this->getWeeklyViews();
        $recentActivities = $this->getRecentActivities($rangeStart, $rangeEnd);

        $topRatedArticles = $this->getTopRatedArticles(5, $rangeStart, $rangeEnd);
        $lowestRatedArticles = $this->getLowestRatedArticles(5, $rangeStart, $rangeEnd);
        $mostProlificAuthors = $this->getMostProlificAuthors(5, $rangeStart, $rangeEnd);
        $authorsTopRated = $this->getAuthorsByHighestRatingRatio(5, $rangeStart, $rangeEnd);
        $authorsLowestRated = $this->getAuthorsByLowestRatingRatio(5, $rangeStart, $rangeEnd);
        $ratingOverview = $this->getRatingOverview($rangeStart, $rangeEnd);
        $authorPerformance = $this->getAuthorPerformanceTable(12, $rangeStart, $rangeEnd);
        $topViewedArticles = $this->getTopViewedArticles(10, $rangeStart, $rangeEnd);
        $categoryRatingStats = $this->getCategoryRatingStats($rangeStart, $rangeEnd);
        $statusDistribution = $this->getDashboardStatusDistribution($rangeStart, $rangeEnd);
        $ratingTrend = $this->getRatingTrend($rangeStart, $rangeEnd);
        $chartSeries = $this->getDashboardChartSeries($rangeStart, $rangeEnd);
        $dailySeries = $this->getDashboardDailySeries(28);

        $stats['rating_total'] = (int) ($ratingOverview['total'] ?? 0);
        $stats['rating_average'] = (float) ($ratingOverview['average'] ?? 0);
        $stats['rating_positive'] = (int) ($ratingOverview['positive_total'] ?? 0);
        $stats['rating_negative'] = (int) ($ratingOverview['negative_total'] ?? 0);
        $stats['featured_total'] = (int) ($statusDistribution['featured'] ?? 0);
        $stats['hot_total'] = (int) ($statusDistribution['hot'] ?? 0);

        $featuredIds = FeaturedNews::query()->active()->pluck('news_id')->all();

        $latestNews = $this->applyDashboardNewsDateFilter(News::with(['author', 'category', 'latestSchedule']), $rangeStart, $rangeEnd)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function (News $news) use ($featuredIds) {
                $scheduleStatus = $news->latestSchedule?->status;

                if (in_array($news->RowID, $featuredIds, true) && (int) $news->Status === 1) {
                    $news->dashboard_status = 'featured';
                } elseif ((int) $news->Status === 1) {
                    $news->dashboard_status = 'published';
                } elseif ($scheduleStatus === NewsSchedule::STATUS_PENDING) {
                    $news->dashboard_status = 'pending';
                } else {
                    $news->dashboard_status = 'draft';
                }

                return $news;
            });

        return view('back.home.home', compact(
            'stats',
            'weeklyViews',
            'recentActivities',
            'latestNews',
            'topRatedArticles',
            'lowestRatedArticles',
            'mostProlificAuthors',
            'authorsTopRated',
            'authorsLowestRated',
            'ratingOverview',
            'authorPerformance',
            'topViewedArticles',
            'categoryRatingStats',
            'statusDistribution',
            'ratingTrend',
            'chartSeries',
            'dailySeries',
            'dateRange'
        ));
    }

    public function api_stats()
    {
        $ratingOverview = $this->getRatingOverview();
        $statusDistribution = $this->getDashboardStatusDistribution();

        return response()->json([
            'success' => true,
            'stats' => [
                'news_total' => News::count(),
                'news_published' => News::query()->where('Status', 1)->count(),
                'news_pending' => NewsSchedule::query()->where('status', NewsSchedule::STATUS_PENDING)->count(),
                'members_total' => User::query()->regularAccounts()->count(),
                'comments_total' => \App\Models\NewsComment::count(),
                'contacts_new' => Contact::query()->unread()->count(),
                'newsletter_total' => Newsletter::count(),
                'rating_average' => (float) ($ratingOverview['average'] ?? 0),
                'rating_total' => (int) ($ratingOverview['total'] ?? 0),
                'rating_positive' => (int) ($ratingOverview['positive_total'] ?? 0),
                'rating_negative' => (int) ($ratingOverview['negative_total'] ?? 0),
            ],
            'status_distribution' => $statusDistribution,
            'chart_series' => $this->getDashboardChartSeries(),
        ]);
    }

    public function api_notifications()
    {
        $userId = (int) Auth::id();
        $notifications = Notification::query()
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return response()->json([
            'success' => true,
            'count' => Notification::unreadCount($userId),
            'notifications' => $notifications->map(function (Notification $notification) {
                return [
                    'id' => (int) $notification->id,
                    'title' => (string) $notification->title,
                    'content' => (string) ($notification->content ?? ''),
                    'link' => $notification->link ?: url('admin/notifications/mark-read/' . $notification->id),
                    'is_read' => (int) $notification->is_read,
                    'icon' => Notification::typeIcon((string) $notification->type),
                    'color' => Notification::typeColor((string) $notification->type),
                    'time' => optional($notification->created_at)->diffForHumans() ?? '',
                ];
            })->values(),
        ]);
    }

    public function api_mark_notif_read(Request $request)
    {
        $userId = (int) Auth::id();
        $notification = Notification::query()
            ->where('id', $request->input('id'))
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo.',
            ], 404);
        }

        $notification->markAsRead();
        NotificationService::clearUnreadCache($userId);

        return response()->json([
            'success' => true,
            'count' => Notification::unreadCount($userId),
        ]);
    }

    public function api_theme(Request $request)
    {
        $theme = $request->input('theme', 'dark');
        if (!in_array($theme, ['dark', 'light'])) {
            $theme = 'dark';
        }
        session(['admin_theme' => $theme]);

        return response()->json([
            'success' => true,
            'theme' => $theme,
        ]);
    }

    // =====================================================
    // NEWS CRUD - với Author, Tags, Schedule Workflow
    // =====================================================

    public function news_list(Request $request)
    {
        // Mỗi bài chỉ 1 dòng: join schedule mới nhất (theo id), tránh trùng khi có nhiều bản ghi news_schedules
        $scheduleLatest = DB::table('news_schedules')
            ->select('news_id', DB::raw('MAX(id) as last_id'))
            ->groupBy('news_id');

        $ratingStats = DB::table('news_ratings')
            ->select('news_id',
                DB::raw('COUNT(id) as rating_count'),
                DB::raw('AVG(score) as rating_avg'),
                DB::raw('COALESCE(SUM(score), 0) as rating_score_sum'))
            ->groupBy('news_id');

        $query = DB::table('news as a')
            ->leftJoin('news_cat as b', 'a.RowIDCat', '=', 'b.RowID')
            ->leftJoin('users as c', 'a.author_id', '=', 'c.id')
            ->leftJoinSub($scheduleLatest, 'ls', function ($join) {
                $join->on('a.RowID', '=', 'ls.news_id');
            })
            ->leftJoin('news_schedules as d', function ($join) {
                $join->on('d.id', '=', 'ls.last_id');
            })
            ->leftJoinSub($ratingStats, 'rs', function ($join) {
                $join->on('a.RowID', '=', 'rs.news_id');
            })
            ->selectRaw('a.*, b.Name as CategoryName, c.fullname as AuthorName, d.status as ScheduleStatus, d.publish_type, d.scheduled_at,
                COALESCE(rs.rating_count, 0) as rating_count,
                COALESCE(rs.rating_avg, 0) as rating_avg')
            ->orderBy('a.RowID', 'DESC');

        if (!$this->canManageAllNews()) {
            $query->where('a.author_id', Auth::id());
        }

        if ($request->filled('keyword')) {
            $kw = '%' . trim($request->keyword) . '%';
            $query->where(function ($q) use ($kw) {
                $q->where('a.Name', 'like', $kw);
                if (Schema::hasColumn('news', 'SmallDescription')) {
                    $q->orWhere('a.SmallDescription', 'like', $kw);
                }
            });
        }
        if ($request->filled('cat')) {
            $query->where('a.RowIDCat', $request->cat);
        }
        if ($request->filled('status')) {
            $query->where('a.Status', (int) $request->status);
        }
        if ($request->filled('author')) {
            $query->where('a.author_id', $request->author);
        }
        if ($request->filled('schedule_status')) {
            $query->where('d.status', $request->schedule_status);
        }

        $News = $query->paginate(20);
        $NewsCategory = \App\Models\NewsCategory::get();
        $authors = $this->historicalAuthorUsers();

        $statsScheduleLatest = DB::table('news_schedules')
            ->select('news_id', DB::raw('MAX(id) as last_id'))
            ->groupBy('news_id');

        $statsRow = DB::table('news as a')
            ->leftJoinSub($statsScheduleLatest, 'sls', function ($join) {
                $join->on('a.RowID', '=', 'sls.news_id');
            })
            ->leftJoin('news_schedules as sd', function ($join) {
                $join->on('sd.id', '=', 'sls.last_id');
            })
            ->selectRaw(
                'COUNT(a.RowID) as news_total,
                SUM(CASE WHEN a.Status = 1 THEN 1 ELSE 0 END) as news_published,
                SUM(CASE WHEN sd.status = ? OR (sd.id IS NULL AND a.Status = 0) THEN 1 ELSE 0 END) as news_draft,
                SUM(CASE WHEN sd.status = ? THEN 1 ELSE 0 END) as news_pending',
                [NewsSchedule::STATUS_DRAFT, NewsSchedule::STATUS_PENDING]
            );

        if (!$this->canManageAllNews()) {
            $statsRow->where('a.author_id', Auth::id());
        }

        $statsRow = $statsRow->first();

        $stats = [
            'news_total' => (int) ($statsRow->news_total ?? 0),
            'news_published' => (int) ($statsRow->news_published ?? 0),
            'news_draft' => (int) ($statsRow->news_draft ?? 0),
            'news_pending' => (int) ($statsRow->news_pending ?? 0),
        ];

        // Rating overview for the page
        $totalRatings = (int) NewsRating::count();
        $avgRating = round((float) (NewsRating::avg('score') ?? 0), 1);
        $byScore = NewsRating::select('score', DB::raw('COUNT(*) as total'))->groupBy('score')->get()->keyBy('score');
        $score5 = (int) ($byScore->get(5)->total ?? 0);
        $score4 = (int) ($byScore->get(4)->total ?? 0);
        $positive = $score5 + $score4;
        $positivePct = $totalRatings > 0 ? round($positive / $totalRatings * 100) : 0;

        return view('back.news.list', compact(
            'News', 'NewsCategory', 'authors', 'stats',
            'totalRatings', 'avgRating', 'positivePct'
        ));
    }

    /**
     * Bảng news kiểu cũ: nếu cột RowID chưa AUTO_INCREMENT thì gán max(RowID)+1 trước insert.
     */
    protected function ensureNewsRowIdForInsert(News $news): void
    {
        if ($news->exists) {
            return;
        }
        $rowIdCol = DB::selectOne("SHOW COLUMNS FROM news WHERE Field = 'RowID'");
        if ($rowIdCol && stripos((string) ($rowIdCol->Extra ?? ''), 'auto_increment') !== false) {
            return;
        }
        $news->RowID = (int) (News::query()->max('RowID') ?? 0) + 1;
    }

    public function news_getAdd(Request $request)
    {
        $NewsCategory = \App\Models\NewsCategory::get();
        $authors = $this->articleAuthorUsers();
        $tags = Tag::active()->orderBy('name')->get(['id', 'name', 'slug']);
        $canPublishDirectly = Auth::user() && Auth::user()->hasPermission('news.approve');

        return view('back.news.add', compact('NewsCategory', 'authors', 'tags', 'canPublishDirectly'));
    }

    public function news_add(Request $request)
    {
        $submitAction = $request->input('submit_action', 'save_draft');
        if (!in_array($submitAction, ['save_draft', 'submit_review', 'publish_now'], true)) {
            $submitAction = 'save_draft';
        }

        $canPublishDirectly = Auth::user() && Auth::user()->hasPermission('news.approve');
        if ($submitAction === 'publish_now' && !$canPublishDirectly) {
            return redirect()->back()
                ->withInput()
                ->with(['flash_level' => 'danger', 'flash_message' => 'Bạn không có quyền xuất bản trực tiếp bài viết.']);
        }

        $request->validate([
            'Alias' => ['nullable', 'string', 'max:255', Rule::unique('news', 'Alias')],
            'MetaTitle' => 'nullable|string|max:70',
            'MetaDescription' => 'nullable|string|max:180',
            'MetaKeyword' => 'nullable|string|max:500',
            'SmallDescription' => 'nullable|string|max:500',
        ]);

        $publishType = $submitAction === 'publish_now'
            ? NewsSchedule::PUBLISH_NOW
            : $request->input('publish_type', NewsSchedule::PUBLISH_NOW);

        if ($submitAction === 'submit_review' && $publishType === NewsSchedule::PUBLISH_SCHEDULE) {
            $request->validate([
                'Name'          => 'required|string|max:255',
                'Description'   => 'required|string',
                'author_id'    => 'nullable|integer|exists:users,id',
                'scheduled_at' => 'required|date|after:now',
            ], [
                'Name.required'        => 'Vui lòng nhập tiêu đề bài viết.',
                'Description.required' => 'Vui lòng nhập nội dung bài viết.',
                'scheduled_at.required' => 'Vui lòng chọn thời gian xuất bản.',
                'scheduled_at.after'    => 'Thời gian xuất bản phải sau thời điểm hiện tại.',
            ]);
        } elseif ($submitAction !== 'save_draft') {
            $request->validate([
                'Name'        => 'required|string|max:255',
                'Description' => 'required|string',
                'author_id'   => 'nullable|integer|exists:users,id',
            ], [
                'Name.required'        => 'Vui lòng nhập tiêu đề bài viết.',
                'Description.required' => 'Vui lòng nhập nội dung bài viết.',
            ]);
        } else {
            $request->validate([
                'Name'      => 'required|string|max:255',
                'author_id' => 'nullable|integer|exists:users,id',
            ], [
                'Name.required' => 'Vui lòng nhập tiêu đề bài viết.',
            ]);
        }

        $authorId = $this->resolveAuthorIdForNews($request->input('author_id'));
        if (!$authorId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['author_id' => 'Vui lòng chọn một tác giả hợp lệ cho bài viết.']);
        }

        $newsStatus = $submitAction === 'publish_now' ? 1 : 0;
        $scheduleStatus = $this->resolveScheduleStatusForAction($submitAction);

        $News = new News;
        $News->RowIDCat = $request->RowIDCat;
        $News->Status = $newsStatus;
        $News->Name = trim((string) $request->Name);
        $this->applyNewsSeoFields($News, $request);
        $News->SmallDescription = $request->SmallDescription;
        $News->Description = $request->Description;
        $News->Views = $request->Views ?? 0;
        $News->author_id = $authorId;
        $this->ensureNewsRowIdForInsert($News);

        if ($request->hasFile('Images')) {
            $News->Images = $this->processNewsImage($request->file('Images'), null);
        }

        $News->save();

        if ($request->tags) {
            $this->syncNewsTags($News->RowID, $request->tags);
        }

        $this->createOrUpdateSchedule(
            $News->RowID,
            $publishType,
            $request->scheduled_at,
            $scheduleStatus,
            true
        );

        $message = match ($submitAction) {
            'submit_review' => 'Đã gửi bài viết vào hàng đợi duyệt.',
            'publish_now' => 'Đã xuất bản bài viết.',
            default => 'Đã lưu bài viết vào bản nháp.',
        };

        return redirect('admin/news/edit/' . $News->RowID)->with([
            'flash_level'   => 'success',
            'flash_message' => $message,
        ]);
    }

    public function news_getedit(Request $request, $RowID)
    {
        $News = News::with('tags')->find($RowID);

        if (!$News) {
            return redirect('admin/news/list')->with([
                'flash_level'   => 'danger',
                'flash_message' => 'Bài viết không tồn tại.',
            ]);
        }

        if (!$this->canManageNews($News)) {
            return redirect('admin/news/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Bạn chỉ được chỉnh sửa bài viết do mình phụ trách.',
            ]);
        }

        $NewsCategory = \App\Models\NewsCategory::get();
        $authors = $this->articleAuthorUsers($News->author_id);
        $tags = Tag::active()->orderBy('name')->get(['id', 'name', 'slug']);
        $schedule = NewsSchedule::where('news_id', $RowID)->first();
        $canPublishDirectly = Auth::user() && Auth::user()->hasPermission('news.approve');

        return view('back.news.edit', compact('News', 'NewsCategory', 'authors', 'tags', 'schedule', 'canPublishDirectly'));
    }

    public function news_edit(Request $request, $RowID)
    {
        $submitAction = $request->input('submit_action', 'save_draft');
        if (!in_array($submitAction, ['save_draft', 'submit_review', 'publish_now'], true)) {
            $submitAction = 'save_draft';
        }

        $canPublishDirectly = Auth::user() && Auth::user()->hasPermission('news.approve');
        if ($submitAction === 'publish_now' && !$canPublishDirectly) {
            return redirect()->back()
                ->withInput()
                ->with(['flash_level' => 'danger', 'flash_message' => 'Bạn không có quyền xuất bản trực tiếp bài viết.']);
        }

        $request->validate([
            'Alias' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('news', 'Alias')->ignore($RowID, 'RowID'),
            ],
            'MetaTitle' => 'nullable|string|max:70',
            'MetaDescription' => 'nullable|string|max:180',
            'MetaKeyword' => 'nullable|string|max:500',
            'SmallDescription' => 'nullable|string|max:500',
        ]);

        $publishType = $submitAction === 'publish_now'
            ? NewsSchedule::PUBLISH_NOW
            : $request->input('publish_type', NewsSchedule::PUBLISH_NOW);

        if ($submitAction === 'submit_review' && $publishType === NewsSchedule::PUBLISH_SCHEDULE) {
            $request->validate([
                'Name'          => 'required|string|max:255',
                'Description'   => 'required|string',
                'author_id'    => 'nullable|integer|exists:users,id',
                'scheduled_at' => 'required|date|after:now',
            ], [
                'Name.required'        => 'Vui lòng nhập tiêu đề bài viết.',
                'Description.required' => 'Vui lòng nhập nội dung bài viết.',
                'scheduled_at.required' => 'Vui lòng chọn thời gian xuất bản.',
                'scheduled_at.after'    => 'Thời gian xuất bản phải sau thời điểm hiện tại.',
            ]);
        } elseif ($submitAction !== 'save_draft') {
            $request->validate([
                'Name'        => 'required|string|max:255',
                'Description' => 'required|string',
                'author_id'   => 'nullable|integer|exists:users,id',
            ], [
                'Name.required'        => 'Vui lòng nhập tiêu đề bài viết.',
                'Description.required' => 'Vui lòng nhập nội dung bài viết.',
            ]);
        } else {
            $request->validate([
                'Name'      => 'required|string|max:255',
                'author_id' => 'nullable|integer|exists:users,id',
            ], [
                'Name.required' => 'Vui lòng nhập tiêu đề bài viết.',
            ]);
        }

        $News = News::find($RowID);
        if (!$News) {
            return redirect('admin/news/list')->with([
                'flash_level'   => 'danger',
                'flash_message' => 'Bài viết không tồn tại.',
            ]);
        }

        if (!$this->canManageNews($News)) {
            return redirect('admin/news/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Bạn chỉ được chỉnh sửa bài viết do mình phụ trách.',
            ]);
        }

        $authorId = $this->resolveAuthorIdForNews($request->input('author_id'), $News->author_id);
        if (!$authorId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['author_id' => 'Vui lòng chọn một tác giả hợp lệ cho bài viết.']);
        }

        $News->RowIDCat = $request->RowIDCat;
        $News->Status = $submitAction === 'publish_now' ? 1 : 0;
        $News->Name = trim((string) $request->Name);
        $this->applyNewsSeoFields($News, $request);
        $News->SmallDescription = $request->SmallDescription;
        $News->Description = $request->Description;
        $News->Views = $request->Views ?? 0;
        $News->author_id = $authorId;

        if ($request->hasFile('Images')) {
            $News->Images = $this->processNewsImage($request->file('Images'), $News->Images);
        }

        $News->save();

        if ($request->tags) {
            $this->syncNewsTags($News->RowID, $request->tags);
        } else {
            $this->syncNewsTags($News->RowID, []);
        }

        $this->createOrUpdateSchedule(
            $News->RowID,
            $publishType,
            $request->scheduled_at,
            $this->resolveScheduleStatusForAction($submitAction),
            true
        );

        $message = match ($submitAction) {
            'submit_review' => 'Đã cập nhật và gửi bài viết vào hàng đợi duyệt.',
            'publish_now' => 'Đã cập nhật và xuất bản bài viết.',
            default => 'Đã lưu bài viết vào bản nháp.',
        };

        return redirect('admin/news/edit/' . $RowID)->with([
            'flash_level'   => 'success',
            'flash_message' => $message,
        ]);
    }

    public function news_delete(Request $request, $RowID)
    {
        $News = News::find($RowID);

        if (!$News) {
            return redirect('admin/news/list')->with(NotificationHelper::newsNotFound());
        }

        if (!$this->canManageNews($News)) {
            return redirect('admin/news/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Bạn không có quyền xóa bài viết này.',
            ]);
        }

        $name = $News->Name;

        if ($News->Images) {
            $imgPath = public_path('images/news/' . $News->Images);
            if (is_file($imgPath)) {
                @unlink($imgPath);
            }
        }

        DB::table('news_tags')->where('news_id', $RowID)->delete();
        NewsSchedule::where('news_id', $RowID)->delete();
        $News->delete();

        return redirect('admin/news/list')->with(NotificationHelper::newsDeleted($name));
    }

    public function news_duplicate(Request $request, $RowID)
    {
        $original = News::find($RowID);

        if (!$original) {
            return redirect('admin/news/list')->with([
                'flash_level'   => 'danger',
                'flash_message' => 'Bài viết không tồn tại.',
            ]);
        }

        if (!$this->canManageNews($original)) {
            return redirect('admin/news/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Bạn không có quyền sao chép bài viết này.',
            ]);
        }

        $duplicate = $original->replicate();
        $duplicate->Name = $original->Name . ' (Bản sao)';
        $duplicate->Alias = $original->Alias . '-copy-' . time();
        $duplicate->Status = 0;
        $duplicate->author_id = $this->resolveAuthorIdForNews(Auth::id(), $original->author_id);
        $this->ensureNewsRowIdForInsert($duplicate);
        $duplicate->save();
        $this->createOrUpdateSchedule($duplicate->RowID, NewsSchedule::PUBLISH_NOW, null, NewsSchedule::STATUS_DRAFT);

        // Copy tags
        $tagIds = DB::table('news_tags')->where('news_id', $RowID)->pluck('tag_id');
        foreach ($tagIds as $tagId) {
            DB::table('news_tags')->insert([
                'news_id'    => $duplicate->RowID,
                'tag_id'     => $tagId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect('admin/news/edit/' . $duplicate->RowID)->with([
            'flash_level'   => 'success',
            'flash_message' => 'Đã sao chép bài viết thành công!',
        ]);
    }

    public function news_bulk_action(Request $request)
    {
        $request->validate([
            'ids'   => 'required|string',
            'action' => 'required|in:delete,show,hide,submit_review',
        ], [
            'ids.required'   => 'Chưa chọn bài viết nào.',
            'action.required' => 'Chưa chọn thao tác.',
        ]);

        $ids = array_filter(array_map('intval', explode(',', $request->ids)));
        if (!$this->canManageAllNews()) {
            $ids = News::query()
                ->whereIn('RowID', $ids)
                ->where('author_id', Auth::id())
                ->pluck('RowID')
                ->map(fn ($id) => (int) $id)
                ->all();
        }
        if (empty($ids)) {
            return redirect('admin/news/list')->with([
                'flash_level'   => 'warning',
                'flash_message' => 'Không có bài viết nào được chọn.',
            ]);
        }

        $count = count($ids);

        switch ($request->action) {
            case 'delete':
                foreach ($ids as $id) {
                    $news = News::find($id);
                    if ($news) {
                        if ($news->Images) {
                            $imgPath = public_path('images/news/' . $news->Images);
                            if (is_file($imgPath)) {
                                @unlink($imgPath);
                            }
                        }
                        DB::table('news_tags')->where('news_id', $id)->delete();
                        NewsSchedule::where('news_id', $id)->delete();
                        $news->delete();
                    }
                }
                $msg = "Đã xóa {$count} bài viết.";
                break;

            case 'show':
                News::whereIn('RowID', $ids)->update(['Status' => 1]);
                $msg = "Đã hiển thị {$count} bài viết.";
                break;

            case 'hide':
                News::whereIn('RowID', $ids)->update(['Status' => 0]);
                $msg = "Đã ẩn {$count} bài viết.";
                break;

            case 'submit_review':
                foreach ($ids as $id) {
                    $news = News::find($id);
                    if ($news) {
                        $news->Status = 0;
                        $news->save();

                        $schedule = NewsSchedule::where('news_id', $id)->first();
                        if (!$schedule) {
                            $schedule = new NewsSchedule();
                            $schedule->news_id = $id;
                            $schedule->created_by = Auth::id();
                        }
                        $schedule->status = NewsSchedule::STATUS_PENDING;
                        $schedule->publish_type = 'now';
                        $schedule->save();
                    }
                }
                $msg = "Đã gửi {$count} bài viết chờ duyệt.";
                break;
        }

        return redirect('admin/news/list')->with(NotificationHelper::bulkAction($request->action, $count));
    }

    // =====================================================
    // TAG MANAGEMENT
    // =====================================================
    public function tag_list(Request $request)
    {
        return app(\App\Http\Controllers\TagController::class)->index($request);
    }

    public function tag_getadd()
    {
        return view('back.tag.add');
    }

    public function tag_add(Request $request)
    {
        return app(\App\Http\Controllers\TagController::class)->store($request);
    }

    public function tag_getedit($id)
    {
        return app(\App\Http\Controllers\TagController::class)->edit($id);
    }

    public function tag_edit(Request $request, $id)
    {
        return app(\App\Http\Controllers\TagController::class)->update($request, $id);
    }

    public function tag_delete(Request $request, $id)
    {
        return app(\App\Http\Controllers\TagController::class)->destroy($request, $id);
    }

    // =====================================================
    // DANH MỤC TIN (news_cat)
    // =====================================================

    public function news_cat_list()
    {
        $NewsCategory = NewsCategory::orderBy('RowID', 'ASC')->get();

        return view('back.news.cat_list', compact('NewsCategory'));
    }

    public function news_cat_getadd()
    {
        return view('back.news.cat_add');
    }

    public function news_cat_add(Request $request)
    {
        $request->validate([
            'Name'   => 'required|string|max:255',
            'Alias'  => 'nullable|string|max:255|unique:news_cat,Alias',
            'Status' => 'nullable|in:0,1',
        ], [
            'Name.required' => 'Vui lòng nhập tên danh mục.',
            'Alias.unique'  => 'Đường dẫn (slug) đã tồn tại.',
        ]);

        $alias = trim((string) $request->Alias);
        if ($alias === '') {
            $alias = Str::slug($request->Name);
        }

        if (NewsCategory::where('Alias', $alias)->exists()) {
            return back()->withInput()->withErrors(['Alias' => 'Đường dẫn đã tồn tại.']);
        }

        $payload = [
            'Name'   => trim($request->Name),
            'Alias'  => $alias,
            'Status' => (int) ($request->Status ?? 1),
            'color'  => $request->color ?: '#6c757d',
            'description' => $request->description ?: null,
        ];

        $rowIdCol = DB::selectOne("SHOW COLUMNS FROM news_cat WHERE Field = 'RowID'");
        if ($rowIdCol && stripos((string) ($rowIdCol->Extra ?? ''), 'auto_increment') === false) {
            $payload['RowID'] = (int) (NewsCategory::query()->max('RowID') ?? 0) + 1;
        }

        $cat = NewsCategory::create($payload);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $this->saveCategoryImage($cat, $request->file('image'));
        }

        return redirect('admin/news_cat/list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Thêm danh mục thành công.',
        ]);
    }

    public function news_cat_getedit($id)
    {
        $NewsCategory = NewsCategory::findOrFail($id);

        return view('back.news.cat_edit', compact('NewsCategory'));
    }

    public function news_cat_edit(Request $request, $id)
    {
        $NewsCategory = NewsCategory::findOrFail($id);

        $request->validate([
            'Name'   => 'required|string|max:255',
            'Alias'  => 'nullable|string|max:255|unique:news_cat,Alias,' . $id . ',RowID',
            'Status' => 'nullable|in:0,1',
        ], [
            'Name.required' => 'Vui lòng nhập tên danh mục.',
            'Alias.unique'  => 'Đường dẫn (slug) đã tồn tại.',
        ]);

        $alias = trim((string) $request->Alias);
        if ($alias === '') {
            $alias = Str::slug($request->Name);
        }

        $NewsCategory->Name = trim($request->Name);
        $NewsCategory->Alias = $alias;
        $NewsCategory->Status = (int) ($request->Status ?? $NewsCategory->Status);
        $NewsCategory->color = $request->color ?: '#6c757d';
        $NewsCategory->description = $request->description ?: null;
        $NewsCategory->save();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $this->saveCategoryImage($NewsCategory, $request->file('image'));
        }

        return redirect('admin/news_cat/list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Cập nhật danh mục thành công.',
        ]);
    }

    public function news_cat_delete(Request $request, $id)
    {
        $cat = NewsCategory::findOrFail($id);

        if (News::where('RowIDCat', $id)->exists()) {
            return redirect('admin/news_cat/list')->with([
                'flash_level'   => 'danger',
                'flash_message' => 'Không thể xóa: vẫn còn bài viết thuộc danh mục này.',
            ]);
        }

        $cat->delete();

        return redirect('admin/news_cat/list')->with([
            'flash_level'   => 'success',
            'flash_message' => 'Đã xóa danh mục.',
        ]);
    }

    /**
     * Cập nhật thứ tự danh mục (nếu bảng có cột Sort).
     */
    public function news_cat_update_sort(Request $request, $id)
    {
        $cat = NewsCategory::find($id);
        if (!$cat) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy danh mục.'], 404);
        }

        if ($request->has('sort') && Schema::hasColumn('news_cat', 'Sort')) {
            $cat->Sort = (int) $request->input('sort');
            $cat->save();
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    // =====================================================
    // STAFF
    // =====================================================

    public function staff_profile()
    {
        return view('back.staff.profile');
    }

    public function staff_profile_post(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'phone'    => 'nullable|string|max:50',
            'address'  => 'nullable|string|max:500',
            'password' => 'nullable|string|min:6',
        ]);

        $user = Auth::user();
        $user->fullname = $request->fullname;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Đã cập nhật thông tin tài khoản.',
        ]);
    }

    public function staff_list(Request $request)
    {
        $keyword = $request->input('keyword', '');
        $level = $request->input('level', '');
        $status = $request->input('status', '');

        $query = User::query()
            ->adminAccounts()
            ->with('roles:id,name,display_name')
            ->withCount('authoredNews')
            ->orderBy('id', 'desc');

        if ($keyword !== '') {
            $kw = '%' . $keyword . '%';
            $query->where(function ($q) use ($kw) {
                $q->where('username', 'like', $kw)
                    ->orWhere('fullname', 'like', $kw)
                    ->orWhere('email', 'like', $kw);
            });
        }

        if ($level !== '' && $level !== null) {
            $query->where('level', (int) $level);
        }

        if ($status !== '' && $status !== null && Schema::hasColumn('users', 'is_active')) {
            $query->where('is_active', (int) $status);
        }

        $user = $query->get();

        return view('back.staff.list', compact('user', 'keyword', 'level', 'status'));
    }

    public function staff_filter(Request $request)
    {
        $qs = http_build_query($request->except(['_token']));

        return redirect()->to(url('admin/admin-manager/list') . ($qs ? '?' . $qs : ''));
    }

    public function staff_add()
    {
        $UserLevel = UserLevel::orderBy('id')->get();
        $roles = Role::query()->orderBy('display_name')->get(['id', 'name', 'display_name', 'description']);

        return view('back.staff.add', compact('UserLevel', 'roles'));
    }

    public function staff_add_post(Request $request)
    {
        $request->validate([
            'level'    => 'required|integer|exists:users_level,id',
            'is_active'=> 'nullable|in:0,1',
            'is_author'=> 'nullable|in:0,1',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'integer|exists:roles,id',
            'fullname' => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'phone'    => 'nullable|string|max:50',
            'address'  => 'nullable|string|max:500',
            'username' => 'required|string|max:100|unique:users,username|regex:/^[a-zA-Z0-9._-]+$/',
            'password' => 'required|string|min:6',
        ]);

        $level = (int) $request->level;
        if (!in_array($level, [1, 2], true)) {
            return redirect()->back()->withInput()->with(['flash_level' => 'warning', 'flash_message' => 'Cấp bậc không hợp lệ.']);
        }

        $u = new User();
        $u->username = $request->username;
        $u->password = Hash::make($request->password);
        $u->fullname = $request->fullname;
        $u->email = $request->email;
        $u->phone = $request->phone ?? '';
        $u->address = $request->address;
        $u->level = $level;
        if (Schema::hasColumn('users', 'is_admin_account')) {
            $u->is_admin_account = 1;
        }
        if (Schema::hasColumn('users', 'is_active')) {
            $u->is_active = (int) ($request->input('is_active', 1));
        }
        if (Schema::hasColumn('users', 'is_author')) {
            $u->is_author = Auth::user()->isAdmin()
                ? (int) $request->input('is_author', 0)
                : 0;
        }
        $u->save();
        $this->syncStaffRoles($u, $request);

        return redirect()->to(url('admin/admin-manager/list'))->with(['flash_level' => 'success', 'flash_message' => 'Đã thêm nhân viên.']);
    }

    public function staff_edit($id)
    {
        $User = User::where('id', $id)->adminAccounts()->firstOrFail();
        $UserLevel = UserLevel::orderBy('id')->get();
        $roles = Role::query()->orderBy('display_name')->get(['id', 'name', 'display_name', 'description']);
        $selectedRoleIds = $User->roles()->pluck('roles.id')->map(fn ($roleId) => (int) $roleId)->all();

        return view('back.staff.edit', compact('User', 'UserLevel', 'roles', 'selectedRoleIds'));
    }

    public function staff_edit_post(Request $request, $id)
    {
        $User = User::where('id', $id)->adminAccounts()->firstOrFail();

        $request->validate([
            'level'    => 'required|integer|exists:users_level,id',
            'is_active'=> 'nullable|in:0,1',
            'is_author'=> 'nullable|in:0,1',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'integer|exists:roles,id',
            'fullname' => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $User->id,
            'phone'    => 'nullable|string|max:50',
            'address'  => 'nullable|string|max:500',
            'password' => 'nullable|string|min:6',
        ]);

        $level = (int) $request->level;
        if (!in_array($level, [1, 2], true)) {
            return redirect()->back()->withInput()->with(['flash_level' => 'warning', 'flash_message' => 'Cấp bậc không hợp lệ.']);
        }

        if ((int) Auth::id() === (int) $User->id) {
            if ($request->input('is_active') === '0' || $request->input('is_active') === 0) {
                return redirect()->back()->withInput()->with(['flash_level' => 'warning', 'flash_message' => 'Bạn không thể tự khóa tài khoản của chính mình.']);
            }
        }

        $User->level = $level;
        $User->fullname = $request->fullname;
        $User->email = $request->email;
        $User->phone = $request->phone ?? '';
        $User->address = $request->address;

        if (Schema::hasColumn('users', 'is_active')) {
            if ((int) Auth::id() !== (int) $User->id) {
                $User->is_active = (int) $request->input('is_active', 1);
            }
        }

        if ($request->filled('password')) {
            $User->password = Hash::make($request->password);
        }

        if (Schema::hasColumn('users', 'is_author') && Auth::user()->isAdmin()) {
            $User->is_author = (int) $request->input('is_author', 0);
        }

        $User->save();
        $this->syncStaffRoles($User, $request);

        return redirect()->to(url('admin/admin-manager/list'))->with(['flash_level' => 'success', 'flash_message' => 'Đã cập nhật nhân viên.']);
    }

    public function staff_delete(Request $request, $id)
    {
        if ((int) Auth::id() === (int) $id) {
            return redirect()->to(url('admin/admin-manager/list'))->with(['flash_level' => 'warning', 'flash_message' => 'Không thể xóa tài khoản đang đăng nhập.']);
        }

        $u = User::where('id', $id)->adminAccounts()->first();
        if (!$u) {
            return redirect()->to(url('admin/admin-manager/list'))->with(['flash_level' => 'warning', 'flash_message' => 'Không tìm thấy nhân viên.']);
        }

        $u->delete();

        return redirect()->to(url('admin/admin-manager/list'))->with(['flash_level' => 'success', 'flash_message' => 'Đã xóa nhân viên.']);
    }

    // =====================================================
    // SYSTEM
    // =====================================================

    public function system()
    {
        $name = System::where('Code', 'name')->first() ?? (object) ['Description' => ''];
        $logo_text = System::where('Code', 'logo_text')->first() ?? (object) ['Description' => 'VNXPRESS'];
        $logo_type = System::where('Code', 'logo_type')->first() ?? (object) ['Description' => 'text'];
        $logo = System::where('Code', 'logo')->first() ?? (object) ['Description' => ''];
        $favicon = System::where('Code', 'favicon')->first() ?? (object) ['Description' => ''];
        $email = System::where('Code', 'email')->first() ?? (object) ['Description' => ''];
        $phone = System::where('Code', 'phone')->first() ?? (object) ['Description' => ''];
        $address = System::where('Code', 'address')->first() ?? (object) ['Description' => ''];
        $map = System::where('Code', 'map')->first() ?? (object) ['Description' => ''];
        $copyright = System::where('Code', 'copyright')->first() ?? (object) ['Description' => ''];

        return view('back.system.system', compact(
            'name',
            'logo_text',
            'logo_type',
            'logo',
            'favicon',
            'email',
            'phone',
            'address',
            'map',
            'copyright'
        ));
    }

    public function system_post(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'logo_text' => 'required|string|max:255',
            'logo_type' => 'required|in:text,image',
            'email'     => 'required|email|max:255',
            'phone'     => 'required|string|max:100',
            'address'   => 'nullable|string|max:500',
            'map'       => 'nullable|string',
            'copyright' => 'nullable|string|max:255',
            'logo'      => 'nullable|file|mimes:jpeg,jpg,png,gif,svg,webp|max:4096',
            'favicon'   => 'nullable|file|mimes:ico,png,gif,jpeg,jpg,svg|max:1024',
        ]);

        $this->saveSystemRow('name', $request->name);
        $this->saveSystemRow('logo_text', $request->logo_text);
        $this->saveSystemRow('logo_type', $request->logo_type);
        $this->saveSystemRow('email', $request->email);
        $this->saveSystemRow('phone', $request->phone);
        $this->saveSystemRow('address', (string) $request->input('address', ''));
        $this->saveSystemRow('map', (string) $request->input('map', ''));
        $this->saveSystemRow('copyright', (string) $request->input('copyright', ''));

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $fn = $this->storeSystemUpload($request->file('logo'), 'images/logo', ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp']);
            if ($fn !== '') {
                $this->saveSystemRow('logo', $fn);
            }
        }

        if ($request->hasFile('favicon') && $request->file('favicon')->isValid()) {
            $fn = $this->storeSystemUpload($request->file('favicon'), 'images/favicon', ['ico', 'png', 'jpg', 'jpeg', 'gif', 'svg']);
            if ($fn !== '') {
                $this->saveSystemRow('favicon', $fn);
            }
        }

        return redirect()->back()->with(['flash_level' => 'success', 'flash_message' => 'Đã lưu cấu hình hệ thống.']);
    }

    // =====================================================
    // PAGE / SOCIAL / NEWSLETTER / CONTACT / SLIDER
    // =====================================================

    public function page_list()
    {
        $page = Page::orderBy('Sort', 'asc')->orderBy('RowID', 'asc')->get();

        return view('back.page.list', compact('page'));
    }

    public function page_edit($id)
    {
        $page = Page::where('RowID', $id)->firstOrFail();

        return view('back.page.edit', compact('page'));
    }

    public function page_edit_post(Request $request, $id)
    {
        $page = Page::where('RowID', $id)->firstOrFail();

        $request->validate([
            'Status'         => 'required|in:0,1',
            'menu_kind'      => 'nullable|string|max:50',
            'Name'           => 'required|string|max:255',
            'Alias'          => 'nullable|string|max:500',
            'Font'           => 'nullable|string|max:100',
            'Sort'           => 'nullable|integer',
            'MetaTitle'      => 'nullable|string',
            'MetaDescription'=> 'nullable|string',
            'MetaKeyword'    => 'nullable|string',
            'Description'    => 'required|string',
            'Images'         => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:4096',
        ]);

        $kind = $request->input('menu_kind', Page::MENU_LINK);
        $allowedKinds = array_keys(Page::menuKindLabels());
        if (!in_array($kind, $allowedKinds, true)) {
            $kind = Page::MENU_LINK;
        }

        $page->Status = (int) $request->Status;
        if (Schema::hasColumn('page', 'menu_kind')) {
            $page->menu_kind = $kind;
        }
        $page->Name = $request->Name;
        $page->Alias = $request->Alias ?? '';
        $page->Font = $request->Font ?? '';
        $page->Sort = (int) $request->input('Sort', 0);
        $page->MetaTitle = $request->MetaTitle ?? '';
        $page->MetaDescription = $request->MetaDescription ?? '';
        $page->MetaKeyword = $request->MetaKeyword ?? '';
        $page->Description = $request->Description;

        if ($request->hasFile('Images') && $request->file('Images')->isValid()) {
            $img = $this->storePageImage($request->file('Images'), $page->Images);
            if ($img !== '') {
                $page->Images = $img;
            }
        }

        $page->save();

        return redirect()->to(url('admin/page/list'))->with(['flash_level' => 'success', 'flash_message' => 'Đã cập nhật trang.']);
    }

    public function social_list()
    {
        $Social = Social::orderBy('Sort', 'asc')->orderBy('RowID', 'asc')->get();

        return view('back.social.list', compact('Social'));
    }

    public function social_edit($id)
    {
        $Social = Social::where('RowID', $id)->firstOrFail();

        return view('back.social.edit', compact('Social'));
    }

    public function social_edit_post(Request $request, $id)
    {
        $row = Social::where('RowID', $id)->firstOrFail();

        $request->validate([
            'Status' => 'required|in:0,1',
            'Alias'  => 'required|string|max:500',
            'Name'   => 'required|string|max:255',
            'Font'   => 'required|string|max:2000',
            'Sort'   => 'nullable|integer|min:0',
        ], [
            'Status.required' => 'Vui lòng chọn trạng thái.',
            'Alias.required'  => 'Vui lòng nhập đường dẫn (URL) mạng xã hội.',
            'Name.required'   => 'Vui lòng nhập tên mạng xã hội.',
            'Font.required'   => 'Vui lòng nhập mã icon (Font).',
            'Font.max'        => 'Mã Font không được vượt quá :max ký tự.',
        ]);

        $row->Status = (int) $request->Status;
        $row->Alias = trim((string) $request->Alias);
        $row->Name = trim((string) $request->Name);
        $row->Font = trim((string) $request->Font);
        $row->Sort = (int) ($request->input('Sort') === '' || $request->input('Sort') === null
            ? $row->Sort
            : $request->Sort);
        $row->save();

        return redirect()->to(url('admin/social/list'))->with(['flash_level' => 'success', 'flash_message' => 'Đã cập nhật mạng xã hội.']);
    }

    public function newsletter_list(Request $request)
    {
        $q = Newsletter::query()->orderByDesc('RowID');

        $kw = trim((string) $request->input('keyword', ''));
        if ($kw !== '') {
            $q->where('Email', 'like', '%' . $kw . '%');
        }

        $st = $request->input('status', '');
        if ($st === '1') {
            $q->where('is_active', true)->whereNull('unsubscribed_at');
        } elseif ($st === '2') {
            $q->where(function ($qq) {
                $qq->where('is_active', false)->orWhereNotNull('unsubscribed_at');
            });
        }

        $Newsletter = $q->paginate(30)->withQueryString();

        $stats = [
            'total'      => Newsletter::count(),
            'active'     => Newsletter::where('is_active', true)->whereNull('unsubscribed_at')->count(),
            'unsub'      => Newsletter::where(function ($qq) {
                $qq->where('is_active', false)->orWhereNotNull('unsubscribed_at');
            })->count(),
            'unreviewed' => Newsletter::where('is_reviewed', false)->count(),
        ];

        return view('back.newsletter.list', compact('Newsletter', 'stats'));
    }

    public function newsletter_export()
    {
        $filename = 'newsletter-' . date('Y-m-d-His') . '.csv';

        return new StreamedResponse(function () {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Email', 'is_active', 'subscribed_at', 'unsubscribed_at', 'ip_address']);

            Newsletter::orderBy('RowID')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $r->Email,
                        $r->is_active ? '1' : '0',
                        $r->subscribed_at ? $r->subscribed_at->format('Y-m-d H:i:s') : '',
                        $r->unsubscribed_at ? $r->unsubscribed_at->format('Y-m-d H:i:s') : '',
                        $r->ip_address ?? '',
                    ]);
                }
            });
            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function newsletter_edit($id)
    {
        $Newsletter = Newsletter::where('RowID', $id)->firstOrFail();

        return view('back.newsletter.edit', compact('Newsletter'));
    }

    public function newsletter_edit_post(Request $request, $id)
    {
        $row = Newsletter::where('RowID', $id)->firstOrFail();

        $request->validate([
            'Email'        => 'required|email|max:255|unique:newsletter,Email,' . $row->RowID . ',RowID',
            'is_active'    => 'required|in:0,1',
            'is_reviewed'  => 'required|in:0,1',
        ]);

        $row->Email = $request->Email;
        $row->is_active = (bool) (int) $request->is_active;
        $row->is_reviewed = (bool) (int) $request->is_reviewed;

        if (!$row->is_active && !$row->unsubscribed_at) {
            $row->unsubscribed_at = now();
        }
        if ($row->is_active) {
            $row->unsubscribed_at = null;
        }

        $row->save();

        return redirect()->to(url('admin/newsletter/list'))->with(['flash_level' => 'success', 'flash_message' => 'Đã cập nhật đăng ký newsletter.']);
    }

    public function newsletter_delete(Request $request, $id)
    {
        $row = Newsletter::where('RowID', $id)->first();
        if ($row) {
            $row->delete();
        }

        return redirect()->to(url('admin/newsletter/list'))->with([
            'flash_level'   => 'success',
            'flash_message' => 'Xóa email khỏi danh sách nhận tin thành công.',
        ]);
    }

    public function contact_list(Request $request)
    {
        $q = Contact::query()->orderByDesc((new Contact())->getKeyName());

        $kw = trim((string) $request->input('keyword', ''));
        if ($kw !== '') {
            $like = '%' . $kw . '%';
            $q->where(function ($qq) use ($like) {
                $qq->where('Name', 'like', $like)
                    ->orWhere('Email', 'like', $like)
                    ->orWhere('Phone', 'like', $like)
                    ->orWhere('subject', 'like', $like);
            });
        }

        $status = $request->input('status', '');
        if ($status === 'new') {
            $q->where('is_reviewed', false)->whereNull('replied_at');
        } elseif ($status === 'read') {
            $q->where('is_reviewed', true)->whereNull('replied_at');
        } elseif ($status === 'replied') {
            $q->whereNotNull('replied_at');
        }

        $cat = $request->input('category', '');
        if ($cat !== '') {
            $q->where('category', $cat);
        }

        $pri = $request->input('priority', '');
        if ($pri !== '') {
            $q->where('priority', $pri);
        }

        $Contact = $q->paginate(30)->withQueryString();

        $stats = [
            'total'   => Contact::count(),
            'new'     => Contact::where('is_reviewed', false)->whereNull('replied_at')->count(),
            'read'    => Contact::where('is_reviewed', true)->whereNull('replied_at')->count(),
            'replied' => Contact::whereNotNull('replied_at')->count(),
        ];

        $categoryLabels = Contact::categoryLabels();
        $priorityLabels = Contact::priorityLabels();
        $categoryColors = Contact::categoryColors();
        $priorityColors = Contact::priorityColors();

        return view('back.contact.list', compact(
            'Contact',
            'stats',
            'categoryLabels',
            'priorityLabels',
            'categoryColors',
            'priorityColors'
        ));
    }

    public function contact_edit($id)
    {
        $Contact = $this->findContactRecord($id);
        $replies = $Contact->replies()->get();
        $staffs = User::query()->adminAccounts()->orderBy('fullname')->get(['id', 'fullname', 'email']);
        $categoryLabels = Contact::categoryLabels();
        $priorityLabels = Contact::priorityLabels();
        $categoryColors = Contact::categoryColors();
        $priorityColors = Contact::priorityColors();

        return view('back.contact.edit', compact('Contact', 'replies', 'staffs', 'categoryLabels', 'priorityLabels', 'categoryColors', 'priorityColors'));
    }

    public function contact_edit_post(Request $request, $id)
    {
        $c = $this->findContactRecord($id);

        $request->validate([
            'Name1'       => 'required|string|max:255',
            'Email'       => 'required|email|max:255',
            'txtPhone'    => 'required|string|max:50',
            'selCategory' => 'nullable|string|max:50',
            'selPriority' => 'nullable|string|max:50',
            'assigned_to' => 'nullable|integer|exists:users,id',
            'txtSubject'  => 'nullable|string|max:500',
            'Message'     => 'required|string',
            'admin_note'  => 'nullable|string',
        ]);

        $c->Name = $request->Name1;
        $c->Email = $request->Email;
        $c->Phone = $request->txtPhone;
        $c->category = $request->input('selCategory', Contact::CATEGORY_OTHER);
        $c->priority = $request->input('selPriority', Contact::PRIORITY_MEDIUM);
        $c->assigned_to = $request->input('assigned_to') ?: null;
        $c->subject = $request->txtSubject;
        $c->Message = $request->Message;
        $c->admin_note = $request->admin_note;

        $c->is_reviewed = $request->has('is_reviewed');

        if ($request->has('is_replied')) {
            if (!$c->replied_at) {
                $c->replied_at = now();
            }
            $c->is_reviewed = true;
        }

        $c->save();

        return redirect()->to(url('admin/contact/edit/' . $c->getKey()))->with(['flash_level' => 'success', 'flash_message' => 'Đã lưu liên hệ.']);
    }

    public function contact_delete(Request $request, $id)
    {
        $c = Contact::query()->find($id);
        if ($c) {
            ContactReply::where('contact_id', $c->getKey())->delete();
            $c->delete();
        }

        return redirect()->to(url('admin/contact/list'))->with(['flash_level' => 'success', 'flash_message' => 'Đã xóa liên hệ.']);
    }

    public function contact_mark_read($id)
    {
        $c = Contact::query()->find($id);
        if ($c) {
            $c->markAsReviewed();
        }

        return redirect()->back()->with(['flash_level' => 'success', 'flash_message' => 'Đã đánh dấu đã xem.']);
    }

    public function contact_mark_replied($id)
    {
        $c = Contact::query()->find($id);
        if ($c) {
            $c->markAsReplied();
        }

        return redirect()->back()->with(['flash_level' => 'success', 'flash_message' => 'Đã đánh dấu đã phản hồi.']);
    }

    public function contactReply(Request $request, $id)
    {
        $c = $this->findContactRecord($id);

        $request->validate([
            'reply_intro'   => 'nullable|string|max:500',
            'reply_content' => 'required|string|min:5|max:5000',
            'reply_outro'   => 'nullable|string|max:500',
        ]);

        $staff = Auth::user();
        ContactReply::create([
            'contact_id'       => $c->getKey(),
            'staff_id'         => $staff->id,
            'staff_name'       => $staff->fullname ?? $staff->username,
            'reply_intro'      => $request->reply_intro,
            'reply_content'    => $request->reply_content,
            'reply_outro'      => $request->reply_outro,
            'recipient_email'  => $c->Email,
            'sent_at'          => now(),
        ]);

        $replyData = [
            'subject'          => $c->subject ?? 'Liên hệ của bạn',
            'contact_name'     => $c->Name,
            'intro'            => $request->reply_intro,
            'original_subject' => $c->subject,
            'original_message' => $c->Message,
            'original_date'    => $c->created_at ? $c->created_at->format('d/m/Y H:i') : '',
            'reply_content'    => $request->reply_content,
            'outro'            => $request->reply_outro,
            'staff_name'       => $staff->fullname ?? $staff->username,
        ];

        try {
            Mail::send('emails.contact_reply', ['reply' => $replyData], function ($message) use ($c, $replyData) {
                $message->to($c->Email)
                    ->subject('Phản hồi từ đội ngũ hỗ trợ - ' . ($replyData['subject'] ?? 'Liên hệ của bạn'));
            });
        } catch (\Throwable $e) {
            Log::error('contactReply mail: ' . $e->getMessage());
        }

        // Lưu nội dung phản hồi cuối cùng vào contact
        $c->last_reply_content = $request->reply_content;
        $c->is_reviewed = true;
        $c->replied_at = now();
        $c->save();

        return redirect()->to(url('admin/contact/edit/' . $c->getKey()))->with(['flash_level' => 'success', 'flash_message' => 'Đã gửi phản hồi.']);
    }

    public function slider_list()
    {
        $Slider = Slider::orderBy('Sort', 'asc')->orderBy('RowID', 'asc')->get();

        return view('back.slider.list', compact('Slider'));
    }

    public function slider_getAdd()
    {
        return view('back.slider.add');
    }

    public function slider_add(Request $request)
    {
        $request->validate([
            'Status' => 'required|in:0,1',
            'Name'   => 'required|string|max:255',
            'Alias'  => 'required|string|max:500',
            'Sort'   => 'nullable|integer',
            'Images' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:4096',
        ]);

        $row = new Slider();
        $row->Status = (int) $request->Status;
        $row->Name = $request->Name;
        $row->Alias = $request->Alias;
        $row->Sort = (int) $request->input('Sort', 1);
        $row->Images = '';

        if ($request->hasFile('Images') && $request->file('Images')->isValid()) {
            $row->Images = $this->storeSliderImage($request->file('Images'));
        }

        $row->save();

        return redirect()->to(url('admin/slider/list'))->with(['flash_level' => 'success', 'flash_message' => 'Đã thêm slider.']);
    }

    public function slider_getedit($RowID)
    {
        $Slider = Slider::where('RowID', $RowID)->firstOrFail();

        return view('back.slider.edit', compact('Slider'));
    }

    public function slider_edit(Request $request, $id)
    {
        $row = Slider::where('RowID', $id)->firstOrFail();

        $request->validate([
            'Status' => 'required|in:0,1',
            'Name'   => 'required|string|max:255',
            'Alias'  => 'required|string|max:500',
            'Sort'   => 'nullable|integer',
            'Images' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:4096',
        ]);

        $row->Status = (int) $request->Status;
        $row->Name = $request->Name;
        $row->Alias = $request->Alias;
        $row->Sort = (int) $request->input('Sort', 0);

        if ($request->hasFile('Images') && $request->file('Images')->isValid()) {
            $img = $this->storeSliderImage($request->file('Images'), $row->Images);
            if ($img !== '') {
                $row->Images = $img;
            }
        }

        $row->save();

        return redirect()->to(url('admin/slider/list'))->with(['flash_level' => 'success', 'flash_message' => 'Đã cập nhật slider.']);
    }

    public function slider_delete(Request $request, $RowID)
    {
        $row = Slider::where('RowID', $RowID)->first();
        if ($row) {
            $path = 'images/slider/' . $row->Images;
            if (!empty($row->Images) && file_exists($path)) {
                @unlink($path);
            }
            $row->delete();
        }

        return redirect()->to(url('admin/slider/list'))->with(['flash_level' => 'success', 'flash_message' => 'Đã xóa slider.']);
    }

    // =====================================================
    // QUẢN LÝ QUẢNG CÁO (ADS)
    // =====================================================

    public function ad_list(Request $request)
    {
        $baseQuery = Ad::query()->popup();
        $adStats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('status', true)->count(),
            'inactive' => (clone $baseQuery)->where('status', false)->count(),
            'views' => (int) (clone $baseQuery)->sum('view_count'),
            'clicks' => (int) (clone $baseQuery)->sum('click_count'),
        ];

        $query = (clone $baseQuery)
            ->orderBy('priority', 'desc')
            ->orderBy('sort', 'asc');

        if ($request->filled('keyword')) {
            $kw = '%' . trim($request->keyword) . '%';
            $query->where('name', 'like', $kw);
        }

        if ($request->filled('status')) {
            $query->where('status', (int) $request->status === 1);
        }

        $Ads = $query->paginate(20);

        return view('back.ad.list', compact('Ads', 'adStats'));
    }

    public function ad_getAdd()
    {
        return view('back.ad.add');
    }

    public function ad_add(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:4096',
            'link' => 'nullable|url|max:500',
            'location' => 'required|in:homepage,article,all',
            'popup_position' => 'nullable|in:center,bottom_right,bottom_left,top_right,top_left',
            'auto_close_seconds' => 'nullable|integer|min:0|max:300',
            'show_once_per_session' => 'nullable|in:0,1',
            'show_close_button' => 'nullable|in:0,1',
            'impression_limit' => 'nullable|integer|min:0|max:999',
            'cooldown_minutes' => 'nullable|integer|min:0|max:525600',
            'show_delay_seconds' => 'nullable|integer|min:0|max:300',
            'status' => 'nullable|in:0,1',
            'sort' => 'nullable|integer|min:0',
            'priority' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'name.required' => 'Vui lòng nhập tên quảng cáo.',
            'type.in' => 'Loại quảng cáo không hợp lệ.',
            'location.in' => 'Vị trí hiển thị không hợp lệ.',
        ]);

        $ad = new Ad();
        $ad->name = trim($request->name);
        $ad->link = $request->filled('link') ? trim($request->link) : null;
        $ad->type = Ad::TYPE_POPUP;
        $ad->location = $request->input('location', Ad::LOC_ALL);
        $ad->popup_position = $request->input('popup_position', 'center');
        $ad->show_once_per_session = $request->has('show_once_per_session') ? (int) $request->show_once_per_session : false;
        $ad->auto_close_seconds = (int) $request->input('auto_close_seconds', 0);
        $ad->show_close_button = $request->has('show_close_button') ? (int) $request->show_close_button : true;
        $ad->impression_limit = (int) $request->input('impression_limit', 1);
        $ad->cooldown_minutes = (int) $request->input('cooldown_minutes', 30);
        $ad->show_delay_seconds = (int) $request->input('show_delay_seconds', 2);
        $ad->banner_width = null;
        $ad->banner_height = null;
        $ad->banner_align = 'center';
        $ad->status = $request->has('status') ? (int) $request->status : true;
        $ad->sort = $request->filled('sort')
            ? (int) $request->input('sort')
            : $this->nextAdSort(Ad::TYPE_POPUP, Ad::LOC_ALL);
        $ad->priority = (int) $request->input('priority', 0);

        if ($request->filled('start_date')) {
            $ad->start_date = \Carbon\Carbon::parse($request->start_date);
        }

        if ($request->filled('end_date')) {
            $ad->end_date = \Carbon\Carbon::parse($request->end_date);
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $ad->image = $this->storeAdImage($request->file('image'));
        }

        $ad->save();

        return redirect()->to(url('admin/ads/list'))->with([
            'flash_level' => 'success',
            'flash_message' => 'Đã thêm quảng cáo mới.',
        ]);
    }

    public function ad_getedit($id)
    {
        $Ad = Ad::popup()->findOrFail($id);

        return view('back.ad.edit', compact('Ad'));
    }

    public function ad_edit(Request $request, $id)
    {
        $ad = Ad::popup()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:4096',
            'link' => 'nullable|url|max:500',
            'location' => 'required|in:homepage,article,all',
            'popup_position' => 'nullable|in:center,bottom_right,bottom_left,top_right,top_left',
            'auto_close_seconds' => 'nullable|integer|min:0|max:300',
            'show_once_per_session' => 'nullable|in:0,1',
            'show_close_button' => 'nullable|in:0,1',
            'impression_limit' => 'nullable|integer|min:0|max:999',
            'cooldown_minutes' => 'nullable|integer|min:0|max:525600',
            'show_delay_seconds' => 'nullable|integer|min:0|max:300',
            'status' => 'nullable|in:0,1',
            'sort' => 'nullable|integer|min:0',
            'priority' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $ad->name = trim($request->name);
        $ad->link = $request->filled('link') ? trim($request->link) : null;
        $ad->type = Ad::TYPE_POPUP;
        $ad->location = $request->input('location', Ad::LOC_ALL);
        $ad->popup_position = $request->input('popup_position', 'center');
        $ad->show_once_per_session = $request->has('show_once_per_session') ? (int) $request->show_once_per_session : false;
        $ad->auto_close_seconds = (int) $request->input('auto_close_seconds', 0);
        $ad->show_close_button = $request->has('show_close_button') ? (int) $request->show_close_button : true;
        $ad->impression_limit = (int) $request->input('impression_limit', 1);
        $ad->cooldown_minutes = (int) $request->input('cooldown_minutes', 30);
        $ad->show_delay_seconds = (int) $request->input('show_delay_seconds', 2);
        $ad->banner_width = null;
        $ad->banner_height = null;
        $ad->banner_align = 'center';
        $ad->status = $request->has('status') ? (int) $request->status : true;
        $ad->sort = $request->filled('sort')
            ? (int) $request->input('sort')
            : $ad->sort;
        $ad->priority = (int) $request->input('priority', 0);

        if ($request->filled('start_date')) {
            $ad->start_date = \Carbon\Carbon::parse($request->start_date);
        } else {
            $ad->start_date = null;
        }

        if ($request->filled('end_date')) {
            $ad->end_date = \Carbon\Carbon::parse($request->end_date);
        } else {
            $ad->end_date = null;
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $ad->image = $this->storeAdImage($request->file('image'), $ad->image);
        }

        $ad->save();

        return redirect()->to(url('admin/ads/edit/' . $id))->with([
            'flash_level' => 'success',
            'flash_message' => 'Đã cập nhật quảng cáo.',
        ]);
    }

    public function ad_delete(Request $request, $id)
    {
        $ad = Ad::popup()->findOrFail($id);

        if ($ad->image) {
            $path = public_path('images/ads/' . $ad->image);
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $ad->delete();

        return redirect()->to(url('admin/ads/list'))->with([
            'flash_level' => 'success',
            'flash_message' => 'Đã xóa quảng cáo.',
        ]);
    }

    /**
     * Lưu ảnh quảng cáo
     */
    private function storeAdImage($file, ?string $oldImage = null): string
    {
        if (!$file || !$file->isValid()) {
            return '';
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true)) {
            return '';
        }

        $dir = public_path('images/ads');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($oldImage && file_exists($dir . DIRECTORY_SEPARATOR . $oldImage)) {
            @unlink($dir . DIRECTORY_SEPARATOR . $oldImage);
        }

        $name = time() . '-' . rand(1000, 9999) . '.' . $ext;
        $file->move($dir, $name);

        return $name;
    }

    private function nextAdSort(string $type, string $location): int
    {
        $maxSort = Ad::query()
            ->where('type', $type)
            ->where('location', $location)
            ->max('sort');

        return ((int) $maxSort) + 1;
    }

    // =====================================================
    // HELPERS
    // =====================================================

    private function historicalAuthorUsers()
    {
        if (!$this->canManageAllNews()) {
            return User::query()
                ->where('id', Auth::id())
                ->get(['id', 'fullname', 'username']);
        }

        return User::query()
            ->authorAccounts()
            ->orderBy('fullname')
            ->get(['id', 'fullname', 'username']);
    }

    private function articleAuthorUsers(?int $selectedAuthorId = null)
    {
        if (!$this->canManageAllNews()) {
            return User::query()
                ->where('id', Auth::id())
                ->get(['id', 'fullname', 'username']);
        }

        $authors = User::query()
            ->authorCandidates()
            ->orderBy('fullname')
            ->get(['id', 'fullname', 'username']);

        if ($selectedAuthorId) {
            $selectedAuthor = User::query()
                ->adminAccounts()
                ->where('id', $selectedAuthorId)
                ->first(['id', 'fullname', 'username']);

            if ($selectedAuthor && !$authors->contains('id', $selectedAuthor->id)) {
                $authors->prepend($selectedAuthor);
                $authors = $authors->sortBy(function ($author) {
                    return mb_strtolower($author->fullname ?? $author->username ?? '');
                })->values();
            }
        }

        return $authors;
    }

    private function resolveAuthorIdForNews($requestedAuthorId, ?int $currentAuthorId = null): ?int
    {
        if (!$this->canManageAllNews()) {
            return Auth::id() ? (int) Auth::id() : $currentAuthorId;
        }

        $requestedAuthorId = $requestedAuthorId ? (int) $requestedAuthorId : null;

        if ($requestedAuthorId) {
            $isCurrentHistoricalAuthor = $currentAuthorId && $requestedAuthorId === (int) $currentAuthorId;
            $isSelectableAuthor = User::query()
                ->authorCandidates()
                ->where('id', $requestedAuthorId)
                ->exists();

            if ($isSelectableAuthor || $isCurrentHistoricalAuthor) {
                return $requestedAuthorId;
            }
        }

        $currentUserId = Auth::id() ? (int) Auth::id() : null;
        if ($currentUserId) {
            $isCurrentUserAuthor = User::query()
                ->authorCandidates()
                ->where('id', $currentUserId)
                ->exists();

            if ($isCurrentUserAuthor) {
                return $currentUserId;
            }

            $isCurrentUserAdminStaff = User::query()
                ->adminAccounts()
                ->active()
                ->where('id', $currentUserId)
                ->exists();

            if ($isCurrentUserAdminStaff) {
                return $currentUserId;
            }
        }

        return $currentAuthorId;
    }

    private function canManageAllNews(): bool
    {
        $user = Auth::user();

        return $user && (
            $user->isAdmin()
            || $user->hasPermission('news.approve')
            || $user->hasPermission('news.edit_all')
        );
    }

    private function syncStaffRoles(User $user, Request $request): void
    {
        if ($user->isAdmin()) {
            $superAdminRoleId = Role::query()->where('name', 'super_admin')->value('id');
            $user->roles()->sync($superAdminRoleId ? [(int) $superAdminRoleId] : []);
            return;
        }

        if (Auth::user()?->isAdmin()) {
            $roleIds = collect($request->input('role_ids', []))
                ->map(fn ($roleId) => (int) $roleId)
                ->filter()
                ->unique()
                ->values()
                ->all();
            $superAdminRoleId = Role::query()->where('name', 'super_admin')->value('id');
            if ($superAdminRoleId) {
                $roleIds = array_values(array_diff($roleIds, [(int) $superAdminRoleId]));
            }
        } else {
            $roleIds = $user->roles()->pluck('roles.id')->map(fn ($roleId) => (int) $roleId)->all();
        }

        if (empty($roleIds)) {
            $defaultRole = $user->isAuthor() ? 'writer' : 'viewer';
            $defaultRoleId = Role::query()->where('name', $defaultRole)->value('id');
            $roleIds = $defaultRoleId ? [(int) $defaultRoleId] : [];
        }

        $user->roles()->sync($roleIds);
    }

    private function canManageNews(News $news): bool
    {
        if ($this->canManageAllNews()) {
            return true;
        }

        return Auth::check() && (int) $news->author_id === (int) Auth::id();
    }

    private function resolveDashboardDateRange(Request $request): array
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $end = $request->filled('end_date')
            ? \Carbon\Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->copy()->endOfDay();

        $start = $request->filled('start_date')
            ? \Carbon\Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->copy()->subMonth()->startOfDay();

        return [
            'start' => $start,
            'end' => $end,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'key' => $this->detectDashboardRangeKey($start, $end),
        ];
    }

    private function detectDashboardRangeKey(\Carbon\Carbon $start, \Carbon\Carbon $end): string
    {
        $today = now()->copy()->startOfDay();
        $endDay = $end->copy()->startOfDay();

        if (!$endDay->equalTo($today)) {
            return 'custom';
        }

        $days = $start->copy()->startOfDay()->diffInDays($today);

        return match ($days) {
            0 => 'today',
            7, 6 => 'week',
            31, 30, 29, 28 => 'month',
            92, 91, 90, 89 => 'quarter',
            366, 365, 364 => 'year',
            default => 'custom',
        };
    }

    private function applyDashboardNewsDateFilter($query, \Carbon\Carbon $start, \Carbon\Carbon $end, string $table = 'news')
    {
        return $query->where(function ($dateQuery) use ($start, $end, $table) {
            $dateColumn = $table . '.Date';
            $createdColumn = $table . '.created_at';

            $dateQuery->whereBetween($dateColumn, [$start->toDateString(), $end->toDateString()])
                ->orWhere(function ($fallbackQuery) use ($start, $end, $dateColumn, $createdColumn) {
                    $fallbackQuery->where(function ($emptyDateQuery) use ($dateColumn) {
                        $emptyDateQuery->whereNull($dateColumn)->orWhere($dateColumn, '');
                    })->whereBetween($createdColumn, [$start, $end]);
                });
        });
    }

    private function getWeeklyViews(): array
    {
        $start = \Carbon\Carbon::today()->subDays(6);
        $end = \Carbon\Carbon::today();
        $stats = NewsViewStat::query()
            ->selectRaw('view_date, SUM(total_views) as total')
            ->whereBetween('view_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('view_date')
            ->pluck('total', 'view_date');

        $labels = [];
        $data = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $labels[] = match ($date->dayOfWeekIso) {
                1 => 'T2',
                2 => 'T3',
                3 => 'T4',
                4 => 'T5',
                5 => 'T6',
                6 => 'T7',
                default => 'CN',
            };
            $data[] = (int) ($stats[$date->toDateString()] ?? 0);
        }

        $today = (int) ($stats[today()->toDateString()] ?? 0);
        $week = array_sum($data);
        $month = (int) (NewsViewStat::query()
            ->whereBetween('view_date', [
                now()->copy()->startOfMonth()->toDateString(),
                now()->copy()->endOfMonth()->toDateString(),
            ])
            ->sum('total_views'));
        $allTime = (int) News::query()->sum('Views');

        return [
            'labels' => $labels,
            'data' => $data,
            'max' => max([1, ...$data]),
            'today' => $today,
            'week' => $week,
            'month' => $month,
            'all_time' => $allTime,
        ];
    }

    private function getRecentActivities(\Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        $activities = collect();

        News::query()
            ->whereBetween('created_at', [$start, $end])
            ->latest('created_at')
            ->limit(4)
            ->get(['RowID', 'Name', 'Title', 'created_at'])
            ->each(function (News $news) use ($activities) {
                $title = $news->Name ?: $news->Title ?: 'Bài viết không tên';

                $activities->push([
                    'time' => $news->created_at,
                    'link' => url('admin/news/edit/' . $news->RowID),
                    'icon' => 'fa-newspaper',
                    'icon_bg' => 'gold',
                    'title' => 'Bài viết mới: <strong>' . e(\Illuminate\Support\Str::limit($title, 70)) . '</strong>',
                    'subtitle' => 'Cập nhật nội dung',
                ]);
            });

        \App\Models\NewsComment::query()
            ->whereBetween('created_at', [$start, $end])
            ->latest('created_at')
            ->limit(4)
            ->get(['id', 'content', 'created_at'])
            ->each(function ($comment) use ($activities) {
                $activities->push([
                    'time' => $comment->created_at,
                    'link' => url('admin/comment/list'),
                    'icon' => 'fa-comments',
                    'icon_bg' => 'blue',
                    'title' => 'Bình luận mới: <strong>' . e(\Illuminate\Support\Str::limit((string) $comment->content, 70)) . '</strong>',
                    'subtitle' => 'Tương tác độc giả',
                ]);
            });

        Contact::query()
            ->whereBetween('created_at', [$start, $end])
            ->latest('created_at')
            ->limit(4)
            ->selectRaw('RowID as id, Name, subject, created_at')
            ->get()
            ->each(function (Contact $contact) use ($activities) {
                $subject = $contact->subject ?: $contact->Name ?: 'Liên hệ mới';

                $activities->push([
                    'time' => $contact->created_at,
                    'link' => url('admin/contact/edit/' . $contact->getKey()),
                    'icon' => 'fa-envelope-open-text',
                    'icon_bg' => 'red',
                    'title' => 'Liên hệ mới: <strong>' . e(\Illuminate\Support\Str::limit($subject, 70)) . '</strong>',
                    'subtitle' => 'Hộp thư liên hệ',
                ]);
            });

        return $activities
            ->sortByDesc('time')
            ->take(8)
            ->values()
            ->all();
    }

    private function getCategoryStats(\Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        $palette = ['#c9a84c', '#4a9eff', '#5cb97b', '#e57373', '#8f7cff', '#00b8a9', '#f78c6b', '#90caf9'];
        $rows = NewsCategory::query()
            ->leftJoin('news', function ($join) use ($start, $end) {
                $join->on('news_cat.RowID', '=', 'news.RowIDCat')
                    ->where('news.Status', 1);
                $this->applyDashboardNewsDateFilter($join, $start, $end);
            })
            ->groupBy('news_cat.RowID', 'news_cat.Name', 'news_cat.color')
            ->orderByDesc(DB::raw('COUNT(news.RowID)'))
            ->limit(6)
            ->get([
                'news_cat.RowID',
                'news_cat.Name',
                'news_cat.color',
                DB::raw('COUNT(news.RowID) as total_news'),
            ]);

        return [
            'labels' => $rows->pluck('Name')->map(fn ($name) => $name ?: 'Chưa đặt tên')->values()->all(),
            'data' => $rows->pluck('total_news')->map(fn ($count) => (int) $count)->values()->all(),
            'colors' => $rows->values()->map(function ($row, $index) use ($palette) {
                return $row->color ?: ($palette[$index % count($palette)] ?? '#c9a84c');
            })->all(),
        ];
    }

    private function getTopAuthors(\Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        return User::query()
            ->adminAccounts()
            ->withCount(['authoredNews as news_count' => function ($query) use ($start, $end) {
                $query->where('Status', 1);
                $this->applyDashboardNewsDateFilter($query, $start, $end);
            }])
            ->orderByDesc('news_count')
            ->orderBy('fullname')
            ->limit(5)
            ->get(['id', 'fullname', 'username', 'level'])
            ->map(function (User $user) {
                return [
                    'name' => $user->fullname ?: $user->username ?: 'Không rõ',
                    'level' => (int) $user->level,
                    'news_count' => (int) $user->news_count,
                ];
            })
            ->all();
    }

    private function getTopRatedArticles(int $limit = 5, ?\Carbon\Carbon $start = null, ?\Carbon\Carbon $end = null): array
    {
        return DB::table('news')
            ->join('news_ratings', 'news.RowID', '=', 'news_ratings.news_id')
            ->where('news.Status', 1)
            ->when($start && $end, fn ($query) => $query->whereBetween('news_ratings.created_at', [$start, $end]))
            ->groupBy('news.RowID', 'news.Name', 'news.Title')
            ->selectRaw('
                news.RowID,
                news.Name,
                news.Title,
                AVG(news_ratings.score) as avg_score,
                COUNT(news_ratings.id) as total_ratings
            ')
            ->orderByDesc('avg_score')
            ->orderByDesc('total_ratings')
            ->limit($limit)
            ->get()
            ->map(function ($news) {
                $title = $news->Name ?: $news->Title ?: 'Không có tiêu đề';
                return [
                    'id' => $news->RowID,
                    'title' => \Illuminate\Support\Str::limit($title, 80),
                    'avg_score' => round((float) $news->avg_score, 2),
                    'total_ratings' => (int) $news->total_ratings,
                    'stars' => $this->buildStarDisplay((float) $news->avg_score),
                ];
            })
            ->all();
    }

    private function getLowestRatedArticles(int $limit = 5, ?\Carbon\Carbon $start = null, ?\Carbon\Carbon $end = null): array
    {
        return DB::table('news')
            ->join('news_ratings', 'news.RowID', '=', 'news_ratings.news_id')
            ->where('news.Status', 1)
            ->when($start && $end, fn ($query) => $query->whereBetween('news_ratings.created_at', [$start, $end]))
            ->groupBy('news.RowID', 'news.Name', 'news.Title')
            ->selectRaw('
                news.RowID,
                news.Name,
                news.Title,
                AVG(news_ratings.score) as avg_score,
                COUNT(news_ratings.id) as total_ratings
            ')
            ->orderBy('avg_score')
            ->orderByDesc('total_ratings')
            ->limit($limit)
            ->get()
            ->map(function ($news) {
                $title = $news->Name ?: $news->Title ?: 'Không có tiêu đề';
                return [
                    'id' => $news->RowID,
                    'title' => \Illuminate\Support\Str::limit($title, 80),
                    'avg_score' => round((float) $news->avg_score, 2),
                    'total_ratings' => (int) $news->total_ratings,
                    'stars' => $this->buildStarDisplay((float) $news->avg_score),
                ];
            })
            ->all();
    }

    private function getMostProlificAuthors(int $limit = 5, \Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        return User::query()
            ->adminAccounts()
            ->whereHas('authoredNews', function ($q) use ($start, $end) {
                $q->where('Status', '>=', 0);
                $this->applyDashboardNewsDateFilter($q, $start, $end);
            })
            ->withCount(['authoredNews as published_count' => function ($q) use ($start, $end) {
                $q->where('Status', 1);
                $this->applyDashboardNewsDateFilter($q, $start, $end);
            }])
            ->withCount(['authoredNews as total_count' => function ($q) use ($start, $end) {
                $q->where('Status', '>=', 0);
                $this->applyDashboardNewsDateFilter($q, $start, $end);
            }])
            ->orderByDesc('published_count')
            ->orderByDesc('total_count')
            ->orderBy('fullname')
            ->limit($limit)
            ->get(['id', 'fullname', 'username', 'level'])
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->fullname ?: $user->username ?: 'Không rõ',
                    'username' => $user->username,
                    'level' => (int) $user->level,
                    'published_count' => (int) $user->published_count,
                    'total_count' => (int) $user->total_count,
                    'draft_count' => max(0, (int) $user->total_count - (int) $user->published_count),
                ];
            })
            ->all();
    }

    private function getAuthorsByHighestRatingRatio(int $limit = 5, ?\Carbon\Carbon $start = null, ?\Carbon\Carbon $end = null): array
    {
        $authorStats = DB::table('news')
            ->join('users', 'news.author_id', '=', 'users.id')
            ->join('news_ratings', 'news.RowID', '=', 'news_ratings.news_id')
            ->where('news.Status', 1)
            ->when($start && $end, fn ($query) => $query->whereBetween('news_ratings.created_at', [$start, $end]))
            ->groupBy('users.id', 'users.fullname', 'users.username', 'users.level')
            ->selectRaw('
                users.id,
                users.fullname,
                users.username,
                users.level,
                AVG(news_ratings.score) as avg_rating,
                COUNT(DISTINCT news.RowID) as rated_articles,
                COUNT(news_ratings.id) as total_ratings,
                SUM(CASE WHEN news_ratings.score >= 4 THEN 1 ELSE 0 END) as positive_ratings,
                SUM(CASE WHEN news_ratings.score <= 2 THEN 1 ELSE 0 END) as negative_ratings,
                (SUM(CASE WHEN news_ratings.score >= 4 THEN 1 ELSE 0 END) / COUNT(news_ratings.id)) * 100 as positive_rate,
                (SUM(CASE WHEN news_ratings.score <= 2 THEN 1 ELSE 0 END) / COUNT(news_ratings.id)) * 100 as negative_rate
            ')
            ->having('rated_articles', '>=', 1)
            ->orderByDesc('positive_rate')
            ->orderByDesc('avg_rating')
            ->orderByDesc('total_ratings')
            ->limit($limit)
            ->get();

        return $authorStats->map(function ($row) {
            return [
                'id' => (int) $row->id,
                'name' => $row->fullname ?: $row->username ?: 'Không rõ',
                'username' => $row->username,
                'level' => (int) $row->level,
                'avg_rating' => round((float) $row->avg_rating, 2),
                'rated_articles' => (int) $row->rated_articles,
                'total_ratings' => (int) $row->total_ratings,
                'positive_ratings' => (int) $row->positive_ratings,
                'negative_ratings' => (int) $row->negative_ratings,
                'positive_rate' => round((float) $row->positive_rate, 1),
                'negative_rate' => round((float) $row->negative_rate, 1),
                'stars' => $this->buildStarDisplay((float) $row->avg_rating),
            ];
        })->all();
    }

    private function getAuthorsByLowestRatingRatio(int $limit = 5, ?\Carbon\Carbon $start = null, ?\Carbon\Carbon $end = null): array
    {
        $authorStats = DB::table('news')
            ->join('users', 'news.author_id', '=', 'users.id')
            ->join('news_ratings', 'news.RowID', '=', 'news_ratings.news_id')
            ->where('news.Status', 1)
            ->when($start && $end, fn ($query) => $query->whereBetween('news_ratings.created_at', [$start, $end]))
            ->groupBy('users.id', 'users.fullname', 'users.username', 'users.level')
            ->selectRaw('
                users.id,
                users.fullname,
                users.username,
                users.level,
                AVG(news_ratings.score) as avg_rating,
                COUNT(DISTINCT news.RowID) as rated_articles,
                COUNT(news_ratings.id) as total_ratings,
                SUM(CASE WHEN news_ratings.score >= 4 THEN 1 ELSE 0 END) as positive_ratings,
                SUM(CASE WHEN news_ratings.score <= 2 THEN 1 ELSE 0 END) as negative_ratings,
                (SUM(CASE WHEN news_ratings.score >= 4 THEN 1 ELSE 0 END) / COUNT(news_ratings.id)) * 100 as positive_rate,
                (SUM(CASE WHEN news_ratings.score <= 2 THEN 1 ELSE 0 END) / COUNT(news_ratings.id)) * 100 as negative_rate
            ')
            ->having('rated_articles', '>=', 1)
            ->orderByDesc('negative_rate')
            ->orderBy('avg_rating')
            ->orderByDesc('total_ratings')
            ->limit($limit)
            ->get();

        return $authorStats->map(function ($row) {
            return [
                'id' => (int) $row->id,
                'name' => $row->fullname ?: $row->username ?: 'Không rõ',
                'username' => $row->username,
                'level' => (int) $row->level,
                'avg_rating' => round((float) $row->avg_rating, 2),
                'rated_articles' => (int) $row->rated_articles,
                'total_ratings' => (int) $row->total_ratings,
                'positive_ratings' => (int) $row->positive_ratings,
                'negative_ratings' => (int) $row->negative_ratings,
                'positive_rate' => round((float) $row->positive_rate, 1),
                'negative_rate' => round((float) $row->negative_rate, 1),
                'stars' => $this->buildStarDisplay((float) $row->avg_rating),
            ];
        })->all();
    }

    private function getRatingOverview(?\Carbon\Carbon $start = null, ?\Carbon\Carbon $end = null): array
    {
        $ratingQuery = NewsRating::query()
            ->when($start && $end, fn ($query) => $query->whereBetween('created_at', [$start, $end]));

        $distribution = (clone $ratingQuery)
            ->selectRaw('score, COUNT(*) as count')
            ->groupBy('score')
            ->pluck('count', 'score')
            ->map(fn ($count) => (int) $count)
            ->toArray();

        $distribution = array_replace([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0], $distribution);
        $totalRatings = array_sum($distribution);

        return [
            'total' => (int) $totalRatings,
            'average' => round((float) ((clone $ratingQuery)->avg('score') ?? 0), 1),
            'rated_articles' => News::query()->where('Status', 1)->whereHas('ratings', function ($query) use ($start, $end) {
                if ($start && $end) {
                    $query->whereBetween('created_at', [$start, $end]);
                }
            })->count(),
            'distribution' => $distribution,
            'max_distribution' => max(1, max($distribution)),
            'positive_total' => (int) (($distribution[4] ?? 0) + ($distribution[5] ?? 0)),
            'negative_total' => (int) (($distribution[1] ?? 0) + ($distribution[2] ?? 0)),
        ];
    }

    private function getAuthorPerformanceTable(int $limit = 12, \Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        $viewsSubquery = NewsViewStat::query()
            ->selectRaw('news_id, SUM(total_views) as range_views')
            ->whereBetween('view_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('news_id');

        $ratingsSubquery = NewsRating::query()
            ->selectRaw('
                news_id,
                COUNT(*) as total_ratings,
                SUM(score) as score_sum,
                SUM(CASE WHEN score >= 4 THEN 1 ELSE 0 END) as positive_ratings,
                SUM(CASE WHEN score <= 2 THEN 1 ELSE 0 END) as negative_ratings
            ')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('news_id');

        $query = User::query()
            ->adminAccounts()
            ->join('news', function ($join) {
                $join->on('users.id', '=', 'news.author_id')
                    ->where('news.Status', 1);
            })
            ->leftJoinSub($viewsSubquery, 'range_views', function ($join) {
                $join->on('news.RowID', '=', 'range_views.news_id');
            })
            ->leftJoinSub($ratingsSubquery, 'range_ratings', function ($join) {
                $join->on('news.RowID', '=', 'range_ratings.news_id');
            });

        $this->applyDashboardNewsDateFilter($query, $start, $end);

        $authors = $query
            ->groupBy('users.id', 'users.fullname', 'users.username', 'users.level')
            ->selectRaw('
                users.id,
                users.fullname,
                users.username,
                users.level,
                COUNT(DISTINCT news.RowID) as posts,
                COALESCE(SUM(range_views.range_views), 0) as views,
                COALESCE(SUM(range_ratings.total_ratings), 0) as total_ratings,
                COALESCE(SUM(range_ratings.score_sum), 0) as score_sum,
                COALESCE(SUM(range_ratings.positive_ratings), 0) as positive_ratings,
                COALESCE(SUM(range_ratings.negative_ratings), 0) as negative_ratings
            ')
            ->orderByDesc('posts')
            ->limit($limit)
            ->get();

        return $authors->map(function ($user) {
            $totalRatings = (int) ($user->total_ratings ?? 0);
            $positiveRatings = (int) ($user->positive_ratings ?? 0);
            $negativeRatings = (int) ($user->negative_ratings ?? 0);

            return [
                'id' => (int) $user->id,
                'name' => $user->fullname ?: $user->username ?: 'Không rõ',
                'username' => $user->username,
                'posts' => (int) $user->posts,
                'views' => (int) $user->views,
                'avg_rating' => $totalRatings > 0 ? round((float) $user->score_sum / $totalRatings, 1) : 0,
                'total_ratings' => $totalRatings,
                'positive_ratings' => $positiveRatings,
                'negative_ratings' => $negativeRatings,
                'positive_rate' => $totalRatings > 0 ? round($positiveRatings / $totalRatings * 100, 1) : 0,
                'negative_rate' => $totalRatings > 0 ? round($negativeRatings / $totalRatings * 100, 1) : 0,
            ];
        })
            ->values()
            ->all();
    }

    private function getTopViewedArticles(int $limit = 10, \Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        return News::query()
            ->with(['author', 'category'])
            ->withSum(['viewStats as range_views' => function ($query) use ($start, $end) {
                $query->whereBetween('view_date', [$start->toDateString(), $end->toDateString()]);
            }], 'total_views')
            ->withCount(['comments as range_comments_count' => function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            }])
            ->withCount(['ratings as range_ratings_count' => function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            }])
            ->withAvg(['ratings as range_rating' => function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            }], 'score')
            ->where('Status', 1)
            ->having('range_views', '>', 0)
            ->orderByDesc('range_views')
            ->orderByDesc('RowID')
            ->limit($limit)
            ->get()
            ->map(function (News $news) {
                return [
                    'id' => (int) $news->RowID,
                    'title' => $news->Name ?: $news->Title ?: 'Không có tiêu đề',
                    'category' => $news->category?->Name ?: 'Chưa phân loại',
                    'category_id' => (int) ($news->RowIDCat ?? 0),
                    'author' => $news->author?->fullname ?: $news->author?->username ?: ($news->Author ?: 'Không rõ'),
                    'views' => (int) ($news->range_views ?? 0),
                    'comments' => (int) ($news->range_comments_count ?? 0),
                    'rating' => round((float) ($news->range_rating ?? 0), 1),
                    'total_ratings' => (int) ($news->range_ratings_count ?? 0),
                    'date' => $news->Date ? \Carbon\Carbon::parse($news->Date)->format('d/m/Y') : optional($news->created_at)->format('d/m/Y'),
                    'status' => (int) $news->Status === 1 ? 'published' : 'draft',
                ];
            })
            ->all();
    }

    private function getCategoryRatingStats(\Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        $rows = DB::table('news_cat')
            ->leftJoin('news', function ($join) use ($start, $end) {
                $join->on('news_cat.RowID', '=', 'news.RowIDCat')
                    ->where('news.Status', 1);
                $this->applyDashboardNewsDateFilter($join, $start, $end);
            })
            ->leftJoin('news_ratings', function ($join) use ($start, $end) {
                $join->on('news.RowID', '=', 'news_ratings.news_id')
                    ->whereBetween('news_ratings.created_at', [$start, $end]);
            })
            ->groupBy('news_cat.RowID', 'news_cat.Name', 'news_cat.color')
            ->orderByDesc(DB::raw('COUNT(DISTINCT news.RowID)'))
            ->limit(6)
            ->selectRaw('
                news_cat.RowID as id,
                news_cat.Name as name,
                news_cat.color as color,
                COUNT(DISTINCT news.RowID) as news_count,
                COUNT(news_ratings.id) as rating_count,
                AVG(news_ratings.score) as avg_rating
            ')
            ->get();

        $palette = ['#d1a53d', '#60a5fa', '#34d399', '#f87171', '#a78bfa', '#fb923c'];

        return $rows->values()->map(function ($row, $index) use ($palette) {
            return [
                'id' => (int) $row->id,
                'name' => $row->name ?: 'Chưa phân loại',
                'color' => $row->color ?: $palette[$index % count($palette)],
                'news_count' => (int) $row->news_count,
                'rating_count' => (int) $row->rating_count,
                'avg_rating' => round((float) ($row->avg_rating ?? 0), 1),
            ];
        })->all();
    }

    private function getDashboardStatusDistribution(?\Carbon\Carbon $start = null, ?\Carbon\Carbon $end = null): array
    {
        $hotCount = 0;

        if (Schema::hasTable('news_tickers')) {
            $hotCount = NewsTicker::query()
                ->where('Status', 1)
                ->when($start && $end, function ($query) use ($start, $end) {
                    $query->whereHas('news', function ($newsQuery) use ($start, $end) {
                        $this->applyDashboardNewsDateFilter($newsQuery, $start, $end);
                    });
                })
                ->count();
        } elseif (Schema::hasColumn('news', 'hot')) {
            $hotQuery = News::query()->where('hot', 1);
            if ($start && $end) {
                $this->applyDashboardNewsDateFilter($hotQuery, $start, $end);
            }
            $hotCount = $hotQuery->count();
        }

        $publishedQuery = News::query()->where('Status', 1);
        if ($start && $end) {
            $this->applyDashboardNewsDateFilter($publishedQuery, $start, $end);
        }

        return [
            'published' => $publishedQuery->count(),
            'pending' => NewsSchedule::query()
                ->where('status', NewsSchedule::STATUS_PENDING)
                ->when($start && $end, fn ($query) => $query->whereBetween('created_at', [$start, $end]))
                ->count(),
            'featured' => FeaturedNews::query()
                ->active()
                ->when($start && $end, function ($query) use ($start, $end) {
                    $query->whereHas('news', function ($newsQuery) use ($start, $end) {
                        $this->applyDashboardNewsDateFilter($newsQuery, $start, $end);
                    });
                })
                ->count(),
            'hot' => (int) $hotCount,
        ];
    }

    private function getRatingTrend(?\Carbon\Carbon $start = null, ?\Carbon\Carbon $end = null): array
    {
        $start = $start ? $start->copy()->startOfMonth() : now()->copy()->startOfMonth()->subMonths(5);
        $end = $end ? $end->copy()->startOfMonth() : now()->copy()->startOfMonth();
        $rows = NewsRating::query()
            ->whereBetween('created_at', [$start->copy()->startOfMonth(), $end->copy()->endOfMonth()])
            ->get(['score', 'created_at'])
            ->groupBy(fn (NewsRating $rating) => optional($rating->created_at)->format('Y-m'));

        $labels = [];
        $data = [];

        for ($date = $start->copy(); $date->lte($end); $date->addMonth()) {
            $key = $date->format('Y-m');
            $labels[] = 'T' . $date->format('n');
            $monthRatings = $rows->get($key, collect());
            $data[] = $monthRatings->count() > 0 ? round((float) $monthRatings->avg('score'), 1) : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getDashboardChartSeries(?\Carbon\Carbon $selectedStart = null, ?\Carbon\Carbon $selectedEnd = null): array
    {
        if ($selectedStart && $selectedEnd) {
            return [
                'selected' => $this->buildDailyDashboardSeries(
                    $selectedStart->copy()->startOfDay(),
                    $selectedEnd->copy()->startOfDay(),
                    'short'
                ),
            ];
        }

        $series = [
            'today' => $this->buildDailyDashboardSeries(now()->copy()->startOfDay(), now()->copy()->startOfDay(), 'Hôm nay'),
            'week' => $this->buildDailyDashboardSeries(now()->copy()->subDays(6)->startOfDay(), now()->copy()->startOfDay(), 'day'),
            'month' => $this->buildDailyDashboardSeries(now()->copy()->subDays(29)->startOfDay(), now()->copy()->startOfDay(), 'short'),
            'quarter' => $this->buildMonthlyDashboardSeries(now()->copy()->subMonths(2)->startOfMonth(), now()->copy()->startOfMonth()),
            'year' => $this->buildMonthlyDashboardSeries(now()->copy()->subMonths(11)->startOfMonth(), now()->copy()->startOfMonth()),
        ];

        return $series;
    }

    private function getDashboardDailySeries(int $days = 365): array
    {
        $start = now()->copy()->subDays($days - 1)->startOfDay();
        $end = now()->copy()->startOfDay();
        $series = $this->buildDailyDashboardSeries($start, $end, 'iso');

        return [
            'dates' => $series['keys'],
            'labels' => $series['labels'],
            'views' => $series['views'],
            'posts' => $series['posts'],
            'ratings' => $series['ratings'],
        ];
    }

    private function buildDailyDashboardSeries(\Carbon\Carbon $start, \Carbon\Carbon $end, string $labelMode): array
    {
        $viewRows = NewsViewStat::query()
            ->selectRaw('view_date, SUM(total_views) as total')
            ->whereBetween('view_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('view_date')
            ->pluck('total', 'view_date');

        $postRows = News::query()
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('Date', [$start->toDateString(), $end->toDateString()])
                    ->orWhereBetween('created_at', [$start, $end->copy()->endOfDay()]);
            })
            ->get(['Date', 'created_at'])
            ->groupBy(function (News $news) {
                return $news->Date
                    ? \Carbon\Carbon::parse($news->Date)->toDateString()
                    : optional($news->created_at)->toDateString();
            })
            ->map->count();

        $ratingRows = NewsRating::query()
            ->whereBetween(DB::raw('DATE(created_at)'), [$start->toDateString(), $end->toDateString()])
            ->get(['score', 'created_at'])
            ->groupBy(fn (NewsRating $rating) => optional($rating->created_at)->toDateString());

        $labels = [];
        $keys = [];
        $views = [];
        $posts = [];
        $ratings = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->toDateString();
            $keys[] = $key;
            $labels[] = match ($labelMode) {
                'Hôm nay' => 'Hôm nay',
                'day' => match ($date->dayOfWeekIso) {
                    1 => 'T2',
                    2 => 'T3',
                    3 => 'T4',
                    4 => 'T5',
                    5 => 'T6',
                    6 => 'T7',
                    default => 'CN',
                },
                'iso' => $key,
                default => $date->format('d/m'),
            };
            $views[] = (int) ($viewRows[$key] ?? 0);
            $posts[] = (int) ($postRows[$key] ?? 0);
            $dayRatings = $ratingRows->get($key, collect());
            $ratings[] = $dayRatings->count() > 0 ? round((float) $dayRatings->avg('score'), 1) : 0;
        }

        return compact('keys', 'labels', 'views', 'posts', 'ratings');
    }

    private function buildMonthlyDashboardSeries(\Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        $rangeEnd = $end->copy()->endOfMonth();

        $viewRows = NewsViewStat::query()
            ->whereBetween('view_date', [$start->toDateString(), $rangeEnd->toDateString()])
            ->get(['view_date', 'total_views'])
            ->groupBy(fn (NewsViewStat $stat) => \Carbon\Carbon::parse($stat->view_date)->format('Y-m'))
            ->map(fn ($items) => (int) $items->sum('total_views'));

        $postRows = News::query()
            ->where(function ($query) use ($start, $rangeEnd) {
                $query->whereBetween('Date', [$start->toDateString(), $rangeEnd->toDateString()])
                    ->orWhereBetween('created_at', [$start, $rangeEnd]);
            })
            ->get(['Date', 'created_at'])
            ->groupBy(function (News $news) {
                return $news->Date
                    ? \Carbon\Carbon::parse($news->Date)->format('Y-m')
                    : optional($news->created_at)->format('Y-m');
            })
            ->map->count();

        $ratingRows = NewsRating::query()
            ->whereBetween('created_at', [$start, $end->copy()->endOfMonth()])
            ->get(['score', 'created_at'])
            ->groupBy(fn (NewsRating $rating) => optional($rating->created_at)->format('Y-m'));

        $keys = [];
        $labels = [];
        $views = [];
        $posts = [];
        $ratings = [];

        for ($date = $start->copy(); $date->lte($end); $date->addMonth()) {
            $key = $date->format('Y-m');
            $keys[] = $key;
            $labels[] = 'T' . $date->format('n');
            $views[] = (int) ($viewRows[$key] ?? 0);
            $posts[] = (int) ($postRows[$key] ?? 0);
            $monthRatings = $ratingRows->get($key, collect());
            $ratings[] = $monthRatings->count() > 0 ? round((float) $monthRatings->avg('score'), 1) : 0;
        }

        return compact('keys', 'labels', 'views', 'posts', 'ratings');
    }

    private function buildStarDisplay(float $score): array
    {
        $full = (int) floor($score);
        $half = ($score - $full) >= 0.5 ? 1 : 0;
        $empty = 5 - $full - $half;

        return [
            'full' => $full,
            'half' => $half,
            'empty' => max(0, $empty),
            'score' => $score,
        ];
    }

    private function findContactRecord($id): Contact
    {
        return Contact::query()->findOrFail($id);
    }

    private function saveSystemRow(string $code, string $description): void
    {
        $row = System::where('Code', $code)->first();
        if (!$row) {
            $row = new System();
            $row->Code = $code;
        }
        $row->Description = $description;
        if (Schema::hasColumn('system', 'Status')) {
            $row->Status = 1;
        }
        $row->save();
    }

    private function storeSystemUpload($file, string $dir, array $allowedExt): string
    {
        if (!$file || !$file->isValid()) {
            return '';
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowedExt, true)) {
            return '';
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $name = rand(100000000, 999999999) . '-' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $file->move($dir, $name);

        return $name;
    }

    private function storePageImage($file, ?string $oldImage): string
    {
        if (!$file || !$file->isValid()) {
            return '';
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true)) {
            return '';
        }

        $dir = 'images/page';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($oldImage && file_exists($dir . '/' . $oldImage)) {
            @unlink($dir . '/' . $oldImage);
        }

        $name = rand(100000000, 999999999) . '-' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $file->move($dir, $name);

        return $name;
    }

    private function storeSliderImage($file, ?string $oldImage = null): string
    {
        if (!$file || !$file->isValid()) {
            return '';
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true)) {
            return '';
        }

        $dir = 'images/slider';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($oldImage && file_exists($dir . '/' . $oldImage)) {
            @unlink($dir . '/' . $oldImage);
        }

        $name = rand(100000000, 999999999) . '-' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $file->move($dir, $name);

        return $name;
    }

    private function processNewsImage($file, ?string $oldImage): string
    {
        if (!$file->isValid()) {
            return '';
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'], true)) {
            return '';
        }

        $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBase = preg_replace('/[^a-zA-Z0-9._-]/', '', $basename);
        if ($safeBase === '') {
            $safeBase = 'image';
        }
        $name = random_int(100000000, 999999999) . '-' . $safeBase . '.' . $ext;

        $newsRoot = public_path('images/news');
        $day = date('Ymd');
        $dayDir = $newsRoot . DIRECTORY_SEPARATOR . $day;
        $tempDir = $newsRoot . DIRECTORY_SEPARATOR . 'temp';

        foreach ([$newsRoot, $tempDir, $dayDir] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        if ($oldImage) {
            $oldFull = public_path('images/news/' . $oldImage);
            if (is_file($oldFull)) {
                @unlink($oldFull);
            }
        }

        $tempFull = $tempDir . DIRECTORY_SEPARATOR . $name;
        $destFull = $dayDir . DIRECTORY_SEPARATOR . $name;

        $file->move($tempDir, $name);

        try {
            if ($ext === 'svg') {
                rename($tempFull, $destFull);
            } else {
                $img = Image::make($tempFull);

                // Keep article covers sharp enough for large hero layouts.
                $img->orientate();

                $maxWidth = 2400;
                $maxHeight = 1600;

                if ($img->width() > $maxWidth || $img->height() > $maxHeight) {
                    $img->resize($maxWidth, $maxHeight, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                if (in_array($ext, ['jpg', 'jpeg', 'webp'], true)) {
                    $img->save($destFull, 90);
                } else {
                    $img->save($destFull);
                }

                if (is_file($tempFull)) {
                    @unlink($tempFull);
                }
            }
        } catch (\Throwable $e) {
            if (is_file($tempFull)) {
                if (!is_file($destFull)) {
                    @rename($tempFull, $destFull);
                } else {
                    @unlink($tempFull);
                }
            }
        }

        return $day . '/' . $name;
    }

    private function syncNewsTags(int $newsId, $tagIds): void
    {
        if (is_string($tagIds)) {
            $tagIds = explode(',', $tagIds);
        }

        DB::table('news_tags')->where('news_id', $newsId)->delete();

        foreach ($tagIds as $tagName) {
            $tagName = trim($tagName);
            if ($tagName === '') continue;

            $tag = Tag::findOrCreateByName($tagName);
            DB::table('news_tags')->insert([
                'news_id'    => $newsId,
                'tag_id'     => $tag->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $tag->incrementPopular();
        }
    }

    private function applyNewsSeoFields(News $news, Request $request): void
    {
        $title = trim((string) $request->input('Name'));
        $alias = Str::slug(trim((string) $request->input('Alias', '')) ?: $title, '-');

        if ($alias === '') {
            $alias = 'bai-viet-' . now()->format('YmdHis');
        }

        $baseAlias = $alias;
        $suffix = 2;
        while (News::query()
            ->where('Alias', $alias)
            ->when($news->exists, function ($query) use ($news) {
                $query->where('RowID', '<>', $news->RowID);
            })
            ->exists()) {
            $alias = $baseAlias . '-' . $suffix;
            $suffix++;
        }

        $news->Alias = $alias;
        $news->MetaTitle = mb_substr(
            trim((string) $request->input('MetaTitle')) ?: $title,
            0,
            70
        );

        $description = trim((string) $request->input('MetaDescription'));
        if ($description === '') {
            $description = trim((string) $request->input('SmallDescription'));
        }
        if ($description === '') {
            $description = trim(strip_tags((string) $request->input('Description')));
        }
        $description = preg_replace('/\s+/u', ' ', $description) ?? $description;

        $news->MetaDescription = mb_substr($description, 0, 180);
        $news->MetaKeyword = mb_substr(
            trim((string) $request->input('MetaKeyword')),
            0,
            500
        );
    }

    private function createOrUpdateSchedule(
        int $newsId,
        string $publishType,
        ?string $scheduledAt,
        ?string $status,
        bool $updateExisting = false
    ): void {
        $schedule = NewsSchedule::where('news_id', $newsId)->first();

        if (!$schedule) {
            $schedule = new NewsSchedule();
            $schedule->news_id = $newsId;
            $schedule->created_by = Auth::id();
        } elseif (!$schedule->created_by) {
            $schedule->created_by = Auth::id();
        }

        $schedule->publish_type = $publishType;
        $schedule->status = $status ?? NewsSchedule::STATUS_DRAFT;

        if ($publishType === NewsSchedule::PUBLISH_SCHEDULE) {
            $schedule->scheduled_at = $scheduledAt ? \Carbon\Carbon::parse($scheduledAt) : null;
            $schedule->published_at = null;
        } else {
            $schedule->scheduled_at = null;
            $schedule->published_at = $schedule->status === NewsSchedule::STATUS_PUBLISHED ? ($schedule->published_at ?? now()) : null;
        }

        $schedule->save();
    }

    private function resolveScheduleStatusForAction(string $submitAction): string
    {
        return match ($submitAction) {
            'submit_review' => NewsSchedule::STATUS_PENDING,
            'publish_now' => NewsSchedule::STATUS_PUBLISHED,
            default => NewsSchedule::STATUS_DRAFT,
        };
    }

    private function saveCategoryImage(NewsCategory $cat, $file): void
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            return;
        }

        $dir = public_path('images/category');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if ($cat->image) {
            $old = $dir . '/' . $cat->image;
            if (is_file($old)) {
                @unlink($old);
            }
        }

        $name = time() . '-' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $file->move($dir, $name);
        $cat->image = $name;
        $cat->save();
    }
}
