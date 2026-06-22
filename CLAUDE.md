# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Workflow

- **Always merge finished work into `main`.** Do the work on a short-lived branch, then fast-forward merge it into `main` and delete the branch (push only when asked). Don't leave completed features sitting on a side branch.

## Overview

Laravel 12 (PHP 8.2+) **cash-on-delivery (COD) e-commerce** app, converted from a quiz/lottery platform: the quiz/contest/games/payment-gateway/client-API modules were stripped out, and a products/categories/orders backend plus a public Blade storefront were built on the surviving admin shell. There is **no payment gateway** (orders are paid on delivery) and **no client REST API**.

Two server-rendered surfaces, both Blade + Tailwind v4 (separate Vite entries):
- **Storefront** — public shop at the root (`/`, `/shop`, `/product/{slug}`, `/cart`, `/checkout`). Guest checkout works without auth; customers may **optionally** register/login (`/register`, `/login`, `/account`, `/account/orders`) via the default `web` session guard on the `users` provider — logged-in orders attach to the user (`orders.user_id`), guest orders leave it null. Routes in [routes/shop.php](routes/shop.php).
- **Admin dashboard** — session-authenticated `admin` guard, under `/admin`. Routes in [routes/admin.php](routes/admin.php).

Note: `User implements MustVerifyEmail`, but there is **no** `verification.verify` route (it left with the API). Do not dispatch `Registered` for storefront signups — it builds that route and 500s; the register controller logs the user in directly instead.

## Commands

```bash
composer dev                 # server + queue worker + log tail (pail) + vite, concurrently
php artisan serve
npm run dev                  # vite dev server
npm run build                # production assets

# Database
php artisan migrate:fresh --seed     # rebuilds schema + sample admin/menu/catalog data

# Tests (Pest)
php artisan test
php artisan test --filter=SomeTestName

# Lint / format
vendor/bin/pint              # fix
vendor/bin/pint --test       # check only
```

The app runs as a standard Laravel front controller (`public/index.php`) — the legacy web installer (`public/installer/` + the `storage/installed` gate) was removed. Set up via the normal flow: `.env`, `php artisan migrate --seed`, `npm run build`.

The scheduler runs `run-task-schedule` every minute ([routes/console.php](routes/console.php)).

## Routing & request flow

