<?php

use App\Models\Product;
use Tests\Feature\Shop\PaymentSettingsHelper;

uses(PaymentSettingsHelper::class);

function makeProduct(array $attributes = []): Product
{
    return Product::create(array_merge([
        'name' => 'Wireless Earbuds',
        'slug' => 'wireless-earbuds-'.uniqid(),
        'price' => 1500,
        'stock' => 10,
        'is_active' => true,
    ], $attributes));
}

it('hides the WhatsApp button when disabled', function () {
    $this->setOptions(['whatsapp_enabled' => 0, 'whatsapp_number' => '8801710733329']);
    $product = makeProduct();

    $this->get(route('shop.product', $product->slug))
        ->assertOk()
        ->assertDontSee('wa.me/8801710733329');
});

it('shows the WhatsApp button when enabled with a number', function () {
    $this->setOptions(['whatsapp_enabled' => 1, 'whatsapp_number' => '8801710733329']);
    $product = makeProduct();

    $this->get(route('shop.product', $product->slug))
        ->assertOk()
        ->assertSee('wa.me/8801710733329');
});

it('hides the contact button when enabled but the number is blank', function () {
    $this->setOptions(['whatsapp_enabled' => 1, 'whatsapp_number' => '']);
    $product = makeProduct();

    // The "share to WhatsApp" link (wa.me/?text=) always exists; what must be
    // absent is the floating contact button, identified by its aria-label.
    $this->get(route('shop.product', $product->slug))
        ->assertOk()
        ->assertDontSee('Contact us on WhatsApp');
});

it('pre-fills the WhatsApp message with the product name', function () {
    $this->setOptions(['whatsapp_enabled' => 1, 'whatsapp_number' => '8801710733329']);
    $product = makeProduct(['name' => 'Smart Watch']);

    $this->get(route('shop.product', $product->slug))
        ->assertOk()
        ->assertSee(rawurlencode('Hi, I am interested in Smart Watch'), false);
});
