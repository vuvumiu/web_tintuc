<?php

namespace App\Services;

use App\Models\CommentVote;
use App\Models\News;
use App\Models\NewsComment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    private const CACHE_PREFIX = 'notif_unread_';
    private const CACHE_TTL = 300; // 5 minutes

    public static function shouldNotify($user, string $type): bool
    {
        if (!$user) {
            return false;
        }
        return $user->hasNotificationEnabled($type);
    }

    public static function notifyCommentNew(
        News $news,
        NewsComment $comment,
        User $commenter
    ): ?Notification {
        if (!$news || !$comment || !$commenter) {
            return null;
        }

        $authorId = $news->author_id;
        if (!$authorId || $authorId === $commenter->id) {
            return null;
        }

        $author = User::find($authorId);
        if (!$author || !self::shouldNotify($author, Notification::TYPE_COMMENT_NEW)) {
            return null;
        }

        $notification = Notification::createNotification(
            $authorId,
            Notification::TYPE_COMMENT_NEW,
            $commenter->username . ' đã bình luận về bài viết "' . $news->Name . '"',
            mb_substr(trim(strip_tags($comment->content ?? '')), 0, 100),
            url($news->Alias . '.html#comment-' . $comment->id),
            $comment->id,
            'news_comment'
        );

        self::clearUnreadCache($authorId);

        return $notification;
    }

    public static function notifyCommentReply(
        News $news,
        NewsComment $reply,
        NewsComment $parent,
        User $replier
    ): ?Notification {
        if (!$news || !$reply || !$parent || !$replier) {
            return null;
        }

        $recipientId = $parent->user_id;
        if (!$recipientId || $recipientId === $replier->id) {
            return null;
        }

        $recipient = User::find($recipientId);
        if (!$recipient || !self::shouldNotify($recipient, Notification::TYPE_COMMENT_REPLY)) {
            return null;
        }

        $notification = Notification::createNotification(
            $recipientId,
            Notification::TYPE_COMMENT_REPLY,
            $replier->username . ' đã trả lời bình luận của bạn',
            mb_substr(trim(strip_tags($reply->content ?? '')), 0, 100),
            url($news->Alias . '.html#comment-' . $reply->id),
            $reply->id,
            'news_comment'
        );

        self::clearUnreadCache($recipientId);

        return $notification;
    }

    public static function notifyCommentVote(
        NewsComment $comment,
        User $voter,
        int $voteType
    ): ?Notification {
        if (!$comment || !$voter) {
            return null;
        }

        if ($comment->user_id === $voter->id) {
            return null;
        }

        $type = $voteType === CommentVote::UPVOTE
            ? Notification::TYPE_COMMENT_UPVOTE
            : Notification::TYPE_COMMENT_DOWNVOTE;

        $recipientId = $comment->user_id;
        if (!$recipientId) {
            return null;
        }

        $recipient = User::find($recipientId);
        if (!$recipient) {
            return null;
        }

        if (!self::shouldNotify($recipient, $type)) {
            return null;
        }

        $news = $comment->news;
        $voteLabel = $voteType === CommentVote::UPVOTE ? 'thích' : 'không thích';

        $notification = Notification::createNotification(
            $recipientId,
            $type,
            $voter->username . " đã {$voteLabel} bình luận của bạn",
            mb_substr(trim(strip_tags($comment->content ?? '')), 0, 120),
            $news ? url($news->Alias . '.html#comment-' . $comment->id) : null,
            $comment->id,
            'news_comment'
        );

        self::clearUnreadCache($recipientId);

        return $notification;
    }

    public static function notifyNewsRated(
        News $news,
        User $rater,
        int $score
    ): ?Notification {
        if (!$news || !$rater) {
            return null;
        }

        $authorId = $news->author_id;
        if (!$authorId || $authorId === $rater->id) {
            return null;
        }

        $author = User::find($authorId);
        if (!$author) {
            return null;
        }

        if (!self::shouldNotify($author, Notification::TYPE_NEWS_RATED)) {
            return null;
        }

        $stars = str_repeat('★', $score) . str_repeat('☆', 5 - $score);

        $notification = Notification::createNotification(
            $authorId,
            Notification::TYPE_NEWS_RATED,
            $rater->username . " đã đánh giá {$score}/5 sao bài viết của bạn",
            $stars,
            url($news->Alias . '.html'),
            $news->RowID,
            'news'
        );

        self::clearUnreadCache($authorId);

        return $notification;
    }

    public static function notifyNewsFavorited(
        News $news,
        User $favoriter
    ): ?Notification {
        if (!$news || !$favoriter) {
            return null;
        }

        $authorId = $news->author_id;
        if (!$authorId || $authorId === $favoriter->id) {
            return null;
        }

        $author = User::find($authorId);
        if (!$author) {
            return null;
        }

        if (!self::shouldNotify($author, Notification::TYPE_NEWS_FAVORITED)) {
            return null;
        }

        $notification = Notification::createNotification(
            $authorId,
            Notification::TYPE_NEWS_FAVORITED,
            $favoriter->username . " đã lưu bài viết của bạn vào yêu thích",
            mb_substr(trim(strip_tags($news->Name ?? '')), 0, 100),
            url($news->Alias . '.html'),
            $news->RowID,
            'news'
        );

        self::clearUnreadCache($authorId);

        return $notification;
    }

    public static function getUnreadCount(int $userId): int
    {
        $cacheKey = self::CACHE_PREFIX . $userId;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            return Notification::where('user_id', $userId)
                ->where('is_read', 0)
                ->count();
        });
    }

    public static function clearUnreadCache(int $userId): void
    {
        Cache::forget(self::CACHE_PREFIX . $userId);
    }

    public static function formatNotification(Notification $notification): array
    {
        return [
            'id'      => $notification->id,
            'type'    => $notification->type,
            'title'   => $notification->title,
            'content' => $notification->content,
            'link'    => $notification->link,
            'is_read' => (bool) $notification->is_read,
            'time'    => $notification->created_at->diffForHumans(),
            'icon'    => Notification::typeIcon($notification->type),
            'color'   => Notification::typeColor($notification->type),
        ];
    }
}
