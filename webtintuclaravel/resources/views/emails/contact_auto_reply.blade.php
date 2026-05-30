<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chúng tôi đã nhận được liên hệ của bạn</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .email-wrapper { background: #fff; border-radius: 8px; max-width: 600px; margin: 0 auto; padding: 30px; }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #eee; margin-bottom: 20px; }
        .header h1 { color: #007bff; margin: 0; font-size: 24px; }
        .header p { color: #666; margin: 5px 0 0; }
        .content { line-height: 1.8; color: #333; }
        .content p { margin: 15px 0; }
        .info-box { background: #f0f8ff; border-left: 4px solid #007bff; padding: 15px; border-radius: 0 5px 5px 0; margin: 20px 0; }
        .info-box p { margin: 5px 0; }
        .btn { display: inline-block; background: #007bff; color: #fff !important; padding: 12px 30px; border-radius: 5px; text-decoration: none; margin-top: 20px; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #999; font-size: 13px; }
        .social-links { margin: 15px 0; }
        .social-links a { margin: 0 8px; text-decoration: none; }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="header">
        <h1>Xin chào, {{ $contact['Name'] }}!</h1>
        <p>Cảm ơn bạn đã liên hệ với chúng tôi</p>
    </div>

    <div class="content">
        <p>Chúng tôi đã nhận được liên hệ của bạn vào lúc <strong>{{ now()->format('H:i d/m/Y') }}</strong>.</p>

        <div class="info-box">
            <p><strong>📋 Nội dung bạn đã gửi:</strong></p>
            @if(!empty($contact['subject']))
            <p><strong>Tiêu đề:</strong> {{ $contact['subject'] }}</p>
            @endif
            <p><strong>Lời nhắn:</strong></p>
            <p style="white-space: pre-wrap; background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ddd;">
                {{ $contact['Message'] }}
            </p>
        </div>

        <p>Nhân viên của chúng tôi sẽ phản hồi bạn trong vòng <strong>24 giờ</strong> làm việc.</p>

        <p>Trong thời gian chờ đợi, bạn có thể:</p>
        <p>&nbsp;&nbsp;• Theo dõi các tin tức mới nhất trên website của chúng tôi<br>
           &nbsp;&nbsp;• Liên hệ trực tiếp qua hotline nếu cần hỗ trợ khẩn cấp</p>

        <p>Trân trọng,<br><strong>Đội ngũ hỗ trợ</strong></p>
    </div>

    <div class="footer">
        <p>Email này được gửi tự động. Vui lòng không trả lời trực tiếp email này.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tất cả các quyền được bảo lưu.</p>
    </div>
</div>
</body>
</html>
