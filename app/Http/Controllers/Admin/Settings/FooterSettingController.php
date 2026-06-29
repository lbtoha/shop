<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class FooterSettingController extends Controller
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

        $settings = config('application_info');
        $menus = Menu::active()->get();

        return view('admin.pages.settings.footer.index', compact('settings', 'buttons', 'menus'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'footer_text' => 'nullable|string',
            'footer_menu_id' => 'nullable|exists:menus,id',
        ]);

        $app_info = getOptionWithJsonDecode('company_info', config('application_info'));
        $app_info['footer_text'] = $validated['footer_text'] ?? '';
        $app_info['footer_menu_id'] = $validated['footer_menu_id'] ?? null;

        storeOption([
            'company_info' => $app_info,
        ]);

        return response()->json(['message' => __('Settings updated successfully'), 'reload' => true]);
    }
}
