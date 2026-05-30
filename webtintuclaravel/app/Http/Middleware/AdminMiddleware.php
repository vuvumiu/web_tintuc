<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Chỉ cho phép admin (level=1) hoặc staff (level=2) truy cập.
     * Tài khoản người dùng thường (level=3 hoặc NULL) sẽ bị từ chối.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            return redirect('/admin/login')->with('notice', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $allowedLevels = [1, 2]; // 1 = Admin, 2 = Staff

        if (!in_array((int) $user->level, $allowedLevels, true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            Auth::logout();
            return redirect('/admin/login')->with('notice', 'Bạn không có quyền truy cập trang quản trị.');
        }

        // Chặn tài khoản bị vô hiệu hóa (không dùng == 0 vì null == 0 trong PHP)
        if (!$user->isAccountActive()) {
            Auth::logout();
            return redirect('/admin/login')->with('notice', 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.');
        }

        return $next($request);
    }
}
