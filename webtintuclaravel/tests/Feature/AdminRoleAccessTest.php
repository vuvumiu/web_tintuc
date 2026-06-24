<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminRoleAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_administrator_has_full_access_without_assigned_roles(): void
    {
        $admin = $this->createInternalUser(1);
        $admin->roles()->detach();

        $this->assertTrue($admin->hasPermission('admin-manager.list'));
        $this->assertTrue($admin->hasPermission('news.approve'));
        $this->assertTrue($admin->hasPermission('ai.use'));
        $this->assertTrue($admin->hasPermission('system.settings'));

        $this->actingAs($admin)->get('/admin/admin-manager/list')->assertOk();
        $this->actingAs($admin)->get('/admin/ai/dashboard')->assertOk();
        $this->actingAs($admin)->get('/admin/system')->assertOk();
    }

    public function test_seo_content_only_receives_own_article_workflow_permissions(): void
    {
        $seo = $this->createInternalUser(2);
        $seo->roles()->detach();

        $this->assertTrue($seo->hasPermission('news.list'));
        $this->assertTrue($seo->hasPermission('news.create'));
        $this->assertTrue($seo->hasPermission('news.edit'));
        $this->assertTrue($seo->hasPermission('news.preview'));

        foreach ([
            'admin-manager.list',
            'author.list',
            'ai.use',
            'system.settings',
            'news.approve',
            'news.delete',
            'news.edit_all',
            'category.list',
            'tag.list',
            'featured.manage',
            'ticker.manage',
        ] as $permission) {
            $this->assertFalse($seo->hasPermission($permission), $permission . ' must be denied');
        }

        $this->actingAs($seo)->get('/admin/news/list')->assertOk();
        $this->actingAs($seo)->get('/admin/news/add')->assertOk();
        $this->actingAs($seo)->get('/admin/news-approval/drafts')->assertOk();
        $this->actingAs($seo)
            ->get('/admin/home')
            ->assertOk()
            ->assertDontSee('admin/admin-manager/list')
            ->assertDontSee('admin/ai/dashboard');
        $this->actingAs($seo)->get('/admin/admin-manager/list')->assertRedirect('admin/home');
        $this->actingAs($seo)->get('/admin/authors/list')->assertRedirect('admin/home');
        $this->actingAs($seo)->get('/admin/ai/dashboard')->assertRedirect('admin/home');
        $this->actingAs($seo)->get('/admin/system')->assertRedirect('admin/home');
        $this->actingAs($seo)->get('/admin/featured/list')->assertRedirect('admin/home');
        $this->actingAs($seo)->get('/admin/news-approval/queue')->assertRedirect('admin/home');
    }

    public function test_seo_content_can_edit_own_article_but_not_another_users_article(): void
    {
        $seo = $this->createInternalUser(2);
        $otherSeo = $this->createInternalUser(2);
        $category = $this->createCategory();
        $ownNews = $this->createNews($category->RowID, $seo->id);
        $otherNews = $this->createNews($category->RowID, $otherSeo->id);

        $this->actingAs($seo)
            ->get('/admin/news/edit/' . $ownNews->RowID)
            ->assertOk();

        $this->actingAs($seo)
            ->get('/admin/news/edit/' . $otherNews->RowID)
            ->assertRedirect('admin/news/list')
            ->assertSessionHas('flash_level', 'danger');
    }

    public function test_seo_content_cannot_publish_or_delete_through_bulk_actions(): void
    {
        $seo = $this->createInternalUser(2);
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $seo->id, 0);

        $this->actingAs($seo)
            ->post('/admin/news/bulk-action', [
                'ids' => (string) $news->RowID,
                'action' => 'show',
            ])
            ->assertRedirect('admin/news/list')
            ->assertSessionHas('flash_level', 'danger');

        $this->assertSame(0, (int) $news->fresh()->Status);

        $this->actingAs($seo)
            ->delete('/admin/news/delete/' . $news->RowID)
            ->assertRedirect('admin/home');

        $this->assertDatabaseHas('news', ['RowID' => $news->RowID]);
    }

    private function createInternalUser(int $level): User
    {
        $attributes = [
            'level' => $level,
            'status' => 1,
        ];

        if (Schema::hasColumn('users', 'is_admin_account')) {
            $attributes['is_admin_account'] = 1;
        }

        if (Schema::hasColumn('users', 'is_active')) {
            $attributes['is_active'] = 1;
        }

        if (Schema::hasColumn('users', 'is_author')) {
            $attributes['is_author'] = 1;
        }

        return User::factory()->create($attributes);
    }

    private function createCategory(): NewsCategory
    {
        $attributes = [
            'RowID' => $this->nextRowId('news_cat'),
            'Name' => 'Danh muc phan quyen ' . fake()->unique()->word(),
            'Alias' => 'danh-muc-phan-quyen-' . fake()->unique()->slug(),
        ];

        if (Schema::hasColumn('news_cat', 'Status')) {
            $attributes['Status'] = 1;
        }

        if (Schema::hasColumn('news_cat', 'Sort')) {
            $attributes['Sort'] = 0;
        }

        return NewsCategory::query()->create($attributes);
    }

    private function createNews(int $categoryId, int $authorId, int $status = 0): News
    {
        $attributes = [
            'RowID' => $this->nextRowId('news'),
            'RowIDCat' => $categoryId,
            'Name' => 'Bai viet phan quyen ' . fake()->unique()->sentence(3),
            'Alias' => 'bai-viet-phan-quyen-' . fake()->unique()->slug(),
            'Description' => '<p>Noi dung phan quyen</p>',
            'author_id' => $authorId,
        ];

        if (Schema::hasColumn('news', 'Status')) {
            $attributes['Status'] = $status;
        }

        if (Schema::hasColumn('news', 'SmallDescription')) {
            $attributes['SmallDescription'] = 'Mo ta ngan';
        }

        if (Schema::hasColumn('news', 'Views')) {
            $attributes['Views'] = 0;
        }

        return News::query()->create($attributes);
    }

    private function nextRowId(string $table): int
    {
        return (int) DB::table($table)->max('RowID') + 1;
    }
}
