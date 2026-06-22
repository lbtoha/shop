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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Google Gemini — powers the AI virtual try-on. The API key is normally
    // managed from the admin AI Settings page (Options); this is the .env fallback.
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_TRYON_MODEL', 'gemini-3.1-flash-image'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID') ?: '679222193682-cck8cffes7ink3slo40fqc5dfea70t3i.apps.googleusercontent.com',
        'client_secret' => env('GOOGLE_CLIENT_SECRET') ?: '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',
        'redirect' => 'http://localhost:3000/callback/google',
    ],

];
