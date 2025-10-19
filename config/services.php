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
    /*
    |--------------------------------------------------------------------------
    | Google Cloud Vision Configuration
    |--------------------------------------------------------------------------
    |
    | Configure Google Cloud Vision API credentials
    | Set GOOGLE_APPLICATION_CREDENTIALS in .env to the path of your
    | service account JSON file
    |
    */
    'google' => [
        'vision' => [
            'credentials_path' => env('GOOGLE_APPLICATION_CREDENTIALS'),
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Amazon Product Advertising API Configuration
    |--------------------------------------------------------------------------
    |
    | Configure Amazon PA-API credentials for affiliate product searches
    |
    */
    'amazon' => [
        'access_key' => env('AMAZON_ACCESS_KEY'),
        'secret_key' => env('AMAZON_SECRET_KEY'),
        'associate_tag' => env('AMAZON_ASSOCIATE_TAG'),
        'region' => env('AMAZON_REGION', 'us-east-1'),
        'marketplace' => env('AMAZON_MARKETPLACE', 'www.amazon.com'),
    ],
];
