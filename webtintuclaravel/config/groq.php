<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Groq API Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình kết nối đến Groq API (OpenAI-compatible).
    | Đăng ký và lấy API Key miễn phí tại: https://console.groq.com/keys
    |
    | Free tier: 30 requests/minute, rate limits thoải mái hơn Gemini
    |
    */

    'api_key' => env('GROQ_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Model Configuration
    |--------------------------------------------------------------------------
    |
    | Model mặc định. Các model miễn phí:
    | - llama-3.3-70b-versatile (推荐 - nhanh nhất)
    | - llama-3.1-8b-instant
    | - qwen-3-8b-chat
    |
    */

    'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),

    /*
    |--------------------------------------------------------------------------
    | Timeout Configuration
    |--------------------------------------------------------------------------
    */

    'timeout' => (int) env('GROQ_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    */

    'max_retries' => 3,
];
