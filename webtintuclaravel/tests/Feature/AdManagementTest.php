<?php

namespace Tests\Feature;

use App\Models\Ad;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Ad::query()->delete();
    }

    public function test_admin_create_forces_popup_ads_logic(): void
    {
        $admin = $this->createAdminUser();

        Ad::create([
            'name' => 'Popup da co',
            'type' => Ad::TYPE_POPUP,
            'location' => Ad::LOC_ALL,
            'status' => true,
            'sort' => 4,
            'priority' => 0,
        ]);

        $this->actingAs($admin)
            ->post('/admin/ads/add', [
                'name' => 'Popup tu tang thu tu',
                'type' => Ad::TYPE_BANNER,
                'location' => Ad::LOC_ARTICLE,
                'status' => '1',
                'priority' => '0',
                'show_close_button' => '1',
                'impression_limit' => '3',
                'cooldown_minutes' => '45',
                'show_delay_seconds' => '5',
            ])
            ->assertRedirect('admin/ads/list')
            ->assertSessionHas('flash_level', 'success');

        $this->assertDatabaseHas('ads', [
            'name' => 'Popup tu tang thu tu',
            'type' => Ad::TYPE_POPUP,
            'location' => Ad::LOC_ARTICLE,
            'sort' => 5,
            'show_once_per_session' => 0,
            'impression_limit' => 3,
            'cooldown_minutes' => 45,
            'show_delay_seconds' => 5,
        ]);
    }

    public function test_admin_list_only_shows_popup_ads(): void
    {
        $admin = $this->createAdminUser();

        Ad::create([
            'name' => 'Popup hien trong admin',
            'type' => Ad::TYPE_POPUP,
            'location' => Ad::LOC_ALL,
            'status' => true,
        ]);

        Ad::create([
            'name' => 'Banner khong con hien',
            'type' => Ad::TYPE_BANNER,
            'location' => Ad::LOC_HOME,
            'status' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/ads/list')
            ->assertOk()
            ->assertSee('Popup hien trong admin')
            ->assertDontSee('Banner khong con hien');
    }

    public function test_legacy_slider_admin_page_redirects_to_popup_ads(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)
            ->get('/admin/slider/list')
            ->assertRedirect('admin/ads/list');
    }

    public function test_random_popup_filters_by_location_status_and_dates(): void
    {
        Ad::create([
            'name' => 'Popup tat',
            'type' => Ad::TYPE_POPUP,
            'location' => Ad::LOC_ALL,
            'status' => false,
        ]);

        Ad::create([
            'name' => 'Popup chua den ngay',
            'type' => Ad::TYPE_POPUP,
            'location' => Ad::LOC_ALL,
            'status' => true,
            'start_date' => now()->addDay(),
        ]);

        Ad::create([
            'name' => 'Popup chi trang chu',
            'type' => Ad::TYPE_POPUP,
            'location' => Ad::LOC_HOME,
            'status' => true,
        ]);

        Ad::create([
            'name' => 'Popup bai viet hop le',
            'type' => Ad::TYPE_POPUP,
            'location' => Ad::LOC_ARTICLE,
            'status' => true,
        ]);

        $ad = Ad::getRandomPopup(Ad::LOC_ARTICLE);

        $this->assertNotNull($ad);
        $this->assertSame('Popup bai viet hop le', $ad->name);
    }

    public function test_homepage_renders_random_popup_and_tracks_view_after_display(): void
    {
        $ad = Ad::create([
            'name' => 'Popup trang public test',
            'type' => Ad::TYPE_POPUP,
            'location' => Ad::LOC_ALL,
            'status' => true,
            'view_count' => 0,
            'impression_limit' => 2,
            'cooldown_minutes' => 15,
            'show_delay_seconds' => 4,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Popup trang public test')
            ->assertSee('vu-popup-overlay', false)
            ->assertSee('data-impression-limit="2"', false)
            ->assertSee('data-cooldown-minutes="15"', false)
            ->assertSee('data-show-delay-seconds="4"', false);

        $this->assertSame(0, $ad->fresh()->view_count);

        $this->post('/ads/track-view/' . $ad->id)
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame(1, $ad->fresh()->view_count);
    }

    public function test_article_page_renders_any_active_popup_after_clicking_news(): void
    {
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID);

        Ad::create([
            'name' => 'Popup ngau nhien tren bai viet',
            'type' => Ad::TYPE_POPUP,
            'location' => Ad::LOC_ARTICLE,
            'status' => true,
        ]);

        $this->get('/' . $news->Alias . '.html')
            ->assertOk()
            ->assertSee('Popup ngau nhien tren bai viet')
            ->assertSee('vu-popup-overlay', false);
    }

    public function test_article_page_does_not_render_homepage_only_popup(): void
    {
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID);

        Ad::create([
            'name' => 'Popup chi hien trang chu',
            'type' => Ad::TYPE_POPUP,
            'location' => Ad::LOC_HOME,
            'status' => true,
        ]);

        $this->get('/' . $news->Alias . '.html')
            ->assertOk()
            ->assertDontSee('Popup chi hien trang chu');
    }

    public function test_banner_and_sidebar_ads_are_no_longer_rendered_inline(): void
    {
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID);

        Ad::create([
            'name' => 'Banner cu khong hien',
            'type' => Ad::TYPE_BANNER,
            'location' => Ad::LOC_ALL,
            'status' => true,
        ]);

        Ad::create([
            'name' => 'Sidebar cu khong hien',
            'type' => Ad::TYPE_SIDEBAR,
            'location' => Ad::LOC_ARTICLE,
            'status' => true,
        ]);

        $this->get('/' . $news->Alias . '.html')
            ->assertOk()
            ->assertDontSee('Banner cu khong hien')
            ->assertDontSee('Sidebar cu khong hien');
    }

    public function test_track_popup_click_increments_click_count(): void
    {
        $ad = Ad::create([
            'name' => 'Popup click test',
            'type' => Ad::TYPE_POPUP,
            'location' => Ad::LOC_ALL,
            'status' => true,
            'click_count' => 3,
        ]);

        $this->post('/ads/track-click/' . $ad->id)
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame(4, $ad->fresh()->click_count);
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

        return User::factory()->create($attributes);
    }

    private function createCategory(array $attrs = []): NewsCategory
    {
        $defaults = [
            'Name' => 'Danh muc test popup',
            'Alias' => 'danh-muc-test-popup-' . uniqid(),
            'Status' => 1,
        ];

        if (Schema::hasColumn('news_cat', 'Sort')) {
            $defaults['Sort'] = 0;
        }

        return NewsCategory::query()->create(array_merge($defaults, $attrs));
    }

    private function createNews(int $categoryId, array $attrs = []): News
    {
        $defaults = [
            'RowID' => $this->nextRowId('news'),
            'RowIDCat' => $categoryId,
            'Name' => 'Bai viet test popup',
            'Alias' => 'bai-viet-test-popup-' . uniqid(),
            'Description' => '<p>Noi dung bai viet de kiem tra popup.</p>',
            'Status' => 1,
            'Views' => 0,
        ];

        if (Schema::hasColumn('news', 'SmallDescription')) {
            $defaults['SmallDescription'] = 'Mo ta ngan';
        }

        News::query()->create(array_merge($defaults, $attrs));

        return News::query()->where('Alias', $defaults['Alias'])->firstOrFail();
    }

    private function nextRowId(string $table): int
    {
        return ((int) DB::table($table)->max('RowID')) + 1;
    }
}
