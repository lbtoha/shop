<?php

return [
    'company_info' => [
        'name' => 'Premium Gallery BD',
        'email' => 'support@premiumgallerybd.com',
        'phone' => '+123 456 789',
        'website' => 'www.premiumgallerybd.com',
        'description' => 'Your premium destination for exquisite products and curated collections. Elevate your lifestyle with our exclusive selection.',
    ],
    'frontend_url' => 'http://localhost:3000',
    'KYC_approval_time' => '3-5 business days', // in minutes
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'theme' => [
        'primary_color' => '#e11d48',
        'secondary_color' => '#b8893f',
    ],
    'logo_favicon' => [
        'logo_light' => '/assets/logo.png',
        'logo_dark' => '/assets/logo.png',
        'favicon' => '/assets/logo.png',
    ],
    'address' => [
        'country' => 'United States',
        'state' => 'California',
        'city' => 'Los Angeles',
        'postal_code' => '90001',
        'address' => '1600 Amphitheatre Parkway, Mountain View, CA 94044',
        'location' => 'https://maps.app.goo.gl/GZ9YQZa2v15d1LhY7',
    ],
    'coins' => [
        'score_ratio' => [
            'coin' => 1,
            'score' => 10, // 1 coin = 10 score
        ],
        'usd_ratio' => [
            'usd' => 1,
            'coin' => 50, // 1 coin = 100 USD
        ],
        'initial_balance' => 500, // Initial balance in coins
    ],

    'referral' => [
        'joining' => 100, // Referral joining bonus in coins
    ],

    'social_medias' => [
        [
            'id' => 1,
            'name' => 'Facebook',
            'link' => 'https://www.facebook.com/jhon.doe',
            'icon' => 'ph ph-facebook-logo',
        ],
        [
            'id' => 2,
            'name' => 'Linkedin',
            'link' => 'https://linkedin.com/jhon_doe',
            'icon' => 'ph ph-linkedin-logo',
        ],
        [
            'id' => 3,
            'name' => 'Twitter',
            'link' => 'https://www.twitter.com/jhon_doe',
            'icon' => 'ph ph-twitter-logo',
        ],
        [
            'id' => 4,
            'name' => 'Instagram',
            'link' => 'https://www.instagram.com/jhon_doe',
            'icon' => 'ph ph-instagram-logo',
        ],
        [
            'id' => 5,
            'name' => 'Youtube',
            'link' => 'https://www.youtube.com/jhon_doe',
            'icon' => 'ph ph-youtube-logo',
        ],
        [
            'id' => 6,
            'name' => 'Tiktok',
            'link' => 'https://www.tiktok.com/jhon_doe',
            'icon' => 'ph ph-tiktok-logo',
        ],
    ],

    'otp' => [
        'expire_time' => 1,
        'digit_range' => [10000, 99999],
    ],

    'defaultCurrency' => [],

    'auth_providers' => [
        [
            'id' => 'google',
            'name' => 'Google',
            'is_enabled' => true,
        ],
        [
            'id' => 'facebook',
            'name' => 'Facebook',
            'is_enabled' => false,
        ],
        [
            'id' => 'github',
            'name' => 'Github',
            'is_enabled' => false,
        ],
        [
            'id' => 'twitter',
            'name' => 'Twitter',
            'is_enabled' => false,
        ],
        [
            'id' => 'linkedin',
            'name' => 'Linkedin',
            'is_enabled' => false,
        ],
    ],

    'footer_text' => 'All rights reserved.',
    'footer_menu_id' => null,
    'auth_left_sidebar_image' => '/assets/client/sign-in-img.webp',
    'mobile_app' => [
        'android' => [
            'link' => 'https://play.google.com/store/apps',
            'icon' => '/assets/client/android.svg',
        ],
        'ios' => [
            'link' => 'https://www.apple.com/app-store/',
            'icon' => '/assets/client/apple.svg',
        ],
    ],
    'admob' => [
        'androidAppId' => 'ca-app-pub-6030102340960445~2567958201',
        'iosAppId' => 'ca-app-pub-6030102340960445~4848181773',
    ],
    'mobile_app_key' => env('MOBILE_APP_KEY', '1234567890'),
];
