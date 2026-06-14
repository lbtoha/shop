<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::active()->orderBy('sort_order')->get();

        $categories = Category::active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->withCount(['products' => fn ($q) => $q->active()])
            ->get();

        // Featured Products (toggleable from admin → Home Sections).
        $featuredProducts = collect();
        if ((int) getOption('home_featured_enabled', 1)) {
            $featuredProducts = Product::active()->inStock()
                ->with('category')->withCount('variants')
                ->where('is_featured', true)
                ->latest()
                ->take(12)
                ->get();
        }
        $featuredTitle = getOption('home_featured_title', __('Featured Products'));

        // Combined "All Products" section (toggleable from admin → Home Sections).
        $allProducts = collect();
        if ((int) getOption('home_all_enabled', 1)) {
            $allLimit = (int) getOption('home_all_limit', 12);
            $allLimit = max(1, min($allLimit, 48));

            $allProducts = Product::active()->inStock()
                ->with('category')->withCount('variants')
                ->latest()
                ->take($allLimit)
                ->get();
        }
        $allTitle = getOption('home_all_title', __('All Products'));

        // Category sections, configured in admin → Home Sections.
        $homeSections = $this->buildHomeSections();

        return view('shop.home', compact(
            'banners', 'categories', 'featuredProducts', 'featuredTitle',
            'allProducts', 'allTitle', 'homeSections'
        ));
    }

    /**
     * Resolve the admin-configured home sections into renderable data.
     * Each enabled row maps a category slug to a product collection.
     *
     * @return array<int, array{title:string, layout:string, products:\Illuminate\Support\Collection, viewAll:string}>
     */
    private function buildHomeSections(): array
    {
        $config = getOptionWithJsonDecode('home_sections', []) ?: [];

        $sections = [];

        foreach ($config as $row) {
            if (empty($row['enabled']) || empty($row['category_slug'])) {
                continue;
            }

            $category = Category::active()->where('slug', $row['category_slug'])->first();
            if (! $category) {
                continue;
            }

            $limit = (int) ($row['limit'] ?? 8);
            $limit = max(1, min($limit, 24));

            $products = Product::active()->inStock()
                ->with('category')->withCount('variants')
                ->where('category_id', $category->id)
                ->latest()
                ->take($limit)
                ->get();

            if ($products->isEmpty()) {
                continue;
            }

            $sections[] = [
                'title' => ! empty($row['title']) ? $row['title'] : $category->name,
                'layout' => ($row['layout'] ?? 'grid') === 'slider' ? 'slider' : 'grid',
                'products' => $products,
                'viewAll' => route('shop.index', ['category' => $category->slug]),
            ];
        }

        return $sections;
    }
}
