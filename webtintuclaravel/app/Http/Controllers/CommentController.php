<?php

namespace App\Http\Controllers;

use App\Models\CommentVote;
use App\Models\News;
use App\Models\NewsComment;
use App\Models\NewsRating;
use App\Services\GeminiAIService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    private ?GeminiAIService $ai = null;

    public function __construct()
    {
        if (config('gemini.features.comment_moderation') && config('gemini.api_key')) {
            $this->ai = app(GeminiAIService::class);
        }
    }

    public function store(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return $this->ajaxError('Vui lòng đăng nhập để bình luận.', 401);
        }

        $request->validate([
            'news_id' => 'required|integer|exists:news,RowID',
            'content' => 'required|string|min:1|max:2000',
        ]);

        $comment = NewsComment::create([
            'news_id' => (int) $request->news_id,
            'user_id' => Auth::id(),
            'content' => trim((string) $request->content),
            'is_active' => true,
        ]);

        $comment->load('user');
        $this->autoModerateCommentIfNeeded($comment);

        $news = News::find($request->news_id);
        if ($news && $comment->is_active) {
            NotificationService::notifyCommentNew($news, $comment, Auth::user());
        }

        return $this->ajaxSuccess('Bình luận đã được gửi.', [
            'comment' => $this->formatComment($comment),
        ]);
    }

    public function reply(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return $this->ajaxError('Vui lòng đăng nhập.', 401);
        }

        $request->validate([
            'news_id' => 'required|integer|exists:news,RowID',
            'parent_id' => 'required|integer|exists:news_comments,id',
            'content' => 'required|string|min:1|max:2000',
        ]);

        $parent = NewsComment::where('id', $request->parent_id)
            ->where('news_id', $request->news_id)
            ->where('is_active', true)
            ->first();

        if (!$parent) {
            return $this->ajaxError('Bình luận gốc không tồn tại hoặc đã bị xóa.', 404);
        }

        $reply = NewsComment::create([
            'news_id' => (int) $request->news_id,
            'user_id' => Auth::id(),
            'parent_id' => (int) $request->parent_id,
            'content' => trim((string) $request->content),
            'is_active' => true,
        ]);

        $reply->load('user');
        $this->autoModerateCommentIfNeeded($reply);

        $news = News::find($request->news_id);
        if ($news && $reply->is_active) {
            NotificationService::notifyCommentReply($news, $reply, $parent, Auth::user());
        }

        return $this->ajaxSuccess('Phản hồi đã được gửi.', [
            'reply' => $this->formatComment($reply),
            'parent_id' => (int) $request->parent_id,
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        if (!Auth::check()) {
            return $this->ajaxError('Vui lòng đăng nhập.', 401);
        }

        $comment = NewsComment::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$comment) {
            return $this->ajaxError('Bình luận không tồn tại hoặc bạn không có quyền sửa.', 403);
        }

        $request->validate([
            'content' => 'required|string|min:1|max:2000',
        ]);

        $comment->content = trim((string) $request->content);
        $comment->save();

        return $this->ajaxSuccess('Bình luận đã được cập nhật.', [
            'id' => $comment->id,
            'content' => e($comment->content),
            'updated_at' => $comment->updated_at->toIso8601String(),
            'time_ago' => $comment->updated_at->diffForHumans(),
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        if (!Auth::check()) {
            return $this->ajaxError('Vui lòng đăng nhập.', 401);
        }

        $comment = NewsComment::find($id);
        if (!$comment) {
            return $this->ajaxError('Bình luận không tồn tại.', 404);
        }

        $isOwner = Auth::id() === (int) $comment->user_id;
        $isAdmin = Auth::user()->canAccessAdmin();

        if (!$isOwner && !$isAdmin) {
            return $this->ajaxError('Bạn không có quyền xóa bình luận này.', 403);
        }

        $deletedReplies = $comment->allReplies()->count();
        $comment->delete();

        return $this->ajaxSuccess($isAdmin && !$isOwner ? 'Đã xóa bình luận.' : 'Bình luận đã được xóa.', [
            'id' => (int) $id,
            'replies_deleted' => $deletedReplies,
        ]);
    }

    public function rate(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return $this->ajaxError('Vui lòng đăng nhập để đánh giá.', 401);
        }

        $request->validate([
            'news_id' => 'required|integer|exists:news,RowID',
            'score' => 'required|integer|min:1|max:5',
        ]);

        $existingRating = NewsRating::where('news_id', (int) $request->news_id)
            ->where('user_id', Auth::id())
            ->first();

        NewsRating::updateOrCreate(
            ['news_id' => (int) $request->news_id, 'user_id' => Auth::id()],
            ['score' => (int) $request->score]
        );

        $news = News::find($request->news_id);
        if ($news && !$existingRating) {
            NotificationService::notifyNewsRated($news, Auth::user(), (int) $request->score);
        }

        $stats = NewsRating::where('news_id', $request->news_id)
            ->selectRaw('AVG(score) as avg_score, COUNT(*) as total')
            ->first();

        $distRows = NewsRating::where('news_id', $request->news_id)
            ->selectRaw('score, COUNT(*) as c')
            ->groupBy('score')
            ->pluck('c', 'score')
            ->toArray();

        $scoreCounts = [];
        for ($i = 1; $i <= 5; $i++) {
            $scoreCounts[$i] = (int) ($distRows[$i] ?? 0);
        }

        return $this->ajaxSuccess('Cảm ơn bạn đã đánh giá.', [
            'avg_score' => round((float) $stats->avg_score, 1),
            'total' => (int) $stats->total,
            'user_score' => (int) $request->score,
            'score_counts' => $scoreCounts,
        ]);
    }

    public function vote(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return $this->ajaxError('Vui lòng đăng nhập để bình chọn.', 401);
        }

        $request->validate([
            'comment_id' => 'required|integer|exists:news_comments,id',
            'vote_type' => 'required|in:1,-1',
        ]);

        $comment = NewsComment::find($request->comment_id);
        if (!$comment) {
            return $this->ajaxError('Không tìm thấy bình luận.', 404);
        }

        try {
            $result = CommentVote::toggleVote(
                (int) $request->comment_id,
                (int) Auth::id(),
                (int) $request->vote_type
            );

            $comment->refresh();
            if ($result['action'] !== 'removed' && $comment->user_id !== Auth::id()) {
                NotificationService::notifyCommentVote($comment, Auth::user(), (int) $request->vote_type);
            }

            return $this->ajaxSuccess('Đã ghi nhận bình chọn.', [
                'upvotes' => $result['upvotes'],
                'downvotes' => $result['downvotes'],
                'user_vote' => $result['new_vote'],
                'action' => $result['action'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Vote error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());

            return $this->ajaxError('Có lỗi xảy ra, vui lòng thử lại.', 500);
        }
    }

    public function loadMore(Request $request): JsonResponse
    {
        $request->validate([
            'news_id' => 'required|integer|exists:news,RowID',
            'page' => 'required|integer|min:1',
        ]);

        $comments = NewsComment::with(['user', 'replies.user'])
            ->where('news_id', $request->news_id)
            ->where('is_active', true)
            ->root()
            ->orderBy('created_at', 'DESC')
            ->paginate(10, ['*'], 'page', (int) $request->page);

        if (!$comments->hasMorePages() && $comments->currentPage() > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Không còn bình luận.',
                'comments' => [],
            ]);
        }

        $html = '';
        foreach ($comments as $comment) {
            $html .= view('front.partials.comment-item', compact('comment'))->render();
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'hasMorePages' => $comments->hasMorePages(),
            'currentPage' => $comments->currentPage(),
        ]);
    }

    private function ajaxSuccess(string $message, array $data = []): JsonResponse
    {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
        ], $data));
    }

    private function ajaxError(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }

    private function formatComment(NewsComment $comment): array
    {
        $createdAt = $comment->created_at instanceof \Carbon\Carbon
            ? $comment->created_at
            : \Carbon\Carbon::parse($comment->created_at);

        return [
            'id' => $comment->id,
            'content' => e($comment->content),
            'created_at' => $createdAt->toIso8601String(),
            'time_ago' => $createdAt->diffForHumans(),
            'upvotes' => $comment->upvote_count,
            'downvotes' => $comment->downvote_count,
            'user_vote' => Auth::check() ? CommentVote::getUserVote($comment->id, Auth::id()) ?? 0 : 0,
            'user' => [
                'id' => $comment->user->id,
                'username' => $comment->user->username,
                'fullname' => $comment->user->fullname,
                'avatar' => $comment->user->avatar_url,
                'initial' => $comment->user->initials ?? strtoupper(substr($comment->user->username, 0, 1)),
                'is_author_admin' => $comment->user->canAccessAdmin(),
            ],
        ];
    }

    private function autoModerateCommentIfNeeded(NewsComment $comment): void
    {
        if (!$this->ai) {
            return;
        }

        try {
            $result = $this->ai->moderateComment(
                $comment->content,
                $comment->user?->fullname ?? $comment->user?->username ?? ''
            );

            if (strtoupper($result['action'] ?? 'FLAG') === 'REJECT') {
                $comment->is_active = false;
                $comment->save();
            }
        } catch (\Throwable $e) {
            Log::warning('AI comment moderation failed', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
