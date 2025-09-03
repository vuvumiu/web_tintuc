<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
    protected $table = 'news_cat';
    protected $primaryKey = 'RowID';
    // Nếu cần factories, bạn thêm use HasFactory
    use HasFactory;
}
