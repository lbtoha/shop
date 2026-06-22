<?php

use App\Notifications\RegistrationEmailOTP;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Shop\PaymentSettingsHelper;

uses(PaymentSettingsHelper::class);

function registerPayload(array $overrides = []): array
{
    return array_merge([
        'first_name' => 'Jane',
        'email' => 'jane-'.uniqid().'@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $overrides);
}

/** Pull the OTP out of the cache for the pending registration in this session. */
function pendingOtp(): string
{
    $token = session('signup_otp_token');

    return Cache::get('signup_otp:'.$token)['otp'];
}

it('creates the account directly when OTP is disabled', function () {
    $this->setOptions(['signup_otp_enabled' => 0]);

    $this->post(route('register'), registerPayload(['email' => 'direct@example.com']))
        ->assertRedirect(route('shop.account.index'));

    $this->assertDatabaseHas('users', ['email' => 'direct@example.com']);
    $this->assertAuthenticated();
});

it('sends an OTP and defers account creation when enabled', function () {
    $this->setOptions(['signup_otp_enabled' => 1]);
    Notification::fake();

    $this->post(route('register'), registerPayload(['email' => 'pending@example.com']))
        ->assertRedirect(route('register.otp'));

    // No user yet, and an OTP email was sent.
    $this->assertDatabaseMissing('users', ['email' => 'pending@example.com']);
    $this->assertGuest();
    Notification::assertSentOnDemand(RegistrationEmailOTP::class);
});

it('creates the account after a correct OTP', function () {
    $this->setOptions(['signup_otp_enabled' => 1]);
    Notification::fake();

    $this->post(route('register'), registerPayload(['email' => 'verify@example.com']))
        ->assertRedirect(route('register.otp'));

    $otp = pendingOtp();

    $this->post(route('register.otp.verify'), ['otp' => $otp])
        ->assertRedirect(route('shop.account.index'));

    $this->assertDatabaseHas('users', ['email' => 'verify@example.com']);
    $this->assertAuthenticated();
    expect(\App\Models\User::where('email', 'verify@example.com')->value('email_verified_at'))->not->toBeNull();
});

it('rejects an incorrect OTP', function () {
    $this->setOptions(['signup_otp_enabled' => 1]);
    Notification::fake();

    $this->post(route('register'), registerPayload(['email' => 'wrong@example.com']));

    $this->post(route('register.otp.verify'), ['otp' => '000000'])
        ->assertSessionHasErrors('otp');

    $this->assertDatabaseMissing('users', ['email' => 'wrong@example.com']);
    $this->assertGuest();
});

it('blocks phone-only signup when OTP is enabled', function () {
    $this->setOptions(['signup_otp_enabled' => 1]);
    Notification::fake();

    $this->post(route('register'), [
        'first_name' => 'Phone User',
        'phone' => '01711122233',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('email');

    $this->assertDatabaseMissing('users', ['phone' => '01711122233']);
    Notification::assertNothingSent();
});

it('phone-only signup still works when OTP is disabled', function () {
    $this->setOptions(['signup_otp_enabled' => 0]);

    $this->post(route('register'), [
        'first_name' => 'Phone User',
        'phone' => '01799988877',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('shop.account.index'));

    $this->assertDatabaseHas('users', ['phone' => '01799988877']);
});

function enableSmsGateway(): void
{
    test()->setOptions([
        'sms_gateway' => json_encode([
            'is_enabled' => true,
            'method' => 'GET',
            'url' => 'https://sms.example.com/send',
            'params' => ['to' => '{to}', 'msg' => '{message}'],
        ]),
    ]);
}

it('sends an SMS OTP for a phone-only signup when the gateway is configured', function () {
    $this->setOptions(['signup_otp_enabled' => 1]);
    enableSmsGateway();
    \Illuminate\Support\Facades\Http::fake([
        'sms.example.com/*' => \Illuminate\Support\Facades\Http::response('OK', 200),
    ]);

    $this->post(route('register'), [
        'first_name' => 'Phone User',
        'phone' => '01711100000',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('register.otp'));

    // No account yet; an SMS was dispatched to the gateway.
    $this->assertDatabaseMissing('users', ['phone' => '01711100000']);
    \Illuminate\Support\Facades\Http::assertSent(fn ($req) => str_contains($req->url(), 'sms.example.com'));
});

it('creates a phone account after a correct SMS OTP and marks phone verified', function () {
    $this->setOptions(['signup_otp_enabled' => 1]);
    enableSmsGateway();
    \Illuminate\Support\Facades\Http::fake([
        'sms.example.com/*' => \Illuminate\Support\Facades\Http::response('OK', 200),
    ]);

    $this->post(route('register'), [
        'first_name' => 'Phone User',
        'phone' => '01722200000',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $otp = pendingOtp();

    $this->post(route('register.otp.verify'), ['otp' => $otp])
        ->assertRedirect(route('shop.account.index'));

    $this->assertDatabaseHas('users', ['phone' => '01722200000']);
    $this->assertAuthenticated();
    expect(\App\Models\User::where('phone', '01722200000')->value('phone_verified_at'))->not->toBeNull();
});

it('resends a fresh OTP', function () {
    $this->setOptions(['signup_otp_enabled' => 1]);
    Notification::fake();

    $this->post(route('register'), registerPayload(['email' => 'resend@example.com']));

    $this->post(route('register.otp.resend'))->assertRedirect();

    // A second email went out (the code may or may not differ, but a send occurred).
    Notification::assertSentOnDemandTimes(RegistrationEmailOTP::class, 2);
    expect(Cache::get('signup_otp:'.session('signup_otp_token'))['otp'])->toBeString();
});
