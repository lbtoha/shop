<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Services\Ecommerce\SteadfastService;
use Illuminate\Http\Request;

class SteadfastSettingController extends Controller
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
            'steadfast_enabled' => getOption('steadfast_enabled', 0),
            'steadfast_api_key' => getOption('steadfast_api_key', ''),
            'steadfast_secret_key' => getOption('steadfast_secret_key', ''),
            'steadfast_base_url' => getOption('steadfast_base_url', 'https://portal.packzy.com/api/v1'),
            'steadfast_auto_send' => getOption('steadfast_auto_send', 0),
            'steadfast_auto_send_status' => getOption('steadfast_auto_send_status', 'processing'),
        ];

        // Show the live COD balance when credentials are valid.
        $balance = SteadfastService::isEnabled() ? SteadfastService::balance() : null;

        return view('admin.pages.settings.steadfast.index', compact('settings', 'buttons', 'balance'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'steadfast_enabled' => 'required|in:0,1',
            'steadfast_api_key' => 'nullable|string|max:255',
            'steadfast_secret_key' => 'nullable|string|max:255',
            'steadfast_base_url' => 'required|url|max:255',
            'steadfast_auto_send' => 'required|in:0,1',
            'steadfast_auto_send_status' => ['required', 'in:'.implode(',', \App\Enums\OrderStatusEnum::values())],
        ]);

        // Keys are required only when the integration is switched on.
        if ((int) $validated['steadfast_enabled'] === 1) {
            $request->validate([
                'steadfast_api_key' => 'required|string|max:255',
                'steadfast_secret_key' => 'required|string|max:255',
            ]);
        }

        storeOption([
            'steadfast_enabled' => $validated['steadfast_enabled'],
            'steadfast_api_key' => $validated['steadfast_api_key'] ?? '',
            'steadfast_secret_key' => $validated['steadfast_secret_key'] ?? '',
            'steadfast_base_url' => rtrim($validated['steadfast_base_url'], '/'),
            'steadfast_auto_send' => $validated['steadfast_auto_send'],
            'steadfast_auto_send_status' => $validated['steadfast_auto_send_status'],
        ]);

        return response()->json(['message' => __('Steadfast courier settings updated.'), 'reload' => true]);
    }

    /**
     * Live "Test connection" — verifies the Api-Key/Secret-Key currently in the
     * form (falling back to saved values) against the Steadfast balance endpoint.
     */
    public function test(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'steadfast_api_key' => 'nullable|string|max:255',
            'steadfast_secret_key' => 'nullable|string|max:255',
            'steadfast_base_url' => 'nullable|string|max:255',
        ]);

        $result = SteadfastService::testConnection(
            $validated['steadfast_api_key'] ?? null,
            $validated['steadfast_secret_key'] ?? null,
            $validated['steadfast_base_url'] ?? null,
        );

        return response()->json(['message' => $result['message']], $result['ok'] ? 200 : 422);
    }
}
