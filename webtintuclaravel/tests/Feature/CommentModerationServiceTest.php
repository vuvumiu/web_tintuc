<?php

namespace Tests\Feature;

use App\Models\NewsComment;
use App\Models\User;
use App\Services\CommentModerationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommentModerationServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_normal_comment_is_automatically_approved(): void
    {
        config(['gemini.features.comment_moderation' => false]);

        $result = app(CommentModerationService::class)->moderate(
            'Bai viet phan tich rat ro rang ' . uniqid(),
            User::factory()->create(),
            '127.0.0.1'
        );

        $this->assertSame(NewsComment::STATUS_APPROVED, $result['status']);
        $this->assertSame(0, $result['score']);
    }

    public function test_obvious_advertising_spam_is_rejected(): void
    {
        config(['gemini.features.comment_moderation' => false]);

        $result = app(CommentModerationService::class)->moderate(
            'casino gia re aaaaaaaaa https://spam-one.example.com https://spam-two.example.com https://spam-three.example.com',
            User::factory()->create(),
            '127.0.0.2'
        );

        $this->assertSame(NewsComment::STATUS_SPAM, $result['status']);
        $this->assertGreaterThanOrEqual(70, $result['score']);
        $this->assertNotSame('', $result['reason']);
    }
}
