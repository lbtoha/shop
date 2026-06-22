<?php

namespace App\Services\Ai;

use App\Exceptions\CustomWebException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Abuse protection for the (billed) AI try-on endpoint. Layers, cheapest first:
 *
 *   1. login gate            — optionally require an account
 *   2. honeypot + min-time   — trivial bot friction (no DB/cache hit)
 *   3. reCAPTCHA             — when the Google reCAPTCHA extension is enabled
 *   4. per-user/IP limits    — N per hour AND M per day (RateLimiter)
 *   5. global daily cap      — site-wide ceiling on Google spend
 *
 * Only `consume()` increments counters — call it after the limits pass but
 * before the billed Gemini request, so failed generations don't burn quota.
 */
class TryOnAbuseGuard
{
    /** Whether the storefront should render the reCAPTCHA widget in the modal. */
    public static function captchaEnabled(): bool
    {
        return (bool) config('extension.recaptcha.is_enabled')
            && filled(config('extension.recaptcha.site_key'));
    }

    /**
     * Run every read-only check. Throws CustomWebException (HTTP 429/403/422) on
     * the first failure. Does NOT increment any counter.
     *
     * @throws CustomWebException
     */
    public function assert(Request $request): void
    {
        $this->assertLoggedInIfRequired($request);
        $this->assertNotBot($request);
        $this->assertCaptcha($request);
        $this->assertWithinUserLimits($request);
        $this->assertWithinGlobalCap();
    }

    /**
     * Record one successful generation against the per-user/IP and global
     * counters. Call this only after a generation actually succeeds.
     */
    public function consume(Request $request): void
    {
        RateLimiter::increment($this->hourKey($request), 3600);
        RateLimiter::increment($this->dayKey($request), 86400);

        Cache::increment($this->globalKey());
        // Make sure the global counter expires at end of day even on first hit.
        Cache::put($this->globalKey().':ttl', 1, now()->endOfDay());
    }

    private function assertLoggedInIfRequired(Request $request): void
    {
        if ((int) getOption('ai_tryon_login_required', 0) === 1 && ! $request->user()) {
            throw new CustomWebException(__('Please log in to use the AI try-on.'), 403);
        }
    }

    /**
     * Honeypot: a hidden field bots tend to fill. Plus a minimum think-time
     * between loading the page and submitting (scripts submit instantly).
     */
    private function assertNotBot(Request $request): void
    {
        if (filled($request->input('website'))) {
            // Silent-ish: treat as a generic failure so bots learn nothing.
            throw new CustomWebException(__('Your request could not be processed.'), 422);
        }

        $startedAt = (int) $request->input('form_started_at');
        if ($startedAt > 0) {
            $elapsedMs = (now()->getTimestamp() * 1000) - $startedAt;
            if ($elapsedMs >= 0 && $elapsedMs < 1500) {
                throw new CustomWebException(__('Please take a moment before submitting.'), 422);
            }
        }
    }

    /**
     * Verify the Google reCAPTCHA token server-side when the extension is on.
     * We verify explicitly here (rather than via RecaptchaValidationRule, which
     * skips JSON/AJAX requests) so the AJAX try-on can't bypass the captcha.
     */
    private function assertCaptcha(Request $request): void
    {
        if (! self::captchaEnabled()) {
            return;
        }

        $token = $request->input('recaptcha_token');

        if (blank($token)) {
            throw new CustomWebException(__('Please complete the verification and try again.'), 422);
        }

        $verified = Http::asForm()
            ->timeout(15)
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('extension.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => $request->ip(),
            ])
            ->json('success');

        if (! $verified) {
            throw new CustomWebException(__('Verification failed. Please try again.'), 422);
        }
    }

    private function assertWithinUserLimits(Request $request): void
    {
        $perHour = max(1, (int) getOption('ai_tryon_per_hour', 5));
        $perDay = max(1, (int) getOption('ai_tryon_per_day', 20));

        if (RateLimiter::tooManyAttempts($this->hourKey($request), $perHour)) {
            $this->deny(RateLimiter::availableIn($this->hourKey($request)));
        }

        if (RateLimiter::tooManyAttempts($this->dayKey($request), $perDay)) {
            $this->deny(RateLimiter::availableIn($this->dayKey($request)));
        }
    }

    private function assertWithinGlobalCap(): void
    {
        $cap = max(1, (int) getOption('ai_tryon_daily_global', 500));

        if ((int) Cache::get($this->globalKey(), 0) >= $cap) {
            throw new CustomWebException(
                __('The AI try-on is busy right now. Please try again tomorrow.'),
                429
            );
        }
    }

    private function deny(int $seconds): void
    {
        $minutes = max(1, (int) ceil($seconds / 60));

        throw new CustomWebException(
            __('You have reached the try-on limit. Please try again in :minutes minute(s).', ['minutes' => $minutes]),
            429
        );
    }

    /** Per-visitor identity: account id when logged in, else hashed IP. */
    private function identity(Request $request): string
    {
        return $request->user()?->id
            ? 'u'.$request->user()->id
            : 'ip'.sha1($request->ip());
    }

    private function hourKey(Request $request): string
    {
        return 'tryon:h:'.$this->identity($request);
    }

    private function dayKey(Request $request): string
    {
        return 'tryon:d:'.$this->identity($request);
    }

    private function globalKey(): string
    {
        return 'tryon:global:'.now()->toDateString();
    }
}
