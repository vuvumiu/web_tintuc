<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận đăng ký nhận tin khuyến mại</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .email-wrapper { background: #fff; border-radius: 8px; max-width: 600px; margin: 0 auto; padding: 30px; }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #eee; margin-bottom: 20px; }
        .header h1 { color: #28a745; margin: 0; font-size: 24px; }
        .header p { color: #666; margin: 5px 0 0; }
        .content { line-height: 1.8; color: #333; }
        .content p { margin: 15px 0; }
        .btn { display: inline-block; background: #28a745; color: #fff !important; padding: 14px 35px; border-radius: 5px; text-decoration: none; font-size: 16px; margin-top: 10px; }
        .btn:hover { background: #218838; }
        .info-box { background: #f0fff4; border-left: 4px solid #28a745; padding: 15px; border-radius: 0 5px 5px 0; margin: 20px 0; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 0 5px 5px 0; margin: 20px 0; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #999; font-size: 13px; }
        .unsubscribe-link { color: #999; font-size: 12px; }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="header">
        <h1>✅ Xác nhận đăng ký nhận tin</h1>
        <p>Bạn đã đăng ký nhận tin khuyến mại với email: <strong>{{ $email }}</strong></p>
    </div>

    <div class="content">
        <p>Xin chào!</p>
        <p>Cảm ơn bạn đã đăng ký nhận tin khuyến mại từ website của chúng tôi.</p>

        <div class="info-box">
            <p>Để hoàn tất đăng ký, vui lòng nhấn vào nút bên dưới:</p>
        </div>

        <p style="text-align: center;">
            <a href="{{ $confirmUrl }}" class="btn">Xác nhận đăng ký</a>
        </p>

        <p style="text-align: center; margin-top: 20px; font-size: 13px; color: #666;">
            Hoặc sao chép và dán link sau vào trình duyệt:<br>
            <a href="{{ $confirmUrl }}" style="word-break: break-all;">{{ $confirmUrl }}</a>
        </p>

        <div class="warning">
            <p><strong>⚠️ Lưu ý:</strong></p>
            <p>• Link xác nhận chỉ có hiệu lực trong <strong>24 giờ</strong>.<br>
               • Nếu bạn không thực hiện đăng ký này, vui lòng bỏ qua email này.</p>
        </div>

        <p>Sau khi xác nhận, bạn sẽ nhận được các thông tin khuyến mại, tin tức mới nhất và ưu đãi đặc biệt từ chúng tôi.</p>

        <p>Trân trọng,<br><strong>Đội ngũ {{ config('app.name') }}</strong></p>
    </div>

    <div class="footer">
        <p>Email này được gửi tự động. Vui lòng không trả lời trực tiếp email này.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tất cả các quyền được bảo lưu.</p>
        <p class="unsubscribe-link">
            Không muốn nhận email? Bạn có thể <a href="{{ route('newsletter.unsubscribe', ['email' => $email]) }}">hủy đăng ký</a> bất cứ lúc nào.
        </p>
    </div>
</div>
</body>
</html>
