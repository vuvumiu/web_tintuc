<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = ['name', 'display_name', 'group', 'description'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public static function groupsList(): array
    {
        return [
            'news' => 'Bai viet',
            'category' => 'Danh muc',
            'staff' => 'Nhan vien',
            'author' => 'Tac gia',
            'account' => 'Tai khoan',
            'system' => 'He thong',
            'newsletter' => 'Newsletter',
            'contact' => 'Lien he',
            'comment' => 'Binh luan',
            'tag' => 'Tags',
            'role' => 'Phan quyen',
        ];
    }
}
