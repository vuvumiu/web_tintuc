<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contact';
    protected $primaryKey = 'RowID';

    public const CATEGORY_CONSULT = 'consult';
    public const CATEGORY_COMPLAINT = 'complaint';
    public const CATEGORY_COOPERATION = 'cooperation';
    public const CATEGORY_OTHER = 'other';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    public const STATUS_NEW = 'new';
    public const STATUS_READ = 'read';
    public const STATUS_REPLIED = 'replied';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'Name',
        'subject',
        'Email',
        'Phone',
        'Message',
        'Content',
        'category',
        'priority',
        'assigned_to',
        'admin_note',
        'replied_at',
        'is_reviewed',
        'ip_address',
        'last_reply_content',
    ];

    protected $casts = [
        'is_reviewed' => 'boolean',
        'replied_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Contact $contact) {
            $message = trim((string) ($contact->getAttribute('Message') ?? ''));
            $content = trim((string) ($contact->getAttribute('Content') ?? ''));

            if ($message === '' && $content !== '') {
                $contact->setAttribute('Message', $content);
            }

            if ($content === '' && $message !== '') {
                $contact->setAttribute('Content', $message);
            }

            if (!Schema::hasColumn('contact', 'Message')) {
                unset($contact->attributes['Message']);
            }
        });
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(ContactReply::class, 'contact_id', 'RowID')->orderByDesc('sent_at');
    }

    public function getRowIDAttribute(): ?int
    {
        return $this->attributes['RowID'] ?? null;
    }

    public function getMessageAttribute($value): ?string
    {
        return $value ?: ($this->attributes['Content'] ?? null);
    }

    public function getContentAttribute($value): ?string
    {
        return $value ?: ($this->attributes['Message'] ?? null);
    }

    public static function categoryLabels(): array
    {
        return [
            self::CATEGORY_CONSULT => 'Tư vấn',
            self::CATEGORY_COMPLAINT => 'Khiếu nại',
            self::CATEGORY_COOPERATION => 'Hợp tác',
            self::CATEGORY_OTHER => 'Khác',
        ];
    }

    public static function priorityLabels(): array
    {
        return [
            self::PRIORITY_LOW => 'Thấp',
            self::PRIORITY_MEDIUM => 'Trung bình',
            self::PRIORITY_HIGH => 'Cao',
        ];
    }

    public static function priorityColors(): array
    {
        return [
            self::PRIORITY_LOW => 'secondary',
            self::PRIORITY_MEDIUM => 'info',
            self::PRIORITY_HIGH => 'danger',
        ];
    }

    public static function categoryColors(): array
    {
        return [
            self::CATEGORY_CONSULT => 'primary',
            self::CATEGORY_COMPLAINT => 'danger',
            self::CATEGORY_COOPERATION => 'success',
            self::CATEGORY_OTHER => 'secondary',
        ];
    }

    public function getStatusAttribute(): string
    {
        if ($this->replied_at) {
            return self::STATUS_REPLIED;
        }

        if ($this->is_reviewed) {
            return self::STATUS_READ;
        }

        return self::STATUS_NEW;
    }

    public function markAsReviewed()
    {
        $this->is_reviewed = true;
        $this->save();
    }

    public function markAsReplied()
    {
        $this->is_reviewed = true;
        $this->replied_at = now();
        $this->save();
    }

    public function scopeUnread($query)
    {
        return $query->where('is_reviewed', false);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }
}
