<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsComment extends Model
{
    use HasFactory;

    protected $table = 'news_comments';

    protected $fillable = [
        'news_id',
        'user_id',
        'parent_id',
        'content',
        'is_active',
        'upvote_count',
        'downvote_count',
        'reply_count',
    ];

    protected $attributes = [
        'upvote_count' => 0,
        'downvote_count' => 0,
        'reply_count' => 0,
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'upvote_count' => 'integer',
        'downvote_count' => 'integer',
        'reply_count' => 'integer',
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
