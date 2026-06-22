<?php

use App\Models\Admin;
use App\Services\Payment\SslCommerzService;
use Tests\Feature\Shop\PaymentSettingsHelper;

uses(PaymentSettingsHelper::class);

function superAdmin(): Admin
{
    // admin_role_id === null → superuser, allowed through AdminAuthMiddleware.
    return Admin::create([
        'first_name' => 'Super',
        'last_name' => 'Admin',
        'email' => 'admin-'.uniqid().'@example.com',
        'password' => bcrypt('password'),
        'admin_role_id' => null,
    ]);
}

it('saves sslcommerz settings and enables the gateway', function () {
    $this->actingAs(superAdmin(), 'admin')
        ->post(route('admin.settings.payment.store'), [
            'sslcommerz_enabled' => 1,
            'sslcommerz_test_mode' => 1,
            'sslcommerz_store_id' => 'mystore',
            'sslcommerz_store_password' => 'secret',
        ])
        ->assertOk();

    $this->resetOptionCache();

    expect(getOption('sslcommerz_enabled'))->toBe('1')
        ->and(getOption('sslcommerz_store_id'))->toBe('mystore')
        ->and(SslCommerzService::isEnabled())->toBeTrue();
});

it('requires credentials when enabling the gateway', function () {
    $this->actingAs(superAdmin(), 'admin')
        ->post(route('admin.settings.payment.store'), [
            'sslcommerz_enabled' => 1,
            'sslcommerz_test_mode' => 1,
            'sslcommerz_store_id' => '',
            'sslcommerz_store_password' => '',
        ])
        ->assertSessionHasErrors(['sslcommerz_store_id', 'sslcommerz_store_password']);
});

it('can save with the gateway disabled and no credentials', function () {
    $this->actingAs(superAdmin(), 'admin')
        ->post(route('admin.settings.payment.store'), [
            'sslcommerz_enabled' => 0,
            'sslcommerz_test_mode' => 1,
        ])
        ->assertOk();

    $this->resetOptionCache();

    expect(SslCommerzService::isEnabled())->toBeFalse();
});
