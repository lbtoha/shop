<?php

namespace App\Services\Tracking;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokCapiService
{
    /**
     * Determine if the Conversions API is fully configured and enabled.
     */
    public static function isEnabled(): bool
    {
        $config = config('extension.tiktok_pixel');
        
        return !empty($config['is_enabled']) && 
               !empty($config['pixel_id']) && 
               !empty($config['access_token']);
    }

    /**
     * Send a CompletePayment event to the TikTok Events API.
     */
    public static function sendCompletePayment(Order $order): void
    {
        if (!self::isEnabled()) {
            return;
        }

        $config = config('extension.tiktok_pixel');
        $pixelId = $config['pixel_id'];
        $accessToken = $config['access_token'];
        $testCode = $config['test_event_code'] ?? null;

        // Format user details (TikTok requires SHA-256 hashed lowercase trimmed values)
        $userData = [];
        
        if ($order->customer_email) {
            $userData['email'] = self::hashValue($order->customer_email);
        }
        
        if ($order->customer_phone) {
            $userData['phone_number'] = self::hashPhone($order->customer_phone);
        }

        // Format custom event items data
        $contents = [];
        foreach ($order->items as $item) {
            $contents[] = [
                'content_id' => (string) $item->product_id,
                'content_name' => (string) $item->product_name,
                'quantity' => (int) $item->quantity,
                'price' => (float) $item->price
            ];
        }

        $properties = [
            'currency' => getOption('currency_code', 'BDT'),
            'value' => (float) $order->total,
            'contents' => $contents
        ];

        // Format the event data object
        $eventData = [
            'event' => 'CompletePayment',
            'event_time' => time(),
            'event_id' => $order->order_number, // Must match client event ID for deduplication
            'user' => $userData,
            'properties' => $properties,
        ];

        // Format complete payload
        $payload = [
            'event_source' => 'web',
            'event_source_id' => $pixelId,
            'data' => [$eventData]
        ];

        if (!empty($testCode)) {
            $payload['test_event_code'] = $testCode;
        }

        try {
            $response = Http::timeout(3)->withHeaders([
                'Access-Token' => $accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://business-api.tiktok.com/open_api/v1.3/event/track/', $payload);

            if ($response->failed()) {
                Log::error('TikTok Events API Error: ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('TikTok Events API Exception: ' . $e->getMessage());
        }
    }

    /**
     * SHA-256 hash formatting helper.
     */
    private static function hashValue(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        return hash('sha256', strtolower(trim($value)));
    }

    /**
     * Bangladesh country-code formatted phone hashing helper.
     */
    private static function hashPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($clean) === 11 && str_starts_with($clean, '01')) {
            $clean = '88' . $clean;
        }
        return hash('sha256', $clean);
    }
}
