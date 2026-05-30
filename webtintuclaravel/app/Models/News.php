<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class News extends Model
{
    protected $primaryKey = 'RowID';
    public $incrementing = false;
    protected $keyType = 'int';

    protected static function booted(): void
    {
        static::saving(function (News $news) {
            $name = trim((string) ($news->getAttribute('Name') ?? ''));
            $title = trim((string) ($news->getAttribute('Title') ?? ''));
            $images = trim((string) ($news->getAttribute('Images') ?? ''));
            $image = trim((string) ($news->getAttribute('Image') ?? ''));

            if ($name === '' && $title !== '') {
                $news->setAttribute('Name', $title);
            }
            if ($title === '' && $name !== '') {
                $news->setAttribute('Title', $name);
            }

            if ($images === '' && $image !== '') {
                $news->setAttribute('Images', $image);
            }
            if ($image === '' && $images !== '') {
                $news->setAttribute('Image', $images);
            }

            if ($news->getAttribute('Views') === null && $news->getAttribute('View') !== null) {
                $news->setAttribute('Views', (int) $news->getAttribute('View'));
            }
            if ($news->getAttribute('View') === null && $news->getAttribute('Views') !== null) {
                $news->setAttribute('View', (int) $news->getAttribute('Views'));
            }

            if ($news->getAttribute('RowIDCat') === null && $news->getAttribute('cat_id') !== null) {
                $news->setAttribute('RowIDCat', (int) $news->getAttribute('cat_id'));
            }
            if ($news->getAttribute('cat_id') === null && $news->getAttribute('RowIDCat') !== null) {
                $news->setAttribute('cat_id', (int) $news->getAttribute('RowIDCat'));
            }

            if ($news->getAttribute('Status') === null && $news->getAttribute('publish') !== null) {
                $news->setAttribute('Status', (int) $news->getAttribute('publish'));
            }
            if ($news->getAttribute('publish') === null && $news->getAttribute('Status') !== null) {
                $news->setAttribute('publish', (int) $news->getAttribute('Status'));
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'RowID';
    }

    public static function find($id, $columns = ['*'])
    {
        return (new static())->newQuery()->where('RowID', $id)->first($columns);
    }

    protected $fillable = [
        'RowID',
        'RowIDCat', 'cat_id', 'Status', 'Name', 'Title', 'MetaTitle', 'MetaDescription',
        'MetaKeyword', 'SmallDescription', 'Description', 'Content',
        'Views', 'View', 'Images', 'Image', 'Alias', 'author_id',
        'Author', 'Date', 'hot', 'tags', 'publish',
    ];

    protected $casts = [
        'Views' => 'integer',
        'author_id' => 'integer',
    ];

    public function getNameAttribute($value): ?string
    {
        return $value ?: ($this->attributes['Title'] ?? null);
    }

    public function getTitleAttribute($value): ?string
    {
        return $value ?: ($this->attributes['Name'] ?? null);
    }

    public function getImagesAttribute($value): ?string
    {
        return $value ?: ($this->attributes['Image'] ?? null);
    }

    public function getViewsAttribute($value): int
    {
        return (int) ($value ?? $this->attributes['View'] ?? 0);
    }

    public function getRowIDCatAttribute($value): ?int
    {
        if ($value !== null) {
            return (int) $value;
        }

        return isset($this->attributes['cat_id']) ? (int) $this->attributes['cat_id'] : null;
    }

    public function getStatusAttribute($value): int
    {
        return (int) ($value ?? $this->attributes['publish'] ?? 0);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(NewsComment::class, 'news_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(NewsRating::class, 'news_id');
    }

    public function viewStats(): HasMany
    {
        return $this->hasMany(NewsViewStat::class, 'news_id', 'RowID');
    }

    public function author(): BelongsTo
    {
        if (Schema::hasColumn($this->getTable(), 'author_id')) {
            return $this->belongsTo(User::class, 'author_id');
        }
        if (Schema::hasColumn($this->getTable(), 'Author')) {
            return $this->belongsTo(User::class, 'Author', 'username');
        }
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'news_tags', 'news_id', 'tag_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class, 'news_id');
    }

    public function schedule(): HasMany
    {
        return $this->hasMany(NewsSchedule::class, 'news_id');
    }

    public function latestSchedule()
    {
        return $this->hasOne(NewsSchedule::class, 'news_id')->latestOfMany();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'RowIDCat', 'RowID');
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('score') ?? 0;
    }

    public function getTotalRatingAttribute()
    {
        return $this->ratings()->count();
    }

    public function scopePublished($query)
    {
        return $query->where('Status', 1);
    }

    public function scopeByCategory($query, $catId)
    {
        return $query->where('RowIDCat', $catId);
    }

    public function getRelatedNews(int $limit = 4): EloquentCollection
    {
        if ($limit <= 0) {
            return new EloquentCollection();
        }

        $related = new EloquentCollection();
        $excludedIds = [(int) $this->RowID];

        $appendUnique = function ($items) use (&$related, &$excludedIds, $limit): void {
            foreach ($items as $item) {
                $id = (int) $item->RowID;
                if (in_array($id, $excludedIds, true)) {
                    continue;
                }

                $related->push($item);
                $excludedIds[] = $id;

                if ($related->count() >= $limit) {
                    break;
                }
            }
        };

        if ($this->RowIDCat) {
            $appendUnique(static::query()
                ->where('RowIDCat', $this->RowIDCat)
                ->where('Status', 1)
                ->whereNotIn('RowID', $excludedIds)
                ->orderBy('RowID', 'DESC')
                ->limit($limit)
                ->get());
        }

        if ($related->count() < $limit && Schema::hasTable('news_tags')) {
            $tagIds = DB::table('news_tags')
                ->where('news_id', $this->RowID)
                ->pluck('tag_id')
                ->all();

            if (!empty($tagIds)) {
                $tagRelatedIds = DB::table('news_tags')
                    ->select('news_id', DB::raw('COUNT(*) as tag_matches'))
                    ->whereIn('tag_id', $tagIds)
                    ->whereNotIn('news_id', $excludedIds)
                    ->groupBy('news_id')
                    ->orderByDesc('tag_matches')
                    ->orderByDesc('news_id')
                    ->limit($limit - $related->count())
                    ->pluck('news_id')
                    ->all();

                if (!empty($tagRelatedIds)) {
                    $items = static::query()
                        ->where('Status', 1)
                        ->whereIn('RowID', $tagRelatedIds)
                        ->get()
                        ->sortBy(function (News $news) use ($tagRelatedIds) {
                            return array_search($news->RowID, $tagRelatedIds, true);
                        })
                        ->values();

                    $appendUnique($items);
                }
            }
        }

        if ($related->count() < $limit) {
            $appendUnique(static::query()
                ->where('Status', 1)
                ->whereNotIn('RowID', $excludedIds)
                ->orderBy('RowID', 'DESC')
                ->limit($limit - $related->count())
                ->get());
        }

        return $related;
    }
}
