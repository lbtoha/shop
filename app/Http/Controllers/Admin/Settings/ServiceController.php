<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
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

        return view('admin.pages.settings.services.index', compact('buttons'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'currencylayer_access_key' => 'required',
        ]);

        storeOption([
            'currencylayer_access_key' => $validated['currencylayer_access_key'],
        ]);

        return response()->json([
            'message' => __('Settings updated successfully'),
        ]);
    }
}
