<?php

return [
    'rate_limit' => [
        'per_minute' => (int) env('COMMENT_RATE_PER_MINUTE', 3),
        'per_hour' => (int) env('COMMENT_RATE_PER_HOUR', 20),
    ],

    'duplicate_window_minutes' => (int) env('COMMENT_DUPLICATE_WINDOW', 30),
    'max_links' => (int) env('COMMENT_MAX_LINKS', 2),
    'auto_approve_score' => (int) env('COMMENT_AUTO_APPROVE_SCORE', 19),
    'reject_score' => (int) env('COMMENT_REJECT_SCORE', 70),

    'blocked_terms' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env(
            'COMMENT_BLOCKED_TERMS',
            'casino,ca cuoc,nhan thuong,kiem tien nhanh,vay nong,telegram,zalo.me'
        ))
    ))),
];
