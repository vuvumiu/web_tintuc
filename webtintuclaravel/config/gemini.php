<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gemini API Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình kết nối đến Google Gemini API.
    | Đăng ký API Key miễn phí tại: https://aistudio.google.com/
    |
    | Free tier: 15 requests/minute, 1500 requests/day (gemini-2.0-flash)
    |
    */

    'api_key' => env('GEMINI_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Model Configuration
    |--------------------------------------------------------------------------
    |
    | Model mặc định sử dụng. Gemini 2.0 Flash là model nhanh và miễn phí.
    | Các model khác có thể dùng: gemini-1.5-flash, gemini-1.5-pro
    |
    */

    'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),

    /*
    |--------------------------------------------------------------------------
    | API Endpoint
    |--------------------------------------------------------------------------
    */

    'api_endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models',

    /*
    |--------------------------------------------------------------------------
    | Timeout Configuration
    |--------------------------------------------------------------------------
    |
    | Thời gian chờ tối đa cho mỗi request (giây).
    |
    */

    'timeout' => (int) env('GEMINI_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Số lần thử lại khi request thất bại.
    |
    */

    'max_retries' => 3,

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Giới hạn số request trên mỗi phút cho từng loại tính năng.
    |
    */

    'rate_limits' => [
        'admin' => 10,   // requests per minute for admin features
        'chatbot' => 5,   // requests per minute for public chatbot
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Toggles
    |--------------------------------------------------------------------------
    |
    | Bật/tắt từng tính năng AI. Đặt false để tắt.
    |
    */

    'features' => [
        'meta_tags' => env('AI_META_TAGS', true),
        'smart_tags' => env('AI_SMART_TAGS', true),
        'comment_moderation' => env('AI_COMMENT_MODERATION', true),
        'chatbot' => env('AI_CHATBOT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Provider Selection
    |--------------------------------------------------------------------------
    |
    | Chọn nhà cung cấp AI: 'gemini' hoặc 'groq'
    |
    | - gemini: Google Gemini (miễn phí, 15 req/phút, 1500 req/ngày)
    | - groq: Groq (miễn phí, 30 req/phút, nhanh hơn)
    |
    */

    'provider' => env('AI_PROVIDER', 'groq'),
];
