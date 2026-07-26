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

    'bunny_stream' => [
        'library_id' => env('BUNNY_STREAM_LIBRARY_ID'),
        'api_key' => env('BUNNY_STREAM_API_KEY'),
        'embed_token_key' => env('BUNNY_STREAM_EMBED_TOKEN_KEY', env('BUNNY_STREAM_API_KEY')),
        'debug' => (bool) env('BUNNY_STREAM_DEBUG', false),
        'embed_token_authentication_enabled' => (bool) env('BUNNY_STREAM_EMBED_TOKEN_AUTHENTICATION_ENABLED', true),
        'base_url' => env('BUNNY_STREAM_BASE_URL', 'https://video.bunnycdn.com'),
        'embed_base_url' => env('BUNNY_STREAM_EMBED_BASE_URL', 'https://iframe.mediadelivery.net/embed'),
        'upload_expiration_seconds' => (int) env('BUNNY_STREAM_UPLOAD_EXPIRATION_SECONDS', 86400),
    ],

];
