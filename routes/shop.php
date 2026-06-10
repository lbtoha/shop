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

Route::prefix('cart')->as('shop.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('add/{product}', [CartController::class, 'add'])->name('add');
    Route::put('update/{product}', [CartController::class, 'update'])->name('update');
    Route::delete('remove/{product}', [CartController::class, 'remove'])->name('remove');
    Route::delete('clear', [CartController::class, 'clear'])->name('clear');
    Route::get('count', [CartController::class, 'count'])->name('count');
    Route::get('fragment', [CartController::class, 'fragment'])->name('fragment');
});

Route::prefix('checkout')->as('shop.checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('confirmation');
    Route::get('confirmation/{orderNumber}/invoice', [CheckoutController::class, 'invoice'])->name('invoice');
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
});
