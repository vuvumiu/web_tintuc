<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\NewsCategory;
use App\Services\GeminiAIService;
use App\Services\GroqAIService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class FrontAiChatSelectionTest extends TestCase
{
    use DatabaseTransactions;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_selection_chat_includes_full_article_context_and_recent_history(): void
    {
        config([
            'gemini.features.chatbot' => true,
            'gemini.provider' => 'gemini',
            'gemini.api_key' => 'fake-key',
        ]);

        $category = $this->createCategory();
        $selectedText = 'Doan van quan trong can duoc AI phan tich ky.';

        $news = $this->createNews(
            $category->RowID,
            '<p>Mo dau bai viet.</p><p>' . $selectedText . ' Them ngu canh truoc va sau de AI hieu dung van de.</p><p>Ket bai.</p>'
        );

        $this->createNews($category->RowID, '<p>Bai viet lien quan thu hai.</p>');

        $gemini = Mockery::mock(GeminiAIService::class);
        $gemini->shouldReceive('checkRateLimit')->once()->andReturnTrue();
        $gemini->shouldReceive('chat')
            ->once()
            ->with(
                'Y nghia cua doan nay la gi?',
                Mockery::on(function (array $context) use ($selectedText) {
                    $this->assertSame($selectedText, $context['selected_text'] ?? null);
                    $this->assertArrayHasKey('chat_history', $context);
                    $this->assertCount(2, $context['chat_history']);
                    $this->assertSame('user', $context['chat_history'][0]['role']);
                    $this->assertSame('assistant', $context['chat_history'][1]['role']);
                    $this->assertArrayHasKey('recent_news', $context);

                    return true;
                })
            )
            ->andReturn('AI tra loi da co context.');

        $groq = Mockery::mock(GroqAIService::class);

        $this->app->instance(GeminiAIService::class, $gemini);
        $this->app->instance(GroqAIService::class, $groq);

        $response = $this
            ->withSession([
                'ai_chat_history' => [
                    ['role' => 'user', 'text' => 'Tom tat bai viet truoc do', 'time' => now()->subMinute()->toIso8601String()],
                    ['role' => 'assistant', 'text' => 'Day la tom tat ngan.', 'time' => now()->subSeconds(30)->toIso8601String()],
                ],
            ])
            ->postJson('/ai/chat', [
                'message' => 'Y nghia cua doan nay la gi?',
                'news_id' => $news->RowID,
                'selected_text' => $selectedText,
            ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'reply' => 'AI tra loi da co context.',
            ]);

        $history = session('ai_chat_history', []);
        $this->assertCount(4, $history);
        $this->assertSame('Y nghia cua doan nay la gi?', $history[2]['text']);
        $this->assertSame('AI tra loi da co context.', $history[3]['text']);
    }

    private function createCategory(): NewsCategory
    {
        $attributes = [
            'RowID' => $this->nextRowId('news_cat'),
            'Name' => 'Danh muc AI test',
            'Alias' => 'danh-muc-ai-test-' . fake()->unique()->slug(),
        ];

        if (Schema::hasColumn('news_cat', 'Status')) {
            $attributes['Status'] = 1;
        }

        if (Schema::hasColumn('news_cat', 'Sort')) {
            $attributes['Sort'] = 0;
        }

        return NewsCategory::query()->create($attributes);
    }

    private function createNews(int $categoryId, string $description): News
    {
        $attributes = [
            'RowID' => $this->nextRowId('news'),
            'RowIDCat' => $categoryId,
            'Name' => 'Bai viet AI test ' . fake()->unique()->sentence(3),
            'Alias' => 'bai-viet-ai-test-' . fake()->unique()->slug(),
            'Description' => $description,
        ];

        if (Schema::hasColumn('news', 'Status')) {
            $attributes['Status'] = 1;
        }

        if (Schema::hasColumn('news', 'SmallDescription')) {
            $attributes['SmallDescription'] = 'Mo ta ngan de test AI selection';
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
