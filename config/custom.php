<?php

return [
    'business' => [
        'name' => env('APP_NAME', 'Business System'),
        'email' => env('MAIL_FROM_ADDRESS', 'noreply@business.com'),
        'phone' => env('BUSINESS_PHONE', ''),
        'timezone' => env('APP_TIMEZONE', 'Asia/Manila'),
    ],
    
    'stripe' => [
        'api_key' => env('STRIPE_API_KEY'),
        'secret' => env('STRIPE_SECRET_KEY'),
    ],
    
    'mail' => [
        'from_address' => env('MAIL_FROM_ADDRESS'),
        'from_name' => env('MAIL_FROM_NAME', env('APP_NAME')),
    ],
];
