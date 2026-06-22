<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AiSettingController extends Controller
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
            'ai_tryon_enabled' => getOption('ai_tryon_enabled', 0),
            'ai_tryon_api_key' => getOption('ai_tryon_api_key', ''),
            'ai_tryon_model' => getOption('ai_tryon_model', config('services.gemini.model', 'gemini-3.1-flash-image')),
        ];

        return view('admin.pages.settings.ai.index', compact('settings', 'buttons'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'ai_tryon_enabled' => 'required|in:0,1',
            'ai_tryon_api_key' => 'nullable|string|max:255',
            'ai_tryon_model' => 'nullable|string|max:100',
        ]);

        // An API key is required to switch the feature on (unless one is in .env).
        if ((int) $validated['ai_tryon_enabled'] === 1
            && blank($validated['ai_tryon_api_key'])
            && blank(config('services.gemini.api_key'))) {
            return response()->json([
                'errors' => ['ai_tryon_api_key' => [__('A Gemini API key is required to enable the try-on.')]],
                'message' => __('A Gemini API key is required to enable the try-on.'),
            ], 422);
        }

        storeOption([
            'ai_tryon_enabled' => $validated['ai_tryon_enabled'],
            'ai_tryon_api_key' => $validated['ai_tryon_api_key'] ?? '',
            'ai_tryon_model' => $validated['ai_tryon_model'] ?: 'gemini-3.1-flash-image',
        ]);

        return response()->json(['message' => __('AI settings updated.'), 'reload' => true]);
    }
}
