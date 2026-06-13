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
            ->withCount(['products' => fn($q) => $q->active()])
            ->get();

        $activeCategory = null;

        $query = Product::active()->with('category')->withCount('variants');

        if ($request->filled('category')) {
            $activeCategory = Category::active()->where('slug', $request->category)->first();
            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('search')) {
            $query->whereLike(['name', 'short_description'], $request->search);
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', true);
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
            ->with(['category', 'images', 'variants'])
            ->where('slug', $slug)
            ->firstOrFail();

        $product->increment('views');

        $related = Product::active()->inStock()
            ->with('category')->withCount('variants')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(5)
            ->get();

        // "You may also like" — popular products outside this category
        $recommended = Product::active()->inStock()
            ->with('category')->withCount('variants')
            ->where('id', '!=', $product->id)
            ->whereNotIn('id', $related->pluck('id'))
            ->orderByDesc('views')
            ->take(5)
            ->get();

        return view('shop.product', compact('product', 'related', 'recommended'));
    }
}
