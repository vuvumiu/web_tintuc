<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturedNews extends Model
{
    protected $table = 'featured_news';

    protected $primaryKey = 'RowID';

    public $timestamps = true;

    protected $fillable = [
        'news_id',
        'position',
        'Sort',
        'Status',
    ];

    protected $casts = [
        'position' => 'integer',
        'Sort' => 'integer',
        'Status' => 'integer',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class, 'news_id', 'RowID');
    }

    public function scopeActive($query)
    {
        return $query->where('Status', 1);
    }

    public function scopeMainFeatured($query)
    {
        return $query->where('position', 1)->where('Status', 1)->orderBy('Sort', 'ASC');
    }

    public function scopeSidebarFeatured($query)
    {
        return $query->where('position', 2)->where('Status', 1)->orderBy('Sort', 'ASC');
    }

    public static function getNextSort($position)
    {
        $max = static::where('position', $position)->max('Sort');
        return ($max ?? 0) + 1;
    }
}
