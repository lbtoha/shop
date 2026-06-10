<?php

namespace App\Services\Ecommerce;

use App\Enums\NotificationType;
use App\Models\Order;
use App\Notifications\UserAutoNotification;
use App\Services\Notification\OrderMessages;
use App\Services\Notification\SmsGateway;
use App\Services\Notification\WhatsAppCloud;
use Illuminate\Support\Facades\Notification;

/**
 * Notifies the customer about order events across every enabled channel:
 * email (NotificationTemplate), SMS (generic HTTP gateway), and WhatsApp
 * (Meta Cloud API). Each channel is independent and failure-isolated so a
 * misconfigured channel never breaks checkout or the admin flow.
 */
class OrderNotifier
{
    public static function orderPlaced(Order $order): void
    {
        self::email($order, NotificationType::ORDER_PLACED, [
            'full_name' => $order->customer_name,
            'email' => $order->customer_email,
            'phone' => $order->customer_phone,
            'order_number' => $order->order_number,
            'order_total' => amountWithSymbol($order->total),
        ]);

        $text = OrderMessages::placed($order);
        self::sms($order, $text);
        self::whatsapp($order, $text);
    }

    public static function statusUpdated(Order $order): void
    {
        self::email($order, NotificationType::ORDER_STATUS_UPDATED, [
            'full_name' => $order->customer_name,
            'email' => $order->customer_email,
            'phone' => $order->customer_phone,
            'order_number' => $order->order_number,
            'order_status' => $order->status_name,
        ]);

        $text = OrderMessages::statusUpdated($order);
        self::sms($order, $text);
        self::whatsapp($order, $text);
    }

    private static function email(Order $order, NotificationType $type, array $variables): void
    {
        if (! $order->customer_email) {
            return;
        }

        try {
            Notification::route('mail', $order->customer_email)
                ->notify(new UserAutoNotification($type, $variables));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private static function sms(Order $order, string $message): void
    {
        if (! $order->customer_phone || ! SmsGateway::isEnabled()) {
            return;
        }

        try {
            SmsGateway::send($order->customer_phone, $message);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private static function whatsapp(Order $order, string $message): void
    {
        if (! $order->customer_phone || ! WhatsAppCloud::isEnabled()) {
            return;
        }

        try {
            WhatsAppCloud::send($order->customer_phone, $message);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
