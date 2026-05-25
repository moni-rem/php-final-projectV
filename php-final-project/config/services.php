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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'bakong_khqr' => [
        'provider' => env('BAKONG_KHQR_PROVIDER', 'fluid'),
        'base_url' => env('BAKONG_KHQR_BASE_URL'),
        'account_id' => env('BAKONG_KHQR_ACCOUNT_ID'),
        'merchant_name' => env('BAKONG_KHQR_MERCHANT_NAME', env('APP_NAME', 'Laravel')),
        'merchant_city' => env('BAKONG_KHQR_MERCHANT_CITY', 'PHNOM PENH'),
        'token' => env('BAKONG_KHQR_TOKEN'),
        'test_mode' => env('BAKONG_KHQR_TEST_MODE', false),
        'static_image' => env('BAKONG_KHQR_STATIC_IMAGE'),
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

];
