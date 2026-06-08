<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Traits\MediaUploader;
use Illuminate\Http\Request;

class AppController extends Controller
{
    use MediaUploader;
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

        $settings = array_merge(config('application_info'), [
            'timezone' => config('app.timezone'),
            'mobile_app_key' => config('app.mobile_app_key'),
        ]);

        return view('admin.pages.settings.general.index', compact('settings', 'buttons'));
    }

    public function infoUpdate(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'company_name' => 'required',
            'company_email' => 'required|email',
            'phone' => 'required',
            'company_website' => 'required',
            'address.country' => 'required',
            'address.state' => 'required',
            'address.city' => 'required',
            'address.postal_code' => 'required',
            'address.address' => 'required',
            'address.location' => 'required',
            'timezone' => 'required',
            'otp_duration' => 'required',
            'otp_range' => 'required|array',
            'frontend_url' => 'required|url',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'referral_joining_fee' => 'required|numeric|min:0',
            'footer_text' => 'nullable|string',
            'auth_left_sidebar_image' => 'nullable|string',
            'android_link' => 'nullable|url',
            'ios_link' => 'nullable|url',
            'android_app_file' => 'nullable|file',
            'ios_app_file' => 'nullable|file',
            'mobile_app_key' => 'nullable|string',
        ]);

        $validated['frontend_url'] = rtrim($validated['frontend_url'], '/');

        storeOption([
            'company_info' => [
                'company_info' => ['name' => $validated['company_name'],
                    'email' => $validated['company_email'],
                    'phone' => $validated['phone'],
                    'website' => $validated['company_website'],
                ],
                'address' => $validated['address'],
                'timezone' => $validated['timezone'],
                'frontend_url' => $validated['frontend_url'],
                'otp' => [
                    'expire_time' => $validated['otp_duration'],
                    'digit_range' => $validated['otp_range'],
                ],
                'theme' => [
                    'primary_color' => $validated['primary_color'] ?? '#6366f1',
                    'secondary_color' => $validated['secondary_color'] ?? '#00D94A',
                ],
                'referral' => [
                    'joining' => $validated['referral_joining_fee'],
                ],
                'footer_text' => $validated['footer_text'],
                'auth_left_sidebar_image' => $validated['auth_left_sidebar_image'],
                'mobile_app' => [
                    'android' => [
                        'link' => $request->hasFile('android_app_file') ? $this->upload($request->file('android_app_file'), 'apps') : $validated['android_link'],
                        'icon' => config('application_info.mobile_app.android.icon'),
                    ],
                    'ios' => [
                        'link' => $request->hasFile('ios_app_file') ? $this->upload($request->file('ios_app_file'), 'apps') : $validated['ios_link'],
                        'icon' => config('application_info.mobile_app.ios.icon'),
                    ],
                ],
                'mobile_app_key' => $validated['mobile_app_key'],
            ],
        ]);

        return response()->json(['message' => __('Settings updated successfully'), 'reload' => true]);
    }
}
