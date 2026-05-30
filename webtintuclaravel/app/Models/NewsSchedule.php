<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsSchedule extends Model
{
    protected $table = 'news_schedules';

    protected $fillable = [
        'news_id', 'created_by', 'approved_by', 'status',
        'publish_type', 'scheduled_at', 'published_at', 'reject_reason',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    const STATUS_DRAFT      = 'draft';
    const STATUS_PENDING    = 'pending';
    const STATUS_APPROVED   = 'approved';
    const STATUS_REJECTED   = 'rejected';
    const STATUS_SCHEDULED  = 'scheduled';
    const STATUS_PUBLISHED  = 'published';

    const PUBLISH_NOW       = 'now';
    const PUBLISH_SCHEDULE  = 'schedule';

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class, 'news_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function submitForReview(): bool
    {
        if (!$this->isDraft() && !$this->isRejected()) {
            return false;
        }
        $this->status = self::STATUS_PENDING;
        return $this->save();
    }

    public function approve(int $approverId): bool
    {
        if (!$this->isPending() && !$this->isScheduled()) {
            return false;
        }
        $this->status = $this->publish_type === self::PUBLISH_SCHEDULE ? self::STATUS_SCHEDULED : self::STATUS_PUBLISHED;
        $this->approved_by = $approverId;
        if ($this->publish_type === self::PUBLISH_NOW) {
            $this->published_at = now();
        }
        return $this->save();
    }

    public function reject(int $approverId, ?string $reason = null): bool
    {
        if (!$this->isPending()) {
            return false;
        }
        $this->status = self::STATUS_REJECTED;
        $this->approved_by = $approverId;
        $this->reject_reason = $reason;
        return $this->save();
    }
}
