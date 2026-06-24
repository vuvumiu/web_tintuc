<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RBACSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'news.list', 'display_name' => 'Xem danh sach bai viet', 'group' => 'news', 'description' => null],
            ['name' => 'news.create', 'display_name' => 'Tao bai viet moi', 'group' => 'news', 'description' => null],
            ['name' => 'news.edit', 'display_name' => 'Chinh sua bai viet', 'group' => 'news', 'description' => null],
            ['name' => 'news.delete', 'display_name' => 'Xoa bai viet', 'group' => 'news', 'description' => null],
            ['name' => 'news.approve', 'display_name' => 'Duyet bai viet', 'group' => 'news', 'description' => null],
            ['name' => 'news.preview', 'display_name' => 'Xem truoc bai viet', 'group' => 'news', 'description' => null],

            ['name' => 'category.list', 'display_name' => 'Xem danh muc', 'group' => 'category', 'description' => null],
            ['name' => 'category.create', 'display_name' => 'Tao danh muc', 'group' => 'category', 'description' => null],
            ['name' => 'category.edit', 'display_name' => 'Sua danh muc', 'group' => 'category', 'description' => null],
            ['name' => 'category.delete', 'display_name' => 'Xoa danh muc', 'group' => 'category', 'description' => null],

            ['name' => 'tag.list', 'display_name' => 'Xem tags', 'group' => 'tag', 'description' => null],
            ['name' => 'tag.create', 'display_name' => 'Tao tags', 'group' => 'tag', 'description' => null],
            ['name' => 'tag.edit', 'display_name' => 'Sua tags', 'group' => 'tag', 'description' => null],
            ['name' => 'tag.delete', 'display_name' => 'Xoa tags', 'group' => 'tag', 'description' => null],

            ['name' => 'admin-manager.list', 'display_name' => 'Xem tai khoan noi bo', 'group' => 'admin-manager', 'description' => null],
            ['name' => 'admin-manager.create', 'display_name' => 'Tao tai khoan noi bo', 'group' => 'admin-manager', 'description' => null],
            ['name' => 'admin-manager.edit', 'display_name' => 'Sua tai khoan noi bo', 'group' => 'admin-manager', 'description' => null],
            ['name' => 'admin-manager.delete', 'display_name' => 'Xoa tai khoan noi bo', 'group' => 'admin-manager', 'description' => null],

            ['name' => 'author.list', 'display_name' => 'Xem danh sach tac gia', 'group' => 'author', 'description' => null],
            ['name' => 'author.manage', 'display_name' => 'Quan ly tu cach tac gia', 'group' => 'author', 'description' => null],

            ['name' => 'member.list', 'display_name' => 'Xem thanh vien', 'group' => 'member', 'description' => null],
            ['name' => 'member.edit', 'display_name' => 'Sua thanh vien', 'group' => 'member', 'description' => null],
            ['name' => 'member.delete', 'display_name' => 'Xoa thanh vien', 'group' => 'member', 'description' => null],
            ['name' => 'member.lock', 'display_name' => 'Khoa thanh vien', 'group' => 'member', 'description' => null],

            ['name' => 'system.settings', 'display_name' => 'Cau hinh he thong', 'group' => 'system', 'description' => null],

            ['name' => 'role.list', 'display_name' => 'Xem phan quyen', 'group' => 'role', 'description' => null],
            ['name' => 'role.create', 'display_name' => 'Tao vai tro', 'group' => 'role', 'description' => null],
            ['name' => 'role.edit', 'display_name' => 'Sua vai tro', 'group' => 'role', 'description' => null],
            ['name' => 'role.delete', 'display_name' => 'Xoa vai tro', 'group' => 'role', 'description' => null],

            ['name' => 'newsletter.list', 'display_name' => 'Xem newsletter', 'group' => 'newsletter', 'description' => null],
            ['name' => 'newsletter.export', 'display_name' => 'Xuat newsletter', 'group' => 'newsletter', 'description' => null],

            ['name' => 'contact.list', 'display_name' => 'Xem lien he', 'group' => 'contact', 'description' => null],
            ['name' => 'contact.reply', 'display_name' => 'Tra loi lien he', 'group' => 'contact', 'description' => null],

            ['name' => 'comment.list', 'display_name' => 'Xem binh luan', 'group' => 'comment', 'description' => null],
            ['name' => 'comment.delete', 'display_name' => 'Xoa binh luan', 'group' => 'comment', 'description' => null],
            ['name' => 'comment.hide', 'display_name' => 'An hien binh luan', 'group' => 'comment', 'description' => null],
            ['name' => 'comment.moderate', 'display_name' => 'Kiem duyet binh luan', 'group' => 'comment', 'description' => null],

            ['name' => 'slider.manage', 'display_name' => 'Quan ly slider', 'group' => 'slider', 'description' => null],
            ['name' => 'ads.manage', 'display_name' => 'Quan ly quang cao', 'group' => 'ads', 'description' => null],
            ['name' => 'social.manage', 'display_name' => 'Quan ly social', 'group' => 'social', 'description' => null],
            ['name' => 'page.manage', 'display_name' => 'Quan ly trang', 'group' => 'page', 'description' => null],
        ];

        $permissionIds = [];
        foreach ($permissions as $permission) {
            $permissionId = DB::table('permissions')->where('name', $permission['name'])->value('id');

            if (!$permissionId) {
                $permissionId = DB::table('permissions')->insertGetId($permission + [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $permissionIds[$permission['name']] = $permissionId;
        }

        $roles = [
            'super_admin' => ['name' => 'super_admin', 'display_name' => 'Quan tri cao cap', 'description' => 'Toan quyen he thong'],
            'editor' => ['name' => 'editor', 'display_name' => 'Bien tap vien', 'description' => 'Quan ly bai viet, tags, duyet bai'],
            'writer' => ['name' => 'writer', 'display_name' => 'Phong vien', 'description' => 'Tao va chinh sua bai viet'],
            'moderator' => ['name' => 'moderator', 'display_name' => 'Kiem duyet vien', 'description' => 'Quan ly binh luan, lien he'],
            'viewer' => ['name' => 'viewer', 'display_name' => 'Nguoi xem', 'description' => 'Chi xem'],
        ];

        $roleIds = [];
        foreach ($roles as $name => $data) {
            $roleId = DB::table('roles')->where('name', $data['name'])->value('id');

            if (!$roleId) {
                $roleId = DB::table('roles')->insertGetId($data + [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $roleIds[$name] = $roleId;
        }

        $rolePermissions = [
            'super_admin' => array_keys($permissionIds),
            'editor' => [
                'news.list', 'news.create', 'news.edit', 'news.approve', 'news.preview',
                'category.list', 'category.create', 'category.edit',
                'tag.list', 'tag.create', 'tag.edit',
                'author.list',
                'contact.list', 'contact.reply',
                'comment.list', 'comment.delete', 'comment.hide', 'comment.moderate',
                'slider.manage', 'ads.manage', 'social.manage', 'page.manage',
            ],
            'writer' => [
                'news.list', 'news.create', 'news.edit', 'news.preview',
                'category.list',
                'tag.list',
                'author.list',
            ],
            'moderator' => [
                'comment.list', 'comment.delete', 'comment.hide', 'comment.moderate',
                'contact.list', 'contact.reply',
                'newsletter.list',
                'member.list', 'member.edit',
                'author.list',
            ],
            'viewer' => [
                'news.list', 'category.list', 'contact.list', 'comment.list', 'author.list',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionsForRole) {
            $roleId = $roleIds[$roleName] ?? null;

            if (!$roleId) {
                continue;
            }

            foreach ($permissionsForRole as $permissionName) {
                $permissionId = $permissionIds[$permissionName] ?? null;

                if (!$permissionId) {
                    continue;
                }

                $exists = DB::table('role_permissions')
                    ->where('role_id', $roleId)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (!$exists) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('RBAC seeded: ' . count($permissionIds) . ' permissions, ' . count($roleIds) . ' roles.');
    }
}
