<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsComment;
use App\Models\Tag;
use App\Services\GroqAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    private GroqAIService $ai;

    public function __construct(GroqAIService $ai)
    {
        $this->ai = $ai;
    }

    public function generateMeta(Request $request)
    {
        if (!config('gemini.features.meta_tags')) {
            return response()->json(['success' => false, 'message' => 'Tính năng tạo Meta bằng AI đang bị tắt.'], 403);
        }

        $rateLimit = config('gemini.rate_limits.admin', 10);
        $key = 'admin_meta_' . ($request->user()?->id ?? $request->ip());
        if (!$this->ai->checkRateLimit($key, $rateLimit)) {
            return response()->json(['success' => false, 'message' => 'Đã đạt giới hạn. Vui lòng chờ 1 phút.'], 429);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
        ]);

        try {
            $result = $this->ai->generateMetaTags(
                $request->input('title'),
                $request->input('description') ?? '',
                $request->input('content') ?? ''
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('AI Meta Generation Error', ['message' => $e->getMessage(), 'provider' => 'groq']);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function suggestTags(Request $request)
    {
        if (!config('gemini.features.smart_tags')) {
            return response()->json(['success' => false, 'message' => 'Tính năng gợi ý Tags bằng AI đang bị tắt.'], 403);
        }

        $rateLimit = config('gemini.rate_limits.admin', 10);
        $key = 'admin_tags_' . ($request->user()?->id ?? $request->ip());
        if (!$this->ai->checkRateLimit($key, $rateLimit)) {
            return response()->json(['success' => false, 'message' => 'Đã đạt giới hạn. Vui lòng chờ 1 phút.'], 429);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:100',
        ]);

        try {
            $suggested = $this->ai->suggestTags(
                $request->input('title'),
                $request->input('content') ?? '',
                $request->input('category') ?? ''
            );

            $suggested = collect($suggested)
                ->filter(fn ($tag) => is_array($tag) && !empty($tag['name']))
                ->map(function ($tag) {
                    $tag['slug'] = \Illuminate\Support\Str::slug($tag['slug'] ?? $tag['name'], '-');
                    return $tag;
                })
                ->values()
                ->all();

            $existingSlugs = Tag::whereIn('slug', collect($suggested)->pluck('slug'))->pluck('slug')->toArray();
            foreach ($suggested as &$tag) {
                $tag['exists'] = in_array($tag['slug'], $existingSlugs, true);
            }

            return response()->json([
                'success' => true,
                'data' => $suggested,
            ]);
        } catch (\Throwable $e) {
            Log::error('AI Tag Suggestion Error', ['message' => $e->getMessage(), 'provider' => 'groq']);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function moderateComment(Request $request)
    {
        if (!config('gemini.features.comment_moderation')) {
            return response()->json(['success' => false, 'message' => 'Tính năng kiểm duyệt bình luận bằng AI đang bị tắt.'], 403);
        }

        $rateLimit = config('gemini.rate_limits.admin', 10);
        $key = 'admin_mod_' . ($request->user()?->id ?? $request->ip());
        if (!$this->ai->checkRateLimit($key, $rateLimit)) {
            return response()->json(['success' => false, 'message' => 'Đã đạt giới hạn. Vui lòng chờ 1 phút.'], 429);
        }

        $request->validate([
            'comment_id' => 'required|integer|exists:news_comments,id',
        ]);

        try {
            $comment = NewsComment::with('user')->findOrFail($request->comment_id);

            $result = $this->ai->moderateComment(
                $comment->content,
                $comment->user?->fullname ?? $comment->user?->username ?? 'Người dùng'
            );

            $action = strtoupper($result['action'] ?? 'FLAG');
            if (!in_array($action, ['APPROVE', 'REJECT', 'FLAG'], true)) {
                $action = 'FLAG';
            }

            if ($action === 'REJECT') {
                $comment->reject(
                    $request->user()?->id,
                    (string) ($result['reason'] ?? 'AI từ chối bình luận.'),
                    true
                );
            } elseif ($action === 'APPROVE') {
                $comment->approve(
                    $request->user()?->id,
                    (string) ($result['reason'] ?? 'AI đã duyệt bình luận.')
                );
            } else {
                $comment->forceFill([
                    'moderation_status' => NewsComment::STATUS_PENDING,
                    'moderation_reason' => (string) ($result['reason'] ?? 'AI yêu cầu xem lại.'),
                    'is_active' => false,
                    'moderated_by' => $request->user()?->id,
                    'moderated_at' => now(),
                ])->save();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'action' => $action,
                    'reason' => $result['reason'] ?? '',
                    'confidence' => (float) ($result['confidence'] ?? 0),
                    'comment_active' => (bool) $comment->is_active,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('AI Comment Moderation Error', ['message' => $e->getMessage(), 'provider' => 'groq']);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function moderateCommentBulk(Request $request)
    {
        if (!config('gemini.features.comment_moderation')) {
            return response()->json(['success' => false, 'message' => 'Tính năng kiểm duyệt bình luận bằng AI đang bị tắt.'], 403);
        }

        $rateLimit = config('gemini.rate_limits.admin', 10);
        $key = 'admin_mod_bulk_' . ($request->user()?->id ?? $request->ip());
        if (!$this->ai->checkRateLimit($key, $rateLimit)) {
            return response()->json(['success' => false, 'message' => 'Đã đạt giới hạn. Vui lòng chờ 1 phút.'], 429);
        }

        $request->validate([
            'comment_ids' => 'required|string',
        ]);

        $ids = array_filter(array_map('intval', explode(',', $request->comment_ids)));
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Không có bình luận nào được chọn.'], 400);
        }

        $results = [];
        $processed = 0;

        foreach ($ids as $id) {
            if ($processed >= 5) {
                break;
            }

            try {
                $comment = NewsComment::with('user')->find($id);
                if (!$comment) {
                    continue;
                }

                $result = $this->ai->moderateComment(
                    $comment->content,
                    $comment->user?->fullname ?? $comment->user?->username ?? 'Người dùng'
                );

                $action = strtoupper($result['action'] ?? 'FLAG');
                if (!in_array($action, ['APPROVE', 'REJECT', 'FLAG'], true)) {
                    $action = 'FLAG';
                }

                if ($action === 'REJECT') {
                    $comment->reject(
                        $request->user()?->id,
                        (string) ($result['reason'] ?? 'AI từ chối bình luận.'),
                        true
                    );
                } elseif ($action === 'APPROVE') {
                    $comment->approve(
                        $request->user()?->id,
                        (string) ($result['reason'] ?? 'AI đã duyệt bình luận.')
                    );
                } else {
                    $comment->forceFill([
                        'moderation_status' => NewsComment::STATUS_PENDING,
                        'moderation_reason' => (string) ($result['reason'] ?? 'AI yêu cầu xem lại.'),
                        'is_active' => false,
                        'moderated_by' => $request->user()?->id,
                        'moderated_at' => now(),
                    ])->save();
                }

                $results[] = [
                    'comment_id' => $id,
                    'action' => $action,
                    'reason' => $result['reason'] ?? '',
                ];

                $processed++;
            } catch (\Throwable $e) {
                Log::warning('Bulk moderation failed for comment', ['id' => $id, 'error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'message' => "Đã kiểm duyệt {$processed} bình luận.",
        ]);
    }

    public function dashboard()
    {
        $isConfigured = $this->ai->isConfigured();
        $features = config('gemini.features', []);
        $providerName = 'Groq (Llama)';

        return view('back.ai.dashboard', compact('isConfigured', 'features', 'providerName'));
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'api_key' => 'nullable|string|max:300',
                'model' => 'nullable|string|max:100',
                'meta_tags' => 'nullable|in:on,off',
                'smart_tags' => 'nullable|in:on,off',
                'comment_moderation' => 'nullable|in:on,off',
                'chatbot' => 'nullable|in:on,off',
            ]);

            $envPath = base_path('.env');
            $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';

            $envContent = $this->updateEnvValue($envContent, 'AI_PROVIDER', 'groq');
            $envContent = $this->updateEnvValue($envContent, 'GROQ_API_KEY', $validated['api_key'] ?? '');
            $envContent = $this->updateEnvValue($envContent, 'GROQ_MODEL', $validated['model'] ?? 'llama-3.3-70b-versatile');
            $envContent = $this->updateEnvValue($envContent, 'AI_META_TAGS', ($validated['meta_tags'] ?? null) === 'on' ? 'true' : 'false');
            $envContent = $this->updateEnvValue($envContent, 'AI_SMART_TAGS', ($validated['smart_tags'] ?? null) === 'on' ? 'true' : 'false');
            $envContent = $this->updateEnvValue($envContent, 'AI_COMMENT_MODERATION', ($validated['comment_moderation'] ?? null) === 'on' ? 'true' : 'false');
            $envContent = $this->updateEnvValue($envContent, 'AI_CHATBOT', ($validated['chatbot'] ?? null) === 'on' ? 'true' : 'false');

            file_put_contents($envPath, $envContent);

            Artisan::call('config:clear');
            Cache::flush();

            return redirect()->back()->with('success', 'Cài đặt AI đã được lưu.');
        }

        return view('back.ai.settings');
    }

    private function updateEnvValue(string $content, string $key, ?string $value): string
    {
        $pattern = "/^{$key}=.*/m";
        $entry = "{$key}={$value}";

        if (preg_match($pattern, $content)) {
            return preg_replace($pattern, $entry, $content);
        }

        return rtrim($content) . "\n" . $entry . "\n";
    }
}
