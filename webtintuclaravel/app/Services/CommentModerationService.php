<?php

namespace App\Services;

use App\Models\NewsComment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CommentModerationService
{
    public function moderate(string $content, User $user, string $ipAddress): array
    {
        $normalized = $this->normalize($content);
        $contentHash = hash('sha256', $normalized);
        $score = 0;
        $reasons = [];

        $duplicateWindow = max(1, (int) config('comments.duplicate_window_minutes', 30));
        $duplicate = NewsComment::query()
            ->where('content_hash', $contentHash)
            ->where(function ($query) use ($user, $ipAddress) {
                $query->where('user_id', $user->id);
                if ($ipAddress !== '') {
                    $query->orWhere('ip_address', $ipAddress);
                }
            })
            ->where('created_at', '>=', now()->subMinutes($duplicateWindow))
            ->exists();

        if ($duplicate) {
            $score += 80;
            $reasons[] = 'Noi dung trung lap trong thoi gian ngan.';
        }

        $links = preg_match_all('~(?:https?://|www\.|[a-z0-9-]+\.(?:com|net|org|vn|io)\b)~iu', $content);
        $maxLinks = max(0, (int) config('comments.max_links', 2));
        if ($links > $maxLinks) {
            $score += 45 + (($links - $maxLinks) * 10);
            $reasons[] = 'Binh luan chua qua nhieu lien ket.';
        }

        foreach ((array) config('comments.blocked_terms', []) as $term) {
            $term = $this->normalize((string) $term);
            if ($term !== '' && Str::contains($normalized, $term)) {
                $score += 35;
                $reasons[] = 'Phat hien cum tu co nguy co spam.';
                break;
            }
        }

        if (preg_match('/(.)\1{7,}/u', $normalized)) {
            $score += 20;
            $reasons[] = 'Ky tu bi lap bat thuong.';
        }

        if (preg_match('/\b(?:0\d{8,10}|\+84\d{8,10})\b/u', $normalized) && $links > 0) {
            $score += 25;
            $reasons[] = 'Co dau hieu quang cao kem thong tin lien he.';
        }

        $recentUserCount = NewsComment::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->count();
        if ($recentUserCount >= max(1, (int) config('comments.rate_limit.per_hour', 20))) {
            $score += 50;
            $reasons[] = 'Tan suat binh luan trong gio qua cao.';
        }

        $ai = $this->moderateWithAi($content, $user);
        if ($ai['action'] === 'REJECT') {
            $score = max($score, 80);
            $reasons[] = $ai['reason'] ?: 'AI danh gia noi dung khong phu hop.';
        } elseif ($ai['action'] === 'FLAG') {
            $score = max($score, 35);
            $reasons[] = $ai['reason'] ?: 'AI yeu cau quan tri vien xem lai.';
        }

        $score = min(100, $score);
        $rejectScore = (int) config('comments.reject_score', 70);
        $approveScore = (int) config('comments.auto_approve_score', 19);

        if ($score >= $rejectScore) {
            $status = NewsComment::STATUS_SPAM;
        } elseif ($score > $approveScore) {
            $status = NewsComment::STATUS_PENDING;
        } else {
            $status = NewsComment::STATUS_APPROVED;
        }

        return [
            'status' => $status,
            'score' => $score,
            'reason' => implode(' ', array_values(array_unique(array_filter($reasons)))),
            'content_hash' => $contentHash,
        ];
    }

    private function moderateWithAi(string $content, User $user): array
    {
        if (!config('gemini.features.comment_moderation')) {
            return ['action' => 'APPROVE', 'reason' => ''];
        }

        try {
            $provider = strtolower((string) config('gemini.provider', 'groq'));
            if ($provider === 'gemini' && config('gemini.api_key')) {
                $service = app(GeminiAIService::class);
            } elseif (config('groq.api_key')) {
                $service = app(GroqAIService::class);
            } else {
                return ['action' => 'APPROVE', 'reason' => ''];
            }

            $result = $service->moderateComment(
                $content,
                $user->fullname ?: $user->username
            );
            $action = strtoupper((string) ($result['action'] ?? 'FLAG'));

            return [
                'action' => in_array($action, ['APPROVE', 'REJECT', 'FLAG'], true) ? $action : 'FLAG',
                'reason' => trim((string) ($result['reason'] ?? '')),
            ];
        } catch (\Throwable $exception) {
            Log::warning('Automatic comment AI moderation failed', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            return ['action' => 'APPROVE', 'reason' => ''];
        }
    }

    private function normalize(string $content): string
    {
        $content = mb_strtolower(strip_tags($content));
        $content = preg_replace('/\s+/u', ' ', $content) ?? $content;

        return trim($content);
    }
}
