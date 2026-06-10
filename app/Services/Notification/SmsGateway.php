<?php

namespace App\Services\Notification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Generic HTTP SMS gateway.
 *
 * Configured via the `sms_gateway` option (JSON), so it works with most
 * Bangladeshi/global providers that expose a simple HTTP API:
 *   {
 *     "is_enabled": true,
 *     "method": "GET" | "POST",
 *     "url": "https://api.provider.com/sendsms",
 *     "params": { "api_key": "xxx", "senderid": "8809...", "number": "{to}", "message": "{message}" }
 *   }
 *
 * The literal tokens {to} and {message} in the params are replaced per send.
 */
class SmsGateway
{
    public static function config(): array
    {
        return getOptionWithJsonDecode('sms_gateway', []) ?: [];
    }

    public static function isEnabled(): bool
    {
        $c = self::config();

        return ! empty($c['is_enabled']) && ! empty($c['url']);
    }

    /**
     * Send an SMS. Returns true on a 2xx response, false otherwise.
     */
    public static function send(string $to, string $message): bool
    {
        if (! self::isEnabled()) {
            return false;
        }

        $c = self::config();
        $method = strtoupper($c['method'] ?? 'GET');
        $url = $c['url'];

        $params = collect($c['params'] ?? [])
            ->map(fn ($v) => str_replace(['{to}', '{message}'], [$to, $message], (string) $v))
            ->toArray();

        try {
            $response = $method === 'POST'
                ? Http::asForm()->timeout(15)->post($url, $params)
                : Http::timeout(15)->get($url, $params);

            if ($response->successful()) {
                return true;
            }

            Log::warning('SMS gateway non-2xx', ['status' => $response->status(), 'body' => $response->body()]);

            return false;
        } catch (\Throwable $e) {
            Log::error('SMS gateway error: '.$e->getMessage());

            return false;
        }
    }
}
