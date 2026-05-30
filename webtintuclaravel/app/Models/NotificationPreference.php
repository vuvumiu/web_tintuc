<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $table = 'notification_preferences';

    protected $fillable = [
        'user_id',
        'notify_comment_new',
        'notify_comment_reply',
        'notify_comment_upvote',
        'notify_comment_downvote',
        'notify_news_rated',
        'notify_news_favorited',
        'notify_news_approved',
        'notify_news_rejected',
        'notify_system',
    ];

    protected $casts = [
        'notify_comment_new'     => 'boolean',
        'notify_comment_reply'   => 'boolean',
        'notify_comment_upvote'  => 'boolean',
        'notify_comment_downvote'=> 'boolean',
        'notify_news_rated'     => 'boolean',
        'notify_news_favorited' => 'boolean',
        'notify_news_approved'  => 'boolean',
        'notify_news_rejected'  => 'boolean',
        'notify_system'         => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'notify_comment_new'     => true,
                'notify_comment_reply'   => true,
                'notify_comment_upvote' => true,
                'notify_comment_downvote'=> true,
                'notify_news_rated'     => true,
                'notify_news_favorited' => true,
                'notify_news_approved'  => true,
                'notify_news_rejected'  => true,
                'notify_system'         => true,
            ]
        );
    }

    public function isEnabled(string $type): bool
    {
        $map = [
            Notification::TYPE_COMMENT_NEW     => 'notify_comment_new',
            Notification::TYPE_COMMENT_REPLY   => 'notify_comment_reply',
            Notification::TYPE_COMMENT_UPVOTE   => 'notify_comment_upvote',
            Notification::TYPE_COMMENT_DOWNVOTE => 'notify_comment_downvote',
            Notification::TYPE_NEWS_RATED        => 'notify_news_rated',
            Notification::TYPE_NEWS_FAVORITED   => 'notify_news_favorited',
            Notification::TYPE_NEWS_APPROVED  => 'notify_news_approved',
            Notification::TYPE_NEWS_REJECTED     => 'notify_news_rejected',
            Notification::TYPE_SYSTEM           => 'notify_system',
        ];

        $key = $map[$type] ?? null;

        if (!$key) {
            return true;
        }

        return (bool) ($this->{$key} ?? true);
    }
}
