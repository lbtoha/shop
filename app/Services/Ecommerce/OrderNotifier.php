<?php

namespace App\Services\Ecommerce;

use App\Enums\NotificationType;
use App\Models\Order;
use App\Notifications\UserAutoNotification;
use Illuminate\Support\Facades\Notification;

/**
 * Sends order-related emails to the customer using the existing
 * NotificationTemplate / UserAutoNotification (email-only) infrastructure.
 *
 * Guests have no User record, so we route the notification to the email
 * captured on the order via Notification::route('mail', ...).
 */
class OrderNotifier
{
    public static function orderPlaced(Order $order): void
    {
        if (! $order->customer_email) {
            return;
        }

        self::send($order, NotificationType::ORDER_PLACED, [
            'full_name' => $order->customer_name,
            'email' => $order->customer_email,
            'phone' => $order->customer_phone,
            'order_number' => $order->order_number,
            'order_total' => amountWithSymbol($order->total),
        ]);
    }

    public static function statusUpdated(Order $order): void
    {
        if (! $order->customer_email) {
            return;
        }

        self::send($order, NotificationType::ORDER_STATUS_UPDATED, [
            'full_name' => $order->customer_name,
            'email' => $order->customer_email,
            'phone' => $order->customer_phone,
            'order_number' => $order->order_number,
            'order_status' => $order->status_name,
        ]);
    }

    private static function send(Order $order, NotificationType $type, array $variables): void
    {
        try {
            Notification::route('mail', $order->customer_email)
                ->notify(new UserAutoNotification($type, $variables));
        } catch (\Throwable $e) {
            // Never let a mail failure break the checkout / admin flow.
            report($e);
        }
    }
}
