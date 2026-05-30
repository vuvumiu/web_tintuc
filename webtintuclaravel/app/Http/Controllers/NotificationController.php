<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at');

        $typeMap = [
            'comment' => ['comment_new', 'comment_reply'],
            'vote'    => ['comment_upvote', 'comment_downvote', 'news_rated', 'news_favorited'],
            'news'    => ['news_approved', 'news_rejected', 'news_published', 'news_hidden', 'news_submitted', 'news_duplicated'],
        ];

        if ($request->filled('type') && $request->type !== 'all') {
            $types = $typeMap[$request->type] ?? [];
            if (!empty($types)) {
                $query->whereIn('type', $types);
            }
        }

        if ($request->boolean('unread_only')) {
            $query->where('is_read', 0);
        }

        if ($request->boolean('mark_all')) {
            Notification::query()
                ->where('user_id', Auth::id())
                ->where('is_read', 0)
                ->update([
                    'is_read' => 1,
                    'read_at' => now(),
                ]);
            NotificationService::clearUnreadCache((int) Auth::id());
        }

        $notifications = $query->paginate(20);
        $unreadCount = Notification::unreadCount((int) Auth::id());

        return view('auth.notifications', compact('notifications', 'unreadCount'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::query()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($notification) {
            $notification->markAsRead();
            NotificationService::clearUnreadCache((int) Auth::id());
        }

        return back();
    }

    public function markAllRead()
    {
        Notification::query()
            ->where('user_id', Auth::id())
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);
        NotificationService::clearUnreadCache((int) Auth::id());

        return back()->with([
            'flash_level' => 'success',
            'flash_message' => 'Đã đánh dấu tất cả thông báo là đã đọc.',
        ]);
    }

    public function delete($id)
    {
        $notification = Notification::query()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($notification) {
            $notification->delete();
            NotificationService::clearUnreadCache((int) Auth::id());
        }

        return back();
    }

    public function apiUnreadCount(): JsonResponse
    {
        $userId = (int) Auth::id();
        $notifications = Notification::query()
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return response()->json([
            'success' => true,
            'count' => Notification::unreadCount($userId),
            'notifications' => $notifications->map(fn (Notification $notification) => $this->transformNotification($notification))->values(),
        ]);
    }

    public function apiMarkRead(Request $request): JsonResponse
    {
        $notification = Notification::query()
            ->where('id', $request->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo.',
            ], 404);
        }

        $notification->markAsRead();
        NotificationService::clearUnreadCache((int) Auth::id());

        return response()->json(['success' => true]);
    }

    public function apiMarkAllRead(): JsonResponse
    {
        Notification::query()
            ->where('user_id', Auth::id())
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);
        NotificationService::clearUnreadCache((int) Auth::id());

        return response()->json([
            'success' => true,
            'count' => 0,
        ]);
    }

    public function apiDelete($id): JsonResponse
    {
        $notification = Notification::query()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo.',
            ], 404);
        }

        $notification->delete();
        NotificationService::clearUnreadCache((int) Auth::id());

        return response()->json([
            'success' => true,
            'count' => Notification::unreadCount((int) Auth::id()),
        ]);
    }

    public function apiList(): JsonResponse
    {
        $notifications = Notification::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'count' => Notification::unreadCount((int) Auth::id()),
            'notifications' => $notifications->map(fn (Notification $notification) => $this->transformNotification($notification))->values(),
        ]);
    }

    private function transformNotification(Notification $notification): array
    {
        $color = Notification::typeColor((string) $notification->type);

        if ($color === 'primary') {
            $color = 'info';
        } elseif ($color === 'secondary') {
            $color = 'default';
        }

        return [
            'id' => (int) $notification->id,
            'title' => (string) $notification->title,
            'content' => (string) ($notification->content ?? ''),
            'link' => $notification->link ?: url('/thong-bao'),
            'is_read' => (int) $notification->is_read,
            'icon' => Notification::typeIcon((string) $notification->type),
            'color' => $color,
            'time' => optional($notification->created_at)->diffForHumans() ?? '',
        ];
    }
}
