<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqAIService
{
    private string $apiKey;
    private string $model;
    private int $timeout;
    private int $maxRetries;
    private array $chatHistory = [];

    public function __construct()
    {
        $this->apiKey = config('groq.api_key', '');
        $this->model = config('groq.model', 'llama-3.3-70b-versatile');
        $this->timeout = (int) config('groq.timeout', 30);
        $this->maxRetries = (int) config('groq.max_retries', 3);
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function chat(string $message, array $context = []): string
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Groq API chưa được cấu hình. Vui lòng thêm GROQ_API_KEY vào file .env.');
        }

        $messages = [];
        $systemPrompt = $this->buildSystemPrompt($context);
        if ($systemPrompt !== '') {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }

        foreach ($this->buildHistoryMessages($context['chat_history'] ?? []) as $historyMessage) {
            $messages[] = $historyMessage;
        }

        foreach ($this->chatHistory as $historyMessage) {
            $messages[] = [
                'role' => $historyMessage['role'],
                'content' => $historyMessage['content'],
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        $lastException = null;
        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => $this->model,
                        'messages' => $messages,
                        'temperature' => 0.7,
                        'max_tokens' => 1500,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['choices'][0]['message']['content'] ?? '';

                    $this->chatHistory[] = ['role' => 'user', 'content' => $message];
                    $this->chatHistory[] = ['role' => 'assistant', 'content' => $reply];

                    if (count($this->chatHistory) > 40) {
                        $this->chatHistory = array_slice($this->chatHistory, -40);
                    }

                    return $reply;
                }

                $statusCode = $response->status();
                $errorBody = $response->json();

                if ($statusCode === 401) {
                    throw new \Exception('Groq API Key không hợp lệ. Vui lòng kiểm tra GROQ_API_KEY trong file .env.');
                }

                if ($statusCode === 429) {
                    throw new \Exception('Groq API đã hết quota. Vui lòng thử lại sau.');
                }

                throw new \Exception('Groq API error: ' . ($errorBody['error']['message'] ?? "HTTP $statusCode"));
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $lastException = new \Exception('Không thể kết nối đến Groq API. Vui lòng kiểm tra kết nối mạng.');
            } catch (\Exception $e) {
                $lastException = $e;
            }

            if ($attempt < $this->maxRetries) {
                usleep(500000 * $attempt);
            }
        }

        throw $lastException ?? new \Exception('Groq AI không phản hồi sau ' . $this->maxRetries . ' lần thử.');
    }

    public function generate(string $prompt, ?string $systemPrompt = null): string
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Groq API chưa được cấu hình. Vui lòng thêm GROQ_API_KEY vào file .env.');
        }

        $messages = [];
        if ($systemPrompt) {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $lastException = null;
        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => $this->model,
                        'messages' => $messages,
                        'temperature' => 0.7,
                        'max_tokens' => 1500,
                    ]);

                if ($response->successful()) {
                    return $response->json()['choices'][0]['message']['content'] ?? '';
                }

                throw new \Exception('Groq API error: HTTP ' . $response->status());
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $lastException = new \Exception('Không thể kết nối đến Groq API. Vui lòng kiểm tra kết nối mạng.');
            } catch (\Exception $e) {
                $lastException = $e;
            }

            if ($attempt < $this->maxRetries) {
                usleep(500000 * $attempt);
            }
        }

        throw $lastException ?? new \Exception('Groq AI không phản hồi.');
    }

    public function generateJson(string $prompt, ?string $systemPrompt = null): array
    {
        $text = $this->generate($prompt, $systemPrompt);
        $text = trim($text);

        if (preg_match('/```json\s*(.*?)\s*```/s', $text, $matches)) {
            $text = $matches[1];
        } elseif (preg_match('/```\s*(.*?)\s*```/s', $text, $matches)) {
            $text = $matches[1];
        }

        $text = trim($text);

        if (($text[0] ?? '') === '`' || ($text[0] ?? '') === "'") {
            $text = substr($text, 1);
        }
        $len = strlen($text);
        if ($len > 0 && ($text[$len - 1] ?? '') === '`') {
            $text = substr($text, 0, -1);
        }

        $text = trim($text);
        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('GroqAI: Failed to parse JSON', ['text' => substr($text, 0, 200)]);
            throw new \Exception('Không thể phân tích phản hồi JSON từ AI.');
        }

        return $decoded;
    }

    public function generateMetaTags(string $title, string $description, string $content): array
    {
        return $this->generateJson(AIPromptTemplates::metaTagsPrompt($title, $description, $content));
    }

    public function suggestTags(string $title, string $content, string $category = ''): array
    {
        return $this->generateJson(AIPromptTemplates::suggestTagsPrompt($title, $content, $category));
    }

    public function moderateComment(string $comment, string $authorName = ''): array
    {
        return $this->generateJson(AIPromptTemplates::moderateCommentPrompt($comment, $authorName));
    }

    public function checkRateLimit(string $key, int $maxPerMinute): bool
    {
        $cacheKey = "groq_rate:{$key}:" . floor(time() / 60);
        $count = (int) Cache::get($cacheKey, 0);

        if ($count >= $maxPerMinute) {
            return false;
        }

        Cache::put($cacheKey, $count + 1, 61);
        return true;
    }

    public function clearHistory(): void
    {
        $this->chatHistory = [];
    }

    private function buildHistoryMessages(array $history): array
    {
        if (empty($history)) {
            return [];
        }

        $messages = [];

        foreach ($history as $item) {
            $role = $item['role'] ?? null;
            $text = trim((string) ($item['text'] ?? ''));

            if (!in_array($role, ['user', 'assistant'], true) || $text === '') {
                continue;
            }

            $messages[] = [
                'role' => $role,
                'content' => mb_substr($text, 0, 1000),
            ];
        }

        return $messages;
    }

    private function buildSystemPrompt(array $context): string
    {
        $lines = [
            'Bạn là trợ lý AI của một trang web tin tức tiếng Việt.',
            'Hãy trả lời thân thiện, ngắn gọn, hữu ích và bám sát nội dung bài viết khi ngữ cảnh được cung cấp.',
            'Nếu có đoạn văn bản được bôi đen, hãy ưu tiên giải thích đúng đoạn đó và liên hệ với toàn bài viết.',
            'Không dựa vào tiêu đề đơn lẻ nếu đã có ngữ cảnh bài viết đầy đủ.',
        ];

        if (!empty($context['title'])) {
            $lines[] = '';
            $lines[] = 'Tiêu đề bài viết: ' . $context['title'];
        }
        if (!empty($context['category'])) {
            $lines[] = 'Danh mục: ' . $context['category'];
        }
        if (!empty($context['description'])) {
            $lines[] = 'Mô tả ngắn: ' . $context['description'];
        }
        if (!empty($context['tags'])) {
            $lines[] = 'Tags: ' . implode(', ', (array) $context['tags']);
        }
        if (!empty($context['selected_text'])) {
            $lines[] = '';
            $lines[] = 'Đoạn văn bản người dùng vừa bôi đen:';
            $lines[] = (string) $context['selected_text'];
        }
        if (!empty($context['selected_excerpt'])) {
            $lines[] = '';
            $lines[] = 'Ngữ cảnh xung quanh đoạn được chọn:';
            $lines[] = (string) $context['selected_excerpt'];
        }
        if (!empty($context['full_content'])) {
            $lines[] = '';
            $lines[] = 'NỘI DUNG BÀI VIẾT ĐẦY ĐỦ:';
            $lines[] = (string) $context['full_content'];
        }
        if (!empty($context['related_news'])) {
            $lines[] = '';
            $lines[] = 'Các bài viết liên quan: ' . $context['related_news'];
        }
        if (!empty($context['recent_news'])) {
            $lines[] = 'Bài viết gần đây trên website: ' . $context['recent_news'];
        }

        $lines[] = '';
        $lines[] = 'Hãy trả lời bằng tiếng Việt có dấu.';

        return implode("\n", $lines);
    }
}
