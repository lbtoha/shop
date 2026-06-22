<?php

use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Services\Payment\SslCommerzService;
use Tests\Feature\Shop\PaymentSettingsHelper;

uses(PaymentSettingsHelper::class);

function makeOrder(array $attributes = []): Order
{
    return Order::create(array_merge([
        'order_number' => 'ORD'.uniqid(),
        'customer_name' => 'Jane Doe',
        'customer_phone' => '01710000000',
        'shipping_address' => '123 Test Road',
        'payment_method' => 'sslcommerz',
        'payment_status' => OrderPaymentStatusEnum::UNPAID->value,
        'status' => OrderStatusEnum::PENDING->value,
        'subtotal' => 1000,
        'total' => 1000,
    ], $attributes));
}

it('forbids retrying an order the visitor does not own', function () {
    $this->enableSslcommerz();
    $order = makeOrder();

    // No session ownership, not logged in, not admin.
    $this->get(route('shop.payment.sslcommerz.retry', $order->order_number))
        ->assertForbidden();
});

it('lets the session buyer retry and redirects to the gateway', function () {
    $this->enableSslcommerz();

    $this->mock(SslCommerzService::class, function ($mock) {
        $mock->shouldReceive('initiate')->once()->andReturn('https://sandbox.sslcommerz.com/PAY/retry1');
    });

    $order = makeOrder();

    // Simulate the session that placed the order.
    $this->withSession(['confirmed_order' => $order->order_number])
        ->get(route('shop.payment.sslcommerz.retry', $order->order_number))
        ->assertRedirect('https://sandbox.sslcommerz.com/PAY/retry1');
});

it('does not allow retrying an already paid order', function () {
    $this->enableSslcommerz();
    $order = makeOrder(['payment_status' => OrderPaymentStatusEnum::PAID->value]);

    $this->withSession(['confirmed_order' => $order->order_number])
        ->get(route('shop.payment.sslcommerz.retry', $order->order_number))
        ->assertRedirect(route('shop.checkout.confirmation', $order->order_number))
        ->assertSessionHas('error');
});

it('does not allow retrying a cancelled order', function () {
    $this->enableSslcommerz();
    $order = makeOrder(['status' => OrderStatusEnum::CANCELLED->value]);

    $this->withSession(['confirmed_order' => $order->order_number])
        ->get(route('shop.payment.sslcommerz.retry', $order->order_number))
        ->assertRedirect(route('shop.checkout.confirmation', $order->order_number))
        ->assertSessionHas('error');
});

it('does not allow retrying a cash-on-delivery order', function () {
    $this->enableSslcommerz();
    $order = makeOrder(['payment_method' => 'cash_on_delivery']);

    $this->withSession(['confirmed_order' => $order->order_number])
        ->get(route('shop.payment.sslcommerz.retry', $order->order_number))
        ->assertRedirect(route('shop.checkout.confirmation', $order->order_number))
        ->assertSessionHas('error');
});
