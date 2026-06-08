<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemConfigurationController extends Controller
{
    public function index(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-arrow-clockwise',
                'type' => 'link',
                'link' => route('admin.settings.index'),
            ],
            [
                'label' => __('System Pagination Settings'),
                'icon' => 'ph ph-dots-three-outline',
                'type' => 'link',
                'link' => route('admin.settings.system-configurations.pagination.index'),
            ],
        ];

        $system_configurations = config('extra_service.system_config');

        return view('admin.pages.settings.system-configuration.index', compact('system_configurations', 'buttons'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $config = config('extra_service.system_config');

        $rules = [];

        foreach ($config as $key => $item) {
            $rules[$key] = 'required|in:0,1';
        }

        $validated = $request->validate($rules);

        $updatedConfig = [];

        foreach ($config as $key => $item) {
            $updatedConfig[$key] = [
                ...$item,
                'is_enabled' => $validated[$key] == 1,
            ];
        }

        storeOption([
            'extra_service_system_config' => $updatedConfig,
        ]);

        return response()->json(['message' => __('Settings updated successfully'), 'reload' => true]);
    }

    public function pagination(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $buttons = [
            [
                'label' => __('Back'),
                'icon' => 'ph ph-arrow-clockwise',
                'type' => 'link',
                'link' => route('admin.settings.system-configurations.index'),
            ],
        ];

        return view('admin.pages.settings.system-configuration.pagination', compact('buttons'));
    }

    public function storePagination(Request $request)
    {
        adminUserHasPermission(permission: 'edit');
        $validated = $request->validate([
            'per_page' => 'required',
            'sort_type' => 'required',
        ]);

        storeOption([
            'extra_service_pagination' => $validated,
        ]);

        return response()->json(['message' => __('Settings updated successfully'), 'reload' => true]);
    }
}
