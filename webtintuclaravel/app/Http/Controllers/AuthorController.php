<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use App\Support\UsersTableSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $keyword = trim((string) $request->input('keyword', ''));
        $authorStatus = (string) $request->input('author_status', '');
        $accountStatus = (string) $request->input('account_status', '');
        $sort = (string) $request->input('sort', 'views');

        $query = $this->authorListQuery($request);

        if ($keyword !== '') {
            $kw = '%' . $keyword . '%';
            $query->where(function ($q) use ($kw) {
                $q->where('users.username', 'like', $kw)
                    ->orWhere('users.fullname', 'like', $kw)
                    ->orWhere('users.email', 'like', $kw);
            });
        }

        if ($accountStatus !== '' && UsersTableSchema::hasIsActiveColumn()) {
            $query->where('users.is_active', (int) $accountStatus);
        }

        if (UsersTableSchema::hasIsAuthorColumn()) {
            if ($authorStatus === 'active') {
                $query->where('users.is_author', 1);
            } elseif ($authorStatus === 'historical') {
                $query->where(function ($q) {
                    $q->whereNull('users.is_author')->orWhere('users.is_author', '!=', 1);
                })->whereExists(function ($newsQuery) {
                    $newsQuery->selectRaw('1')
                        ->from('news')
                        ->whereColumn('news.author_id', 'users.id');
                });
            }
        }

        match ($sort) {
            'articles' => $query->orderByDesc('total_articles'),
            'published' => $query->orderByDesc('published_articles'),
            'comments' => $query->orderByDesc('active_comments'),
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('rating_count'),
            'name' => $query->orderBy('users.fullname')->orderBy('users.username'),
            default => $query->orderByDesc('total_views'),
        };
        $query->orderByDesc('users.id');

        $summaryRows = (clone $query)->get();
        $summary = [
            'total_authors' => $summaryRows->count(),
            'active_authors' => $summaryRows->filter(fn ($author) => (int) ($author->is_author ?? 0) === 1)->count(),
            'historical_authors' => $summaryRows
                ->filter(fn ($author) => (int) ($author->is_author ?? 0) !== 1 && (int) ($author->total_articles ?? 0) > 0)
                ->count(),
            'total_articles' => (int) $summaryRows->sum('total_articles'),
            'published_articles' => (int) $summaryRows->sum('published_articles'),
            'workflow_articles' => (int) $summaryRows->sum('workflow_articles'),
            'total_views' => (int) $summaryRows->sum('total_views'),
            'active_comments' => (int) $summaryRows->sum('active_comments'),
            'rating_count' => (int) $summaryRows->sum('rating_count'),
            'rating_avg' => (int) $summaryRows->sum('rating_count') > 0
                ? round(((float) $summaryRows->sum('rating_score_sum')) / ((int) $summaryRows->sum('rating_count')), 1)
                : 0,
        ];

        $authors = $query->paginate(15)->withQueryString();
        $categories = NewsCategory::query()->orderBy('Name')->get(['RowID', 'Name']);

        return view('back.author.list', compact(
            'authors',
            'summary',
            'categories',
            'keyword',
            'authorStatus',
            'accountStatus',
            'sort'
        ));
    }

    public function show(Request $request, int $id)
    {
        $author = User::query()->adminAccounts()->findOrFail($id);

        $baseNewsQuery = $this->authorNewsBaseQuery($author, $request);
        $newsMetricQuery = $this->authorNewsWithMetricsQuery($author, $request);

        $statsRow = (clone $newsMetricQuery)
            ->selectRaw(
                'COUNT(DISTINCT n.RowID) as total_articles,
                SUM(CASE WHEN n.Status = 1 THEN 1 ELSE 0 END) as published_articles,
                SUM(CASE WHEN n.Status = 1 THEN 0 ELSE 1 END) as workflow_articles,
                COALESCE(SUM(n.Views), 0) as total_views,
                COALESCE(SUM(comment_stats.active_comments), 0) as active_comments,
                COALESCE(SUM(rating_stats.rating_count), 0) as rating_count,
                COALESCE(SUM(rating_stats.rating_score_sum), 0) as rating_score_sum'
            )
            ->first();

        $stats = [
            'total_articles' => (int) ($statsRow->total_articles ?? 0),
            'published_articles' => (int) ($statsRow->published_articles ?? 0),
            'workflow_articles' => (int) ($statsRow->workflow_articles ?? 0),
            'total_views' => (int) ($statsRow->total_views ?? 0),
            'active_comments' => (int) ($statsRow->active_comments ?? 0),
            'rating_count' => (int) ($statsRow->rating_count ?? 0),
            'rating_avg' => (int) ($statsRow->rating_count ?? 0) > 0
                ? round(((float) $statsRow->rating_score_sum) / ((int) $statsRow->rating_count), 1)
                : 0,
        ];

        $latestArticle = (clone $baseNewsQuery)
            ->orderByDesc('created_at')
            ->orderByDesc('RowID')
            ->first();

        $topViewedArticle = (clone $baseNewsQuery)
            ->orderByDesc('Views')
            ->orderByDesc('created_at')
            ->orderByDesc('RowID')
            ->first();

        $latestArticles = (clone $baseNewsQuery)
            ->orderByDesc('created_at')
            ->orderByDesc('RowID')
            ->limit(5)
            ->get();

        $topViewedArticles = (clone $baseNewsQuery)
            ->orderByDesc('Views')
            ->orderByDesc('created_at')
            ->orderByDesc('RowID')
            ->limit(5)
            ->get();

        $articles = (clone $newsMetricQuery)
            ->leftJoin('news_cat as cat', 'n.RowIDCat', '=', 'cat.RowID')
            ->selectRaw(
                'n.*,
                cat.Name as category_name,
                COALESCE(comment_stats.active_comments, 0) as active_comments,
                COALESCE(rating_stats.rating_count, 0) as rating_count,
                COALESCE(rating_stats.rating_avg, 0) as rating_avg'
            )
            ->orderByDesc('n.created_at')
            ->orderByDesc('n.RowID')
            ->paginate(12)
            ->withQueryString();

        $categories = NewsCategory::query()
            ->orderBy('Name')
            ->get(['RowID', 'Name']);

        return view('back.author.detail', compact(
            'author',
            'stats',
            'latestArticle',
            'topViewedArticle',
            'latestArticles',
            'topViewedArticles',
            'articles',
            'categories'
        ));
    }

    public function toggle(Request $request, int $id)
    {
        $author = User::query()->adminAccounts()->findOrFail($id);

        if (!$request->user() || !$request->user()->hasAnyPermission(['author.manage', 'admin-manager.edit'])) {
            return redirect()->back()->with([
                'flash_level' => 'danger',
                'flash_message' => 'Ban khong co quyen thay doi tu cach tac gia.',
            ]);
        }

        if (!UsersTableSchema::hasIsAuthorColumn()) {
            return redirect()->back()->with([
                'flash_level' => 'warning',
                'flash_message' => 'Cot is_author chua san sang. Hay chay migrate truoc.',
            ]);
        }

        $author->is_author = $author->isAuthor() ? 0 : 1;
        $author->save();

        return redirect()->back()->with([
            'flash_level' => 'success',
            'flash_message' => $author->is_author
                ? 'Da bat tu cach tac gia cho tai khoan nay.'
                : 'Da tat tu cach tac gia. Cac bai cu van giu lich su tac gia.',
        ]);
    }

    protected function authorListQuery(Request $request): Builder
    {
        $commentStats = DB::table('news_comments')
            ->selectRaw(
                'news_id,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_comments'
            )
            ->groupBy('news_id');

        $ratingStats = DB::table('news_ratings')
            ->selectRaw(
                'news_id,
                COUNT(id) as rating_count,
                AVG(score) as rating_avg,
                COALESCE(SUM(score), 0) as rating_score_sum'
            )
            ->groupBy('news_id');

        $newsStats = DB::table('news as n')
            ->leftJoinSub($commentStats, 'comment_stats', function ($join) {
                $join->on('n.RowID', '=', 'comment_stats.news_id');
            })
            ->leftJoinSub($ratingStats, 'rating_stats', function ($join) {
                $join->on('n.RowID', '=', 'rating_stats.news_id');
            })
            ->whereNotNull('n.author_id');

        if ($request->filled('category_id')) {
            $newsStats->where('n.RowIDCat', (int) $request->input('category_id'));
        }

        if ($request->filled('from_date')) {
            $newsStats->whereDate('n.created_at', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $newsStats->whereDate('n.created_at', '<=', $request->input('to_date'));
        }

        $newsStats->selectRaw(
            'n.author_id,
            COUNT(DISTINCT n.RowID) as total_articles,
            SUM(CASE WHEN n.Status = 1 THEN 1 ELSE 0 END) as published_articles,
            SUM(CASE WHEN n.Status = 1 THEN 0 ELSE 1 END) as workflow_articles,
            COALESCE(SUM(n.Views), 0) as total_views,
            COALESCE(SUM(comment_stats.active_comments), 0) as active_comments,
            COALESCE(SUM(rating_stats.rating_count), 0) as rating_count,
            COALESCE(SUM(rating_stats.rating_score_sum), 0) as rating_score_sum,
            CASE
                WHEN COALESCE(SUM(rating_stats.rating_count), 0) > 0
                THEN COALESCE(SUM(rating_stats.rating_score_sum), 0) / COALESCE(SUM(rating_stats.rating_count), 0)
                ELSE 0
            END as rating_avg'
        )->groupBy('n.author_id');

        return User::query()
            ->authorAccounts()
            ->leftJoinSub($newsStats, 'author_stats', function ($join) {
                $join->on('users.id', '=', 'author_stats.author_id');
            })
            ->select('users.*')
            ->selectRaw(
                'COALESCE(author_stats.total_articles, 0) as total_articles,
                COALESCE(author_stats.published_articles, 0) as published_articles,
                COALESCE(author_stats.workflow_articles, 0) as workflow_articles,
                COALESCE(author_stats.total_views, 0) as total_views,
                COALESCE(author_stats.active_comments, 0) as active_comments,
                COALESCE(author_stats.rating_count, 0) as rating_count,
                COALESCE(author_stats.rating_score_sum, 0) as rating_score_sum,
                COALESCE(author_stats.rating_avg, 0) as rating_avg'
            );
    }

    protected function authorNewsBaseQuery(User $author, Request $request): Builder
    {
        $query = News::query()->where('author_id', $author->id);

        if ($request->filled('category_id')) {
            $query->where('RowIDCat', (int) $request->input('category_id'));
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        return $query;
    }

    protected function authorNewsWithMetricsQuery(User $author, Request $request): Builder
    {
        $commentStats = DB::table('news_comments')
            ->selectRaw(
                'news_id,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_comments'
            )
            ->groupBy('news_id');

        $ratingStats = DB::table('news_ratings')
            ->selectRaw(
                'news_id,
                COUNT(id) as rating_count,
                AVG(score) as rating_avg,
                COALESCE(SUM(score), 0) as rating_score_sum'
            )
            ->groupBy('news_id');

        return $this->authorNewsBaseQuery($author, $request)
            ->from('news as n')
            ->leftJoinSub($commentStats, 'comment_stats', function ($join) {
                $join->on('n.RowID', '=', 'comment_stats.news_id');
            })
            ->leftJoinSub($ratingStats, 'rating_stats', function ($join) {
                $join->on('n.RowID', '=', 'rating_stats.news_id');
            });
    }
}
