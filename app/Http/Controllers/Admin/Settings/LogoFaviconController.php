<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoFaviconController extends Controller
{
    public function index(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        return view('admin.pages.settings.general.logo-favicon.index');
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'logo_light' => 'required',
            'logo_dark' => 'required',
            'favicon' => 'required',
        ]);
        storeOption([
            'logo_favicon' => [
                'logo_light' => $validated['logo_light'],
                'logo_dark' => $validated['logo_dark'],
                'favicon' => $validated['favicon'],
            ],
        ]);

        return response()->json(['message' => __('Settings updated successfully')]);
    }
}
