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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
    ],

    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect_uri' => env('MICROSOFT_REDIRECT_URI'),
    ],

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'sms' => [
        'provider' => env('SMS_PROVIDER', 'tunisie_telecom'),
        'api_key' => env('SMS_API_KEY'),
        'sender_name' => env('SMS_SENDER_NAME', 'SehaDigital'),
    ],

    'payment' => [
        'gateway' => env('PAYMENT_GATEWAY', 'smt'),
        'api_key' => env('PAYMENT_API_KEY'),
        'secret_key' => env('PAYMENT_SECRET_KEY'),
        'environment' => env('PAYMENT_ENVIRONMENT', 'sandbox'),
    ],

];
