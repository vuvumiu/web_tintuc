<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email'    => 'Email không hợp lệ.',
            'email.exists'   => 'Email này không tồn tại trong hệ thống.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->isAccountActive()) {
            return back()->withErrors(['email' => 'Tài khoản không hợp lệ hoặc đã bị vô hiệu hóa.']);
        }

        $token = \Str::random(64);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token'      => Hash::make($token),
                'token_raw'  => $token,
                'created_at' => Carbon::now(),
            ]
        );

        $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($request->email));

        try {
            Mail::to($request->email)->send(new ResetPasswordMail($user, $resetUrl, $token));
        } catch (\Throwable $e) {
            Log::error('Lỗi gửi email reset password: ' . $e->getMessage());
        }

        return back()->with('status', 'Chúng tôi đã gửi email đặt lại mật khẩu. Vui lòng kiểm tra hộp thư của bạn.');
    }

    public function showResetForm(Request $request, $token)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không hợp lệ.',
            'email.exists'   => 'Email không tồn tại.',
        ]);

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!$record) {
            return redirect('/forgot-password')->withErrors(['email' => 'Token không hợp lệ.']);
        }

        $isValid = Hash::check($token, $record->token);

        if (!$isValid) {
            return redirect('/forgot-password')->withErrors(['email' => 'Token đã hết hạn hoặc không hợp lệ.']);
        }

        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return redirect('/forgot-password')->withErrors(['email' => 'Link đặt lại mật khẩu đã hết hạn (60 phút).']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required'     => 'Vui lòng nhập mật khẩu mới.',
            'password.min'         => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed'   => 'Xác nhận mật khẩu không khớp.',
        ]);

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Token không hợp lệ.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect('/dang-nhap')->with('success', 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập.');
    }
}
