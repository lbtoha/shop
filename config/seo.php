<?php

return [
    // Basic Meta Tags
    'title' => 'QuiziX | A Online Quiz and Contest Platform',
    'description' => 'QuiziX is a modern quiz and contest platform that helps you test your knowledge and compete with others. Join now and start your quiz journey!',
    'author' => 'QuiziX',
    'robots' => 'index, follow',
    'image' => '/uploads/images/og-image.jpg',
    'keywords' => 'Quiz, Contest, Online, Quiziz, QuiziX, QuiziX.com, Quiziz.com',
    'canonical_link' => '',
    'alternates' => [
        'canonical' => '',
    ],
    'openGraph' => [
        'title' => 'QuiziX | A Online Quiz and Contest Platform',
        'description' => 'QuiziX is a modern quiz and contest platform that helps you test your knowledge and compete with others. Join now and start your quiz journey!',
        'type' => 'website',
        'url' => '',
        'site_name' => 'QuiziX',
        'locale' => 'en_US',
        'image' => '/uploads/images/og-image.jpg',
        'imageAlt' => 'QuiziX',
        'imageWidth' => '1200',
        'imageHeight' => '630',
    ],

    // Twitter Meta Tags
    'twitter' => [
        'card' => 'summary_large_image',
        'site' => '@QuiziX',
        'creator' => '@QuiziX',
        'title' => 'QuiziX | A Online Quiz and Contest Platform',
        'description' => 'QuiziX is a modern quiz and contest platform that helps you test your knowledge and compete with others. Join now and start your quiz journey!',
        'image' => '/uploads/images/og-image.jpg',
    ],

    // Additional Meta Tags
    'meta' => [
        // Google Verification (for Search Console)
        [
            'name' => 'google-site-verification',
            'content' => 'your_google_verification_code',
        ],

        // Facebook App ID (if applicable)
        [
            'property' => 'fb:app_id',
            'content' => 'your_facebook_app_id',
        ],

        // Mobile Viewport Optimization
        [
            'name' => 'theme-color',
            'content' => '#FF0000',
        ],
        [
            'name' => 'msapplication-TileColor',
            'content' => '#FFFFFF',
        ],
        [
            'name' => 'apple-mobile-web-app-capable',
            'content' => 'yes',
        ],
        [
            'name' => 'apple-mobile-web-app-status-bar-style',
            'content' => 'black',
        ],
        [
            'name' => 'application-name',
            'content' => 'QuiziX',
        ],
    ],

    // Favicons (Branding)
    'favicon' => [
        [
            'rel' => 'icon',
            'type' => 'image/png',
            'href' => '/uploads/favicon.png',
        ],
        [
            'rel' => 'apple-touch-icon',
            'sizes' => '180x180',
            'href' => '/uploads/apple-touch-icon.png',
        ],
    ],
    // Structured Data (Schema.org JSON-LD for SEO)
    'structured_data' => [
        'script' => [
            'type' => 'application/ld+json',
            'content' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'QuiziX',
                'url' => '',
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => '/search?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
        ],
    ],
];
