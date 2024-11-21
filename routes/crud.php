<?

use Illuminate\Support\Facades\Route;


Route::resources([
    'subDistricts' => App\Http\Controllers\CRUD\SubDistrictController::class,
    'shippingCosts' => App\Http\Controllers\CRUD\ShippingCostController::class,
    'productImages' => App\Http\Controllers\CRUD\ProductImageController::class,
    'products' => App\Http\Controllers\CRUD\ProductController::class,
    'carts' => App\Http\Controllers\CRUD\CartController::class
]);
