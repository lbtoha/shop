<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandPartnersController extends Controller
{
    public function index(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $partners = getOptionWithJsonDecode('brand_partners', []);

        return view('admin.pages.settings.general.brand-partners.index', compact('partners'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'show_brand_partners' => 'required|in:0,1',
            'partners' => 'nullable|array',
            'partners.*.name' => 'nullable|string|max:100',
            'partners.*.logo' => 'nullable|string|max:255',
        ]);

        // Filter out completely empty rows
        $partners = collect($validated['partners'] ?? [])
            ->filter(fn($p) => filled($p['name']) || filled($p['logo']))
            ->values()
            ->toArray();

        storeOption([
            'show_brand_partners' => $validated['show_brand_partners'],
            'brand_partners' => $partners,
        ]);

        return response()->json(['message' => __('Brand partners updated successfully')]);
    }
}
