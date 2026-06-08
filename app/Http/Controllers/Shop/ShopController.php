<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Product listing with optional category filter, search, and sorting.
     */
    public function index(Request $request)
    {
        $categories = Category::active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->withCount('products')
            ->get();

        $activeCategory = null;

        $query = Product::active()->with('category');

        if ($request->filled('category')) {
            $activeCategory = Category::active()->where('slug', $request->category)->first();
            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
        }

        if ($request->filled('search')) {
            $query->whereLike(['name', 'short_description'], $request->search);
        }

        match ($request->get('sort')) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('shop.shop', compact('products', 'categories', 'activeCategory'));
    }

    /**
     * Single product detail page.
     */
    public function show(string $slug)
    {
        $product = Product::active()
            ->with(['category', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();

        $product->increment('views');

        $related = Product::active()->inStock()
            ->with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(4)
            ->get();

        return view('shop.product', compact('product', 'related'));
    }
}
