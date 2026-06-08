# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Laravel 12 (PHP 8.2+) backend for a quiz/contest/lottery platform. It serves two distinct surfaces from one codebase:

- **Admin dashboard** — server-rendered Blade + Alpine.js, session-authenticated, under the `/admin` prefix.
- **Client API** — JSON REST API for the mobile/PWA frontend, Sanctum token-authenticated, under the `/api/v1` prefix.

## Commands

```bash
# Run everything (server + queue worker + log tail + vite) concurrently
composer dev

# Individual processes
php artisan serve
php artisan queue:listen --tries=1
php artisan pail            # live log tail
npm run dev                 # vite dev server
npm run build               # production asset build

# Database
php artisan migrate
php artisan migrate:fresh --seed

# Tests (Pest)
php artisan test                              # all
php artisan test --testsuite=Unit            # one suite (Unit | Feature)
php artisan test --filter=SomeTestName       # single test
vendor/bin/pest tests/Feature/ExampleTest.php

# Lint / format (Laravel Pint)
vendor/bin/pint            # fix
vendor/bin/pint --test     # check only
```

The scheduler runs `run-task-schedule` every minute (see [routes/console.php](routes/console.php)); ensure `schedule:work` or a cron entry is active in environments that depend on scheduled tasks.

## Routing architecture

Routes are wired in [bootstrap/app.php](bootstrap/app.php) (not the default Laravel convention), which mounts four groups:

- `/admin` → [routes/admin.php](routes/admin.php) — `web` + `SetAppLocal` middleware, `admin.` name prefix.
- `/api/v1` → [routes/api/v1/client.php](routes/api/v1/client.php) — `api` + `SetAppLocal`, `api.v1.` name prefix.
- `/payment` → [routes/payment.php](routes/payment.php) — CSRF excluded (gateway callbacks/webhooks).
- Root `/` redirects to the admin dashboard.

API exceptions are normalized to JSON in `bootstrap/app.php`'s `withExceptions` block: validation errors → `422` with `{statusCode, message, errors}`, missing records → `404`, maintenance mode → `503` with payload. When adding API error handling, follow this existing shape rather than throwing raw exceptions.

## Authentication (multi-guard)

Defined in [config/auth.php](config/auth.php). The two guards that matter:

- **`admin`** — session driver, `admins` provider (the `Admin` model). Protected by `AdminAuthMiddleware`, which additionally enforces **per-route menu permissions**: an admin's `role->module_caps` is checked against the menu config in [config/menu.php](config/menu.php) via `getMenuCaps()`/`isCurrentUrlMatched()`. Admins without a role (`admin_role_id === null`) are superusers.
- **`client`** — Sanctum driver for the API (`auth:client` / `guest:client` middleware on client routes). Social login via Socialite, plus OTP and 2FA flows in `Api/V1/Client/Auth/AuthenticationController`.

## Code organization

Controllers mirror the two surfaces:

- `app/Http/Controllers/Admin/**` — feature-grouped (Quiz, Contest, Article, Deposit, Withdraw, Settings, etc.).
- `app/Http/Controllers/Api/V1/Client/**` — the client API, grouped by domain (Auth, Quiz, Contest, Profile, games, Article).

Business logic lives in `app/Services/**`, not controllers. Key service areas: `Payment`, `Quiz`, `Contest`, `Ai`, `Sms`, `Firebase`, `Coin`, `Bonus`, `Games`. Service providers in `app/Providers` bind these (e.g. `PaymentServiceProvider`, `SmsServiceProvider`, `AiServiceProvider`, `NotificationServiceProvider`).

## Key patterns

**Payment gateways** — Each gateway is a class in [app/Services/Payment/Methods/](app/Services/Payment/Methods/) extending `AbstractPayment` (implements `pay()`, `handleSuccess()`, `handleFailed()`). Gateways are discovered/configured through `PaymentInterface` and seeded defaults live in `PaymentMethodReserve::make()`. To add a gateway, create a `*Service` class following the existing ones and register its config there. Payments flow through `Payable` / `PaymentTypeEnum` (DEPOSIT, etc.).

**AI providers** — [app/Services/Ai/](app/Services/Ai/) has interchangeable providers (`OpenAiService`, `GeminiService`, `DeepSeekService`) sharing `BaseService`, used for question generation; token usage tracked via `AiTokenLog`.

**Translations / i18n** — Localizable models use a paired `*Translation` model (e.g. `Article` + `ArticleTranslation`, `Contest` + `ContestTranslation`, `Question` + `QuestionTranslation`) with a `translation` attribute appended to the model's `$appends`. Locale is set per-request by `SetAppLocal` from `session('locale')`. Use the `getTranslations()` / `translateText()` helpers.

**Options / settings** — Global key-value settings are stored via the `Option` model and accessed through global helpers `getOption()`, `getOptionWithJsonDecode()`, `storeOption()`. Many runtime settings (currencies, maintenance mode, payment config) flow through this rather than `.env`.

**Enums** — Domain status/type values are PHP enums in [app/Enums/](app/Enums/) with `label()` methods, cast on models (e.g. `'status' => ArticleStatusEnum::class`) and surfaced via `*_name` accessor attributes.

**Global helpers** — [app/Helpers/functions.php](app/Helpers/functions.php) is autoloaded (composer `files`) and defines many globals used throughout Blade and controllers: `convertCurrency()`, `amountWithSymbol()`, `adminUserHasPermission()`, `authorizedMenus()`, `placeImage()`/`placeAvatar()`, `inputSanitize()`, `protectOnDemo()`, etc. Check here before writing new utility logic.

**Reusable traits** — [app/Traits/](app/Traits/) holds cross-cutting model/controller behavior: `MediaUploader`, `Formatter`, `PaymentHelper`, `NotificationHelper`, `EmailAndPhoneOTPVerification`, `QuestionTrait`. Prefer composing these.

**Demo mode** — `DemoMiddleware` and the `protectOnDemo()` helper block mutating actions when the app runs as a demo. Respect this when adding admin write operations.

## Frontend assets

Admin UI is Blade + Alpine.js + Tailwind v4, bundled with Vite ([vite.config.js](vite.config.js)). jQuery, Quill, ApexCharts, SweetAlert2, Select2, and Firebase JS SDK are in the mix. This repo is the backend/dashboard; the client-facing app consumes the `/api/v1` API separately.

## Notifications

Multi-channel: email (`MailServiceProvider`), SMS (`Sms` services + `SmsServiceProvider`, multiple providers via Twilio/Vonage), and push via Firebase (`Firebase` services + `kreait/laravel-firebase`). Templates are data-driven through `NotificationTemplate` / `NotificationTemplateBody` models; logs in `NotificationLog`.
