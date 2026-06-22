<?php

namespace App\Http\Controllers\Shop\Auth;

use App\Enums\UserStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\RegistrationEmailOTP;
use App\Services\Notification\SmsGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /** Cache key prefix + lifetime for a pending (unverified) registration. */
    private const PENDING_PREFIX = 'signup_otp:';

    public function create()
    {
        if (auth()->check()) {
            return redirect()->route('shop.account.index');
        }

        return view('shop.auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            // Either email or phone is required; both may be given.
            'email' => 'nullable|required_without:phone|email|max:255|unique:users,email',
            'phone' => 'nullable|required_without:email|string|max:30|unique:users,phone',
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'email.required_without' => __('Please provide an email or a phone number.'),
            'phone.required_without' => __('Please provide a phone number or an email.'),
        ]);

        // OTP off → create the account directly (original behaviour).
        if (! $this->otpEnabled()) {
            return $this->createAndLogin($request, $validated);
        }

        // Pick the delivery channel: email when an address was given, otherwise
        // SMS (only if the gateway is configured). A phone-only signup with no
        // SMS gateway can't be verified — ask for an email instead of stranding.
        $email = $validated['email'] ?? null;
        $phone = $validated['phone'] ?? null;

        if (filled($email)) {
            $channel = 'email';
            $destination = $email;
        } elseif (filled($phone) && SmsGateway::isEnabled()) {
            $channel = 'phone';
            $destination = $phone;
        } else {
            throw ValidationException::withMessages([
                'email' => __('Email verification is required to sign up. Please provide an email address.'),
            ]);
        }

        // Stash the pending registration server-side (never trust it from the
        // client on the verify step) and deliver a one-time code.
        $token = (string) Str::uuid();
        $otp = (string) $this->generateOtp();

        Cache::put(self::PENDING_PREFIX.$token, [
            'data' => $validated,
            'otp' => $otp,
            'channel' => $channel,
            'destination' => $destination,
        ], now()->addMinutes($this->otpMinutes()));

        $this->sendOtp($channel, $destination, $otp);

        $request->session()->put('signup_otp_token', $token);

        return redirect()->route('register.otp')
            ->with('success', __('We sent a verification code to :destination.', ['destination' => $destination]));
    }

    /** Deliver the OTP over the chosen channel (email notification or SMS gateway). */
    private function sendOtp(string $channel, string $destination, string $otp): void
    {
        if ($channel === 'phone') {
            $minutes = $this->otpMinutes();
            SmsGateway::send($destination, __('Your verification code is :otp. It expires in :minutes minute(s).', ['otp' => $otp, 'minutes' => $minutes]));

            return;
        }

        Notification::route('mail', $destination)->notify(new RegistrationEmailOTP($otp));
    }

    /** Show the OTP entry screen for the pending registration. */
    public function showOtp(Request $request)
    {
        $pending = $this->pending($request);

        if (! $pending) {
            return redirect()->route('register')->with('error', __('Your verification session expired. Please sign up again.'));
        }

        return view('shop.auth.verify-otp', [
            'destination' => $pending['destination'],
            'channel' => $pending['channel'],
        ]);
    }

    /** Verify the OTP and create the account. */
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string']);

        $token = $request->session()->get('signup_otp_token');
        $pending = $token ? Cache::get(self::PENDING_PREFIX.$token) : null;

        if (! $pending) {
            return redirect()->route('register')->with('error', __('Your verification session expired. Please sign up again.'));
        }

        if (! hash_equals($pending['otp'], trim((string) $request->input('otp')))) {
            throw ValidationException::withMessages(['otp' => __('The code you entered is incorrect.')]);
        }

        // Re-check uniqueness in case the email/phone was taken while verifying.
        $request->merge($pending['data']);
        $request->validate([
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30|unique:users,phone',
        ]);

        Cache::forget(self::PENDING_PREFIX.$token);
        $request->session()->forget('signup_otp_token');

        // Mark whichever channel was actually verified.
        $data = $pending['data'];
        $data[$pending['channel'] === 'phone' ? 'phone_verified_at' : 'email_verified_at'] = now();

        return $this->createAndLogin($request, $data);
    }

    /** Resend the code for the pending registration. */
    public function resendOtp(Request $request)
    {
        $token = $request->session()->get('signup_otp_token');
        $pending = $token ? Cache::get(self::PENDING_PREFIX.$token) : null;

        if (! $pending) {
            return redirect()->route('register')->with('error', __('Your verification session expired. Please sign up again.'));
        }

        $otp = (string) $this->generateOtp();
        $pending['otp'] = $otp;
        Cache::put(self::PENDING_PREFIX.$token, $pending, now()->addMinutes($this->otpMinutes()));

        $this->sendOtp($pending['channel'], $pending['destination'], $otp);

        return back()->with('success', __('A new code has been sent to :destination.', ['destination' => $pending['destination']]));
    }

    /**
     * Create the user and log them in. Shared by the direct and OTP-verified paths.
     */
    private function createAndLogin(Request $request, array $data)
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'username' => $this->uniqueUsername($data['email'] ?? $data['phone']),
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status' => UserStatusEnum::ACTIVE->value,
            'email_verified_at' => $data['email_verified_at'] ?? null,
            'phone_verified_at' => $data['phone_verified_at'] ?? null,
        ]);

        auth()->login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('shop.account.index'))->with('success', __('Welcome! Your account has been created.'));
    }

    private function pending(Request $request): ?array
    {
        $token = $request->session()->get('signup_otp_token');

        return $token ? Cache::get(self::PENDING_PREFIX.$token) : null;
    }

    private function otpEnabled(): bool
    {
        return (int) getOption('signup_otp_enabled', 0) === 1;
    }

    private function otpMinutes(): int
    {
        return max(1, (int) config('application_info.otp.expire_time', 5));
    }

    private function generateOtp(): int
    {
        $range = config('application_info.otp.digit_range', [10000, 99999]);

        return mt_rand((int) $range[0], (int) $range[1]);
    }

    private function uniqueUsername(string $identifier): string
    {
        // Works for both email (slug of the local part) and phone numbers.
        $base = Str::slug(Str::before($identifier, '@'), '_') ?: 'user';
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base.'_'.$i++;
        }

        return $username;
    }
}
