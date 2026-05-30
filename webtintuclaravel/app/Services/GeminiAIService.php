<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GeminiAIService
{
    public function __construct()
    {
    }

    private function getApiKey(): string
    {
        return config('gemini.api_key', '');
    }

    private function getModel(): string
    {
        return config('gemini.model', 'gemini-2.0-flash');
    }

    private function getEndpoint(): string
    {
        return config('gemini.api_endpoint', 'https://generativelanguage.googleapis.com/v1beta/models');
    }

    private function getTimeout(): int
    {
        return (int) config('gemini.timeout', 30);
    }

    private function getMaxRetries(): int
    {
        return (int) config('gemini.max_retries', 3);
    }

    public function isConfigured(): bool
    {
        return !empty($this->getApiKey());
    }

    /**
     * Generate text using Gemini API with retry logic.
     */
    public function generate(string $prompt, ?string $systemPrompt = null): string
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Gemini API chưa được cấu hình. Vui lòng thêm GEMINI_API_KEY vào file .env');
        }

        $apiKey = $this->getApiKey();
        $model = $this->getModel();
        $endpoint = $this->getEndpoint();
        $timeout = $this->getTimeout();
        $maxRetries = $this->getMaxRetries();

        $contents = [];

        if ($systemPrompt) {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $systemPrompt . "\n\n" . $prompt]]
            ];
        } else {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $prompt]]
            ];
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ],
        ];

        $url = "{$endpoint}/{$model}:generateContent?key={$apiKey}";

        $lastException = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::timeout($timeout)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $payload);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        return trim($data['candidates'][0]['content']['parts'][0]['text']);
                    }

                    if (isset($data['candidates'][0]['finishReason'])) {
                        $reason = $data['candidates'][0]['finishReason'];
                        if ($reason === 'SAFETY' || $reason === 'RECITATION') {
                            throw new \Exception('Nội dung bị chặn bởi bộ lọc an toàn của Gemini.');
                        }
                    }

                    throw new \Exception('Không nhận được phản hồi hợp lệ từ Gemini.');
                }

                $statusCode = $response->status();
                $errorBody = $response->json();

                if ($statusCode === 429) {
                    throw new \Exception('Quota API Gemini dùng chung của website đã hết hoặc project Google AI Studio đang bị giới hạn tốc độ.');
                }

                if ($statusCode === 403 || $statusCode === 400) {
                    throw new \Exception('API Key Gemini không hợp lệ hoặc chưa được cấu hình.');
                }

                throw new \Exception('Gemini API error: ' . ($errorBody['error']['message'] ?? "HTTP $statusCode"));

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $lastException = new \Exception('Không thể kết nối đến Gemini API. Vui lòng kiểm tra kết nối mạng.');
            } catch (\Illuminate\Http\Client\RequestException $e) {
                $lastException = new \Exception('Lỗi kết nối Gemini: ' . $e->getMessage());
            } catch (\Exception $e) {
                $lastException = $e;
            }

            if ($attempt < $maxRetries) {
                usleep(500000 * $attempt);
            }
        }

        throw $lastException ?? new \Exception('Gemini AI không phản hồi sau ' . $maxRetries . ' lần thử.');
    }

    /**
     * Generate JSON response by parsing text output.
     */
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
            Log::warning('GeminiAI: Failed to parse JSON response', [
                'original_text' => substr($text, 0, 500),
                'json_error' => json_last_error_msg(),
            ]);
            throw new \Exception('Không thể phân tích phản hồi JSON từ AI. Vui lòng thử lại.');
        }

        return $decoded;
    }

    /**
     * Check rate limit for a given key.
     */
    public function checkRateLimit(string $key, int $maxPerMinute): bool
    {
        $cacheKey = "gemini_rate:{$key}:" . floor(time() / 60);

        $count = (int) Cache::get($cacheKey, 0);

        if ($count >= $maxPerMinute) {
            return false;
        }

        Cache::put($cacheKey, $count + 1, 61);

        return true;
    }

    /**
     * Generate meta tags (title, description, keywords) for a news article.
     */
    public function generateMetaTags(string $title, string $description, string $content): array
    {
        $prompt = AIPromptTemplates::metaTagsPrompt($title, $description, $content);
        return $this->generateJson($prompt);
    }

    /**
     * Suggest tags for a news article.
     */
    public function suggestTags(string $title, string $content, string $category = ''): array
    {
        $prompt = AIPromptTemplates::suggestTagsPrompt($title, $content, $category);
        return $this->generateJson($prompt);
    }

    /**
     * Moderate a comment.
     */
    public function moderateComment(string $comment, string $authorName = ''): array
    {
        $prompt = AIPromptTemplates::moderateCommentPrompt($comment, $authorName);
        return $this->generateJson($prompt);
    }

    /**
     * Chat with context.
     */
    public function chat(string $message, array $context = []): string
    {
        $prompt = AIPromptTemplates::chatPrompt($message, $context);
        $systemPrompt = AIPromptTemplates::chatSystemPrompt();
        return $this->generate($prompt, $systemPrompt);
    }
}
