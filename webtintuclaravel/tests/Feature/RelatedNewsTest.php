<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RelatedNewsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_detail_page_falls_back_to_latest_published_related_news(): void
    {
        $mainCategory = $this->createCategory('Danh muc rieng');
        $fallbackCategory = $this->createCategory('Danh muc fallback');

        $main = $this->createNews($mainCategory->RowID, [
            'Name' => 'Bai viet khong co cung danh muc',
            'Alias' => 'bai-viet-khong-co-cung-danh-muc-' . uniqid(),
            'Status' => 1,
        ]);

        $draftSameCategory = $this->createNews($mainCategory->RowID, [
            'Name' => 'Bai nhap khong duoc goi y',
            'Alias' => 'bai-nhap-khong-duoc-goi-y-' . uniqid(),
            'Status' => 0,
        ]);

        $fallback = $this->createNews($fallbackCategory->RowID, [
            'Name' => 'Bai lien quan fallback dang xuat ban',
            'Alias' => 'bai-lien-quan-fallback-dang-xuat-ban-' . uniqid(),
            'Status' => 1,
        ]);

        $this->get('/' . $main->Alias . '.html')
            ->assertOk()
            ->assertSee($fallback->Name)
            ->assertDontSee($draftSameCategory->Name);
    }

    private function createCategory(string $name): NewsCategory
    {
        $attributes = [
            'RowID' => $this->nextRowId('news_cat'),
            'Name' => $name . ' ' . uniqid(),
            'Alias' => 'danh-muc-' . uniqid(),
        ];

        if (Schema::hasColumn('news_cat', 'Status')) {
            $attributes['Status'] = 1;
        }

        return NewsCategory::query()->create($attributes);
    }

    private function createNews(int $categoryId, array $overrides = []): News
    {
        $rowId = $this->nextRowId('news');
        $attributes = [
            'RowID' => $rowId,
            'RowIDCat' => $categoryId,
            'cat_id' => $categoryId,
            'Name' => 'Bai viet test ' . uniqid(),
            'Title' => 'Bai viet test ' . uniqid(),
            'Alias' => 'bai-viet-test-' . uniqid(),
            'SmallDescription' => 'Mo ta ngan',
            'Description' => '<p>Noi dung bai viet test.</p>',
            'Content' => '<p>Noi dung bai viet test.</p>',
            'Status' => 1,
            'publish' => 1,
            'Views' => 0,
            'View' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('news', 'id')) {
            $attributes['id'] = $rowId;
        }

        return News::query()->create(array_merge($attributes, $overrides));
    }

    private function nextRowId(string $table): int
    {
        return (int) DB::table($table)->max('RowID') + 1;
    }
}

