<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    protected $table = 'social';
    protected $primaryKey = 'RowID';
    public $timestamps = false;

    protected $fillable = [
        'Name',
        'Alias',
        'Font',
        'Sort',
        'Status',
    ];
}
