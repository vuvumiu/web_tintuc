<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFavorite extends Model
{
    protected $table = 'user_favorites';

    protected $fillable = ['user_id', 'news_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class, 'news_id');
    }

    public static function isFavorited(int $userId, int $newsId): bool
    {
        return static::where('user_id', $userId)->where('news_id', $newsId)->exists();
    }

    public static function toggle(int $userId, int $newsId): bool
    {
        $existing = static::where('user_id', $userId)->where('news_id', $newsId)->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        static::create(['user_id' => $userId, 'news_id' => $newsId]);
        return true;
    }
}
