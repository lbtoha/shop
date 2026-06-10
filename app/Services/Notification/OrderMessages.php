<?php

namespace App\Services\Notification;

use App\Models\Order;

/**
 * Builds the short text messages used for SMS / WhatsApp order notifications.
 *
 * Admins can override the templates via the `order_messages` option (JSON):
 *   { "placed": "...", "status": "..." }
 * with placeholders {name} {order} {total} {status} {site}.
 */
class OrderMessages
{
    public static function placed(Order $order): string
    {
        $template = self::template('placed', self::defaultPlaced());

        return self::fill($template, $order);
    }

    public static function statusUpdated(Order $order): string
    {
        $template = self::template('status', self::defaultStatus());

        return self::fill($template, $order);
    }

    /**
     * The wa.me click-to-send URL for the admin "Notify on WhatsApp" button.
     */
    public static function whatsappLink(Order $order, ?string $message = null): string
    {
        $number = preg_replace('/[^0-9]/', '', $order->customer_phone);
        $text = rawurlencode($message ?? self::statusUpdated($order));

        return "https://wa.me/{$number}?text={$text}";
    }

    private static function fill(string $template, Order $order): string
    {
        return strtr($template, [
            '{name}' => $order->customer_name,
            '{order}' => $order->order_number,
            '{total}' => amountWithSymbol($order->total),
            '{status}' => $order->status_name ?? ($order->status?->label() ?? ''),
            '{site}' => config('application_info.company_info.name', config('app.name')),
        ]);
    }

    private static function template(string $key, string $default): string
    {
        $messages = getOptionWithJsonDecode('order_messages', []) ?: [];

        return ! empty($messages[$key]) ? $messages[$key] : $default;
    }

    private static function defaultPlaced(): string
    {
        return 'Hi {name}, your order {order} ({total}) has been placed. Thank you for shopping with {site}!';
    }

    private static function defaultStatus(): string
    {
        return 'Hi {name}, your order {order} status is now: {status}. — {site}';
    }
}
