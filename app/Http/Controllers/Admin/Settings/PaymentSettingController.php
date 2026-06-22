<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
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
            'sslcommerz_enabled' => getOption('sslcommerz_enabled', 0),
            'sslcommerz_test_mode' => getOption('sslcommerz_test_mode', 1),
            'sslcommerz_store_id' => getOption('sslcommerz_store_id', ''),
            'sslcommerz_store_password' => getOption('sslcommerz_store_password', ''),
            'sslcommerz_logo' => getOption('sslcommerz_logo', ''),
            'sslcommerz_default_logo' => \App\Services\Payment\SslCommerzService::DEFAULT_LOGO,
        ];

        return view('admin.pages.settings.payment.index', compact('settings', 'buttons'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'sslcommerz_enabled' => 'required|in:0,1',
            'sslcommerz_test_mode' => 'required|in:0,1',
            'sslcommerz_store_id' => 'nullable|string|max:191',
            'sslcommerz_store_password' => 'nullable|string|max:191',
            'sslcommerz_logo' => 'nullable|string|max:2048',
        ]);

        // Credentials are required only when the gateway is switched on.
        if ((int) $validated['sslcommerz_enabled'] === 1) {
            $request->validate([
                'sslcommerz_store_id' => 'required|string|max:191',
                'sslcommerz_store_password' => 'required|string|max:191',
            ]);
        }

        storeOption([
            'sslcommerz_enabled' => $validated['sslcommerz_enabled'],
            'sslcommerz_test_mode' => $validated['sslcommerz_test_mode'],
            'sslcommerz_store_id' => $validated['sslcommerz_store_id'] ?? '',
            'sslcommerz_store_password' => $validated['sslcommerz_store_password'] ?? '',
            // Empty = fall back to the bundled default logo.
            'sslcommerz_logo' => $validated['sslcommerz_logo'] ?? '',
        ]);

        return response()->json(['message' => __('Payment settings updated.'), 'reload' => true]);
    }
}
