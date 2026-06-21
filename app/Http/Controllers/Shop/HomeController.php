<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Repositories\HomeSectionRepository;

class HomeController extends Controller
{
    public function index(HomeSectionRepository $homeSections)
    {
        $banners = Banner::active()->orderBy('sort_order')->get();

        $categories = Category::active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->withCount(['products' => fn ($q) => $q->active()])
            ->get();

        // Fully resolved, cached home sections configured in admin → Home Sections.
        $sections = $homeSections->forStorefront();

        return view('shop.home', compact('banners', 'categories', 'sections'));
    }
}
