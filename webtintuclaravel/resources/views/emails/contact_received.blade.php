<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liên hệ mới từ website</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .email-wrapper { background: #fff; border-radius: 8px; max-width: 600px; margin: 0 auto; padding: 30px; }
        h2 { color: #e74c3c; margin-top: 0; }
        .field { margin-bottom: 15px; padding: 10px; background: #f9f9f9; border-radius: 5px; }
        .field-label { font-weight: bold; color: #555; font-size: 13px; text-transform: uppercase; }
        .field-value { margin-top: 5px; color: #333; font-size: 15px; }
        .category-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .bg-primary { background: #007bff; color: #fff; }
        .bg-danger { background: #dc3545; color: #fff; }
        .bg-success { background: #28a745; color: #fff; }
        .bg-secondary { background: #6c757d; color: #fff; }
        .footer { margin-top: 20px; font-size: 12px; color: #999; text-align: center; }
    </style>
</head>
<body>
<div class="email-wrapper">
    <h2>📩 Liên hệ mới từ website</h2>

    <div class="field">
        <div class="field-label">Họ và tên</div>
        <div class="field-value">{{ $contact['Name'] }}</div>
    </div>

    @if(!empty($contact['subject']))
    <div class="field">
        <div class="field-label">Tiêu đề</div>
        <div class="field-value">{{ $contact['subject'] }}</div>
    </div>
    @endif

    @if(!empty($contact['category']))
    <div class="field">
        <div class="field-label">Phân loại</div>
        <div class="field-value">
            <span class="category-badge bg-{{ $contact['category_color'] ?? 'secondary' }}">
                {{ $contact['category_label'] ?? $contact['category'] }}
            </span>
        </div>
    </div>
    @endif

    @if(!empty($contact['priority']))
    <div class="field">
        <div class="field-label">Mức ưu tiên</div>
        <div class="field-value">
            <span class="category-badge bg-{{ $contact['priority_color'] ?? 'secondary' }}">
                {{ $contact['priority_label'] ?? $contact['priority'] }}
            </span>
        </div>
    </div>
    @endif

    <div class="field">
        <div class="field-label">Email</div>
        <div class="field-value"><a href="mailto:{{ $contact['Email'] }}">{{ $contact['Email'] }}</a></div>
    </div>

    @if(!empty($contact['Phone']))
    <div class="field">
        <div class="field-label">Số điện thoại</div>
        <div class="field-value"><a href="tel:{{ $contact['Phone'] }}">{{ $contact['Phone'] }}</a></div>
    </div>
    @endif

    <div class="field">
        <div class="field-label">Lời nhắn</div>
        <div class="field-value" style="white-space: pre-wrap;">{{ $contact['Message'] }}</div>
    </div>

    @if(!empty($contact['ip_address']))
    <div class="field">
        <div class="field-label">IP người gửi</div>
        <div class="field-value">{{ $contact['ip_address'] }}</div>
    </div>
    @endif

    <div class="footer">
        Email này được gửi tự động từ website. Vui lòng không trả lời trực tiếp email này.
    </div>
</div>
</body>
</html>
