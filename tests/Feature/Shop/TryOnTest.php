<?php

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Shop\PaymentSettingsHelper;

uses(PaymentSettingsHelper::class);

function tryOnProduct(array $attributes = []): Product
{
    return Product::create(array_merge([
        'name' => 'Linen Shirt',
        'slug' => 'linen-shirt-'.uniqid(),
        'price' => 1500,
        'stock' => 5,
        'is_active' => true,
        'thumbnail' => 'https://example.com/shirt.jpg',
    ], $attributes));
}

function enableTryOn(): void
{
    test()->setOptions([
        'ai_tryon_enabled' => 1,
        'ai_tryon_api_key' => 'AIzaTESTKEY',
        'ai_tryon_model' => 'gemini-3.1-flash-image',
    ]);
}

it('returns 422 when try-on is disabled', function () {
    $this->setOptions(['ai_tryon_enabled' => 0]);
    $product = tryOnProduct();

    $this->postJson(route('shop.product.try-on', $product->slug), [
        'photo' => UploadedFile::fake()->image('me.jpg'),
    ])->assertStatus(422);
});

it('validates that a photo is required', function () {
    enableTryOn();
    $product = tryOnProduct();

    $this->postJson(route('shop.product.try-on', $product->slug), [])
        ->assertStatus(422)
        ->assertJsonValidationErrors('photo');
});

it('generates a try-on image from the gemini response', function () {
    enableTryOn();
    Storage::fake('public');

    // Fake the product image fetch AND the Gemini generateContent call.
    Http::fake([
        'example.com/*' => Http::response('FAKE-PRODUCT-IMAGE-BYTES', 200, ['Content-Type' => 'image/jpeg']),
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [[
                'content' => ['parts' => [
                    ['text' => 'Here is your try-on.'],
                    ['inline_data' => ['mime_type' => 'image/png', 'data' => base64_encode('GENERATED-IMAGE-BYTES')]],
                ]],
            ]],
        ], 200),
    ]);

    $product = tryOnProduct();

    $response = $this->postJson(route('shop.product.try-on', $product->slug), [
        'photo' => UploadedFile::fake()->image('me.jpg', 600, 800),
    ]);

    $response->assertOk()
        ->assertJsonStructure(['message', 'image_url']);

    // A result file was written to the tryon/ directory.
    $files = Storage::disk('public')->files('tryon');
    expect($files)->toHaveCount(1);
    expect(Storage::disk('public')->get($files[0]))->toBe('GENERATED-IMAGE-BYTES');
});

it('surfaces a friendly error when gemini fails', function () {
    enableTryOn();
    Storage::fake('public');

    Http::fake([
        'example.com/*' => Http::response('IMG', 200, ['Content-Type' => 'image/jpeg']),
        'generativelanguage.googleapis.com/*' => Http::response(['error' => 'quota'], 500),
    ]);

    $product = tryOnProduct();

    $this->postJson(route('shop.product.try-on', $product->slug), [
        'photo' => UploadedFile::fake()->image('me.jpg'),
    ])->assertStatus(502);
});

function tryOnAdmin(): \App\Models\Admin
{
    return \App\Models\Admin::create([
        'first_name' => 'Super',
        'last_name' => 'Admin',
        'email' => 'ai-admin-'.uniqid().'@example.com',
        'password' => bcrypt('password'),
        'admin_role_id' => null,
    ]);
}

it('reports a successful gemini connection test', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [[
                'content' => ['parts' => [
                    ['inline_data' => ['mime_type' => 'image/png', 'data' => base64_encode('IMG')]],
                ]],
            ]],
        ], 200),
    ]);

    $this->actingAs(tryOnAdmin(), 'admin')
        ->postJson(route('admin.settings.ai.test'), [
            'ai_tryon_api_key' => 'AIzaTESTKEY',
            'ai_tryon_model' => 'gemini-3.1-flash-image',
        ])
        ->assertOk()
        ->assertJsonFragment(['message' => 'Connection OK — Gemini returned an image. Try-on is ready.']);
});

it('reports a failed gemini connection test', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response(['error' => ['message' => 'API key not valid']], 400),
    ]);

    $this->actingAs(tryOnAdmin(), 'admin')
        ->postJson(route('admin.settings.ai.test'), [
            'ai_tryon_api_key' => 'BADKEY',
            'ai_tryon_model' => 'gemini-3.1-flash-image',
        ])
        ->assertStatus(422);
});

it('test connection fails when no key is provided', function () {
    $this->setOptions(['ai_tryon_api_key' => '']);

    $this->actingAs(tryOnAdmin(), 'admin')
        ->postJson(route('admin.settings.ai.test'), [])
        ->assertStatus(422)
        ->assertJsonFragment(['message' => 'No API key configured.']);
});

it('prunes old try-on images', function () {
    Storage::fake('public');
    Storage::disk('public')->put('tryon/old.png', 'x');
    Storage::disk('public')->put('tryon/new.png', 'y');

    // Age the "old" file past the TTL.
    touch(Storage::disk('public')->path('tryon/old.png'), now()->subHours(48)->getTimestamp());

    $this->artisan('tryon:prune', ['--hours' => 24])->assertSuccessful();

    expect(Storage::disk('public')->exists('tryon/old.png'))->toBeFalse();
    expect(Storage::disk('public')->exists('tryon/new.png'))->toBeTrue();
});
