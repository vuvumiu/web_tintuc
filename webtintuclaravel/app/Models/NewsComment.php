<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsComment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_SPAM = 'spam';

    protected $table = 'news_comments';

    protected $fillable = [
        'news_id',
        'user_id',
        'parent_id',
        'content',
        'is_active',
        'moderation_status',
        'moderation_reason',
        'spam_score',
        'ip_address',
        'user_agent',
        'content_hash',
        'moderated_by',
        'moderated_at',
        'upvote_count',
        'downvote_count',
        'reply_count',
    ];

    protected $attributes = [
        'upvote_count' => 0,
        'downvote_count' => 0,
        'reply_count' => 0,
        'moderation_status' => self::STATUS_APPROVED,
        'spam_score' => 0,
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'upvote_count' => 'integer',
        'downvote_count' => 'integer',
        'reply_count' => 'integer',
        'spam_score' => 'integer',
        'moderated_at' => 'datetime',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class, 'news_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(CommentVote::class, 'comment_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(NewsComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(NewsComment::class, 'parent_id')
            ->where('is_active', true)
            ->where('moderation_status', self::STATUS_APPROVED)
            ->orderBy('created_at', 'ASC');
    }

    public function allReplies(): HasMany
    {
        return $this->hasMany(NewsComment::class, 'parent_id')
            ->orderBy('created_at', 'ASC');
    }

    public function scopeRoot($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('parent_id')
                ->orWhere('parent_id', 0);
        });
    }

    public function scopeApproved($query)
    {
        return $query->where('is_active', true)
            ->where('moderation_status', self::STATUS_APPROVED);
    }

    public function scopePendingModeration($query)
    {
        return $query->where('moderation_status', self::STATUS_PENDING);
    }

    public function approve(?int $moderatorId = null, ?string $reason = null): void
    {
        $this->forceFill([
            'moderation_status' => self::STATUS_APPROVED,
            'moderation_reason' => $reason,
            'is_active' => true,
            'moderated_by' => $moderatorId,
            'moderated_at' => now(),
        ])->save();
    }

    public function reject(?int $moderatorId = null, ?string $reason = null, bool $spam = false): void
    {
        $this->forceFill([
            'moderation_status' => $spam ? self::STATUS_SPAM : self::STATUS_REJECTED,
            'moderation_reason' => $reason,
            'is_active' => false,
            'moderated_by' => $moderatorId,
            'moderated_at' => now(),
        ])->save();
    }

    protected static function booted(): void
    {
        static::created(function (NewsComment $comment) {
            if ($comment->parent_id) {
                $comment->parent()->update(['reply_count' => $comment->parent()->first()?->allReplies()->count() ?? 0]);
            }
        });

        static::deleted(function (NewsComment $comment) {
            if ($comment->parent_id) {
                $parent = $comment->parent()->first();
                if ($parent) {
                    $parent->decrement('reply_count', 1);
                }
            }
        });
    }
}
