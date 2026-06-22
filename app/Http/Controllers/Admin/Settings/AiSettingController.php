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
            'ai_tryon_login_required' => getOption('ai_tryon_login_required', 0),
            'ai_tryon_per_hour' => getOption('ai_tryon_per_hour', 5),
            'ai_tryon_per_day' => getOption('ai_tryon_per_day', 20),
            'ai_tryon_daily_global' => getOption('ai_tryon_daily_global', 500),
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
            'ai_tryon_login_required' => 'required|in:0,1',
            'ai_tryon_per_hour' => 'required|integer|min:1|max:100',
            'ai_tryon_per_day' => 'required|integer|min:1|max:1000',
            'ai_tryon_daily_global' => 'required|integer|min:1|max:100000',
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
            'ai_tryon_login_required' => $validated['ai_tryon_login_required'],
            'ai_tryon_per_hour' => $validated['ai_tryon_per_hour'],
            'ai_tryon_per_day' => $validated['ai_tryon_per_day'],
            'ai_tryon_daily_global' => $validated['ai_tryon_daily_global'],
        ]);

        return response()->json(['message' => __('AI settings updated.'), 'reload' => true]);
    }

    /**
     * Live "Test Connection" — pings Gemini with the key/model currently in the
     * form (falling back to the saved values) so the admin can confirm it works
     * before relying on it. This makes one real, billed Gemini request.
     */
    public function test(Request $request, \App\Services\Ai\GeminiTryOnService $tryOn)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'ai_tryon_api_key' => 'nullable|string|max:255',
            'ai_tryon_model' => 'nullable|string|max:100',
        ]);

        $result = $tryOn->testConnection(
            $validated['ai_tryon_api_key'] ?? null,
            $validated['ai_tryon_model'] ?? null,
        );

        return response()->json(['message' => $result['message']], $result['ok'] ? 200 : 422);
    }
}
