<?php

namespace App\Rules;

use App\Facades\Flash;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class RecaptchaValidationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * Skips validation for mobile API requests (Accept: application/json)
     * since mobile apps cannot render web reCAPTCHA widgets.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! config('extension.recaptcha.is_enabled')) {
            return;
        }

        // Skip reCAPTCHA for mobile app requests
        if (request()->expectsJson() && ! $value) {
            return;
        }

        if (! $value) {
            Flash::error(__('Captcha verification failed'));
            $fail(__('Captcha token is required'));

            return;
        }

        $response = Http::post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('extension.recaptcha.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        if (! $response->json('success')) {
            Flash::error(__('Captcha verification failed'));
            $fail(__('Captcha verification failed'));
        }
    }
}
