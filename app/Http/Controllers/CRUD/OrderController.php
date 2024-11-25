<?php

namespace App\Http\Controllers\CRUD;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\ShippingStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected function spartaValidation($request, $id = "")
    {
        $required = "";
        if ($id == "") {
            $required = "required";
        }
        $rules = [
            'status' => 'required',
        ];

        $messages = [
            'status.required' => 'Nama Order harus diisi.',
        ];
        $validator = Validator::make($request, $rules, $messages);

        if ($validator->fails()) {
            $message = [
                'judul' => 'Gagal',
                'type' => 'error',
                'message' => $validator->errors()->first(),
            ];
            return response()->json($message, 400);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $sortby = $request->sortby;
        $order = $request->order;
        $status = explode(',', $request->status);

        $orders = Order::with([
            'user.userInfo',
            'orderItems.productVariant.productVariantImages',
            'shippingCost.subDistrict',
            'review',
            'orderItems.productVariant.product',
            "shippingStatus"
        ])
            ->where(function ($query) use ($search) {
                $query->where('status', 'like', "%$search%");
            })
            ->when($sortby, function ($query) use ($sortby, $order) {
                $query->orderBy($sortby, $order ?? 'asc');
            })
            ->when($status, function ($query) use ($status) {
                $query->whereIn('status', $status);
            })
            ->get();

        return new CrudResource('success', 'Data Order', $orders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data_req = $request->all();
        // return $data_req;
        $validate = $this->spartaValidation($data_req);
        if ($validate) {
            return $validate;
        }

        DB::beginTransaction();
        try {
            $order = Order::create($request->only(['user_id', 'shipping_cost_id', 'total_price', 'total_payment', 'status', 'address', 'shipping_cost']));

            foreach ($request->carts as $item) {
                // escape product
                unset($item['product_variant'], $item['created_at'], $item['updated_at']);
                $order->orderItems()->create($item);
                // delete cart
            }
            Cart::where('user_id', $request->user_id)->delete();
            DB::commit();
            return new CrudResource('success', 'Data Berhasil Disimpan', $order->load('orderItems'));
        } catch (\Throwable $th) {
            DB::rollBack();
            // error
            return response()->json(['message' => $th->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data_req = $request->all();
        // return $data_req;
        $validate = $this->spartaValidation($data_req, $id);
        if ($validate) {
            return $validate;
        }

        ShippingStatus::find($id)->update([
            'status' => $data_req['status'],
        ]);


        return new CrudResource('success', 'Data Berhasil Diubah', []);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Order::findOrFail($id);
        // delete data
        $data->delete();

        return new CrudResource('success', 'Data Berhasil Dihapus', $data);
    }
}
