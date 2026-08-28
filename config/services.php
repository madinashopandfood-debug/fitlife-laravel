<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Telegram bot token/chat ID, Meta Pixel ID, and Meta CAPI access token
    | are NOT stored here — they're managed at runtime through the Admin ->
    | Settings panel and persisted (encrypted, where applicable) in the
    | `settings` database table via App\Models\Setting. See
    | App\Services\TelegramService and App\Services\MetaCapiService.
    |
    | This file is kept for standard Laravel framework compatibility and any
    | future third-party integrations (mail providers, etc.).
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

];
