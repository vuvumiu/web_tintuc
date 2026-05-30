<?php

namespace Tests\Feature;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\BackController;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Notification;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminNewsManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_news_pages_render_successfully(): void
    {
        $user = $this->createAdminUser();

        $this->actingAs($user)
            ->get('/admin/news/list')
            ->assertOk()
            ->assertSee('admin/news/bulk-action');

        $this->actingAs($user)
            ->get('/admin/news/add')
            ->assertOk()
            ->assertSee('id="ckeditor"', false);
    }

    public function test_sync_news_tags_can_clear_all_existing_tags(): void
    {
        $user = $this->createAdminUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $user->id);
        $tag = $this->createTag($news->RowID);

        DB::table('news_tags')->insert([
            'news_id' => $news->RowID,
            'tag_id' => $tag->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $controller = app(BackController::class);
        $method = new \ReflectionMethod($controller, 'syncNewsTags');
        $method->setAccessible(true);
        $method->invoke($controller, $news->RowID, '');

        $this->assertDatabaseMissing('news_tags', [
            'news_id' => $news->RowID,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_admin_can_add_news_and_submit_review(): void
    {
        $user = $this->createAdminUser();
        $category = $this->createCategory();
        $alias = 'bai-viet-gui-duyet-' . uniqid();

        $response = $this->actingAs($user)
            ->post('/admin/news/add', [
                'Name' => 'Bai viet gui duyet',
                'Alias' => $alias,
                'RowIDCat' => $category->RowID,
                'Description' => '<p>Noi dung gui duyet</p>',
                'publish_type' => 'now',
                'submit_action' => 'submit_review',
            ]);

        $news = News::query()->where('Alias', $alias)->first();

        $this->assertNotNull($news);
        $response->assertRedirect('admin/news/edit/' . $news->RowID)
            ->assertSessionHas('flash_level', 'success');

        $this->assertSame(0, (int) $news->fresh()->Status);
        $this->assertDatabaseHas('news_schedules', [
            'news_id' => $news->RowID,
            'status' => 'pending',
            'publish_type' => 'now',
        ]);
    }

    public function test_admin_can_add_news_and_submit_scheduled_review(): void
    {
        $user = $this->createAdminUser();
        $category = $this->createCategory();
        $scheduledAt = now()->addDay()->format('Y-m-d H:i:s');
        $alias = 'bai-viet-hen-gio-gui-duyet-' . uniqid();

        $response = $this->actingAs($user)
            ->post('/admin/news/add', [
                'Name' => 'Bai viet hen gio gui duyet',
                'Alias' => $alias,
                'RowIDCat' => $category->RowID,
                'Description' => '<p>Noi dung hen gio gui duyet</p>',
                'publish_type' => 'schedule',
                'scheduled_at' => $scheduledAt,
                'submit_action' => 'submit_review',
            ]);

        $news = News::query()->where('Alias', $alias)->first();

        $this->assertNotNull($news);
        $response->assertRedirect('admin/news/edit/' . $news->RowID)
            ->assertSessionHas('flash_level', 'success');

        $this->assertDatabaseHas('news_schedules', [
            'news_id' => $news->RowID,
            'status' => 'pending',
            'publish_type' => 'schedule',
        ]);
    }

    public function test_admin_without_author_flag_can_add_news_with_default_author(): void
    {
        $user = $this->createAdminUser(['is_author' => 0]);
        $category = $this->createCategory();
        $alias = 'bai-viet-admin-khong-la-tac-gia-' . uniqid();

        $response = $this->actingAs($user)
            ->post('/admin/news/add', [
                'Name' => 'Bai viet admin khong la tac gia',
                'Alias' => $alias,
                'RowIDCat' => $category->RowID,
                'Description' => '<p>Noi dung gui duyet</p>',
                'publish_type' => 'now',
                'submit_action' => 'submit_review',
            ]);

        $news = News::query()->where('Alias', $alias)->first();

        $this->assertNotNull($news);
        $response->assertRedirect('admin/news/edit/' . $news->RowID)
            ->assertSessionHas('flash_level', 'success');

        $this->assertDatabaseHas('news_schedules', [
            'news_id' => $news->RowID,
            'status' => 'pending',
        ]);
    }

    public function test_notification_helper_news_deleted_message(): void
    {
        $result = NotificationHelper::newsDeleted('Bài viết Test');
        $this->assertEquals('success', $result['flash_level']);
        $this->assertEquals("Đã xóa bài viết 'Bài viết Test' thành công.", $result['flash_message']);
    }

    public function test_notification_helper_news_not_found_message(): void
    {
        $result = NotificationHelper::newsNotFound();
        $this->assertEquals('warning', $result['flash_level']);
        $this->assertEquals('Không tìm thấy bài viết cần xóa.', $result['flash_message']);
    }

    public function test_news_delete_not_found_returns_warning(): void
    {
        $user = $this->createAdminUser();

        $this->actingAs($user)
            ->deleteJson('/admin/news/delete/99999')
            ->assertRedirect('admin/news/list')
            ->assertSessionHas('flash_level', 'warning')
            ->assertSessionHas('flash_message', 'Không tìm thấy bài viết cần xóa.');
    }

    public function test_news_bulk_action_delete_returns_success_with_count(): void
    {
        $user = $this->createAdminUser();
        $category = $this->createCategory();
        $news1 = $this->createNews($category->RowID, $user->id);
        $news2 = $this->createNews($category->RowID, $user->id);

        $this->actingAs($user)
            ->post('/admin/news/bulk-action', [
                'ids'    => "{$news1->RowID},{$news2->RowID}",
                'action' => 'delete',
            ])
            ->assertRedirect('admin/news/list')
            ->assertSessionHas('flash_level', 'success')
            ->assertSessionHas('flash_message', 'Đã xóa 2 bài viết được chọn.');
    }

    public function test_news_bulk_action_show_returns_success(): void
    {
        $user = $this->createAdminUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $user->id);

        $this->actingAs($user)
            ->post('/admin/news/bulk-action', [
                'ids'    => (string) $news->RowID,
                'action' => 'show',
            ])
            ->assertRedirect('admin/news/list')
            ->assertSessionHas('flash_level', 'success')
            ->assertSessionHas('flash_message', 'Đã hiển thị 1 bài viết được chọn.');
    }

    public function test_news_bulk_action_hide_returns_success(): void
    {
        $user = $this->createAdminUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $user->id);

        $this->actingAs($user)
            ->post('/admin/news/bulk-action', [
                'ids'    => (string) $news->RowID,
                'action' => 'hide',
            ])
            ->assertRedirect('admin/news/list')
            ->assertSessionHas('flash_level', 'success')
            ->assertSessionHas('flash_message', 'Đã ẩn 1 bài viết được chọn.');
    }

    public function test_news_bulk_action_submit_review_returns_success(): void
    {
        $user = $this->createAdminUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $user->id);

        $this->actingAs($user)
            ->post('/admin/news/bulk-action', [
                'ids'    => (string) $news->RowID,
                'action' => 'submit_review',
            ])
            ->assertRedirect('admin/news/list')
            ->assertSessionHas('flash_level', 'success')
            ->assertSessionHas('flash_message', 'Đã gửi để phê duyệt 1 bài viết được chọn.');
    }

    public function test_news_bulk_action_helper_empty_ids_returns_warning(): void
    {
        $result = NotificationHelper::bulkAction('delete', 0);
        $this->assertEquals('warning', $result['flash_level']);
        $this->assertEquals('Vui lòng chọn ít nhất một bài viết.', $result['flash_message']);
    }

    public function test_news_duplicate_helper_returns_success(): void
    {
        $result = NotificationHelper::newsDuplicated('Bài viết Test');
        $this->assertEquals('success', $result['flash_level']);
        $this->assertEquals("Đã sao chép bài viết 'Bài viết Test' thành bản nháp mới.", $result['flash_message']);
    }

    public function test_news_duplicate_not_found_helper_returns_danger(): void
    {
        $result = NotificationHelper::newsDuplicateNotFound();
        $this->assertEquals('danger', $result['flash_level']);
        $this->assertEquals('Bài viết không tồn tại.', $result['flash_message']);
    }

    public function test_notification_helper_flash_returns_correct_array(): void
    {
        $result = NotificationHelper::flash('success', 'Test message');
        $this->assertEquals(['flash_level' => 'success', 'flash_message' => 'Test message'], $result);
    }

    public function test_notification_helper_bulk_action_counts_correctly(): void
    {
        $result = NotificationHelper::bulkAction('delete', 3);
        $this->assertEquals('success', $result['flash_level']);
        $this->assertEquals('Đã xóa 3 bài viết được chọn.', $result['flash_message']);

        $resultEmpty = NotificationHelper::bulkAction('show', 0);
        $this->assertEquals('warning', $resultEmpty['flash_level']);
        $this->assertEquals('Vui lòng chọn ít nhất một bài viết.', $resultEmpty['flash_message']);
    }

    public function test_notification_helper_news_added_message(): void
    {
        $result = NotificationHelper::newsAdded('Bài viết mới');
        $this->assertEquals('success', $result['flash_level']);
        $this->assertEquals("Đã thêm bài viết 'Bài viết mới' thành công.", $result['flash_message']);
    }

    public function test_notification_helper_bulk_action_api(): void
    {
        $result = NotificationHelper::bulkActionApi(5);
        $this->assertTrue($result['success']);
        $this->assertEquals('Đã thực hiện trên 5 bài viết.', $result['message']);

        $resultEmpty = NotificationHelper::bulkActionApi(0);
        $this->assertFalse($resultEmpty['success']);
        $this->assertEquals('Vui lòng chọn ít nhất một bài viết.', $resultEmpty['message']);
    }

    public function test_notification_model_news_types_have_icons(): void
    {
        $types = [
            Notification::TYPE_NEWS_PUBLISHED,
            Notification::TYPE_NEWS_HIDDEN,
            Notification::TYPE_NEWS_SUBMITTED,
            Notification::TYPE_NEWS_DUPLICATED,
            Notification::TYPE_NEWS_BULK_ACTION,
        ];

        foreach ($types as $type) {
            $icon = Notification::typeIcon($type);
            $color = Notification::typeColor($type);
            $this->assertNotEmpty($icon, "Icon for {$type} should not be empty");
            $this->assertNotEmpty($color, "Color for {$type} should not be empty");
        }
    }

    private function createAdminUser(array $overrides = []): User
    {
        $attributes = [
            'level' => 1,
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

        return User::factory()->create(array_merge($attributes, $overrides));
    }

    private function createCategory(): NewsCategory
    {
        $attributes = [
            'RowID' => $this->nextRowId('news_cat'),
            'Name' => 'Danh muc test ' . fake()->unique()->word(),
            'Alias' => 'danh-muc-test-' . fake()->unique()->slug(),
        ];

        if (Schema::hasColumn('news_cat', 'Status')) {
            $attributes['Status'] = 1;
        }

        if (Schema::hasColumn('news_cat', 'Sort')) {
            $attributes['Sort'] = 0;
        }

        return NewsCategory::query()->create($attributes);
    }

    private function createNews(int $categoryId, int $authorId): News
    {
        $attributes = [
            'RowID' => $this->nextRowId('news'),
            'RowIDCat' => $categoryId,
            'Name' => 'Bai viet test ' . fake()->unique()->sentence(3),
            'Alias' => 'bai-viet-test-' . fake()->unique()->slug(),
            'Description' => '<p>Noi dung ban dau</p>',
        ];

        if (Schema::hasColumn('news', 'Status')) {
            $attributes['Status'] = 1;
        }

        if (Schema::hasColumn('news', 'SmallDescription')) {
            $attributes['SmallDescription'] = 'Mo ta ngan ban dau';
        }

        if (Schema::hasColumn('news', 'Views')) {
            $attributes['Views'] = 0;
        }

        if (Schema::hasColumn('news', 'author_id')) {
            $attributes['author_id'] = $authorId;
        }

        return News::query()->create($attributes);
    }

    private function createTag(int $newsId): Tag
    {
        $attributes = [
            'name' => 'Tag can xoa',
            'slug' => 'tag-can-xoa-' . $newsId,
        ];

        if (Schema::hasColumn('tags', 'status')) {
            $attributes['status'] = 1;
        }

        return Tag::query()->create($attributes);
    }

    private function nextRowId(string $table): int
    {
        return (int) DB::table($table)->max('RowID') + 1;
    }
}
