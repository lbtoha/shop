<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            'robot_text' => 'User-agent: *
                    Disallow: /admin/
                    Disallow: /login/
                    Disallow: /register/
                    Disallow: /dashboard/
                    Disallow: /cart/
                    Disallow: /checkout/
                    Disallow: /profile/

                    Allow: /

                    Sitemap: https://yourdomain.com/sitemap.xml
                    ',
            'sitemap' => '<?xml version="1.0" encoding="UTF-8"?>
                        <urlset
                            xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
                        >
                            <!-- Home Page -->
                            <url>
                                <loc>https://yourdomain.com/</loc>
                                <lastmod>01/01/2026 12:00 am</lastmod>
                                <changefreq>daily</changefreq>
                                <priority>1.0</priority>
                            </url>

                            <!-- About Us Page -->
                            <url>
                                <loc>https://yourdomain.com/about-us</loc>
                                <lastmod>01/01/2026 12:00 am</lastmod>
                                <changefreq>monthly</changefreq>
                                <priority>0.8</priority>
                            </url>

                            <!-- Contact Us Page -->
                            <url>
                                <loc>https://yourdomain.com/contact-us</loc>
                                <lastmod>01/01/2026 12:00 am</lastmod>
                                <changefreq>monthly</changefreq>
                                <priority>0.7</priority>
                            </url>
                        </urlset>
                        ',
            'gdpr_cookies' => json_encode([
                'is_enabled' => true,
                'description' => 'We may use cookies or any other tracking technologies when you visit our website, including any other media form, mobile website, or mobile application related or connected to help customize the Site and improve your experience.',
            ]),
        ];

        foreach ($options as $key => $value) {
            \App\Models\Option::updateOption($key, $value);
        }
    }
}
