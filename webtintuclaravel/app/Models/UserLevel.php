<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLevel extends Model
{
    protected $table = 'users_level';
    protected $primaryKey = 'id';
    // Nếu cần factories, bạn thêm use HasFactory
    use HasFactory;
}
