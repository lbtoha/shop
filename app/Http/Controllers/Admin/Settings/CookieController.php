<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CookieController extends Controller
{
    public function index()
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

        $gdpr_cookies = getOptionWithJsonDecode('gdpr_cookies', [
            'is_enabled' => false,
            'title' => '',
            'description' => '',
        ]);

        return view('admin.pages.settings.gdpr-cookie.index', compact('gdpr_cookies', 'buttons'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');
        $validated = $request->validate([
            'is_enabled' => 'required',
            'title' => 'required',
            'description' => 'required',
        ]);

        storeOption([
            'gdpr_cookies' => [
                ...$validated,
                'is_enabled' => $validated['is_enabled'] == 'true' ? true : false,
            ],
        ]);

        return response()->json(['message' => __('Settings updated successfully'), 'reload' => true]);
    }
}
