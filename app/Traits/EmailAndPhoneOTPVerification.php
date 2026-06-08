<?php

namespace App\Traits;

use App\Notifications\RegistrationEmailOTP;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

trait EmailAndPhoneOTPVerification
{
    /**
     * Send Phone OTP
     * $param
     */
    public function sendPhoneOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|',
        ]);

        $key = $this->generateKey('phone', $validated['phone']);

        $payload = [
            'otp' => $this->generateOTP(),
            'generated_at' => now(),
        ];

        // cache for 5 minutes
        Cache::put($key, $payload, 60 * config('application_info.otp.expire_time'));

        // SMS delivery removed; phone OTP is cached only.

        return $this->withSuccess([
            'status' => 'success',
        ]);
    }

    /**
     * Send Driver Email OTP
     */
    public function sendEmailOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $key = $this->generateKey('email', $validated['email']);

        $payload = [
            'otp' => $this->generateOTP(),
            'generated_at' => now(),
        ];

        // cache for 5 minutes
        Cache::put($key, $payload, 60 * config('application_info.otp.expire_time'));

        Notification::route('mail', $validated['email'])->notify(new RegistrationEmailOTP($payload['otp']));

        return $this->withSuccess([
            'status' => 'success',
        ]);
    }

    /**
     * Verify Phone OTP
     */
    public function verifyPhoneOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required',
            'otp' => 'required',
        ]);

        $otp = $validated['otp'];

        $key = $this->generateKey('phone', $validated['phone']);

        $old = Cache::get($key);

        if (! $old) {
            throw ValidationException::withMessages(['phone' => __('Invalid Request')]);
        }
        if (! is_array($old)) {
            throw ValidationException::withMessages(['phone' => __('Invalid Request')]);
        }
        if (! isset($old['otp'])) {
            throw ValidationException::withMessages(['phone' => __('Invalid Request')]);
        }
        if (! isset($old['generated_at'])) {
            throw ValidationException::withMessages(['phone' => __('Invalid Request')]);
        }
        if (! $old['generated_at'] instanceof Carbon) {
            throw ValidationException::withMessages(['phone' => __('Invalid Request')]);
        }
        if ($old['generated_at']->addMinutes(5)->lte(now())) {
            throw ValidationException::withMessages(['otp' => __('OTP Expired')]);
        }
        if ($old['otp'] != $otp) {
            throw ValidationException::withMessages(['otp' => __('Invalid OTP')]);
        }

        return true;
    }

    /**
     * Verify Email OTP
     */
    public function verifyEmailOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        $otp = $validated['otp'];

        $key = $this->generateKey('email', $validated['email']);

        $old = Cache::get($key);

        if (! $old) {
            throw ValidationException::withMessages(['email' => __('Invalid Request')]);
        }
        if (! is_array($old)) {
            throw ValidationException::withMessages(['email' => __('Invalid Request')]);
        }
        if (! isset($old['otp'])) {
            throw ValidationException::withMessages(['email' => __('Invalid Request')]);
        }
        if (! isset($old['generated_at'])) {
            throw ValidationException::withMessages(['email' => __('Invalid Request')]);
        }
        if (! $old['generated_at'] instanceof Carbon) {
            throw ValidationException::withMessages(['email' => __('Invalid Request')]);
        }
        if ($old['generated_at']->addMinutes(5)->lte(now())) {
            throw ValidationException::withMessages(['otp' => __('OTP Expired')]);
        }
        if ($old['otp'] != $otp) {
            throw ValidationException::withMessages(['otp' => __('Invalid OTP')]);
        }

        return true;
    }

    private function generateOTP(): int
    {
        $digit_range = config('application_info.otp.digit_range');

        return mt_rand((int) $digit_range[0], (int) $digit_range[1]);
    }

    private function generateKey(string $name, string $value)
    {
        return "{$name}-{$value}";
    }
}
