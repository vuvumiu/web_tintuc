<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            [
                'name' => 'author.list',
                'display_name' => 'Xem danh sach tac gia',
                'group' => 'author',
                'description' => null,
            ],
            [
                'name' => 'author.manage',
                'display_name' => 'Quan ly tu cach tac gia',
                'group' => 'author',
                'description' => null,
            ],
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

        $rolePermissions = [
            'super_admin' => ['author.list', 'author.manage'],
            'editor' => ['author.list'],
            'writer' => ['author.list'],
            'moderator' => ['author.list'],
            'viewer' => ['author.list'],
        ];

        foreach ($rolePermissions as $roleName => $permissionsForRole) {
            $roleId = DB::table('roles')->where('name', $roleName)->value('id');

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
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')
            ->whereIn('name', ['author.list', 'author.manage'])
            ->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            DB::table('role_permissions')
                ->whereIn('permission_id', $permissionIds->all())
                ->delete();

            DB::table('permissions')
                ->whereIn('id', $permissionIds->all())
                ->delete();
        }
    }
};
