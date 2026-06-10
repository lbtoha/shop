<?php

namespace App\Services\Notification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhatsApp Cloud API (Meta) text sender.
 *
 * Configured via the `whatsapp_cloud` option (JSON):
 *   {
 *     "is_enabled": true,
 *     "token": "EAAB...",
 *     "phone_number_id": "1234567890",
 *     "api_version": "v21.0"
 *   }
 *
 * Note: outside the 24h customer-service window, Meta only delivers
 * pre-approved template messages. Free-form text used here works when the
 * customer has recently messaged the business; otherwise switch to a template.
 */
class WhatsAppCloud
{
    public static function config(): array
    {
        return getOptionWithJsonDecode('whatsapp_cloud', []) ?: [];
    }

    public static function isEnabled(): bool
    {
        $c = self::config();

        return ! empty($c['is_enabled']) && ! empty($c['token']) && ! empty($c['phone_number_id']);
    }

    public static function send(string $to, string $message): bool
    {
        if (! self::isEnabled()) {
            return false;
        }

        $c = self::config();
        $version = $c['api_version'] ?? 'v21.0';
        $url = "https://graph.facebook.com/{$version}/{$c['phone_number_id']}/messages";

        // E.164-ish: digits only (Meta expects no '+' or separators).
        $number = preg_replace('/[^0-9]/', '', $to);

        try {
            $response = Http::withToken($c['token'])
                ->timeout(15)
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'to' => $number,
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('WhatsApp Cloud non-2xx', ['status' => $response->status(), 'body' => $response->body()]);

            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp Cloud error: '.$e->getMessage());

            return false;
        }
    }
}
