<?php

use App\Models\Order;
use App\Enums\OrderStatusEnum;
use App\Enums\OrderPaymentStatusEnum;
use Tests\Feature\Shop\PaymentSettingsHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(PaymentSettingsHelper::class, RefreshDatabase::class);

function createTestOrder(array $attributes = []): Order
{
    return Order::create(array_merge([
        'order_number' => '20260623-123456',
        'customer_name' => 'John Doe',
        'customer_phone' => '01712345678',
        'customer_email' => 'john@example.com',
        'shipping_address' => '123 Main St',
        'city' => 'Dhaka',
        'zip_code' => '1200',
        'status' => OrderStatusEnum::PENDING->value,
        'payment_method' => 'cod',
        'payment_status' => OrderPaymentStatusEnum::UNPAID->value,
        'subtotal' => 1000,
        'shipping_cost' => 60,
        'total' => 1060,
    ], $attributes));
}

it('can display the order tracking search page', function () {
    $this->get(route('shop.track'))
        ->assertOk()
        ->assertSee('Track Your Order')
        ->assertSee('Order Number')
        ->assertSee('Billing Phone');
});

it('can track a valid order with exact matching phone number', function () {
    $order = createTestOrder([
        'order_number' => '20260623-100001',
        'customer_phone' => '01712345678',
    ]);

    $this->get(route('shop.track', [
        'order_number' => '20260623-100001',
        'phone' => '01712345678'
    ]))
        ->assertOk()
        ->assertSee('Order')
        ->assertSee('#20260623-100001')
        ->assertSee('Placed')
        ->assertSee('John Doe');
});

it('can track a valid order with phone number containing spaces or symbols', function () {
    $order = createTestOrder([
        'order_number' => '20260623-100002',
        'customer_phone' => '+88 017-1234-5678',
    ]);

    $this->get(route('shop.track', [
        'order_number' => '20260623-100002',
        'phone' => '01712345678'
    ]))
        ->assertOk()
        ->assertSee('Order')
        ->assertSee('#20260623-100002')
        ->assertSee('John Doe');
});

it('shows an error when the phone number does not match', function () {
    $order = createTestOrder([
        'order_number' => '20260623-100003',
        'customer_phone' => '01712345678',
    ]);

    $this->get(route('shop.track', [
        'order_number' => '20260623-100003',
        'phone' => '01899999999'
    ]))
        ->assertOk()
        ->assertSee('The phone number provided does not match our records');
});

it('shows an error when the order number is not found', function () {
    $this->get(route('shop.track', [
        'order_number' => 'NONEXISTENT',
        'phone' => '01712345678'
    ]))
        ->assertOk()
        ->assertSee('No order was found with that order number');
});
