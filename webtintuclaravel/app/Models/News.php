<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $primaryKey = 'RowID';
    protected $fillable = [
        'RowIDCat', 'Status', 'Name', 'MetaTitle', 'MetaDescription', 
        'MetaKeyword', 'SmallDescription', 'Description'
    ];
}