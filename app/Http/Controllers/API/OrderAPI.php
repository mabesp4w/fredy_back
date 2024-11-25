<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderVariant;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;

class OrderAPI extends Controller
{
    public function index(Request $request) {}
    public function all(Request $request)
    {
        $user_id = $request->user_id;
        $status = $request->status;
        $orders = Order::with(['orderItems.productVariant.productVariantImages', 'orderItems.productVariant.product',  'user.userInfo', 'shippingCost', "shippingStatus"])
            ->where('user_id', $user_id)
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status)
                    ->orWhereHas('shippingStatus', function ($query) use ($status) {
                        $query->where('status', $status);
                    });
            })
            ->get();
        return new CrudResource('success', 'Data Order', $orders);
    }
}
