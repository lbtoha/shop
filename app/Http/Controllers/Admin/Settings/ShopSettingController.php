<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShopSettingController extends Controller
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

        $settings = [
            'currency_symbol' => getOption('currency_symbol', '$'),
            'currency_code' => getOption('currency_code', 'USD'),
            'shipping_cost' => getOption('shipping_cost', 0),
            'show_ratings' => getOption('show_ratings', 0),
        ];

        return view('admin.pages.settings.shop.index', compact('settings', 'buttons'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'currency_symbol' => 'required|string|max:8',
            'currency_code' => 'required|string|max:8',
            'shipping_cost' => 'required|numeric|min:0',
            'show_ratings' => 'required|in:0,1',
        ]);

        storeOption([
            'currency_symbol' => $validated['currency_symbol'],
            'currency_code' => $validated['currency_code'],
            'shipping_cost' => $validated['shipping_cost'],
            'show_ratings' => $validated['show_ratings'],
        ]);

        return response()->json(['message' => __('Shop settings updated.'), 'reload' => true]);
    }
}
