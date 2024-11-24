<?php

namespace App\Http\Controllers\API;

use App\Models\ShippingCost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;

class ShippingCostAPI extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sortby = $request->sortby;
        $order = $request->order;
        $limit = $request->limit;

        $data = ShippingCost::where(function ($query) use ($search) {
            $query->where('village_nm', 'like', "%$search%");
        })
            ->when($sortby, function ($query) use ($sortby, $order) {
                $query->orderBy($sortby, $order ?? 'asc');
            })
            ->paginate($limit);
        return new CrudResource('success', 'Data ShippingCost', $data);
    }

    // all shippingCost
    public function all()
    {
        $data = ShippingCost::with('subDistrict')->get();
        return new CrudResource('success', 'Data ShippingCost', $data);
    }
}
