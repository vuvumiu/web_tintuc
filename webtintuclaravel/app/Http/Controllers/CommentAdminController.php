<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\News;
use App\Models\NewsComment;
use App\Models\NewsRating;
use App\Services\NotificationService;

class CommentAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    // ── Bình luận ──────────────────────────────────────────
    public function index(Request $request)
    {
        $query = NewsComment::with(['user', 'news', 'parent.user'])
            ->orderBy('created_at', 'DESC');

        if ($request->input('type') === 'root') {
            $query->root();
        } elseif ($request->input('type') === 'reply') {
            $query->whereNotNull('parent_id')->where('parent_id', '>', 0);
        }

        if ($request->has('keyword') && $request->keyword != '') {
            $query->where('content', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', (int) $request->is_active);
        }
        if ($request->filled('moderation_status')) {
            $query->where('moderation_status', $request->moderation_status);
        }
        if ($request->filled('news_id')) {
            $query->where('news_id', (int) $request->news_id);
        }

        $comments = $query->paginate(20);
        $stats = [
            'total' => NewsComment::count(),
            'active' => NewsComment::where('moderation_status', NewsComment::STATUS_APPROVED)->count(),
            'pending' => NewsComment::where('moderation_status', NewsComment::STATUS_PENDING)->count(),
            'rejected' => NewsComment::where('moderation_status', NewsComment::STATUS_REJECTED)->count(),
            'spam' => NewsComment::where('moderation_status', NewsComment::STATUS_SPAM)->count(),
        ];
        $news = News::orderByDesc('RowID')->get(['RowID', 'Name']);

        return view('back.comment.list', compact('comments', 'stats', 'news'));
    }

    public function toggle($id)
    {
        $comment = NewsComment::findOrFail($id);
        if ($comment->is_active) {
            $comment->reject(Auth::id(), 'Đã được quản trị viên ẩn.');
            $msg = 'Bình luận đã được ẩn.';
        } else {
            $comment->approve(Auth::id(), 'Đã được quản trị viên hiển thị.');
            $msg = 'Bình luận đã được hiển thị.';
        }

        return back()->with([
            'flash_level'   => 'success',
            'flash_message' => $msg,
        ]);
    }

    public function moderate(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,spam',
            'reason' => 'nullable|string|max:500',
        ]);

        $comment = NewsComment::with(['news', 'user', 'parent'])->findOrFail($id);
        $reason = trim((string) $request->input('reason'));
        $wasApproved = $comment->moderation_status === NewsComment::STATUS_APPROVED;

        if ($request->action === 'approve') {
            $comment->approve(Auth::id(), $reason ?: 'Đã được quản trị viên duyệt.');
            if (!$wasApproved && $comment->news && $comment->user) {
                if ($comment->parent_id && $comment->parent) {
                    NotificationService::notifyCommentReply(
                        $comment->news,
                        $comment,
                        $comment->parent,
                        $comment->user
                    );
                } else {
                    NotificationService::notifyCommentNew($comment->news, $comment, $comment->user);
                }
            }
            $message = 'Bình luận đã được duyệt.';
        } else {
            $isSpam = $request->action === 'spam';
            $comment->reject(
                Auth::id(),
                $reason ?: ($isSpam ? 'Đã được đánh dấu là spam.' : 'Không đạt yêu cầu kiểm duyệt.'),
                $isSpam
            );
            $message = $isSpam ? 'Bình luận đã được đánh dấu spam.' : 'Bình luận đã bị từ chối.';
        }

        return back()->with([
            'flash_level' => 'success',
            'flash_message' => $message,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $comment = NewsComment::with('replies')->findOrFail($id);
        $comment->replies()->delete();
        $comment->delete();

        return back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Xóa bình luận và các phản hồi thành công.',
        ]);
    }

    // ── Đánh giá sao ──────────────────────────────────────
    public function ratingList(Request $request)
    {
        // Dùng Eloquent để apply được $casts từ Model (date sẽ là Carbon object)
        $query = NewsRating::with([
                'news' => function ($query) {
                    $query->select('RowID', 'Name', 'Title', 'Alias');
                },
                'user:id,username,fullname,email',
            ])
            ->orderByDesc('created_at');

        if ($request->filled('news_id')) {
            $query->where('news_id', $request->news_id);
        }

        if ($request->filled('score')) {
            $query->where('score', $request->score);
        }

        if ($request->filled('keyword')) {
            $kw = '%' . $request->keyword . '%';
            $query->whereHas('user', function ($q) use ($kw) {
                $q->where('username', 'like', $kw)
                    ->orWhere('fullname', 'like', $kw)
                    ->orWhere('email', 'like', $kw);
            });
        }

        $ratings = $query->paginate(20);
        $news = News::orderByDesc('RowID')->get(['RowID', 'Name']);

        $stats = [
            'total'   => NewsRating::count(),
            'avg'     => round(NewsRating::avg('score') ?? 0, 1),
            'byScore' => NewsRating::select('score', \DB::raw('COUNT(*) as total'))
                ->groupBy('score')
                ->get(),
        ];

        return view('back.rating.list', compact('ratings', 'stats', 'news'));
    }

    public function ratingDelete(Request $request, $id)
    {
        \DB::table('news_ratings')->where('id', $id)->delete();

        return back()->with([
            'flash_level'   => 'success',
            'flash_message' => 'Xóa đánh giá thành công.',
        ]);
    }

    // ── Bulk Actions ───────────────────────────────────────
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'   => 'required|string',
            'action' => 'required|in:delete,show,hide,approve,reject,spam',
        ], [
            'ids.required'   => 'Chưa chọn bình luận nào.',
            'action.required' => 'Chưa chọn thao tác.',
        ]);

        $ids = array_filter(array_map('intval', explode(',', $request->ids)));
        if (empty($ids)) {
            return back()->with([
                'flash_level'   => 'warning',
                'flash_message' => 'Không có bình luận nào được chọn.',
            ]);
        }

        $requiredPermission = match ($request->action) {
            'delete' => 'comment.delete',
            'show', 'hide' => 'comment.hide',
            'approve', 'reject', 'spam' => 'comment.moderate',
        };

        abort_unless(Auth::user()->hasPermission($requiredPermission), 403);

        $count = count($ids);

        switch ($request->action) {
            case 'delete':
                foreach ($ids as $id) {
                    $c = NewsComment::with('replies')->find($id);
                    if ($c) {
                        $c->replies()->delete();
                        $c->delete();
                    }
                }
                $msg = "Đã xóa {$count} bình luận và phản hồi.";
                break;

            case 'show':
            case 'approve':
                NewsComment::whereIn('id', $ids)->get()->each(function (NewsComment $comment) {
                    $comment->approve(Auth::id(), 'Đã được duyệt hàng loạt.');
                });
                $msg = "Đã duyệt {$count} bình luận.";
                break;

            case 'hide':
            case 'reject':
                NewsComment::whereIn('id', $ids)->get()->each(function (NewsComment $comment) {
                    $comment->reject(Auth::id(), 'Đã bị từ chối hàng loạt.');
                });
                $msg = "Đã từ chối {$count} bình luận.";
                break;

            case 'spam':
                NewsComment::whereIn('id', $ids)->get()->each(function (NewsComment $comment) {
                    $comment->reject(Auth::id(), 'Đã bị đánh dấu spam hàng loạt.', true);
                });
                $msg = "Đã đánh dấu spam {$count} bình luận.";
                break;
        }

        return back()->with([
            'flash_level'   => 'success',
            'flash_message' => $msg,
        ]);
    }

    public function ratingBulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ], [
            'ids.required' => 'Chưa chọn đánh giá nào.',
        ]);

        $ids = array_filter(array_map('intval', explode(',', $request->ids)));
        if (empty($ids)) {
            return back()->with([
                'flash_level'   => 'warning',
                'flash_message' => 'Không có đánh giá nào được chọn.',
            ]);
        }

        $count = count($ids);
        \DB::table('news_ratings')->whereIn('id', $ids)->delete();

        return back()->with([
            'flash_level'   => 'success',
            'flash_message' => "Đã xóa {$count} đánh giá.",
        ]);
    }
}
