<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'ml' => [
        'endpoint' => env('ML_SERVICE_ENDPOINT', 'http://localhost:5000'),
        'api_key' => env('ML_SERVICE_API_KEY'),
        'timeout' => env('ML_SERVICE_TIMEOUT', 30),
        'retry_attempts' => env('ML_SERVICE_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('ML_SERVICE_RETRY_DELAY', 1),
        'price_constraints' => [
            'min_price' => env('ML_MIN_PRICE', 500000),    // Minimum 500k IDR
            'max_price' => env('ML_MAX_PRICE', 100000000), // Maximum 100M IDR
            'outlier_threshold' => env('ML_OUTLIER_THRESHOLD', 0.95), // 95th percentile
        ],
    ],

];
