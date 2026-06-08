<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            ['title' => 'New Collection 2026', 'subtitle' => 'Discover the latest arrivals', 'button_text' => 'Shop Now', 'link' => '/shop', 'sort_order' => 1],
            ['title' => 'Exclusive Trends', 'subtitle' => 'Hand-picked styles for you', 'button_text' => 'Explore', 'link' => '/shop', 'sort_order' => 2],
            ['title' => 'Cash on Delivery', 'subtitle' => 'Order now, pay when it arrives', 'button_text' => 'Start Shopping', 'link' => '/shop', 'sort_order' => 3],
        ];

        foreach ($banners as $banner) {
            Banner::create([...$banner, 'is_active' => true]);
        }
    }
}
