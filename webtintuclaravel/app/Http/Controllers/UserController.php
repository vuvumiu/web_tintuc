<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getLogin()
    {
        return view('back.login');
    }

    public function postLogin(Request $request)
    {
        if ($request->username == '' || $request->password == '') {
            return redirect('/admin/login')->with('notice', 'Tài khoản hoặc mật khẩu không được để trống.');
        }

        // Lấy user trước để kiểm tra trạng thái
        $user = \App\Models\User::where('username', $request->username)->first();

        if ($user && !$user->isAccountActive()) {
            return redirect('/admin/login')->with('notice', 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.');
        }

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();

            // Chuyển hướng tài khoản thường ra ngoài admin
            $user = Auth::user();
            if (!in_array((int) $user->level, [1, 2], true)) {
                Auth::logout();
                return redirect('/admin/login')->with('notice', 'Tài khoản khách chỉ đăng nhập được ở trang người dùng (/dang-nhap), không dùng trang quản trị.');
            }

            return redirect('/admin/home');
        } else {
            return redirect('/admin/login')->with('notice', 'Tài khoản và mật khẩu không chính xác.');
        }
    }

    public function getLogout()
    {
        Auth::logout();
        return redirect('/admin/login');
    }
}
