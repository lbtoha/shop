<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthSettingController extends Controller
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
            'signup_otp_enabled' => getOption('signup_otp_enabled', 0),
        ];

        return view('admin.pages.settings.auth.index', compact('settings', 'buttons'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'signup_otp_enabled' => 'required|in:0,1',
        ]);

        storeOption([
            'signup_otp_enabled' => $validated['signup_otp_enabled'],
        ]);

        return response()->json(['message' => __('Customer auth settings updated.'), 'reload' => true]);
    }
}
