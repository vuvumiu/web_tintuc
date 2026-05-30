<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsCategory extends Model
{
    protected $table = 'news_cat';

    protected $primaryKey = 'RowID';

    public $timestamps = false;

    protected $fillable = [
        'RowID',
        'Name',
        'Alias',
        'Status',
        'image',
        'color',
        'description',
        'Sort',
    ];

    use HasFactory;

    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'RowIDCat', 'RowID');
    }
}
