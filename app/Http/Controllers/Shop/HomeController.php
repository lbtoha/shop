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
            ->withCount('products')
            ->get();

        $newCollection = Product::active()->inStock()
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        // "Hot Sale" = products that carry a higher compare-at price (i.e. discounted)
        $hotSale = Product::active()->inStock()
            ->with('category')
            ->whereNotNull('compare_at_price')
            ->whereColumn('compare_at_price', '>', 'price')
            ->latest()
            ->take(8)
            ->get();

        $ladiesThreePiece = Product::active()->inStock()
            ->with('category')
            ->whereHas('category', function ($q) {
                $q->where('name', 'Ladies Three Piece');
            })
            ->latest()
            ->take(8)
            ->get();

        return view('shop.home', compact('banners', 'categories', 'newCollection', 'hotSale', 'ladiesThreePiece'));
    }
}
