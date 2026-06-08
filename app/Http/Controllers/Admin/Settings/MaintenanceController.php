<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenance = getOptionWithJsonDecode('maintenance_mode', []);
        return view('admin.pages.settings.maintenance.index', compact('maintenance'));
    }
    public function store(Request $request)
    {
        adminUserHasPermission('edit');
        $validated = $request->validate([
            'image' => 'required',
            'countdown' => 'required',
            'login_secret_key' => 'required',
            'description' => 'required',
            'status' => 'required',
        ]);
        storeOption(options: [
            'maintenance_mode' => [
                ...$validated,
                'secret_key' => $validated['login_secret_key'],
            ],
        ]);
        if ($validated['status'] == 1) {
            Artisan::call('down', [
                '--secret' => $validated['login_secret_key'],
            ]);
            return response()->json(['message' => __('Maintenance mode activated.'), 'reload' => true]);
        }
        if ($validated['status'] == 0) {
            Artisan::call('up');
            return response()->json(['message' => __('Maintenance mode deactivated.'), 'reload' => true]);
        }
        return response()->json(['message' => __('Maintenance mode updated.'), 'reload' => true]);
    }
}
