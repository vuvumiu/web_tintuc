<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'content', 'link',
        'reference_id', 'reference_type', 'is_read', 'read_at',
    ];

    protected $casts = [
        'is_read' => 'integer',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const TYPE_COMMENT_NEW       = 'comment_new';
    const TYPE_COMMENT_REPLY     = 'comment_reply';
    const TYPE_COMMENT_UPVOTE    = 'comment_upvote';
    const TYPE_COMMENT_DOWNVOTE  = 'comment_downvote';
    const TYPE_NEWS_APPROVED     = 'news_approved';
    const TYPE_NEWS_REJECTED     = 'news_rejected';
    const TYPE_NEWS_PUBLISHED    = 'news_published';
    const TYPE_NEWS_HIDDEN       = 'news_hidden';
    const TYPE_NEWS_SUBMITTED    = 'news_submitted';
    const TYPE_NEWS_DUPLICATED   = 'news_duplicated';
    const TYPE_NEWS_BULK_ACTION  = 'news_bulk_action';
    const TYPE_NEWS_RATED        = 'news_rated';
    const TYPE_NEWS_FAVORITED    = 'news_favorited';
    const TYPE_SYSTEM            = 'system';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->is_read = 1;
            $this->read_at = now();
            $this->save();
        }
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', 0);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public static function unreadCount(int $userId): int
    {
        return static::where('user_id', $userId)->where('is_read', 0)->count();
    }

    public static function createNotification(
        int $userId,
        string $type,
        string $title,
        ?string $content = null,
        ?string $link = null,
        ?int $referenceId = null,
        ?string $referenceType = null
    ): self {
        return static::create([
            'user_id'       => $userId,
            'type'          => $type,
            'title'         => $title,
            'content'       => $content,
            'link'          => $link,
            'reference_id'  => $referenceId,
            'reference_type'=> $referenceType,
            'is_read'       => 0,
        ]);
    }

    public static function typeIcon(string $type): string
    {
        return match ($type) {
            self::TYPE_COMMENT_NEW       => 'fa-comment',
            self::TYPE_COMMENT_REPLY     => 'fa-reply',
            self::TYPE_COMMENT_UPVOTE    => 'fa-thumbs-up',
            self::TYPE_COMMENT_DOWNVOTE  => 'fa-thumbs-down',
            self::TYPE_NEWS_APPROVED     => 'fa-check-circle',
            self::TYPE_NEWS_REJECTED     => 'fa-times-circle',
            self::TYPE_NEWS_PUBLISHED    => 'fa-globe',
            self::TYPE_NEWS_HIDDEN       => 'fa-eye-slash',
            self::TYPE_NEWS_SUBMITTED    => 'fa-paper-plane',
            self::TYPE_NEWS_DUPLICATED   => 'fa-copy',
            self::TYPE_NEWS_BULK_ACTION  => 'fa-layer-group',
            self::TYPE_NEWS_RATED        => 'fa-star',
            self::TYPE_NEWS_FAVORITED    => 'fa-heart',
            default                      => 'fa-bell',
        };
    }

    public static function typeColor(string $type): string
    {
        return match ($type) {
            self::TYPE_COMMENT_NEW       => 'info',
            self::TYPE_COMMENT_REPLY     => 'primary',
            self::TYPE_COMMENT_UPVOTE    => 'success',
            self::TYPE_COMMENT_DOWNVOTE  => 'warning',
            self::TYPE_NEWS_APPROVED     => 'success',
            self::TYPE_NEWS_REJECTED     => 'danger',
            self::TYPE_NEWS_PUBLISHED    => 'success',
            self::TYPE_NEWS_HIDDEN       => 'warning',
            self::TYPE_NEWS_SUBMITTED    => 'info',
            self::TYPE_NEWS_DUPLICATED   => 'primary',
            self::TYPE_NEWS_BULK_ACTION  => 'danger',
            self::TYPE_NEWS_RATED        => 'warning',
            self::TYPE_NEWS_FAVORITED    => 'danger',
            default                      => 'secondary',
        };
    }
}
