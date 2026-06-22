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

function fakeGeminiSuccess(): void
{
    Http::fake([
        'example.com/*' => Http::response('IMG', 200, ['Content-Type' => 'image/jpeg']),
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [['content' => ['parts' => [
                ['inline_data' => ['mime_type' => 'image/png', 'data' => base64_encode('OUT')]],
            ]]]],
        ], 200),
    ]);
}

it('blocks guests when login is required', function () {
    enableTryOn();
    $this->setOptions(['ai_tryon_login_required' => 1]);
    Storage::fake('public');
    $product = tryOnProduct();

    $this->postJson(route('shop.product.try-on', $product->slug), [
        'photo' => UploadedFile::fake()->image('me.jpg'),
    ])->assertStatus(403);
});

it('rejects a submission that fills the honeypot', function () {
    enableTryOn();
    Storage::fake('public');
    fakeGeminiSuccess();
    $product = tryOnProduct();

    $this->postJson(route('shop.product.try-on', $product->slug), [
        'photo' => UploadedFile::fake()->image('me.jpg'),
        'website' => 'http://spam.example',
    ])->assertStatus(422);

    // The billed call never happened.
    Http::assertNothingSent();
});

it('enforces the per-hour visitor limit', function () {
    enableTryOn();
    $this->setOptions(['ai_tryon_per_hour' => 2, 'ai_tryon_per_day' => 100, 'ai_tryon_daily_global' => 1000]);
    Storage::fake('public');
    fakeGeminiSuccess();
    $product = tryOnProduct();

    // First two succeed, third is throttled.
    for ($i = 0; $i < 2; $i++) {
        $this->postJson(route('shop.product.try-on', $product->slug), [
            'photo' => UploadedFile::fake()->image("me{$i}.jpg"),
        ])->assertOk();
    }

    $this->postJson(route('shop.product.try-on', $product->slug), [
        'photo' => UploadedFile::fake()->image('me3.jpg'),
    ])->assertStatus(429);
});

it('enforces the site-wide daily cap', function () {
    enableTryOn();
    $this->setOptions(['ai_tryon_per_hour' => 100, 'ai_tryon_per_day' => 100, 'ai_tryon_daily_global' => 1]);
    Storage::fake('public');
    fakeGeminiSuccess();
    $product = tryOnProduct();

    $this->postJson(route('shop.product.try-on', $product->slug), [
        'photo' => UploadedFile::fake()->image('a.jpg'),
    ])->assertOk();

    // Global cap of 1 is now reached — next visitor is blocked.
    $this->postJson(route('shop.product.try-on', $product->slug), [
        'photo' => UploadedFile::fake()->image('b.jpg'),
    ])->assertStatus(429);
});

it('does not consume quota when generation fails', function () {
    enableTryOn();
    $this->setOptions(['ai_tryon_per_hour' => 1, 'ai_tryon_per_day' => 100, 'ai_tryon_daily_global' => 1000]);
    Storage::fake('public');

    // Gemini fails the first call, then succeeds on the retry (one stub set,
    // sequenced — re-calling Http::fake() mid-test does not reliably replace).
    Http::fake([
        'example.com/*' => Http::response('IMG', 200, ['Content-Type' => 'image/jpeg']),
        'generativelanguage.googleapis.com/*' => Http::sequence()
            ->push(['error' => 'boom'], 500)
            ->push(['candidates' => [['content' => ['parts' => [
                ['inline_data' => ['mime_type' => 'image/png', 'data' => base64_encode('OUT')]],
            ]]]]], 200),
    ]);

    $product = tryOnProduct();

    // Failed generation (502) must not burn the single allowed attempt.
    $this->postJson(route('shop.product.try-on', $product->slug), [
        'photo' => UploadedFile::fake()->image('me.jpg'),
    ])->assertStatus(502);

    // A retry is still allowed (limit not consumed) — now succeeds.
    $this->postJson(route('shop.product.try-on', $product->slug), [
        'photo' => UploadedFile::fake()->image('me2.jpg'),
    ])->assertOk();
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
