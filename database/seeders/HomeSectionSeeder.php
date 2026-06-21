<?php

namespace Database\Seeders;

use App\Enums\HomeSectionLayoutEnum;
use App\Enums\HomeSectionSourceEnum;
use App\Models\Category;
use App\Models\HomeSection;
use Illuminate\Database\Seeder;

class HomeSectionSeeder extends Seeder
{
    public function run(): void
    {
        // Reproduce the previous default home page as editable, database-driven sections.
        $sections = [
            [
                'title' => 'Featured Products',
                'subtitle' => 'Exclusive',
                'source' => HomeSectionSourceEnum::FEATURED->value,
                'layout' => HomeSectionLayoutEnum::SLIDER->value,
                'product_limit' => 12,
                'sort_order' => 1,
            ],
            [
                // Admin-controlled "All Products" section: hand-pick products in
                // admin, or leave empty to fall back to the latest products.
                'title' => 'All Products',
                'subtitle' => 'Trending',
                'source' => HomeSectionSourceEnum::PRODUCTS->value,
                'layout' => HomeSectionLayoutEnum::GRID->value,
                'product_ids' => [],
                'fallback_latest' => true,
                'product_limit' => 12,
                'sort_order' => 2,
            ],
        ];

        // Showcase the "any category, independently" use case with a carousel
        // for the first non-default category that exists (e.g. boys'/kids').
        $spotlight = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->skip(1)
            ->first();

        if ($spotlight) {
            $sections[] = [
                'title' => $spotlight->name,
                'subtitle' => 'Collection',
                'source' => HomeSectionSourceEnum::CATEGORY->value,
                'layout' => HomeSectionLayoutEnum::CAROUSEL->value,
                'category_id' => $spotlight->id,
                'product_limit' => 8,
                'sort_order' => 3,
            ];
        }

        foreach ($sections as $section) {
            HomeSection::create([...$section, 'is_active' => true]);
        }
    }
}
