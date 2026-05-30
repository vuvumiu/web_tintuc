<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        .email-wrapper { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .email-header { background: #343a40; color: #fff; padding: 24px 32px; }
        .email-header h1 { margin: 0; font-size: 1.4rem; }
        .email-body { padding: 32px; }
        .email-body p { color: #444; line-height: 1.7; margin: 0 0 16px; }
        .email-body .highlight { color: #333; font-weight: 600; }
        .btn-reset { display: inline-block; background: #007bff; color: #fff !important; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-weight: 600; margin: 16px 0 24px; }
        .btn-reset:hover { background: #0056b3; }
        .warning-box { background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 16px; margin: 20px 0; font-size: .9rem; color: #856404; }
        .email-footer { background: #f8f9fa; padding: 20px 32px; border-top: 1px solid #dee2e6; font-size: .85rem; color: #888; text-align: center; }
        .divider { border: none; border-top: 1px solid #eee; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>🔑 Yêu cầu đặt lại mật khẩu</h1>
        </div>
        <div class="email-body">
            <p>Xin chào <strong>{{ $userName }}</strong>,</p>
            <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản <span class="highlight">{{ $user->email }}</span> của bạn.</p>
            <p>Nhấn nút bên dưới để đặt lại mật khẩu mới:</p>
            <a href="{{ $resetUrl }}" class="btn-reset">Đặt lại mật khẩu</a>
            <p>Hoặc sao chép và dán đường dẫn sau vào trình duyệt:</p>
            <p style="word-break: break-all; font-size: .85rem; color: #666;">{{ $resetUrl }}</p>
            <div class="warning-box">
                ⚠️ <strong>Lưu ý bảo mật:</strong><br>
                • Link này có hiệu lực trong <strong>60 phút</strong>.<br>
                • Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.<br>
                • Không chia sẻ link này cho bất kỳ ai.
            </div>
            <hr class="divider">
            <p style="font-size: .85rem; color: #888; margin-bottom: 0;">
                Email được gửi tự động từ hệ thống {{ config('app.name') }}.<br>
                Vui lòng không trả lời email này.
            </p>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Tất cả các quyền được bảo lưu.
        </div>
    </div>
</body>
</html>
