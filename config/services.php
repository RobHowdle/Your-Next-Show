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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'what3words' => [
        'api_key' => env('W3W_API_KEY'),
    ],

    'eventbrite' => [
        'key' => env('EVENTBRITE_PRIVATE_TOKEN'),
    ],

    'skiddle' => [
        'client_id' => env('SKIDDLE_CLIENT_ID'),
        'client_secret' => env('SKIDDLE_CLIENT_SECRET'),
        'redirect' => env('SKIDDLE_REDIRECT_URI'),
        'api_key' => env('SKIDDLE_API_KEY'),
    ],

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],
];