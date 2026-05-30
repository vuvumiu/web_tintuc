<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsComment;
use App\Models\NewsRating;
use App\Models\User;
use App\Services\GroqAIService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class AdminToolsPagesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_ai_pages_render_with_groq_labels(): void
    {
        $user = $this->createAdminUser();

        $this->actingAs($user)
            ->get('/admin/ai/dashboard')
            ->assertOk()
            ->assertSee('Groq (Llama)');

        $this->actingAs($user)
            ->get('/admin/ai/settings')
            ->assertOk()
            ->assertSee('Groq API Key');
    }

    public function test_admin_ai_endpoints_use_groq_service(): void
    {
        config([
            'gemini.features.meta_tags' => true,
            'gemini.features.smart_tags' => true,
            'gemini.features.comment_moderation' => true,
        ]);

        $user = $this->createAdminUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $user->id);
        $comment = NewsComment::query()->create([
            'news_id' => $news->RowID,
            'user_id' => $user->id,
            'content' => 'Bình luận kiểm thử',
            'is_active' => 1,
        ]);

        $groq = Mockery::mock(GroqAIService::class);
        $groq->shouldReceive('checkRateLimit')->andReturnTrue();
        $groq->shouldReceive('generateMetaTags')->once()->andReturn([
            'meta_title' => 'Tiêu đề SEO',
            'meta_description' => 'Mô tả SEO được tạo bằng Groq.',
            'meta_keywords' => 'tin tức, groq',
        ]);
        $groq->shouldReceive('suggestTags')->once()->andReturn([
            ['name' => 'Công nghệ', 'slug' => 'cong-nghe'],
        ]);
        $groq->shouldReceive('moderateComment')->once()->andReturn([
            'action' => 'APPROVE',
            'reason' => 'Bình luận hợp lệ',
            'confidence' => 0.9,
        ]);
        $this->app->instance(GroqAIService::class, $groq);

        $this->actingAs($user)
            ->postJson('/admin/ai/generate-meta', [
                'title' => 'Bài viết kiểm thử',
                'description' => 'Mô tả ngắn',
                'content' => 'Nội dung bài viết',
            ])
            ->assertOk()
            ->assertJsonPath('data.meta_title', 'Tiêu đề SEO');

        $this->actingAs($user)
            ->postJson('/admin/ai/suggest-tags', [
                'title' => 'Bài viết kiểm thử',
                'content' => 'Nội dung bài viết',
                'category' => 'Công nghệ',
            ])
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Công nghệ');

        $this->actingAs($user)
            ->postJson('/admin/ai/moderate-comment', [
                'comment_id' => $comment->id,
            ])
            ->assertOk()
            ->assertJsonPath('data.action', 'APPROVE');
    }

    public function test_admin_rating_and_contact_pages_render(): void
    {
        $user = $this->createAdminUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $user->id);

        NewsRating::query()->create([
            'news_id' => $news->RowID,
            'user_id' => $user->id,
            'score' => 5,
        ]);

        $contact = Contact::query()->create([
            'Name' => 'Nguyễn Văn A',
            'Email' => 'a@example.com',
            'Phone' => '0900000000',
            'subject' => 'Cần hỗ trợ',
            'Message' => 'Tôi cần hỗ trợ nội dung này.',
            'category' => Contact::CATEGORY_CONSULT,
            'priority' => Contact::PRIORITY_MEDIUM,
            'is_reviewed' => 0,
        ]);

        $this->actingAs($user)
            ->get('/admin/rating/list')
            ->assertOk()
            ->assertSee('Quản lý đánh giá sao');

        $this->actingAs($user)
            ->get('/admin/contact/list')
            ->assertOk()
            ->assertSee('Nguyễn Văn A');

        $this->actingAs($user)
            ->get('/admin/contact/edit/' . $contact->RowID)
            ->assertOk()
            ->assertSee('Gửi phản hồi cho khách hàng');
    }

    private function createAdminUser(): User
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

        return User::factory()->create($attributes);
    }

    private function createCategory(): NewsCategory
    {
        $attributes = [
            'RowID' => $this->nextRowId('news_cat'),
            'Name' => 'Danh mục kiểm thử ' . fake()->unique()->word(),
            'Alias' => 'danh-muc-kiem-thu-' . fake()->unique()->slug(),
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
            'Name' => 'Bài viết kiểm thử ' . fake()->unique()->sentence(3),
            'Alias' => 'bai-viet-kiem-thu-' . fake()->unique()->slug(),
            'Description' => '<p>Nội dung ban đầu</p>',
        ];

        if (Schema::hasColumn('news', 'Status')) {
            $attributes['Status'] = 1;
        }

        if (Schema::hasColumn('news', 'SmallDescription')) {
            $attributes['SmallDescription'] = 'Mô tả ngắn ban đầu';
        }

        if (Schema::hasColumn('news', 'Views')) {
            $attributes['Views'] = 0;
        }

        if (Schema::hasColumn('news', 'author_id')) {
            $attributes['author_id'] = $authorId;
        }

        return News::query()->create($attributes);
    }

    private function nextRowId(string $table): int
    {
        return (int) DB::table($table)->max('RowID') + 1;
    }
}
