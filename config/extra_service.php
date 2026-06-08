<?php

return [
    'system_config' => [
        'user_registration' => [
            'is_enabled' => true,
            'title' => 'User Registration',
            'description' => 'Allow users to register on the website.',
        ],
        'agreement_trams_and_policy' => [
            'is_enabled' => true,
            'title' => 'Agreement, Trams and Policy',
            'description' => 'Allow users to accept the agreement, trams and policy.',
        ],
        'force_secure_password' => [
            'is_enabled' => false,
            'title' => 'Force Secure Password',
            'description' => 'Force users to use secure passwords.',
        ],
        'email_verification' => [
            'is_enabled' => false,
            'title' => 'Email Verification',
            'description' => 'Allow users to verify their email address.',
        ],
        'phone_verification' => [
            'is_enabled' => false,
            'title' => 'Phone Verification',
            'description' => 'Allow users to verify their phone number.',
        ],
        'force_https' => [
            'is_enabled' => false,
            'title' => 'Force HTTPS',
            'description' => 'Force users to use HTTPS.',
        ],
        'email_notification' => [
            'is_enabled' => false,
            'title' => 'Email Notification',
            'description' => 'Allow users to receive email notifications.',
        ],
    ],

    'site_pagination_config' => [
        'per_page' => 10,
        'sort_type' => 'desc',
        'cache_time' => 3600, // 1 hour
    ],

    'cookie_consent' => [
        'is_enabled' => true,
        'title' => 'Cookie Consent',
        'description' => 'This website uses cookies to improve your experience. By continuing to use this website, you agree to our use of cookies.',
    ],
];
