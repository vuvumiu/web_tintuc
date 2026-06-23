<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\NewsSchedule;
use App\Models\News;
use App\Models\Notification;

class NewsScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function approvalQueue(Request $request)
    {
        $query = NewsSchedule::with(['news', 'creator'])
            ->whereHas('news')
            ->whereIn('status', ['pending', 'scheduled'])
            ->orderBy('created_at', 'DESC');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $schedules = $query->paginate(20);

        $stats = [
            'pending'   => NewsSchedule::where('status', 'pending')->count(),
            'scheduled' => NewsSchedule::where('status', 'scheduled')->count(),
            'draft'     => NewsSchedule::where('status', 'draft')->count(),
        ];

        return view('back.news.approval', compact('schedules', 'stats'));
    }

    public function approve(Request $request, $id)
    {
        $schedule = NewsSchedule::with('news')->findOrFail($id);

        if (!$schedule->isPending() && !$schedule->isScheduled()) {
            return back()->with([
                'flash_level'   => 'danger',
                'flash_message' => 'Bài viết không ở trạng thái chờ duyệt.',
            ]);
        }

        $schedule->approve(Auth::id());

        if ($schedule->news) {
            $schedule->news->Status = $schedule->isScheduled() ? 0 : 1;
            $schedule->news->save();

            if ($schedule->news->author_id) {
                Notification::createNotification(
                    $schedule->news->author_id,
                    Notification::TYPE_NEWS_APPROVED,
                    'Bài viết "' . $schedule->news->Name . '" đã được duyệt và xuất bản!',
                    null,
                    url($schedule->news->Alias . '.html'),
                    $schedule->news->RowID,
                    'news'
                );
            }
        }

        return back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Duyệt bài thành công!',
        ]);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ], [
            'reject_reason.required' => 'Vui lòng nhập lý do từ chối.',
        ]);

        $schedule = NewsSchedule::with('news')->findOrFail($id);

        if (!$schedule->isPending()) {
            return back()->with([
                'flash_level'   => 'danger',
                'flash_message' => 'Bài viết không ở trạng thái chờ duyệt.',
            ]);
        }

        $schedule->reject(Auth::id(), $request->reject_reason);

        if ($schedule->news) {
            $schedule->news->Status = 0;
            $schedule->news->save();
        }

        if ($schedule->news && $schedule->news->author_id) {
            Notification::createNotification(
                $schedule->news->author_id,
                Notification::TYPE_NEWS_REJECTED,
                'Bài viết "' . $schedule->news->Name . '" bị từ chối.',
                $request->reject_reason,
                url('admin/news/edit/' . $schedule->news->RowID),
                $schedule->news->RowID,
                'news'
            );
        }

        return back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Đã từ chối bài viết.',
        ]);
    }

    public function submitReview($newsId)
    {
        $news = News::findOrFail($newsId);
        $schedule = NewsSchedule::where('news_id', $newsId)->first();

        if (!$schedule) {
            $schedule = NewsSchedule::create([
                'news_id'    => $newsId,
                'created_by' => Auth::id(),
                'status'     => NewsSchedule::STATUS_PENDING,
                'publish_type' => NewsSchedule::PUBLISH_NOW,
            ]);
        } else {
            $schedule->submitForReview();
        }

        $news->Status = 0;
        $news->save();

        return back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Đã gửi bài viết để duyệt.',
        ]);
    }

    public function drafts(Request $request)
    {
        $user = Auth::user();
        $canReviewAll = $user && ($user->isAdmin() || $user->hasPermission('news.approve'));

        $scheduleLatest = DB::table('news_schedules')
            ->select('news_id', DB::raw('MAX(id) as last_id'))
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
            ->selectRaw('
                a.RowID as news_id,
                a.Name,
                a.Alias,
                a.author_id,
                a.updated_at as news_updated_at,
                a.created_at as news_created_at,
                b.Name as CategoryName,
                COALESCE(c.fullname, c.username) as AuthorName,
                d.id as schedule_id,
                d.status,
                d.created_by,
                d.reject_reason,
                d.updated_at as schedule_updated_at
            ')
            ->where(function ($q) {
                $q->whereIn('d.status', [NewsSchedule::STATUS_DRAFT, NewsSchedule::STATUS_REJECTED])
                    ->orWhere(function ($legacy) {
                        $legacy->whereNull('d.id')
                            ->where('a.Status', 0);
                    });
            });

        if (!$canReviewAll) {
            $query->where(function ($q) use ($user) {
                $q->where('d.created_by', $user->id)
                    ->orWhere(function ($legacy) use ($user) {
                        $legacy->whereNull('d.id')
                            ->where('a.author_id', $user->id);
                    });
            });
        }

        $query->orderByRaw('COALESCE(d.updated_at, a.updated_at, a.created_at) DESC')
            ->orderByDesc('a.RowID');

        $drafts = $query->paginate(20);
        $isGlobalDrafts = $canReviewAll;

        return view('back.news.drafts', compact('drafts', 'isGlobalDrafts'));
    }
}
