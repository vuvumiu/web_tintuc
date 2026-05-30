<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = [
        'name', 'slug', 'meta_title', 'meta_description', 'popular_count', 'status',
    ];

    protected $casts = [
        'popular_count' => 'integer',
        'status' => 'integer',
    ];

    public function news(): HasMany
    {
        return $this->hasMany(NewsTag::class, 'tag_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('popular_count', 'DESC');
    }

    public function incrementPopular(): void
    {
        $this->increment('popular_count');
    }

    public static function findOrCreateByName(string $name): self
    {
        $slug = Str::slug($name);
        $tag = static::where('slug', $slug)->first();

        if (!$tag) {
            $tag = static::create([
                'name' => $name,
                'slug' => $slug,
            ]);
        }

        return $tag;
    }
}
