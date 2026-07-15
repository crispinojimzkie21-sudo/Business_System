<?php

return [
    'business' => [
        'name' => env('APP_NAME', 'Business'),
        'email' => env('MAIL_FROM_ADDRESS'),
        'timezone' => env('APP_TIMEZONE', 'UTC'),
    ],
    
    'stripe' => [
        'api_key' => env('STRIPE_API_KEY'),
        'secret' => env('STRIPE_SECRET_KEY'),
    ],
    
    'twilio' => [
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
    ],
];
