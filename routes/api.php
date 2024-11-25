<?php

use Illuminate\Support\Facades\Route;

// categories
Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [App\Http\Controllers\API\CategoryAPI::class, 'index'])->name('categories');
    Route::get('all', [App\Http\Controllers\API\CategoryAPI::class, 'all'])->name('categories.all');
    Route::get('{id}', [App\Http\Controllers\API\CategoryAPI::class, 'detail'])->name('categories.detail');
});

// products
Route::group(['prefix' => 'products'], function () {
    Route::get('/', [App\Http\Controllers\API\ProdukAPI::class, 'index'])->name('products');
    Route::get('welcome', [App\Http\Controllers\API\ProdukAPI::class, 'welcome'])->name('products.welcome');
    Route::get('getProductIds', [App\Http\Controllers\API\ProdukAPI::class, 'getProductIds'])->name('products.getProductIds');
    Route::get('detail/{id}', [App\Http\Controllers\API\ProdukAPI::class, 'detail'])->name('products.detail');
    Route::get('category/{category_id}', [App\Http\Controllers\API\ProdukAPI::class, 'category'])->name('products.category');
});

// shippingCosts
Route::group(['prefix' => 'shippingCosts'], function () {
    Route::get('/', [App\Http\Controllers\API\ShippingCostAPI::class, 'index'])->name('shippingCosts');
    Route::get('all', [App\Http\Controllers\API\ShippingCostAPI::class, 'all'])->name('shippingCosts.all');
});

// carts
Route::group(['prefix' => 'carts'], function () {
    Route::get('/', [App\Http\Controllers\API\CartAPI::class, 'getCartData'])->name('getCartData');
    Route::post('addToCartSession', [App\Http\Controllers\API\CartAPI::class, 'addToCartSession'])->name('addToCartSession');
    Route::post('removeFromCartSession', [App\Http\Controllers\API\CartAPI::class, 'removeFromCartSession'])->name('removeFromCartSession');
    Route::post('copySessionCartToDatabase', [App\Http\Controllers\API\CartAPI::class, 'copySessionCartToDatabase'])->name('copySessionCartToDatabase');
    Route::post('addToCartDatabase', [App\Http\Controllers\API\CartAPI::class, 'addToCartDatabase'])->name('addToCartDatabase');
    Route::post('setCartQuantity', [App\Http\Controllers\API\CartAPI::class, 'setCartQuantity'])->name('setCartQuantity');
    Route::post('removeFromCartDatabase', [App\Http\Controllers\API\CartAPI::class, 'removeFromCartDatabase'])->name('removeFromCartDatabase');
});

// payments
Route::group(['prefix' => 'payment'], function () {
    Route::post('/', [App\Http\Controllers\API\PaymentAPI::class, 'submitPayment'])->name('submitPayment');
    Route::get('transactionStatus/{orderId}', [App\Http\Controllers\API\PaymentAPI::class, 'getTransactionStatus'])->name('getTransactionStatus');
    Route::post('callback', [App\Http\Controllers\API\PaymentAPI::class, 'paymentCallback'])->name('paymentCallback');
});

// orders
Route::group(['prefix' => 'orders'], function () {
    Route::get('/', [App\Http\Controllers\API\OrderAPI::class, 'index'])->name('orders');
    Route::get('all', [App\Http\Controllers\API\OrderAPI::class, 'all'])->name('orders.all');
});
