<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\UserFavorite;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsInteractionController extends Controller
{
    public function favorite(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập.'], 401);
        }

        $request->validate([
            'news_id' => 'required|integer|exists:news,RowID',
        ]);

        $favorited = UserFavorite::toggle(Auth::id(), $request->news_id);
        $count = UserFavorite::where('news_id', $request->news_id)->count();

        if ($favorited) {
            $news = News::find($request->news_id);
            if ($news && $news->author_id && $news->author_id !== Auth::id()) {
                NotificationService::notifyNewsFavorited($news, Auth::user());
            }
        }

        return response()->json([
            'success' => true,
            'favorited' => $favorited,
            'count' => $count,
            'message' => $favorited ? 'Đã thêm vào yêu thích!' : 'Đã bỏ yêu thích.',
        ]);
    }

    public function favoriteList(Request $request)
    {
        $favorites = UserFavorite::with(['news' => function ($q) {
            $q->where('Status', 1)
              ->whereNotNull('RowID')
              ->select('RowID', 'RowIDCat', 'Name', 'Alias', 'Images', 'SmallDescription', 'Views', 'created_at');
        }, 'news.category'])
            ->where('user_id', Auth::id())
            ->whereNotNull('news_id')
            ->whereHas('news', function ($q) {
                $q->where('Status', 1);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(12);

        return view('auth.favorites', compact('favorites'));
    }

    public function vote(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập.'], 401);
        }

        $request->validate([
            'comment_id' => 'required|integer|exists:news_comments,id',
            'vote_type' => 'required|in:1,-1',
        ]);

        $comment = \App\Models\NewsComment::find($request->comment_id);

        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bình luận.'], 404);
        }

        $result = \App\Models\CommentVote::toggleVote(
            $request->comment_id,
            Auth::id(),
            (int) $request->vote_type
        );

        $comment->refresh();

        if ($result['action'] !== 'removed' && $comment->user_id !== Auth::id()) {
            NotificationService::notifyCommentVote($comment, Auth::user(), (int) $request->vote_type);
        }

        return response()->json([
            'success' => true,
            'upvotes' => $comment->upvote_count,
            'downvotes' => $comment->downvote_count,
            'user_vote' => $result['new_vote'],
            'action' => $result['action'],
        ]);
    }

    public function preview($id)
    {
        $news = News::with(['author', 'tags'])->find($id);

        if (!$news) {
            return redirect('admin/news/list')->with([
                'flash_level' => 'danger',
                'flash_message' => 'Bài viết không tồn tại.',
            ]);
        }

        $comments = $news->comments()
            ->with(['user', 'replies.user'])
            ->approved()
            ->root()
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get();

        $avgRating = $news->ratings()->avg('score') ?? 0;
        $totalRating = $news->ratings()->count();

        $relatedNews = $news->getRelatedNews(4);

        return view('front.news.preview', compact('news', 'comments', 'avgRating', 'totalRating', 'relatedNews'));
    }
}
