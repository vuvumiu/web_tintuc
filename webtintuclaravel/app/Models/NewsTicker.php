<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsTicker extends Model
{
    protected $table = 'news_tickers';

    protected $primaryKey = 'RowID';

    public $timestamps = true;

    protected $fillable = [
        'RowID',
        'news_id',
        'title',
        'alias',
        'Status',
        'Sort',
    ];

    protected $casts = [
        'Status' => 'integer',
        'Sort' => 'integer',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class, 'news_id', 'RowID');
    }

    public function scopeActive($query)
    {
        return $query->where('Status', 1)->orderBy('Sort', 'ASC');
    }

    public function getDisplayTitle(): string
    {
        if (!empty($this->title)) {
            return $this->title;
        }
        if ($this->news) {
            return $this->news->Name;
        }
        return '';
    }

    public function getDisplayUrl(): string
    {
        if ($this->news) {
            return url($this->news->Alias . '.html');
        }
        if (!empty($this->alias)) {
            return url($this->alias);
        }
        return '#';
    }
}
