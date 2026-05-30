<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    protected $table = 'system';
    protected $primaryKey = 'RowID';
    protected $fillable = ['Code', 'Description', 'logo_type', 'Status'];
    use HasFactory;
}
