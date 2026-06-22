<?php

use App\Enums\OrderPaymentStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Product;
use App\Services\Payment\SslCommerzService;
use Tests\Feature\Shop\PaymentSettingsHelper;

uses(PaymentSettingsHelper::class);

function cartProduct(array $attributes = []): Product
{
    return Product::create(array_merge([
        'name' => 'Test Product',
        'slug' => 'test-product-'.uniqid(),
        'price' => 1000,
        'stock' => 10,
        'is_active' => true,
    ], $attributes));
}

function checkoutPayload(array $overrides = []): array
{
    return array_merge([
        'customer_name' => 'Jane Doe',
        'customer_phone' => '01710000000',
        'shipping_address' => '123 Test Road, Dhaka',
        'city' => 'Dhaka',
        'shipping_area' => 'inside',
    ], $overrides);
}

it('places a cash-on-delivery order and redirects to confirmation', function () {
    $product = cartProduct();
    $this->post(route('shop.cart.add', $product->id), ['quantity' => 1]);

    $response = $this->post(route('shop.checkout.store'), checkoutPayload([
        'payment_method' => 'cash_on_delivery',
    ]));

    $order = Order::firstOrFail();

    expect($order->payment_method)->toBe('cash_on_delivery')
        ->and($order->payment_status)->toBe(OrderPaymentStatusEnum::UNPAID)
        ->and($order->status)->toBe(OrderStatusEnum::PENDING);

    $response->assertRedirect(route('shop.checkout.confirmation', $order->order_number));
});

it('falls back to COD when sslcommerz is chosen but the gateway is disabled', function () {
    // Gateway not enabled → the requested online method is ignored.
    $product = cartProduct();
    $this->post(route('shop.cart.add', $product->id), ['quantity' => 1]);

    $this->post(route('shop.checkout.store'), checkoutPayload([
        'payment_method' => 'sslcommerz',
    ]))->assertRedirect();

    expect(Order::firstOrFail()->payment_method)->toBe('cash_on_delivery');
});

it('redirects to the gateway when sslcommerz is chosen and enabled', function () {
    $this->enableSslcommerz();

    // Avoid a real network call: fake the gateway to return a known URL.
    $this->mock(SslCommerzService::class, function ($mock) {
        $mock->shouldReceive('initiate')->once()->andReturn('https://sandbox.sslcommerz.com/PAY/abc123');
    });

    $product = cartProduct();
    $this->post(route('shop.cart.add', $product->id), ['quantity' => 1]);

    $response = $this->post(route('shop.checkout.store'), checkoutPayload([
        'payment_method' => 'sslcommerz',
    ]));

    $order = Order::firstOrFail();

    // Order is recorded for online payment, still unpaid until the callback.
    expect($order->payment_method)->toBe('sslcommerz')
        ->and($order->payment_status)->toBe(OrderPaymentStatusEnum::UNPAID);

    $response->assertRedirect('https://sandbox.sslcommerz.com/PAY/abc123');
});

it('falls back to confirmation when the gateway hand-off throws', function () {
    $this->enableSslcommerz();

    $this->mock(SslCommerzService::class, function ($mock) {
        $mock->shouldReceive('initiate')->andThrow(new \Exception('gateway down'));
    });

    $product = cartProduct();
    $this->post(route('shop.cart.add', $product->id), ['quantity' => 1]);

    $order = null;
    $response = $this->post(route('shop.checkout.store'), checkoutPayload([
        'payment_method' => 'sslcommerz',
    ]));

    $order = Order::firstOrFail();

    // Order still exists; customer is sent to confirmation with an error notice.
    $response->assertRedirect(route('shop.checkout.confirmation', $order->order_number))
        ->assertSessionHas('error');
});
