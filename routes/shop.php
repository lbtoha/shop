<?php

use App\Http\Controllers\Shop\AccountController;
use App\Http\Controllers\Shop\Auth\LoginController;
use App\Http\Controllers\Shop\Auth\RegisterController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Storefront language switch (en/bn) — stores locale in session for SetAppLocal.
Route::get('/language/{code}', function (string $code) {
    if (in_array($code, ['en', 'bn'], true)) {
        session()->put('locale', $code);
    }

    return back();
})->name('shop.language');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('shop.product');

// AI virtual try-on (Gemini) — generate a preview of the customer wearing a product.
Route::post('/product/{slug}/try-on', [\App\Http\Controllers\Shop\TryOnController::class, 'generate'])->name('shop.product.try-on');

Route::prefix('cart')->as('shop.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('add/{product}', [CartController::class, 'add'])->name('add');
    Route::put('update/{lineKey}', [CartController::class, 'update'])->name('update')->where('lineKey', '[0-9]+(:[0-9]+)?');
    Route::delete('remove/{lineKey}', [CartController::class, 'remove'])->name('remove')->where('lineKey', '[0-9]+(:[0-9]+)?');
    Route::delete('clear', [CartController::class, 'clear'])->name('clear');
    Route::get('count', [CartController::class, 'count'])->name('count');
    Route::get('fragment', [CartController::class, 'fragment'])->name('fragment');
    Route::post('coupon', [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::delete('coupon', [CartController::class, 'removeCoupon'])->name('coupon.remove');
});

Route::prefix('wishlist')->as('shop.wishlist.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Shop\WishlistController::class, 'index'])->name('index');
    Route::post('toggle/{product}', [\App\Http\Controllers\Shop\WishlistController::class, 'toggle'])->name('toggle');
    Route::get('count', [\App\Http\Controllers\Shop\WishlistController::class, 'count'])->name('count');
});

Route::prefix('checkout')->as('shop.checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('confirmation');
    Route::get('confirmation/{orderNumber}/invoice', [CheckoutController::class, 'invoice'])->name('invoice');
});

/**
 * SSLCommerz online-payment callbacks. CSRF-exempt (see bootstrap/app.php) — the
 * gateway posts back from its own servers without our token.
 */
Route::prefix('payment/sslcommerz')->as('shop.payment.sslcommerz.')->group(function () {
    Route::match(['get', 'post'], 'success', [\App\Http\Controllers\Shop\PaymentController::class, 'success'])->name('success');
    Route::match(['get', 'post'], 'failed', [\App\Http\Controllers\Shop\PaymentController::class, 'failed'])->name('failed');
    Route::match(['get', 'post'], 'cancel', [\App\Http\Controllers\Shop\PaymentController::class, 'cancel'])->name('cancel');
    Route::post('ipn', [\App\Http\Controllers\Shop\PaymentController::class, 'ipn'])->name('ipn');
    Route::get('retry/{orderNumber}', [\App\Http\Controllers\Shop\PaymentController::class, 'retry'])->name('retry');
});

/**
 * Customer accounts (optional — guest checkout still works).
 * Uses the default `web` session guard on the users provider.
 */
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('account')->as('shop.account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('orders/{orderNumber}', [AccountController::class, 'showOrder'])->name('order');
    Route::post('orders/{orderNumber}/cancel', [AccountController::class, 'cancelOrder'])->name('orders.cancel');
    Route::get('orders/{orderNumber}/invoice', [AccountController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('profile', [AccountController::class, 'profile'])->name('profile');
    Route::post('profile', [AccountController::class, 'updateProfile'])->name('profile.update');
});
