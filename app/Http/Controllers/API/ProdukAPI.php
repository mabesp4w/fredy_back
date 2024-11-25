<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;

class ProdukAPI extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search'); // Ambil parameter 'search'
        $products = ProductVariant::query();

        if ($query) {
            $products->whereHas('product', function ($product) use ($query) {
                $product->where('product_nm', 'like', "%{$query}%");
            })
                ->orWhere('variant_nm', 'like', "%{$query}%");
        }

        $products = $products->with(['product.category', 'productVariantImages', 'review.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return new CrudResource('success', 'Data Product', $products);
    }
    public function category($category_id, Request $request)
    {
        $search = $request->search;
        $sortby = $request->sortby;
        $order = $request->order;
        $limit = $request->limit ?? 10;

        $products = Product::with(['category', 'productVariants.productVariantImages'])
            ->where('category_id', $category_id)
            ->whereExists(function ($query) {  // Hapus type hint dari sini
                $query->select(DB::raw(1))
                    ->from('product_variants')
                    ->whereColumn('product_variants.product_id', 'products.id');
            })
            ->where(function ($query) use ($search) {
                $query->where('product_nm', 'like', "%$search%")
                    ->orWhereHas('productVariants', function ($query) use ($search) {
                        $query->where('variant_nm', 'like', "%$search%");
                    });
            })
            ->orderBy($sortby ?? 'product_nm', $order ?? 'asc')
            ->paginate($limit);

        return new CrudResource('success', 'Data Product', $products);
    }

    function detail($id)
    {
        $product = Product::with(['category', 'productVariants.productVariantImages', 'productVariants.review.user', 'productVariants.orderItem.order'])
            ->whereExists(function ($query) {  // Hapus type hint dari sini
                $query->select(DB::raw(1))
                    ->from('product_variants')
                    ->whereColumn('product_variants.product_id', 'products.id');
            })
            ->find($id);

        return new CrudResource('success', 'Data Product', $product);
    }

    public function welcome()
    {
        // new product
        $newProduct = Product::with(['category', 'productVariants.productVariantImages'])
            ->whereExists(function ($query) {  // Hapus type hint dari sini
                $query->select(DB::raw(1))
                    ->from('product_variants')
                    ->whereColumn('product_variants.product_id', 'products.id');
            })
            ->orderBy('created_at', 'desc')
            ->take(8)->get();
        // bestSeller
        $bestSellers = Product::with(['category', 'productVariants.productVariantImages'])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('product_variants')
                    ->whereColumn('product_variants.product_id', 'products.id');
            })
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('order_items', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->select([
                'products.id',
                'products.name',
                'products.category_id',
                'products.created_at',
                'products.updated_at',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_ordered')
            ])
            ->groupBy('products.id', 'products.name', 'products.category_id', 'products.created_at', 'products.updated_at')
            ->orderByDesc('total_ordered')
            ->take(8)
            ->get();

        return new CrudResource('success', 'Data Product', ['newProduct' => $newProduct, 'bestSellers' => $bestSellers]);
    }

    public function getProductIds(Request $request)
    {
        $Ids = $request->input('ids', []);
        $productIds = array_column($Ids, 'product_id');

        $products = ProductVariant::whereIn('product_id', $productIds)
            ->with(['product.category', 'productImages'])
            ->get();

        return new CrudResource('success', 'Data Product', $products);
    }
}