Routes are wired in [bootstrap/app.php](bootstrap/app.php) (not Laravel's default convention): the `/admin` group → [routes/admin.php](routes/admin.php) (`web` + `SetAppLocal`, `admin.` name prefix), plus root redirect and a `not-found` route. The former `/api/v1` and `/payment` groups were removed.

E-commerce admin routes live in [routes/admin.php](routes/admin.php) inside the `DemoMiddleware` group: `admin.categories.*` and `admin.products.*` (resource, no `show`), and `admin.orders.{index,show,update-status,destroy}` (orders are created by checkout, so no create/edit).

## Authentication

The `admin` guard ([config/auth.php](config/auth.php)) is session-based on the `Admin` model. `AdminAuthMiddleware` enforces **per-route menu permissions**: an admin's `role->module_caps` is checked against [config/menu.php](config/menu.php) via `getMenuCaps()`/`isCurrentUrlMatched()`. Admins with `admin_role_id === null` are superusers. The `client` Sanctum guard still exists in config but the customer-facing API was removed; the `User` model now represents storefront customers for admin management only.

## E-commerce domain

Models ([app/Models/](app/Models/)): `Category` (self-nesting via `parent_id`, Sluggable), `Product` (Sluggable, `category()`, `images()`, scopes `active()`/`inStock()`, `isInStock()`), `ProductImage`, `Order`, `OrderItem`, `Banner` (hero slides). Order state is enum-cast: `OrderStatusEnum` (pending→confirmed→processing→shipped→delivered / cancelled) and `OrderPaymentStatusEnum` (unpaid/paid/refunded), each with `label()`/`color()`/`values()`. Admin CRUD modules: Category, Product, Order (status updates only), Banner — all under [app/Http/Controllers/Admin/](app/Http/Controllers/Admin/).

**Checkout (COD)** lives in [app/Services/Ecommerce/](app/Services/Ecommerce/):
- `Cart` — session-backed cart keyed by product id; product price/stock are always read fresh from the DB (never trusted from session).
- `CheckoutService::placeOrder()` — runs in a `DB::transaction` with `lockForUpdate()` on each product so concurrent checkouts can't oversell; decrements stock, snapshots product name/price into `OrderItem`, generates the order number via `UniqueCodeGenerator::make(Order::class, 'order_number', 6, 'ORD')`, records the order as `cash_on_delivery`/`unpaid`/`pending`, and clears the cart. Reads flat shipping via `getOption('shipping_cost', 0)` (set on the admin Shop Settings page, `admin.settings.shop`). Throws `CustomWebException` (422) on empty cart or out-of-stock.
- `OrderNotifier` — emails the customer on order placement (`ORDER_PLACED`) and admin status change (`ORDER_STATUS_UPDATED`) via `Notification::route('mail', $order->customer_email)`. Gated by the admin's global `email_notification.is_enabled` flag (off by default) and queued (`queue.default=database`), so emails won't appear without a queue worker + the flag on.

## Storefront (public shop)

Controllers in [app/Http/Controllers/Shop/](app/Http/Controllers/Shop/): `HomeController` (hero from `Banner` records with a static fallback when none exist + New Collection/Hot Sale/Featured product sections), `ShopController` (listing with category/search/sort filter + product detail, increments `views`), `CartController` (add is JSON for AJAX; update/remove redirect; `count` feeds the header badge), `CheckoutController` (guest COD: form → `CheckoutService::placeOrder()` → `OrderNotifier::orderPlaced` → confirmation by order number), `AccountController` (dashboard/orders/order detail for logged-in customers), and `Shop/Auth/{Register,Login}Controller`.

Views in [resources/views/shop/](resources/views/shop/) extend `shop.layouts.app` (header with cart badge + category nav, footer). The product card is an **anonymous component** registered via `Blade::anonymousComponentNamespace('shop.components', 'shop')` in `AppServiceProvider` — use it as `<x-shop::product-card :product="$p" />` (double-colon namespace syntax, matching admin's `<x-admin::...>`; the dot form `<x-shop.product-card>` will NOT resolve). Assets: `resources/shop/css/app.css` (Tailwind v4, warm-neutral theme via `@theme` CSS vars) and `resources/shop/js/app.js` (vanilla: AJAX add-to-cart, cart badge, hero carousel, mobile menu) — both registered as Vite inputs in [vite.config.js](vite.config.js). Note: many dead admin JS files (quiz/contest/firebase/etc.) remain referenced in the Vite input list — harmless, left from the strip.

The root `/` serves the storefront home (was the admin-dashboard redirect before the conversion).

## Admin controller & view conventions

Follow the existing dashboard idiom (see [UserController](app/Http/Controllers/Admin/User/UserController.php) / [RoleController](app/Http/Controllers/Admin/AdminUser/RoleController.php) / the e-commerce controllers as templates):

- Every action starts with `adminUserHasPermission(permission: 'read'|'create'|'edit'|'delete')` (global helper, authorizes by admin role).
- Lists go through `App\Services\ModalIndexQuey::get($query, $with)` — returns a paginator and auto-applies search (via the model's `getSearchAttribute()`), sort, and date filters. Views receive a `$columns` array (`['label', 'key'?, 'header_class'?, 'render' => fn($row) => html]`) plus `$buttons`/`$tab_buttons`.
- Action columns build `$action_buttons` and `return view('admin.components.table-action', compact('action_buttons'))->render();`.
- `store`/`update`/`destroy` return **JSON** `['message' => __('...'), 'redirect' => route('admin....index')]`; validation via a FormRequest in `app/Http/Requests/Admin/<Module>/`. The global `app.js` submits forms with class `form-submit-edit` over AJAX and follows the `redirect`.
- Money is rendered with the `amountWithSymbol()` helper; status badges use `<span class="status {enum->color()} capitalize">{{ $enum->label() }}</span>`.

Blade: wrap pages in `<x-admin-app-layout>` → `<div class="white-box">`; use `<x-admin::page-header>`, `<x-admin::table>`, and form components `<x-admin::text-input-group>`, `<x-admin::textarea-group>`, `<x-admin::editor>`, `<x-admin::number-input-group>` (currency symbol on by default; pass `:with_currencySymbol="false"` for non-money), `<x-admin::select-option>` (options are passed as `<option>` slot children, **not** an `:options` prop), `<x-admin::switch>` (uses `:value` 0/1 + a `:types` array, not a boolean `:checked`), and `<x-admin::file-uploader>` (stores a file-manager path string in a text input — controllers just save the string, no manual upload). **Read the component file in [resources/views/admin/components/](resources/views/admin/components/) before using it** — prop names vary.

Product gallery images are submitted as an `images[]` array of path strings; `ProductController` creates one `ProductImage` per non-empty path, and `update()` uses delete-and-recreate to sync.

## Settings, currency, helpers

- **Options/settings** are key-value rows on the `Option` model via `getOption()`/`getOptionWithJsonDecode()`/`storeOption()`. Many runtime values (company info, theme, currency symbol) flow through here rather than `.env`; `FlyServiceProvider` loads them into config at boot.
- **Currency is single/fixed** — the multi-currency `Currency` model was removed. `convertCurrency()` is identity, `currencyRate()` returns 1, and `currencySymbol()`/`amountWithSymbol()` read a `currency_symbol` option.
- [app/Helpers/functions.php](app/Helpers/functions.php) is autoloaded and holds globals used across Blade/controllers: `amountWithSymbol()`, `adminUserHasPermission()`, `authorizedMenus()`, `placeImage()`/`placeAvatar()`, `inputSanitize()`, `protectOnDemo()` (a display value-masker for demo mode — it does **not** block actions), etc. Check here before writing utility logic.

## Notifications

Email-only (SMS/Firebase push were removed). Templates are data-driven via `NotificationTemplate`/`NotificationTemplateBody`; `UserAutoNotification` resolves to the `mail` channel. Media library is `unisharp/laravel-filemanager` (wired in admin routes; used for product/category images).
