<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function __invoke(Request $request)
    {
        adminUserHasPermission(permission: 'edit');
        $validated = $request->validate([
            'key' => 'required',
            'value' => 'required',
        ]);

        storeOption([
            $validated['key'] => is_array($validated['value']) ? json_encode($validated['value']) : $validated['value'],
        ]);

        return response()->json([
            'message' => __('Settings updated successfully'),
            'reload' => true,
        ]);
    }
}
