<?php

namespace App\Http\Controllers\CRUD;

use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;
use Illuminate\Support\Facades\Validator;

class ProductVariantController extends Controller
{
    protected function spartaValidation($request, $id = "")
    {
        $required = "";
        if ($id == "") {
            $required = "required";
        }
        $rules = [
            'variant_nm' => 'required',
        ];

        $messages = [
            'variant_nm.required' => 'Nama ProductVariant harus diisi.',
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
        $product_id = $request->product_id;
        $data = ProductVariant::with('product')
            ->where(function ($query) use ($search) {
                $query->where('variant_nm', 'like', "%$search%");
            })
            ->where(function ($query) use ($product_id) {
                $query->where('product_id', $product_id);
            })
            ->orderBy($sortby ?? 'variant_nm', $order ?? 'asc')
            ->paginate(10);
        return new CrudResource('success', 'Data ProductVariant', $data);
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
        ProductVariant::create($data_req);

        $data = ProductVariant::with('product')
            ->latest()->first();

        return new CrudResource('success', 'Data Berhasil Disimpan', $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = ProductVariant::with('product')
            ->find($id);
        return new CrudResource('success', 'Data ProductVariant', $data);
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

        ProductVariant::find($id)->update($data_req);

        $data = ProductVariant::with('product')
            ->find($id);

        return new CrudResource('success', 'Data Berhasil Diubah', $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = ProductVariant::findOrFail($id);
        // delete data
        $data->delete();

        return new CrudResource('success', 'Data Berhasil Dihapus', $data);
    }
}
