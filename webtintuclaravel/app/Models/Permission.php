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
            'ai' => 'Cong cu AI',
            'category' => 'Danh muc',
            'admin-manager' => 'Nhan vien noi bo',
            'member' => 'Thanh vien',
            'author' => 'Tac gia',
            'system' => 'He thong',
            'newsletter' => 'Newsletter',
            'contact' => 'Lien he',
            'comment' => 'Binh luan',
            'tag' => 'Tags',
            'role' => 'Phan quyen',
            'featured' => 'Bai viet noi bat',
            'ticker' => 'Tin nong',
            'ads' => 'Quang cao',
            'social' => 'Mang xa hoi',
            'page' => 'Trang noi dung',
        ];
    }
}
