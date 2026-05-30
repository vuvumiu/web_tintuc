<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Services\GeminiAIService;
use App\Services\GroqAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    private $ai;
    private string $provider;

    public function __construct(GeminiAIService $gemini, GroqAIService $groq)
    {
        $provider = config('gemini.provider', 'gemini');

        if ($provider === 'groq' && config('groq.api_key')) {
            $this->ai = $groq;
            $this->provider = 'groq';
        } elseif (config('gemini.api_key')) {
            $this->ai = $gemini;
            $this->provider = 'gemini';
        } else {
            $this->ai = $groq;
            $this->provider = 'groq';
        }
    }

    public function chat(Request $request)
    {
        if (!config('gemini.features.chatbot')) {
            return response()->json(['success' => false, 'message' => 'Chatbot dang bi tat.'], 403);
        }

        $rateLimit = config('gemini.rate_limits.chatbot', 5);
        $ip = $request->ip();
        if (!$this->ai->checkRateLimit("chatbot_{$ip}", $rateLimit)) {
            return response()->json([
                'success' => false,
                'message' => 'Ban gui qua nhanh. Vui long cho mot chut.',
            ], 429);
        }

        $request->validate([
            'message' => 'required|string|max:500',
            'news_id' => 'nullable|integer',
            'selected_text' => 'nullable|string|max:2000',
        ]);

        $message = trim($request->input('message'));
        $selectedText = $this->normalizePlainText((string) $request->input('selected_text', ''));
        $chatHistory = $this->buildChatHistory($request);
        $context = $this->buildContext(
            $request->input('news_id'),
            $selectedText,
            $chatHistory
        );

        try {
            $reply = $this->ai->chat($message, $context);
            $this->storeInSession($request, $message, $reply);

            return response()->json([
                'success' => true,
                'reply' => $reply,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Chat Error', ['message' => $e->getMessage(), 'provider' => $this->provider]);

            $msg = $e->getMessage();
            $status = 500;

            if (str_contains($msg, 'quota') || str_contains($msg, 'rate_limit') || str_contains($msg, 'exceeded')) {
                $status = 429;
                $msg = 'Dich vu AI tam thoi het quota. Vui long thu lai sau vai phut.';
            } elseif (str_contains($msg, 'api_key') || str_contains($msg, 'cau hinh') || str_contains($msg, 'chua duoc cau hinh')) {
                $status = 503;
                $msg = 'Dich vu AI (' . $this->provider . ') chua duoc cau hinh dung. Vui long lien he quan tri vien.';
            } elseif (str_contains($msg, 'khong the ket noi') || str_contains($msg, 'mang')) {
                $status = 503;
            } else {
                $msg = 'Co loi xay ra: ' . $msg;
            }

            return response()->json([
                'success' => false,
                'message' => $msg,
            ], $status);
        }
    }

    public function clearChat(Request $request)
    {
        if (method_exists($this->ai, 'clearHistory')) {
            $this->ai->clearHistory();
        }

        $request->session()->forget('ai_chat_history');

        return response()->json(['success' => true]);
    }

    private function buildContext(?int $newsId, string $selectedText = '', array $chatHistory = []): array
    {
        $context = [];

        if ($newsId) {
            $news = News::with(['category', 'tags'])
                ->where('RowID', $newsId)
                ->where('Status', 1)
                ->first();

            if ($news) {
                $context['title'] = $news->Name;
                $context['category'] = $news->category?->Name;
                $context['description'] = $news->SmallDescription;
                $context['tags'] = $news->getRelation('tags')->pluck('name')->toArray();

                $rawContent = $this->normalizePlainText((string) ($news->Description ?? ''));
                if (mb_strlen($rawContent) > 4000) {
                    $rawContent = mb_substr($rawContent, 0, 4000) . '...';
                }
                $context['full_content'] = $rawContent;

                $relatedNews = $news->getRelatedNews(4)
                    ->pluck('Name')
                    ->filter()
                    ->values()
                    ->toArray();

                if (!empty($relatedNews)) {
                    $context['related_news'] = implode('; ', $relatedNews);
                }

                if ($selectedText !== '') {
                    $context['selected_text'] = $selectedText;

                    $selectedExcerpt = $this->extractSelectedExcerpt($rawContent, $selectedText);
                    if ($selectedExcerpt !== '') {
                        $context['selected_excerpt'] = $selectedExcerpt;
                    }
                }
            }
        }

        $recentNews = News::where('Status', 1)
            ->orderBy('RowID', 'DESC')
            ->take(5)
            ->pluck('Name')
            ->toArray();

        if (!empty($recentNews)) {
            $context['recent_news'] = implode('; ', $recentNews);
        }

        if (!empty($chatHistory)) {
            $context['chat_history'] = $chatHistory;
        }

        if ($selectedText !== '' && empty($context['selected_text'])) {
            $context['selected_text'] = $selectedText;
        }

        return $context;
    }

    private function buildChatHistory(Request $request): array
    {
        $history = $request->session()->get('ai_chat_history', []);

        if (!is_array($history) || empty($history)) {
            return [];
        }

        $history = array_slice($history, -10);

        return array_values(array_filter(array_map(function ($item) {
            if (!is_array($item)) {
                return null;
            }

            $role = $item['role'] ?? null;
            $text = $this->normalizePlainText((string) ($item['text'] ?? ''));

            if (!in_array($role, ['user', 'assistant'], true) || $text === '') {
                return null;
            }

            return [
                'role' => $role,
                'text' => mb_substr($text, 0, 1000),
            ];
        }, $history)));
    }

    private function extractSelectedExcerpt(string $fullContent, string $selectedText, int $radius = 260): string
    {
        $fullContent = $this->normalizePlainText($fullContent);
        $selectedText = $this->normalizePlainText($selectedText);

        if ($fullContent === '' || $selectedText === '') {
            return '';
        }

        $position = mb_stripos($fullContent, $selectedText);
        if ($position === false) {
            return '';
        }

        $start = max(0, $position - $radius);
        $length = mb_strlen($selectedText) + ($radius * 2);
        $excerpt = mb_substr($fullContent, $start, $length);

        if ($start > 0) {
            $excerpt = '...' . ltrim($excerpt);
        }

        if (($start + $length) < mb_strlen($fullContent)) {
            $excerpt = rtrim($excerpt) . '...';
        }

        return $excerpt;
    }

    private function normalizePlainText(string $text): string
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    private function storeInSession(Request $request, string $userMessage, string $aiReply): void
    {
        $history = $request->session()->get('ai_chat_history', []);

        $history[] = [
            'role' => 'user',
            'text' => $userMessage,
            'time' => now()->toIso8601String(),
        ];

        $history[] = [
            'role' => 'assistant',
            'text' => $aiReply,
            'time' => now()->toIso8601String(),
        ];

        if (count($history) > 40) {
            $history = array_slice($history, -40);
        }

        $request->session()->put('ai_chat_history', $history);
    }
}
