<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Newsletter extends Model
{
    use HasFactory;

    protected $table = 'newsletter';
    protected $primaryKey = 'RowID';

    protected $fillable = [
        'Email',
        'is_active',
        'token',
        'subscribed_at',
        'unsubscribed_at',
        'ip_address',
        'is_reviewed',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_reviewed' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->token = Str::random(64);
            $model->subscribed_at = now();
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('unsubscribed_at');
    }

    public function scopeReviewed($query)
    {
        return $query->where('is_reviewed', true);
    }

    public function scopeUnreviewed($query)
    {
        return $query->where('is_reviewed', false);
    }

    public function markAsReviewed()
    {
        $this->is_reviewed = true;
        $this->save();
    }

    public function markAsUnsubscribed()
    {
        $this->is_active = false;
        $this->unsubscribed_at = now();
        $this->save();
    }

    public function markAsSubscribed()
    {
        $this->is_active = true;
        $this->unsubscribed_at = null;
        $this->subscribed_at = now();
        $this->token = Str::random(64);
        $this->save();
    }
}
