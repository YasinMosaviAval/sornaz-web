<?php

return [
    'enabled' => true,
    'cache' => false,
    'mail' => [
        'host' => env('MAIL_HOST', 'localhost'),
        'port' => (int)env('MAIL_PORT', 587),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'username' => env('MAIL_USER', env('MAIL_USERNAME', '')),
        'password' => env('MAIL_PASS', env('MAIL_PASSWORD', '')),
        'from' => env('MAIL_FROM', env('MAIL_FROM_ADDRESS', 'no-reply@sornaz.com')),
        'from_name' => env('MAIL_FROM_NAME', env('APP_NAME', 'Sornaz Academy')),
        'verify_peer' => filter_var(env('MAIL_VERIFY_PEER', true), FILTER_VALIDATE_BOOLEAN),
    ],
    'sms' => [
        'provider' => env('SMS_PROVIDER', 'kavenegar'),
        'api_key' => env('SMS_API_KEY', ''),
        'sender' => env('SMS_SENDER', ''),
        'kavenegar_template' => env('SMS_KAVENEGAR_TEMPLATE', ''),
        'kavenegar_forgot_template' => env('SMS_KAVENEGAR_FORGOT_TEMPLATE', 'sornazforget'),
        'username' => env('SMS_USERNAME', ''),
        'password' => env('SMS_PASSWORD', ''),
        'from' => env('SMS_FROM', ''),
    ],
];
