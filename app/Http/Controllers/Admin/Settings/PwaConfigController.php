<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PwaConfigController extends Controller
{
    public function index(Request $request)
    {
        adminUserHasPermission(permission: 'read');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-arrow-clockwise',
                'type' => 'link',
                'link' => route('admin.settings.index'),
            ],
        ];

        $pwa = config('pwa');

        return view('admin.pages.settings.pwa-configuration.index', compact('buttons', 'pwa'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'background_color' => 'required',
            'theme_color' => 'nullable|string',
            'icons' => 'nullable|array',
            'icons.*.src' => 'nullable|string',
            'screenshots' => 'nullable|array',
            'screenshots.*.src' => 'nullable|string',
        ]);

        if (isset($validated['icons']) && count($validated['icons']) > 0) {
            $icons = config('pwa.icons');

            foreach ($validated['icons'] as $key => $icon) {
                $icons[$key] = [
                    'src' => $icon['src'],
                    'sizes' => $key == 0 ? '192x192' : '512x512',
                    'type' => 'image/png',
                ];
            }

            $validated['icons'] = $icons;
        }

        if (isset($validated['screenshots']) && count($validated['screenshots']) > 0) {
            $screenshots = config('pwa.icons');

            foreach ($validated['screenshots'] as $key => $screenshot) {
                $screenshots[$key] = [
                    'src' => $screenshot['src'],
                    'type' => 'image/png',
                    'form_factor' => $key == 0 ? 'wide' : 'narrow',
                    'sizes' => $key == 0 ? '1366x768' : '400x800',
                    'label' => $key == 0 ? 'Desktop view' : 'Mobile view',
                ];
            }

            $validated['screenshots'] = $screenshots;
        }

        storeOption([
            'pwa_config' => $validated,
        ]);

        return response()->json(['message' => __('Settings updated successfully'), 'reload' => true]);
    }
}
