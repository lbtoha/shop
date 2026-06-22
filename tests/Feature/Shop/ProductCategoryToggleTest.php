<?php

use App\Models\Category;
use App\Models\Product;
use Tests\Feature\Shop\PaymentSettingsHelper;

uses(PaymentSettingsHelper::class);

function categorisedProduct(): Product
{
    $category = Category::create([
        'name' => 'Gadgets',
        'slug' => 'gadgets-'.uniqid(),
        'is_active' => true,
    ]);

    return Product::create([
        'name' => 'Cool Gadget',
        'slug' => 'cool-gadget-'.uniqid(),
        'price' => 999,
        'stock' => 5,
        'is_active' => true,
        'category_id' => $category->id,
    ]);
}

it('shows the category link when the toggle is on', function () {
    $this->setOptions(['show_product_category' => 1]);
    $product = categorisedProduct();

    $this->get(route('shop.product', $product->slug))
        ->assertOk()
        ->assertSee('CATEGORY', false);
});

it('hides the category link when the toggle is off', function () {
    $this->setOptions(['show_product_category' => 0]);
    $product = categorisedProduct();

    $this->get(route('shop.product', $product->slug))
        ->assertOk()
        ->assertDontSee('CATEGORY', false);
});
