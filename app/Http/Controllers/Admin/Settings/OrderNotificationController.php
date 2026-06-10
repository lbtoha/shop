<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderNotificationController extends Controller
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
            'email_enabled' => (bool) config('extra_service.system_config.email_notification.is_enabled'),
            'sms' => getOptionWithJsonDecode('sms_gateway', []) ?: [],
            'whatsapp' => getOptionWithJsonDecode('whatsapp_cloud', []) ?: [],
            'messages' => getOptionWithJsonDecode('order_messages', []) ?: [],
        ];

        return view('admin.pages.settings.order-notifications.index', compact('settings', 'buttons'));
    }

    public function store(Request $request)
    {
        adminUserHasPermission(permission: 'edit');

        $validated = $request->validate([
            'email_enabled' => 'nullable|boolean',

            'sms_enabled' => 'nullable|boolean',
            'sms_method' => 'nullable|in:GET,POST',
            'sms_url' => 'nullable|url',
            'sms_params' => 'nullable|string', // raw "key=value" lines

            'wa_enabled' => 'nullable|boolean',
            'wa_token' => 'nullable|string',
            'wa_phone_number_id' => 'nullable|string',
            'wa_api_version' => 'nullable|string|max:10',

            'msg_placed' => 'nullable|string|max:1000',
            'msg_status' => 'nullable|string|max:1000',
        ]);

        // Email toggle lives in the extra_service system_config option group
        // (loaded into config('extra_service.system_config') by FlyServiceProvider).
        $systemConfig = getOptionWithJsonDecode('extra_service_system_config', config('extra_service.system_config')) ?: [];
        $systemConfig['email_notification']['is_enabled'] = (bool) ($validated['email_enabled'] ?? false);
        storeOption(['extra_service_system_config' => $systemConfig]);

        // SMS gateway
        storeOption([
            'sms_gateway' => [
                'is_enabled' => (bool) ($validated['sms_enabled'] ?? false),
                'method' => $validated['sms_method'] ?? 'GET',
                'url' => $validated['sms_url'] ?? '',
                'params' => $this->parseParams($validated['sms_params'] ?? ''),
            ],
        ]);

        // WhatsApp Cloud
        storeOption([
            'whatsapp_cloud' => [
                'is_enabled' => (bool) ($validated['wa_enabled'] ?? false),
                'token' => $validated['wa_token'] ?? '',
                'phone_number_id' => $validated['wa_phone_number_id'] ?? '',
                'api_version' => ($validated['wa_api_version'] ?? '') ?: 'v21.0',
            ],
        ]);

        // Message templates
        storeOption([
            'order_messages' => [
                'placed' => $validated['msg_placed'] ?? '',
                'status' => $validated['msg_status'] ?? '',
            ],
        ]);

        return response()->json(['message' => __('Notification settings updated.'), 'reload' => true]);
    }

    /**
     * Parse "key=value" newline-separated text into a params map.
     */
    private function parseParams(string $raw): array
    {
        $params = [];
        foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
            $line = trim($line);
            if ($line === '' || ! str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            if ($key !== '') {
                $params[$key] = trim($value);
            }
        }

        return $params;
    }
}
