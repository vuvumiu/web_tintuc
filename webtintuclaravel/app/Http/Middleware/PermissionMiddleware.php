<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string ...$permissions)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/admin/login')->with('notice', 'Vui lòng đăng nhập để tiếp tục.');
        }

        if (!$user->isAdminStaffAccount() || !$user->isAccountActive()) {
            return redirect('/admin/login')->with('notice', 'Bạn không có quyền truy cập.');
        }

        foreach ($permissions as $permission) {
            $perms = explode('|', $permission);
            foreach ($perms as $p) {
                if ($user->hasPermission(trim($p))) {
                    return $next($request);
                }
            }
        }

        return redirect('admin/home')->with([
            'flash_level'   => 'danger',
            'flash_message' => 'Bạn không có quyền thực hiện thao tác này.',
        ]);
    }
}
