<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $table = 'newsletter';
    protected $primaryKey = 'RowID';
    // Nếu cần factories, bạn thêm use HasFactory
    use HasFactory;
}
