<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\News;
use App\Models\NewsComment;
use App\Models\NewsRating;

class CommentAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    // ── Bình luận ──────────────────────────────────────────
    public function index(Request $request)
    {
        $query = NewsComment::with(['user', 'news'])
            ->root()
            ->orderBy('created_at', 'DESC');

        if ($request->has('keyword') && $request->keyword != '') {
            $query->where('content', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', (int) $request->is_active);
        }

        $comments = $query->paginate(20);
        $stats = [
            'total'   => NewsComment::root()->count(),
            'active'  => NewsComment::root()->where('is_active', 1)->count(),
            'hidden'  => NewsComment::root()->where('is_active', 0)->count(),
        ];
        $news = News::orderByDesc('RowID')->get(['RowID', 'Name']);

        return view('back.comment.list', compact('comments', 'stats', 'news'));
    }

    public function toggle($id)
    {
        $comment = NewsComment::findOrFail($id);
        $comment->is_active = $comment->is_active ? 0 : 1;
        $comment->save();

        $msg = $comment->is_active ? 'Bình luận đã được hiển thị.' : 'Bình luận đã được ẩn.';

        return back()->with([
            'flash_level'   => 'success',
            'flash_message' => $msg,
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
            'action' => 'required|in:delete,show,hide',
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
                NewsComment::whereIn('id', $ids)->update(['is_active' => 1]);
                $msg = "Đã hiển thị {$count} bình luận.";
                break;

            case 'hide':
                NewsComment::whereIn('id', $ids)->update(['is_active' => 0]);
                $msg = "Đã ẩn {$count} bình luận.";
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
