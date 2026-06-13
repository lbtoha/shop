<?php

return [
    'admin' => [
        'menu' => [
            [
                'title' => 'Dashboard',
                'link' => 'admin.dashboard',
                'icon' => 'ph ph-house',
            ],
            [
                'title' => 'Catalog',
                'link' => 'admin.products.index',
                'icon' => 'ph ph-storefront',
                'parent_menu' => true,
                'parent' => 'admin/products',
                'submenus' => [
                    ['title' => 'Products', 'link' => 'admin.products.index'],
                    ['title' => 'Categories', 'link' => 'admin.categories.index'],
                ],
            ],
            [
                'title' => 'Marketing',
                'link' => 'admin.coupons.index',
                'icon' => 'ph ph-megaphone',
                'parent_menu' => true,
                'parent' => 'admin/coupons',
                'submenus' => [
                    ['title' => 'Coupons', 'link' => 'admin.coupons.index'],
                ],
            ],
            [
                'title' => 'Appearance',
                'link' => 'admin.banners.index',
                'icon' => 'ph ph-palette',
                'parent_menu' => true,
                'parent' => 'admin/banners',
                'submenus' => [
                    ['title' => 'Hero Banners', 'link' => 'admin.banners.index'],
                ],
            ],
            [
                'title' => 'Orders',
                'link' => 'admin.orders.index',
                'icon' => 'ph ph-shopping-cart',
            ],
            [
                'title' => 'Manage Users',
                'link' => 'admin.users',
                'icon' => 'ph ph-users-three',
                'parent_menu' => true,
                'parent' => 'admin/users',
                'submenus' => [
                    ['title' => 'Users', 'link' => 'admin.users.index'],
                    ['title' => 'Send Notification', 'link' => 'admin.users.notifications.index'],
                ],
            ],
            [
                'title' => 'Admin Users',
                'link' => 'admin.admins*',
                'icon' => 'ph ph-users',
                'parent' => 'admin/admins',
                'parent_menu' => true,
                'submenus' => [
                    ['title' => 'Roles', 'link' => 'admin.admin-roles.index'],
                    ['title' => 'Admin Users', 'link' => 'admin.admins.index'],
                ],
            ],
            [
                'title' => 'Settings',
                'link' => 'admin.settings.index',
                'icon' => 'ph ph-gear',
                'parent' => 'admin/settings',
                'parent_menu' => true,
                'submenus' => [
                    [
                        'title' => 'System Settings',
                        'parent' => 'system',
                        'submenus' => [
                            [
                                'title' => 'General Settings',
                                'link' => 'admin.settings.app.index',
                                'icon' => 'ph ph-gear',
                                'text' => 'Configure basic system settings',
                            ],
                            [
                                'title' => 'Shop Settings',
                                'link' => 'admin.settings.shop.index',
                                'icon' => 'ph ph-storefront',
                                'text' => 'Currency symbol and flat shipping cost for the shop.',
                            ],
                            [
                                'title' => 'Order Notifications',
                                'link' => 'admin.settings.order-notifications.index',
                                'icon' => 'ph ph-bell-ringing',
                                'text' => 'Email, SMS gateway and WhatsApp notifications for orders.',
                            ],
                            [
                                'title' => 'System Configuration',
                                'link' => 'admin.settings.system-configurations.index',
                                'icon' => 'ph ph-wrench',
                                'text' => 'Adjust core system configuration settings.',
                            ],
                            [
                                'title' => 'Cron Job Settings',
                                'link' => 'admin.settings.task-schedules.index',
                                'icon' => 'ph ph-briefcase',
                                'text' => 'Configure cron jobs for automated tasks in the system.',
                            ],
                            [
                                'title' => 'Maintenance Mode',
                                'link' => 'admin.settings.maintenance.index',
                                'icon' => 'ph ph-gear-fine',
                                'text' => 'Enable or disable maintenance mode for the site.',
                            ],
                            [
                                'title' => 'GDPR Cookie',
                                'link' => 'admin.settings.gdpr-cookies.index',
                                'icon' => 'ph ph-cookie',
                                'text' => 'Manage GDPR compliance for cookies and data privacy.',
                            ],
                        ],
                    ],
                    [
                        'title' => 'User Settings',
                        'parent' => 'user-settings',
                        'submenus' => [
                            [
                                'title' => 'Notification Settings',
                                'link' => 'admin.settings.notification.services',
                                'icon' => 'ph ph-bell',
                                'text' => 'Manage notification preferences for users',
                            ],
                        ],
                    ],
                    [
                        'title' => 'SEO & Meta',
                        'parent' => 'seo',
                        'submenus' => [
                            [
                                'title' => 'SEO Configuration',
                                'link' => 'admin.settings.seo.index.page',
                                'icon' => 'ph ph-google-chrome-logo',
                                'text' => 'Adjust SEO-related settings for better search engine ranking.',
                            ],
                            [
                                'title' => 'Sitemap XML',
                                'link' => 'admin.settings.seo.sitemap',
                                'icon' => 'ph ph-map-trifold',
                                'text' => 'Manage and generate XML sitemaps for SEO.',
                            ],
                            [
                                'title' => 'Robots TXT',
                                'link' => 'admin.settings.seo.robots',
                                'icon' => 'ph ph-robot',
                                'text' => 'Configure the robots.txt file for web crawlers.',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Theme Settings',
                        'parent' => 'theme',
                        'submenus' => [
                            [
                                'title' => 'Logo and Favicon',
                                'link' => 'admin.settings.logo-favicon.index',
                                'icon' => 'ph ph-intersect',
                                'text' => "Set up and manage the website's logo and favicon.",
                            ],
                            [
                                'title' => 'PWA',
                                'link' => 'admin.settings.pwa.index',
                                'icon' => 'ph ph-intersect',
                                'text' => 'Set up and manage your PWA settings.',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Integration Settings',
                        'parent' => 'integration',
                        'submenus' => [
                            [
                                'title' => 'Services Settings',
                                'link' => 'admin.settings.services.index',
                                'icon' => 'ph ph-package',
                                'text' => 'Configure third-party API service settings',
                            ],
                            [
                                'title' => 'Extensions',
                                'link' => 'admin.settings.extensions.index',
                                'icon' => 'ph ph-puzzle-piece',
                                'text' => 'Manage available extensions for the system.',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Localization',
                        'parent' => 'localization',
                        'submenus' => [
                            [
                                'title' => 'Language',
                                'link' => 'admin.settings.languages.index',
                                'icon' => 'ph ph-globe',
                                'text' => 'Configure language settings for the website.',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Navigation',
                        'parent' => 'navigation',
                        'submenus' => [
                            [
                                'title' => 'Menu',
                                'link' => 'admin.settings.menus.index',
                                'icon' => 'ph ph-list',
                                'text' => 'Manage the main navigation menu',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Extras',
                'link' => 'admin.extras*',
                'icon' => 'ph ph-aperture',
                'parent' => 'admin/extras',
                'parent_menu' => true,
                'submenus' => [
                    ['title' => 'App Info', 'link' => 'admin.extras.application-info'],
                    ['title' => 'Update', 'link' => 'admin.extras.application-update'],
                ],
            ],
        ],
    ],
];
