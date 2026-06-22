<?php

use App\Enums\UserStatusEnum;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function makeCustomer(array $attributes = []): User
{
    return User::create(array_merge([
        'first_name' => 'Test',
        'username' => 'cust_'.uniqid(),
        'email' => 'cust-'.uniqid().'@example.com',
        'phone' => '0171'.random_int(1000000, 9999999),
        'password' => Hash::make('password123'),
        'status' => UserStatusEnum::ACTIVE->value,
    ], $attributes));
}

it('registers a customer with email only', function () {
    $this->post(route('register'), [
        'first_name' => 'Email User',
        'email' => 'emailonly@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('shop.account.index'));

    $this->assertDatabaseHas('users', ['email' => 'emailonly@example.com']);
    $this->assertAuthenticated();
});

it('registers a customer with phone only', function () {
    $this->post(route('register'), [
        'first_name' => 'Phone User',
        'phone' => '01711122233',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('shop.account.index'));

    $this->assertDatabaseHas('users', ['phone' => '01711122233']);
    $this->assertAuthenticated();
});

it('rejects registration without email or phone', function () {
    $this->post(route('register'), [
        'first_name' => 'No Contact',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors(['email', 'phone']);

    $this->assertGuest();
});

it('logs in with email', function () {
    $user = makeCustomer(['email' => 'login@example.com', 'password' => Hash::make('secret123')]);

    $this->post(route('login'), [
        'login' => 'login@example.com',
        'password' => 'secret123',
    ])->assertRedirect(route('shop.account.index'));

    $this->assertAuthenticatedAs($user);
});

it('logs in with phone', function () {
    $user = makeCustomer(['phone' => '01799988877', 'password' => Hash::make('secret123')]);

    $this->post(route('login'), [
        'login' => '01799988877',
        'password' => 'secret123',
    ])->assertRedirect(route('shop.account.index'));

    $this->assertAuthenticatedAs($user);
});

it('rejects login with wrong credentials', function () {
    makeCustomer(['phone' => '01700000001', 'password' => Hash::make('secret123')]);

    $this->post(route('login'), [
        'login' => '01700000001',
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('login');

    $this->assertGuest();
});
