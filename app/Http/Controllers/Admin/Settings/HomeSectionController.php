<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    public function index()
    {
        adminUserHasPermission(permission: 'read');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-arrow-left',
                'type' => 'link',
                'link' => route('admin.settings.index'),
            ],
        ];

        // Selectable categories (top-level, active).
        $categories = Category::active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        // Saved section configuration (array of rows).
        $sections = getOptionWithJsonDecode('home_sections', []) ?: [];

        // Toggle for the auto "Featured Products" section.
        $featuredEnabled = (int) getOption('home_featured_enabled', 1);
        $featuredTitle = getOption('home_featured_title', __('Featured Products'));

        // Toggle for the combined "All Products" section.
        $allEnabled = (int) getOption('home_all_enabled', 1);
        $allTitle = getOption('home_all_title', __('All Products'));
        $allLimit = (int) getOption('home_all_limit', 12);

        return view('admin.pages.settings.home-sections.index', compact(
            'buttons', 'categories', 'sections', 'featuredEnabled', 'featuredTitle',
            'allEnabled', 'allTitle', 'allLimit'
        ));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'featured_enabled' => 'required|in:0,1',
            'featured_title' => 'nullable|string|max:120',

            'all_enabled' => 'required|in:0,1',
            'all_title' => 'nullable|string|max:120',
            'all_limit' => 'required|integer|min:1|max:48',

            'sections' => 'array',
            'sections.*.category_slug' => 'required|string|exists:categories,slug',
            'sections.*.title' => 'nullable|string|max:120',
            'sections.*.layout' => 'required|in:slider,grid',
            'sections.*.limit' => 'required|integer|min:1|max:24',
            'sections.*.enabled' => 'required|in:0,1',
        ]);

        // Normalise + re-index, preserving the submitted order as sort order.
        $sections = collect($validated['sections'] ?? [])
            ->map(fn ($row) => [
                'category_slug' => $row['category_slug'],
                'title' => trim($row['title'] ?? '') ?: null,
                'layout' => $row['layout'],
                'limit' => (int) $row['limit'],
                'enabled' => (int) $row['enabled'],
            ])
            ->values()
            ->all();

        storeOption([
            'home_featured_enabled' => (int) $validated['featured_enabled'],
            'home_featured_title' => trim($validated['featured_title'] ?? '') ?: __('Featured Products'),
            'home_all_enabled' => (int) $validated['all_enabled'],
            'home_all_title' => trim($validated['all_title'] ?? '') ?: __('All Products'),
            'home_all_limit' => (int) $validated['all_limit'],
            'home_sections' => $sections,
        ]);

        return response()->json([
            'message' => __('Home sections updated.'),
            'reload' => true,
        ]);
    }
}
