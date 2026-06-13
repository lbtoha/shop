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
            ->withCount(['products' => fn($q) => $q->active()])
            ->get();

        $newCollection = Product::active()->inStock()
            ->with('category')->withCount('variants')
            ->latest()
            ->take(8)
            ->get();

        // Featured Products
        $featuredProducts = Product::active()->inStock()
            ->with('category')->withCount('variants')
            ->where('is_featured', true)
            ->latest()
            ->take(12)
            ->get();
 
        $ladiesThreePiece = Product::active()->inStock()
            ->with('category')->withCount('variants')
            ->whereHas('category', function ($q) {
                $q->where('name', "Women's Products");
            })
            ->latest()
            ->take(8)
            ->get();
 
        return view('shop.home', compact('banners', 'categories', 'newCollection', 'featuredProducts', 'ladiesThreePiece'));
    }
}
