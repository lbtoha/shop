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
            'whatsapp_enabled' => getOption('whatsapp_enabled', 0),
            'whatsapp_number' => getOption('whatsapp_number', ''),
            'show_product_category' => getOption('show_product_category', 1),
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
            'whatsapp_enabled' => 'required|in:0,1',
            'whatsapp_number' => 'nullable|string|max:30',
            'show_product_category' => 'required|in:0,1',
        ]);

        // Number is required only when the WhatsApp button is switched on.
        if ((int) $validated['whatsapp_enabled'] === 1) {
            $request->validate(['whatsapp_number' => 'required|string|max:30']);
        }

        storeOption([
            'currency_symbol' => $validated['currency_symbol'],
            'currency_code' => $validated['currency_code'],
            'shipping_cost' => $validated['shipping_cost'],
            'show_ratings' => $validated['show_ratings'],
            'whatsapp_enabled' => $validated['whatsapp_enabled'],
            'whatsapp_number' => $validated['whatsapp_number'] ?? '',
            'show_product_category' => $validated['show_product_category'],
        ]);

        return response()->json(['message' => __('Shop settings updated.'), 'reload' => true]);
    }
}
