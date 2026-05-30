<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use App\Models\User;
use App\Support\UsersTableSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function getRegister()
    {
        return view('auth.register');
    }

    public function postRegister(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'username.required' => 'Tên đăng nhập không được để trống.',
            'username.unique' => 'Tên đăng nhập đã tồn tại.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $data = [
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ];

        if (UsersTableSchema::hasIsAdminAccountColumn()) {
            $data['is_admin_account'] = 0;
        }

        if (UsersTableSchema::hasIsActiveColumn()) {
            $data['is_active'] = 1;
        }

        User::create($data);

        return redirect('/dang-nhap')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
    }

    public function getLogin()
    {
        return view('auth.login');
    }

    public function postLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Tên đăng nhập không được để trống.',
            'password.required' => 'Mật khẩu không được để trống.',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->isAccountActive()) {
                Auth::logout();

                return back()
                    ->withErrors(['username' => 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.'])
                    ->onlyInput('username');
            }

            $request->session()->regenerate();

            if (in_array((int) $user->level, [1, 2], true)) {
                return redirect()->intended('/admin/home');
            }

            return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
        }

        return back()
            ->withErrors(['username' => 'Tên đăng nhập hoặc mật khẩu không chính xác.'])
            ->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/dang-nhap')->with('success', 'Đã đăng xuất.');
    }

    public function myAccount()
    {
        $user = Auth::user();

        return view('auth.my-account', compact('user'));
    }

    public function notificationSettings()
    {
        $user = Auth::user();
        $preferences = NotificationPreference::forUser((int) $user->id);

        return view('auth.notification-settings', compact('user', 'preferences'));
    }

    public function notificationSettingsPost(Request $request)
    {
        $user = Auth::user();
        $preferences = NotificationPreference::forUser((int) $user->id);
        $fields = [
            'notify_comment_new',
            'notify_comment_reply',
            'notify_comment_upvote',
            'notify_comment_downvote',
            'notify_news_rated',
            'notify_news_favorited',
            'notify_news_approved',
            'notify_news_rejected',
            'notify_system',
        ];

        foreach ($fields as $field) {
            $preferences->{$field} = $request->boolean($field);
        }

        $preferences->save();

        return redirect()->back()->with('success', 'Đã cập nhật cài đặt thông báo.');
    }

    public function myAccountPost(Request $request)
    {
        $request->validate([
            'fullname' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.email' => 'Email khong hop le.',
            'email.unique' => 'Email nay da duoc su dung.',
            'avatar.image' => 'File phai la hinh anh.',
            'avatar.max' => 'Kich thuoc anh khong qua 2MB.',
        ]);

        $user = Auth::user();
        $user->fullname = $request->fullname ?? $user->fullname;
        $user->email = $request->email ?? $user->email;
        $user->phone = $request->phone ?? $user->phone;
        $user->address = $request->address ?? $user->address;

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $path = public_path('images/users/');

            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            if ($user->avatar && file_exists($path . $user->avatar)) {
                @unlink($path . $user->avatar);
            }

            $file->move($path, $filename);
            $user->avatar = $filename;
        }

        $user->save();

        return back()->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Mật khẩu hiện tại không được để trống.',
            'new_password.required' => 'Mật khẩu mới không được để trống.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('pw_error', 'Mật khẩu hiện tại không chính xác.')->withInput();
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('pw_success', 'Đổi mật khẩu thành công!');
    }
}
