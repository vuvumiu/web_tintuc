<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Phản hồi liên hệ</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .email-wrapper { background: #fff; border-radius: 8px; max-width: 640px; margin: 0 auto; padding: 30px; }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #eee; margin-bottom: 20px; }
        .header h1 { color: #007bff; margin: 0; font-size: 24px; }
        .header p { color: #666; margin: 5px 0 0; }
        .content { line-height: 1.8; color: #333; }
        .content p { margin: 15px 0; }
        .original-box { background: #f8f9fa; border-left: 4px solid #6c757d; padding: 15px; border-radius: 0 5px 5px 0; margin: 20px 0; }
        .original-box p { margin: 5px 0; }
        .original-label { font-weight: bold; color: #555; font-size: 13px; text-transform: uppercase; }
        .reply-box { background: #f0f8ff; border-left: 4px solid #007bff; padding: 15px; border-radius: 0 5px 5px 0; margin: 20px 0; }
        .reply-box p { margin: 5px 0; white-space: pre-wrap; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #999; font-size: 13px; }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="header">
        <h1>Phản hồi từ đội ngũ hỗ trợ</h1>
        <p>Xin chào {{ $reply['contact_name'] }}, chúng tôi đã phản hồi liên hệ của bạn.</p>
    </div>

    <div class="content">
        @if(!empty($reply['intro']))
        <p>{{ $reply['intro'] }}</p>
        @endif

        @if(!empty($reply['original_subject']) || !empty($reply['original_message']))
        <div class="original-box">
            <p class="original-label">📩 Tin nhắn gốc của bạn:</p>
            @if(!empty($reply['original_subject']))
            <p><strong>Tiêu đề:</strong> {{ $reply['original_subject'] }}</p>
            @endif
            @if(!empty($reply['original_message']))
            <p><strong>Nội dung:</strong></p>
            <p>{{ $reply['original_message'] }}</p>
            @endif
            @if(!empty($reply['original_date']))
            <p><em>Ngày gửi: {{ $reply['original_date'] }}</em></p>
            @endif
        </div>
        @endif

        @if(!empty($reply['reply_content']))
        <div class="reply-box">
            <p class="original-label" style="color: #007bff;">📋 Phản hồi từ chúng tôi:</p>
            <p>{{ $reply['reply_content'] }}</p>
        </div>
        @endif

        @if(!empty($reply['outro']))
        <p>{{ $reply['outro'] }}</p>
        @endif

        <p>Trân trọng,<br><strong>{{ $reply['staff_name'] ?? 'Đội ngũ hỗ trợ' }}</strong></p>
    </div>

    <div class="footer">
        <p>Email này được gửi tự động. Vui lòng không trả lời trực tiếp email này.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tất cả các quyền được bảo lưu.</p>
    </div>
</div>
</body>
</html>
