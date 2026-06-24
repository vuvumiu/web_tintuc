<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminRoleAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_seo_role_has_content_tools_without_approval_or_ticker_access(): void
    {
        $seo = $this->createStaffWithPermissions('seo-test', [
            'news.list',
            'news.create',
            'news.edit',
            'news.edit_all',
            'news.preview',
            'featured.manage',
            'ai.use',
        ]);

        $this->assertTrue($seo->hasPermission('news.edit_all'));
        $this->assertTrue($seo->hasPermission('featured.manage'));
        $this->assertTrue($seo->hasPermission('ai.use'));
        $this->assertFalse($seo->hasPermission('news.approve'));
        $this->assertFalse($seo->hasPermission('ticker.manage'));

        $this->actingAs($seo)->get('/admin/news/list')->assertOk();
        $this->actingAs($seo)->get('/admin/featured/list')->assertOk();
        $this->actingAs($seo)->get('/admin/ai/dashboard')->assertOk();
        $this->actingAs($seo)->get('/admin/news-approval/queue')->assertRedirect('admin/home');
        $this->actingAs($seo)->get('/admin/ticker/list')->assertRedirect('admin/home');
    }

    public function test_writer_role_is_limited_to_author_workflow(): void
    {
        $writer = $this->createStaffWithPermissions('writer-test', [
            'news.list',
            'news.create',
            'news.edit',
            'news.preview',
            'category.list',
            'tag.list',
            'ai.use',
        ], true);

        $this->assertTrue($writer->hasPermission('news.create'));
        $this->assertTrue($writer->hasPermission('news.edit'));
        $this->assertFalse($writer->hasPermission('news.edit_all'));
        $this->assertFalse($writer->hasPermission('news.approve'));
        $this->assertFalse($writer->hasPermission('featured.manage'));

        $this->actingAs($writer)->get('/admin/news/list')->assertOk();
        $this->actingAs($writer)->get('/admin/news-approval/drafts')->assertOk();
        $this->actingAs($writer)->get('/admin/featured/list')->assertRedirect('admin/home');
        $this->actingAs($writer)->get('/admin/news-approval/queue')->assertRedirect('admin/home');
    }

    private function createStaffWithPermissions(string $roleName, array $permissionNames, bool $isAuthor = false): User
    {
        $permissions = collect($permissionNames)->map(function (string $name) {
            return Permission::query()->firstOrCreate(
                ['name' => $name],
                [
                    'display_name' => $name,
                    'group' => explode('.', $name)[0],
                ]
            );
        });

        $role = Role::query()->firstOrCreate(
            ['name' => $roleName],
            [
                'display_name' => $roleName,
                'description' => 'Role used by access tests',
            ]
        );
        $role->permissions()->sync($permissions->pluck('id'));

        $attributes = [
            'level' => 2,
            'status' => 1,
        ];

        if (Schema::hasColumn('users', 'is_admin_account')) {
            $attributes['is_admin_account'] = 1;
        }

        if (Schema::hasColumn('users', 'is_active')) {
            $attributes['is_active'] = 1;
        }

        if (Schema::hasColumn('users', 'is_author')) {
            $attributes['is_author'] = $isAuthor ? 1 : 0;
        }

        $user = User::factory()->create($attributes);
        $user->roles()->sync([$role->id]);

        return $user->fresh();
    }
}
