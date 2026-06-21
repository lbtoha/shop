<?php

return [
    // Basic Meta Tags
    'title' => 'Premium Gallery BD | Exclusive Online Shop',
    'description' => 'Premium Gallery BD is your ultimate destination for exquisite collections and high-quality products. Discover our premium selection today!',
    'author' => 'Premium Gallery BD',
    'robots' => 'index, follow',
    'image' => '/uploads/images/og-image.jpg',
    'keywords' => 'Premium Gallery, Premium Gallery BD, Online Shop, E-commerce Bangladesh',
    'canonical_link' => '',
    'alternates' => [
        'canonical' => '',
    ],
    'openGraph' => [
        'title' => 'Premium Gallery BD | Exclusive Online Shop',
        'description' => 'Premium Gallery BD is your ultimate destination for exquisite collections and high-quality products. Discover our premium selection today!',
        'type' => 'website',
        'url' => '',
        'site_name' => 'Premium Gallery BD',
        'locale' => 'en_US',
        'image' => '/uploads/images/og-image.jpg',
        'imageAlt' => 'Premium Gallery BD',
        'imageWidth' => '1200',
        'imageHeight' => '630',
    ],

    // Twitter Meta Tags
    'twitter' => [
        'card' => 'summary_large_image',
        'site' => '@PremiumGalleryBD',
        'creator' => '@PremiumGalleryBD',
        'title' => 'Premium Gallery BD | Exclusive Online Shop',
        'description' => 'Premium Gallery BD is your ultimate destination for exquisite collections and high-quality products. Discover our premium selection today!',
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
            'content' => '#E11D48',
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
            'content' => 'Premium Gallery BD',
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
                'name' => 'Premium Gallery BD',
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
