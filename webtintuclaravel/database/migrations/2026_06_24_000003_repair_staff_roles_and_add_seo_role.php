<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('permissions')
            || !Schema::hasTable('roles')
            || !Schema::hasTable('role_permissions')
            || !Schema::hasTable('user_roles')) {
            return;
        }

        $permissions = [
            ['name' => 'news.edit_all', 'display_name' => 'Chỉnh sửa mọi bài viết', 'group' => 'news'],
            ['name' => 'admin-manager.list', 'display_name' => 'Xem tài khoản nội bộ', 'group' => 'admin-manager'],
            ['name' => 'admin-manager.create', 'display_name' => 'Tạo tài khoản nội bộ', 'group' => 'admin-manager'],
            ['name' => 'admin-manager.edit', 'display_name' => 'Sửa tài khoản nội bộ', 'group' => 'admin-manager'],
            ['name' => 'admin-manager.delete', 'display_name' => 'Xóa tài khoản nội bộ', 'group' => 'admin-manager'],
            ['name' => 'member.list', 'display_name' => 'Xem thành viên', 'group' => 'member'],
            ['name' => 'member.edit', 'display_name' => 'Sửa thành viên', 'group' => 'member'],
            ['name' => 'member.delete', 'display_name' => 'Xóa thành viên', 'group' => 'member'],
            ['name' => 'member.lock', 'display_name' => 'Khóa thành viên', 'group' => 'member'],
            ['name' => 'ads.manage', 'display_name' => 'Quản lý quảng cáo', 'group' => 'ads'],
            ['name' => 'featured.manage', 'display_name' => 'Quản lý bài viết nổi bật', 'group' => 'featured'],
            ['name' => 'ticker.manage', 'display_name' => 'Quản lý tin nóng', 'group' => 'ticker'],
            ['name' => 'ai.use', 'display_name' => 'Sử dụng công cụ AI nội dung', 'group' => 'ai'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission + [
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        DB::table('roles')->updateOrInsert(
            ['name' => 'seo'],
            [
                'display_name' => 'Chuyên viên SEO',
                'description' => 'Tối ưu SEO bài viết, trang, tags và nội dung phân phối',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $rolePermissions = [
            'super_admin' => DB::table('permissions')->pluck('name')->all(),
            'editor' => [
                'news.list', 'news.create', 'news.edit', 'news.edit_all', 'news.delete',
                'news.approve', 'news.preview',
                'category.list', 'category.create', 'category.edit',
                'tag.list', 'tag.create', 'tag.edit', 'tag.delete',
                'author.list',
                'comment.list', 'comment.delete', 'comment.hide', 'comment.moderate',
                'featured.manage', 'ticker.manage', 'social.manage', 'page.manage', 'ai.use',
            ],
            'seo' => [
                'news.list', 'news.create', 'news.edit', 'news.edit_all', 'news.preview',
                'category.list',
                'tag.list', 'tag.create', 'tag.edit',
                'featured.manage', 'social.manage', 'page.manage', 'ai.use',
            ],
            'writer' => [
                'news.list', 'news.create', 'news.edit', 'news.preview',
                'category.list', 'tag.list', 'ai.use',
            ],
            'moderator' => [
                'comment.list', 'comment.delete', 'comment.hide', 'comment.moderate',
                'contact.list', 'contact.reply',
                'newsletter.list',
                'member.list', 'member.edit',
                'author.list',
            ],
            'viewer' => [
                'news.list', 'category.list', 'contact.list', 'comment.list',
            ],
        ];

        $permissionIds = DB::table('permissions')->pluck('id', 'name');
        $roleIds = DB::table('roles')->pluck('id', 'name');

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $roleId = $roleIds->get($roleName);
            if (!$roleId) {
                continue;
            }

            $ids = collect($permissionNames)
                ->map(fn ($name) => $permissionIds->get($name))
                ->filter()
                ->values();

            DB::table('role_permissions')->where('role_id', $roleId)->delete();

            foreach ($ids as $permissionId) {
                DB::table('role_permissions')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $permissionId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        if (!Schema::hasTable('users')) {
            return;
        }

        DB::table('users')
            ->where('level', 2)
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('user_roles')
                    ->whereColumn('user_roles.user_id', 'users.id');
            })
            ->orderBy('id')
            ->each(function ($user) use ($roleIds) {
                $roleName = (int) ($user->is_author ?? 0) === 1 ? 'writer' : 'viewer';
                $roleId = $roleIds->get($roleName);
                if (!$roleId) {
                    return;
                }

                DB::table('user_roles')->insert([
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        $seoRoleId = DB::table('roles')->where('name', 'seo')->value('id');
        if ($seoRoleId) {
            DB::table('user_roles')->where('role_id', $seoRoleId)->delete();
            DB::table('role_permissions')->where('role_id', $seoRoleId)->delete();
            DB::table('roles')->where('id', $seoRoleId)->delete();
        }

        $permissionNames = [
            'news.edit_all',
            'admin-manager.list',
            'admin-manager.create',
            'admin-manager.edit',
            'admin-manager.delete',
            'member.list',
            'member.edit',
            'member.delete',
            'member.lock',
            'ads.manage',
            'featured.manage',
            'ticker.manage',
            'ai.use',
        ];
        $permissionIds = DB::table('permissions')->whereIn('name', $permissionNames)->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
