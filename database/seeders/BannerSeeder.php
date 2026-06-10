<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            ['title' => 'Festive Collection 2026', 'subtitle' => 'Handcrafted ethnic wear for the season', 'button_text' => 'Shop Now', 'link' => '/shop', 'image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=1600&q=80', 'sort_order' => 1],
            ['title' => 'Premium Saree & Kurtis', 'subtitle' => 'Experience the elegance of loom fabrics', 'button_text' => 'Explore', 'link' => '/shop', 'image' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=1600&q=80', 'sort_order' => 2],
            ['title' => 'Kids Girls Outfits', 'subtitle' => 'Comfortable traditional wear for children', 'button_text' => 'Browse', 'link' => '/shop', 'image' => 'https://images.unsplash.com/photo-1608748010899-18f300247112?w=1600&q=80', 'sort_order' => 3],
        ];

        foreach ($banners as $banner) {
            Banner::create([...$banner, 'is_active' => true]);
        }
    }
}
