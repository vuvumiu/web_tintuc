<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsViewStat extends Model
{
    protected $table = 'news_view_stats';

    protected $fillable = [
        'news_id',
        'view_date',
        'total_views',
    ];

    protected $casts = [
        'news_id' => 'integer',
        'view_date' => 'date',
        'total_views' => 'integer',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class, 'news_id', 'RowID');
    }

    public static function recordView(int $newsId, ?CarbonInterface $viewedAt = null): void
    {
        $date = ($viewedAt ?: now())->toDateString();

        $stat = static::query()->firstOrNew([
            'news_id' => $newsId,
            'view_date' => $date,
        ]);

        $stat->total_views = (int) ($stat->total_views ?? 0) + 1;
        $stat->save();
    }
}
