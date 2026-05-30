<?php

namespace Tests\Feature;

use App\Models\CommentVote;
use App\Models\News;
use App\Models\NewsComment;
use App\Models\NewsRating;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Models\UserFavorite;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SocialNotificationTest extends TestCase
{
    use DatabaseTransactions;

    private function createUser(array $attrs = []): User
    {
        $defaults = [
            'username' => 'user_' . fake()->unique()->slug(3),
            'name'     => fake()->name(),
            'email'    => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ];

        if (Schema::hasColumn('users', 'is_admin')) {
            $defaults['is_admin'] = 0;
        }
        if (Schema::hasColumn('users', 'is_author')) {
            $defaults['is_author'] = 0;
        }
        if (Schema::hasColumn('users', 'account_status')) {
            $defaults['account_status'] = 'active';
        }

        return User::query()->create(array_merge($defaults, $attrs));
    }

    private function createCategory(array $attrs = []): \App\Models\NewsCategory
    {
        $defaults = [
            'Name'  => 'Danh muc ' . fake()->unique()->sentence(2),
            'Alias' => 'danh-muc-' . fake()->unique()->slug(),
            'Status'=> 1,
        ];

        if (Schema::hasColumn('news_category', 'Description')) {
            $defaults['Description'] = fake()->sentence();
        }
        if (Schema::hasColumn('news_category', 'Sort')) {
            $defaults['Sort'] = 0;
        }

        return \App\Models\NewsCategory::query()->create(array_merge($defaults, $attrs));
    }

    private function createNews(int $categoryId, ?int $authorId = null, array $attrs = []): News
    {
        $defaults = [
            'RowID'     => $this->nextRowId('news'),
            'RowIDCat'  => $categoryId,
            'Name'      => 'Bai viet ' . fake()->unique()->sentence(3),
            'Alias'     => 'bai-viet-' . fake()->unique()->slug(),
            'Status'    => 1,
            'Views'     => 0,
        ];

        if (Schema::hasColumn('news', 'SmallDescription')) {
            $defaults['SmallDescription'] = 'Mo ta ngan';
        }
        if (Schema::hasColumn('news', 'Description')) {
            $defaults['Description'] = '<p>Noi dung bai viet</p>';
        }
        if (Schema::hasColumn('news', 'author_id') && $authorId) {
            $defaults['author_id'] = $authorId;
        }

        $payload = array_merge($defaults, $attrs);
        News::query()->create($payload);

        return News::query()->where('Alias', $payload['Alias'])->firstOrFail();
    }

    private function nextRowId(string $table): int
    {
        $max = DB::table($table)->max('RowID') ?? 0;
        return $max + 1;
    }

    // ── NotificationPreference Tests ──────────────────────────────────────

    public function test_notification_preference_model_can_be_created(): void
    {
        $user = $this->createUser();

        $pref = NotificationPreference::create([
            'user_id'                => $user->id,
            'notify_comment_new'     => true,
            'notify_comment_reply'   => true,
            'notify_comment_upvote'  => true,
            'notify_comment_downvote'=> true,
            'notify_news_rated'      => true,
            'notify_news_favorited'  => true,
            'notify_news_approved'   => true,
            'notify_news_rejected'   => true,
            'notify_system'          => true,
        ]);

        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $user->id,
            'notify_comment_new' => 1,
            'notify_news_rated' => 1,
        ]);
    }

    public function test_notification_preference_for_user_creates_default(): void
    {
        $user = $this->createUser();

        $pref = NotificationPreference::forUser($user->id);

        $this->assertEquals($user->id, $pref->user_id);
        $this->assertTrue($pref->notify_comment_new);
        $this->assertTrue($pref->notify_comment_upvote);
        $this->assertTrue($pref->notify_news_rated);
        $this->assertTrue($pref->notify_system);
    }

    public function test_notification_preference_for_user_returns_existing(): void
    {
        $user = $this->createUser();
        NotificationPreference::create(['user_id' => $user->id, 'notify_comment_upvote' => false]);

        $pref = NotificationPreference::forUser($user->id);

        $this->assertFalse($pref->notify_comment_upvote);
    }

    public function test_notification_preference_is_enabled(): void
    {
        $user = $this->createUser();
        $pref = NotificationPreference::create([
            'user_id'               => $user->id,
            'notify_comment_upvote' => false,
            'notify_news_rated'     => true,
        ]);

        $this->assertFalse($pref->isEnabled(Notification::TYPE_COMMENT_UPVOTE));
        $this->assertTrue($pref->isEnabled(Notification::TYPE_NEWS_RATED));
        $this->assertTrue($pref->isEnabled('unknown_type'));
    }

    public function test_user_has_notification_enabled_calls_preference(): void
    {
        $user = $this->createUser();
        NotificationPreference::create([
            'user_id'             => $user->id,
            'notify_comment_new'  => false,
        ]);

        $this->assertFalse($user->hasNotificationEnabled(Notification::TYPE_COMMENT_NEW));
        $this->assertTrue($user->hasNotificationEnabled(Notification::TYPE_COMMENT_REPLY));
    }

    public function test_user_has_notification_enabled_without_preference_returns_true(): void
    {
        $user = $this->createUser();

        $this->assertTrue($user->hasNotificationEnabled(Notification::TYPE_COMMENT_NEW));
    }

    // ── NotificationService Tests ──────────────────────────────────────────

    public function test_should_notify_returns_false_for_null_user(): void
    {
        $result = NotificationService::shouldNotify(null, Notification::TYPE_COMMENT_UPVOTE);

        $this->assertFalse($result);
    }

    public function test_notify_comment_vote_returns_null_for_self_vote(): void
    {
        $author = $this->createUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $author->id);

        $comment = NewsComment::create([
            'news_id'  => $news->RowID,
            'user_id'  => $author->id,
            'content'  => 'Bình luận của tác giả',
            'is_active'=> true,
        ]);

        $result = NotificationService::notifyCommentVote($comment, $author, CommentVote::UPVOTE);

        $this->assertNull($result);
        $this->assertEquals(0, Notification::where('user_id', $author->id)->count());
    }

    public function test_notify_comment_vote_creates_notification(): void
    {
        $author = $this->createUser();
        $voter   = $this->createUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $author->id);

        $comment = NewsComment::create([
            'news_id'  => $news->RowID,
            'user_id'  => $author->id,
            'content'  => 'Bình luận của tác giả',
            'is_active'=> true,
        ]);

        $notif = NotificationService::notifyCommentVote($comment, $voter, CommentVote::UPVOTE);

        $this->assertNotNull($notif);
        $this->assertEquals($author->id, $notif->user_id);
        $this->assertEquals(Notification::TYPE_COMMENT_UPVOTE, $notif->type);
        $this->assertStringContainsString($voter->username, $notif->title);
        $this->assertEquals(1, Notification::where('user_id', $author->id)->count());
    }

    public function test_notify_comment_downvote_creates_notification(): void
    {
        $author  = $this->createUser();
        $voter   = $this->createUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $author->id);

        $comment = NewsComment::create([
            'news_id'  => $news->RowID,
            'user_id'  => $author->id,
            'content'  => 'Bình luận bị dislike',
            'is_active'=> true,
        ]);

        $notif = NotificationService::notifyCommentVote($comment, $voter, CommentVote::DOWNVOTE);

        $this->assertNotNull($notif);
        $this->assertEquals(Notification::TYPE_COMMENT_DOWNVOTE, $notif->type);
        $this->assertStringContainsString('không thích', $notif->title);
    }

    public function test_notify_news_rated_creates_notification(): void
    {
        $author  = $this->createUser();
        $rater   = $this->createUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $author->id);

        $notif = NotificationService::notifyNewsRated($news, $rater, 5);

        $this->assertNotNull($notif);
        $this->assertEquals($author->id, $notif->user_id);
        $this->assertEquals(Notification::TYPE_NEWS_RATED, $notif->type);
        $this->assertStringContainsString('5/5', $notif->title);
    }

    public function test_notify_news_rated_returns_null_for_self_rating(): void
    {
        $author  = $this->createUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $author->id);

        $notif = NotificationService::notifyNewsRated($news, $author, 5);

        $this->assertNull($notif);
    }

    public function test_notify_news_favorited_creates_notification(): void
    {
        $author  = $this->createUser();
        $saver   = $this->createUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $author->id);

        $notif = NotificationService::notifyNewsFavorited($news, $saver);

        $this->assertNotNull($notif);
        $this->assertEquals($author->id, $notif->user_id);
        $this->assertEquals(Notification::TYPE_NEWS_FAVORITED, $notif->type);
        $this->assertStringContainsString('lưu', $notif->title);
    }

    public function test_notify_news_favorited_returns_null_for_self_favorite(): void
    {
        $author  = $this->createUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $author->id);

        $notif = NotificationService::notifyNewsFavorited($news, $author);

        $this->assertNull($notif);
    }

    public function test_notify_disabled_via_preference_returns_null(): void
    {
        $author  = $this->createUser();
        $rater   = $this->createUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $author->id);

        NotificationPreference::create([
            'user_id'          => $author->id,
            'notify_news_rated'=> false,
        ]);

        $notif = NotificationService::notifyNewsRated($news, $rater, 4);

        $this->assertNull($notif);
    }

    public function test_get_unread_count_returns_correct_number(): void
    {
        $user = $this->createUser();

        Notification::createNotification($user->id, Notification::TYPE_COMMENT_NEW, 'Test 1');
        Notification::createNotification($user->id, Notification::TYPE_COMMENT_REPLY, 'Test 2');
        Notification::createNotification($user->id, Notification::TYPE_NEWS_RATED, 'Test 3', null, null, null, null);

        $count = NotificationService::getUnreadCount($user->id);

        $this->assertEquals(3, $count);
    }

    public function test_format_notification_returns_expected_array(): void
    {
        $user = $this->createUser();
        $notif = Notification::createNotification(
            $user->id,
            Notification::TYPE_COMMENT_UPVOTE,
            'Test title',
            'Test content',
            '/test-link'
        );

        $formatted = NotificationService::formatNotification($notif);

        $this->assertEquals($notif->id, $formatted['id']);
        $this->assertEquals('comment_upvote', $formatted['type']);
        $this->assertEquals('Test title', $formatted['title']);
        $this->assertEquals('Test content', $formatted['content']);
        $this->assertEquals('/test-link', $formatted['link']);
        $this->assertFalse($formatted['is_read']);
        $this->assertArrayHasKey('time', $formatted);
        $this->assertEquals('fa-thumbs-up', $formatted['icon']);
        $this->assertEquals('success', $formatted['color']);
    }

    public function test_clear_unread_cache_for_user(): void
    {
        $user = $this->createUser();

        Notification::createNotification($user->id, Notification::TYPE_COMMENT_NEW, 'Cached');
        NotificationService::clearUnreadCache($user->id);

        $this->assertTrue(true);
    }

    // ── Notification Model Tests ───────────────────────────────────────────

    public function test_notification_type_icon_returns_correct_icon(): void
    {
        $this->assertEquals('fa-thumbs-up', Notification::typeIcon(Notification::TYPE_COMMENT_UPVOTE));
        $this->assertEquals('fa-thumbs-down', Notification::typeIcon(Notification::TYPE_COMMENT_DOWNVOTE));
        $this->assertEquals('fa-star', Notification::typeIcon(Notification::TYPE_NEWS_RATED));
        $this->assertEquals('fa-heart', Notification::typeIcon(Notification::TYPE_NEWS_FAVORITED));
        $this->assertEquals('fa-bell', Notification::typeIcon('unknown'));
    }

    public function test_notification_type_color_returns_correct_color(): void
    {
        $this->assertEquals('success', Notification::typeColor(Notification::TYPE_COMMENT_UPVOTE));
        $this->assertEquals('warning', Notification::typeColor(Notification::TYPE_COMMENT_DOWNVOTE));
        $this->assertEquals('warning', Notification::typeColor(Notification::TYPE_NEWS_RATED));
        $this->assertEquals('danger', Notification::typeColor(Notification::TYPE_NEWS_FAVORITED));
        $this->assertEquals('secondary', Notification::typeColor('unknown'));
    }

    public function test_notification_mark_as_read(): void
    {
        $user = $this->createUser();
        $notif = Notification::createNotification($user->id, Notification::TYPE_COMMENT_NEW, 'Test');

        $this->assertEquals(0, $notif->is_read);
        $notif->markAsRead();
        $notif->refresh();

        $this->assertEquals(1, $notif->is_read);
        $this->assertNotNull($notif->read_at);
    }

    public function test_notification_unread_scope(): void
    {
        $user = $this->createUser();
        Notification::createNotification($user->id, Notification::TYPE_COMMENT_NEW, 'Unread 1');
        Notification::createNotification($user->id, Notification::TYPE_COMMENT_REPLY, 'Unread 2');
        $read = Notification::createNotification($user->id, Notification::TYPE_NEWS_RATED, 'Read');
        $read->markAsRead();

        $unread = Notification::unread()->where('user_id', $user->id)->count();

        $this->assertEquals(2, $unread);
    }

    public function test_notification_unread_count(): void
    {
        $user = $this->createUser();
        Notification::createNotification($user->id, Notification::TYPE_COMMENT_NEW, 'Test 1');
        Notification::createNotification($user->id, Notification::TYPE_COMMENT_NEW, 'Test 2');

        $this->assertEquals(2, Notification::unreadCount($user->id));
    }

    public function test_notification_create_notification(): void
    {
        $user = $this->createUser();

        $notif = Notification::createNotification(
            $user->id,
            Notification::TYPE_COMMENT_UPVOTE,
            'Ai do thich binh luan cua ban',
            'Noi dung binh luan',
            '/bai-viet.html#comment-1',
            1,
            'news_comment'
        );

        $this->assertEquals($user->id, $notif->user_id);
        $this->assertEquals(Notification::TYPE_COMMENT_UPVOTE, $notif->type);
        $this->assertEquals('Ai do thich binh luan cua ban', $notif->title);
        $this->assertEquals('Noi dung binh luan', $notif->content);
        $this->assertEquals('/bai-viet.html#comment-1', $notif->link);
        $this->assertEquals(1, $notif->reference_id);
        $this->assertEquals('news_comment', $notif->reference_type);
        $this->assertEquals(0, $notif->is_read);
    }

    // ── API Endpoint Tests ─────────────────────────────────────────────────

    public function test_api_unread_count_requires_auth(): void
    {
        $this->getJson('/api/notifications/unread')
            ->assertStatus(401);
    }

    public function test_api_unread_count_returns_correct_data(): void
    {
        $user = $this->createUser();

        Notification::createNotification($user->id, Notification::TYPE_COMMENT_UPVOTE, 'Test upvote');
        Notification::createNotification($user->id, Notification::TYPE_NEWS_RATED, 'Test rated');

        $response = $this->actingAs($user, 'web')->getJson('/api/notifications/unread');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'count',
                'notifications' => [
                    '*' => ['id', 'title', 'icon', 'color', 'time'],
                ],
            ])
            ->assertJson(['success' => true, 'count' => 2]);
    }

    public function test_api_mark_read_marks_notification(): void
    {
        $user = $this->createUser();
        $notif = Notification::createNotification($user->id, Notification::TYPE_COMMENT_NEW, 'Test');

        $response = $this->actingAs($user, 'web')
            ->postJson("/api/notifications/{$notif->id}/read");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $notif->refresh();
        $this->assertEquals(1, $notif->is_read);
    }

    public function test_api_mark_read_returns_404_for_wrong_user(): void
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $notif = Notification::createNotification($user1->id, Notification::TYPE_COMMENT_NEW, 'Test');

        $response = $this->actingAs($user2, 'web')
            ->postJson("/api/notifications/{$notif->id}/read");

        $response->assertStatus(404);
    }

    public function test_api_mark_all_read(): void
    {
        $user = $this->createUser();
        Notification::createNotification($user->id, Notification::TYPE_COMMENT_NEW, 'Test 1');
        Notification::createNotification($user->id, Notification::TYPE_COMMENT_REPLY, 'Test 2');

        $response = $this->actingAs($user, 'web')
            ->postJson('/api/notifications/read-all');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'count' => 0]);

        $this->assertEquals(0, Notification::unreadCount($user->id));
    }

    public function test_api_delete_removes_notification(): void
    {
        $user = $this->createUser();
        $notif = Notification::createNotification($user->id, Notification::TYPE_NEWS_FAVORITED, 'Test');

        $response = $this->actingAs($user, 'web')
            ->deleteJson("/api/notifications/{$notif->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('notifications', ['id' => $notif->id]);
    }

    public function test_api_list_returns_notifications(): void
    {
        $user = $this->createUser();
        Notification::createNotification($user->id, Notification::TYPE_COMMENT_UPVOTE, 'Test');

        $response = $this->actingAs($user, 'web')
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'count',
                'notifications',
            ]);
    }

    public function test_admin_notification_api_returns_and_marks_read(): void
    {
        $adminAttributes = [
            'level' => 1,
            'status' => 1,
        ];

        if (Schema::hasColumn('users', 'is_admin_account')) {
            $adminAttributes['is_admin_account'] = 1;
        }
        if (Schema::hasColumn('users', 'is_active')) {
            $adminAttributes['is_active'] = 1;
        }

        $admin = $this->createUser($adminAttributes);
        $notif = Notification::createNotification($admin->id, Notification::TYPE_COMMENT_NEW, 'Admin notif');

        $this->actingAs($admin, 'web')
            ->getJson('/admin/api/notifications')
            ->assertOk()
            ->assertJson(['success' => true, 'count' => 1]);

        $this->actingAs($admin, 'web')
            ->postJson('/admin/api/mark-notif-read', ['id' => $notif->id])
            ->assertOk()
            ->assertJson(['success' => true, 'count' => 0]);

        $notif->refresh();
        $this->assertEquals(1, $notif->is_read);
    }

    // ── NotificationController Filter Tests ────────────────────────────────

    public function test_notification_index_page_renders(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user, 'web')
            ->get('/thong-bao');

        $response->assertStatus(200);
    }

    public function test_notification_index_with_type_comment_filter(): void
    {
        $user = $this->createUser();
        Notification::createNotification($user->id, Notification::TYPE_COMMENT_NEW, 'Comment notif');
        Notification::createNotification($user->id, Notification::TYPE_NEWS_RATED, 'Rated notif');

        $response = $this->actingAs($user, 'web')
            ->get('/thong-bao?type=comment');

        $response->assertStatus(200);
    }

    public function test_notification_index_with_unread_only_filter(): void
    {
        $user = $this->createUser();
        Notification::createNotification($user->id, Notification::TYPE_COMMENT_NEW, 'Unread');
        $read = Notification::createNotification($user->id, Notification::TYPE_COMMENT_REPLY, 'Read');
        $read->markAsRead();

        $response = $this->actingAs($user, 'web')
            ->get('/thong-bao?unread_only=1');

        $response->assertStatus(200);
    }

    public function test_notification_settings_page_renders(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user, 'web')
            ->get('/thong-bao/cai-dat');

        $response->assertStatus(200);
    }

    public function test_notification_settings_post_updates_preferences(): void
    {
        $user = $this->createUser();
        NotificationPreference::forUser($user->id);

        $response = $this->actingAs($user, 'web')
            ->post('/thong-bao/cai-dat', [
                'notify_comment_new'      => '1',
                'notify_comment_reply'    => '1',
                'notify_comment_upvote'   => '0',
                'notify_comment_downvote' => '1',
                'notify_news_rated'       => '1',
                'notify_news_favorited'   => '1',
                'notify_news_approved'    => '1',
                'notify_news_rejected'    => '1',
                'notify_system'           => '0',
            ]);

        $response->assertRedirect();

        $pref = NotificationPreference::where('user_id', $user->id)->first();
        $this->assertFalse($pref->notify_comment_upvote);
        $this->assertFalse($pref->notify_system);
        $this->assertTrue($pref->notify_comment_new);
    }

    public function test_comment_store_respects_author_comment_new_preference(): void
    {
        $author = $this->createUser();
        $commenter = $this->createUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $author->id);

        NotificationPreference::create([
            'user_id' => $author->id,
            'notify_comment_new' => false,
        ]);

        $this->actingAs($commenter, 'web')
            ->postJson('/binh-luan', [
                'news_id' => $news->RowID,
                'content' => 'Bình luận mới cần test thông báo',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('notifications', [
            'user_id' => $author->id,
            'type' => Notification::TYPE_COMMENT_NEW,
        ]);
    }

    public function test_comment_reply_respects_parent_author_reply_preference(): void
    {
        $parentAuthor = $this->createUser();
        $replier = $this->createUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $parentAuthor->id);

        $parent = NewsComment::create([
            'news_id' => $news->RowID,
            'user_id' => $parentAuthor->id,
            'content' => 'Bình luận gốc',
            'is_active' => true,
        ]);

        NotificationPreference::create([
            'user_id' => $parentAuthor->id,
            'notify_comment_reply' => false,
        ]);

        $this->actingAs($replier, 'web')
            ->postJson('/binh-luan/phan-hoi', [
                'news_id' => $news->RowID,
                'parent_id' => $parent->id,
                'content' => 'Trả lời bình luận cần test thông báo',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('notifications', [
            'user_id' => $parentAuthor->id,
            'type' => Notification::TYPE_COMMENT_REPLY,
        ]);
    }

    public function test_comment_vote_removal_does_not_create_extra_notification(): void
    {
        $author = $this->createUser();
        $voter = $this->createUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $author->id);

        $comment = NewsComment::create([
            'news_id' => $news->RowID,
            'user_id' => $author->id,
            'content' => 'Bình luận để test gỡ vote',
            'is_active' => true,
        ]);

        $this->actingAs($voter, 'web')
            ->postJson('/binh-luan/vote', [
                'comment_id' => $comment->id,
                'vote_type' => CommentVote::UPVOTE,
            ])
            ->assertOk()
            ->assertJson(['success' => true, 'action' => 'added']);

        $this->actingAs($voter, 'web')
            ->postJson('/binh-luan/vote', [
                'comment_id' => $comment->id,
                'vote_type' => CommentVote::UPVOTE,
            ])
            ->assertOk()
            ->assertJson(['success' => true, 'action' => 'removed']);

        $this->assertEquals(1, Notification::where('user_id', $author->id)
            ->where('type', Notification::TYPE_COMMENT_UPVOTE)
            ->count());
    }

    public function test_rating_update_does_not_create_duplicate_notification(): void
    {
        $author = $this->createUser();
        $rater = $this->createUser();
        $category = $this->createCategory();
        $news = $this->createNews($category->RowID, $author->id);

        $this->actingAs($rater, 'web')
            ->postJson('/danh-gia-sao', [
                'news_id' => $news->RowID,
                'score' => 4,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->actingAs($rater, 'web')
            ->postJson('/danh-gia-sao', [
                'news_id' => $news->RowID,
                'score' => 5,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertEquals(1, Notification::where('user_id', $author->id)
            ->where('type', Notification::TYPE_NEWS_RATED)
            ->count());
    }
}
