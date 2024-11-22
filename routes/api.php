<?

use Illuminate\Support\Facades\Route;

// categories
Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [App\Http\Controllers\API\CategoryAPI::class, 'index'])->name('categories');
    Route::get('all', [App\Http\Controllers\API\CategoryAPI::class, 'all'])->name('categories.all');
});

// products
Route::group(['prefix' => 'products'], function () {
    Route::get('/', [App\Http\Controllers\API\ProdukAPI::class, 'index'])->name('products');
    Route::get('welcome', [App\Http\Controllers\API\ProdukAPI::class, 'welcome'])->name('products.welcome');
    Route::get('getProductIds', [App\Http\Controllers\API\ProdukAPI::class, 'getProductIds'])->name('products.getProductIds');
    Route::get('detail/{id}', [App\Http\Controllers\API\ProdukAPI::class, 'detail'])->name('products.detail');
    Route::get('category/{category_id}', [App\Http\Controllers\API\ProdukAPI::class, 'category'])->name('products.category');
});
