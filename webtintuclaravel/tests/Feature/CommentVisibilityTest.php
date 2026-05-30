<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CommentVisibilityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_detail_page_shows_imported_root_comments_with_parent_id_zero(): void
    {
        $categoryId = $this->nextRowId('news_cat');
        DB::table('news_cat')->insert([
            'RowID' => $categoryId,
            'Name' => 'Danh muc binh luan',
            'Alias' => 'danh-muc-binh-luan-' . uniqid(),
            'Status' => 1,
        ]);

        $newsId = $this->nextRowId('news');
        $alias = 'bai-viet-co-binh-luan-parent-zero-' . uniqid();
        $news = [
            'RowID' => $newsId,
            'RowIDCat' => $categoryId,
            'cat_id' => $categoryId,
            'Name' => 'Bai viet co binh luan parent zero',
            'Title' => 'Bai viet co binh luan parent zero',
            'Alias' => $alias,
            'SmallDescription' => 'Mo ta ngan',
            'Description' => '<p>Noi dung test.</p>',
            'Content' => '<p>Noi dung test.</p>',
            'Status' => 1,
            'publish' => 1,
            'Views' => 0,
            'View' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('news', 'id')) {
            $news['id'] = $newsId;
        }

        DB::table('news')->insert($news);

        $user = User::factory()->create(['status' => 1]);
        DB::table('news_comments')->insert([
            'news_id' => $newsId,
            'user_id' => $user->id,
            'parent_id' => 0,
            'content' => 'Binh luan AI import phai hien thi',
            'is_active' => 1,
            'upvote_count' => 0,
            'downvote_count' => 0,
            'reply_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get('/' . $alias . '.html')
            ->assertOk()
            ->assertSee('Binh luan AI import phai hien thi');
    }

    private function nextRowId(string $table): int
    {
        return (int) DB::table($table)->max('RowID') + 1;
    }
}

