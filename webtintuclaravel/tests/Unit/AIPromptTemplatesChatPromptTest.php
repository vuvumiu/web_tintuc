<?php

namespace Tests\Unit;

use App\Services\AIPromptTemplates;
use Tests\TestCase;

class AIPromptTemplatesChatPromptTest extends TestCase
{
    public function test_chat_prompt_includes_full_article_selection_and_history_context(): void
    {
        $prompt = AIPromptTemplates::chatPrompt('Doan nay co y nghia gi?', [
            'title' => 'Bai viet test',
            'category' => 'Cong nghe',
            'description' => 'Mo ta ngan',
            'tags' => ['AI', 'Chatbot'],
            'full_content' => 'Noi dung day du cua bai viet dang duoc xem.',
            'selected_text' => 'Doan text duoc boi den.',
            'selected_excerpt' => 'Ngu canh xung quanh doan text duoc boi den.',
            'recent_news' => 'Tin 1; Tin 2',
            'chat_history' => [
                ['role' => 'user', 'text' => 'Tom tat bai viet'],
                ['role' => 'assistant', 'text' => 'Tom tat ngan gon'],
            ],
        ]);

        $this->assertStringContainsString('NGỮ CẢNH BÀI VIẾT ĐANG XEM:', $prompt);
        $this->assertStringContainsString('NỘI DUNG BÀI VIẾT ĐẦY ĐỦ:', $prompt);
        $this->assertStringContainsString('Doan text duoc boi den.', $prompt);
        $this->assertStringContainsString('Ngu canh xung quanh doan text duoc boi den.', $prompt);
        $this->assertStringContainsString('LỊCH SỬ CHAT GẦN ĐÂY:', $prompt);
        $this->assertStringContainsString('CÂU HỎI HIỆN TẠI CỦA NGƯỜI DÙNG:', $prompt);
    }
}
