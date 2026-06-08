<?php

use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('shop.product');

Route::prefix('cart')->as('shop.cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('add/{product}', [CartController::class, 'add'])->name('add');
    Route::put('update/{product}', [CartController::class, 'update'])->name('update');
    Route::delete('remove/{product}', [CartController::class, 'remove'])->name('remove');
    Route::get('count', [CartController::class, 'count'])->name('count');
});

Route::prefix('checkout')->as('shop.checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('confirmation');
});
