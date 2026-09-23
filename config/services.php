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

    'mailtrap' => [
        'api_token' => env('MAILTRAP_API_TOKEN'),
        'mode' => env('MAILTRAP_API_MODE', 'sending'),
        'sandbox_inbox_id' => env('MAILTRAP_SANDBOX_INBOX_ID'),
        'from' => [
            'address' => env('MAILTRAP_FROM_ADDRESS'),
            'name' => env('MAILTRAP_FROM_NAME', env('MAIL_FROM_NAME', env('APP_NAME'))),
        ],
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
