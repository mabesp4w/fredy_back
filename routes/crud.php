<?php

use Illuminate\Support\Facades\Route;


Route::resources([
    'subDistricts' => App\Http\Controllers\CRUD\SubDistrictController::class,
    'shippingCosts' => App\Http\Controllers\CRUD\ShippingCostController::class,
    'categories' => App\Http\Controllers\CRUD\CategoryController::class,
    'products' => App\Http\Controllers\CRUD\ProductController::class,
    'variants' => App\Http\Controllers\CRUD\ProductVariantController::class,
    'productImages' => App\Http\Controllers\CRUD\ProductImageController::class,
    'userInfos' => App\Http\Controllers\CRUD\UserInfoController::class,
    'orders' => App\Http\Controllers\CRUD\OrderController::class,
    'reviews' => App\Http\Controllers\CRUD\ReviewController::class
]);
