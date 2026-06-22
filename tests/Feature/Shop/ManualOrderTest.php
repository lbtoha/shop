<?php

use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;

function adminUser(): Admin
{
    // Null role → superuser, allowed through AdminAuthMiddleware.
    return Admin::create([
        'first_name' => 'Super',
        'last_name' => 'Admin',
        'email' => 'admin-'.uniqid().'@example.com',
        'password' => bcrypt('password'),
        'admin_role_id' => null,
    ]);
}

function product(array $attributes = []): Product
{
    return Product::create(array_merge([
        'name' => 'Test Product',
        'slug' => 'test-product-'.uniqid(),
        'price' => 1000,
        'stock' => 10,
        'is_active' => true,
    ], $attributes));
}

it('creates a manual order, decrements stock, and marks the source', function () {
    $p = product(['stock' => 5, 'price' => 1200]);

    $response = $this->actingAs(adminUser(), 'admin')
        ->post(route('admin.orders.store'), [
            'customer_name' => 'WhatsApp Customer',
            'customer_phone' => '01710000000',
            'shipping_address' => '123 Test Road, Dhaka',
            'shipping_cost' => 60,
            'items' => [
                ['product_id' => $p->id, 'quantity' => 2],
            ],
        ]);

    $response->assertOk()->assertJsonStructure(['message', 'redirect']);

    $order = Order::firstOrFail();

    expect($order->source)->toBe('manual')
        ->and($order->payment_method)->toBe('cash_on_delivery')
        ->and((float) $order->subtotal)->toBe(2400.0)
        ->and((float) $order->shipping_cost)->toBe(60.0)
        ->and((float) $order->total)->toBe(2460.0)
        ->and($order->items)->toHaveCount(1);

    // Stock decremented from 5 to 3.
    expect($p->fresh()->stock)->toBe(3);
});

it('rejects a manual order with no items', function () {
    $this->actingAs(adminUser(), 'admin')
        ->postJson(route('admin.orders.store'), [
            'customer_name' => 'No Items',
            'customer_phone' => '01710000000',
            'shipping_address' => 'Somewhere',
            'items' => [],
        ])
        ->assertStatus(422);
});

it('rejects a manual order that exceeds stock', function () {
    $p = product(['stock' => 1]);

    $this->actingAs(adminUser(), 'admin')
        ->postJson(route('admin.orders.store'), [
            'customer_name' => 'Too Many',
            'customer_phone' => '01710000000',
            'shipping_address' => 'Somewhere',
            'items' => [
                ['product_id' => $p->id, 'quantity' => 5],
            ],
        ])
        ->assertStatus(422);

    // Stock untouched because the transaction rolled back.
    expect($p->fresh()->stock)->toBe(1);
    expect(Order::count())->toBe(0);
});

it('product search returns matching active products as json', function () {
    product(['name' => 'Bluetooth Speaker']);
    product(['name' => 'Cotton Shirt']);

    $this->actingAs(adminUser(), 'admin')
        ->getJson(route('admin.orders.product-search', ['q' => 'Bluetooth']))
        ->assertOk()
        ->assertJsonFragment(['name' => 'Bluetooth Speaker'])
        ->assertJsonMissing(['name' => 'Cotton Shirt']);
});
