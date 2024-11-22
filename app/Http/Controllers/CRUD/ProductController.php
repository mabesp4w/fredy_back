<?php

namespace App\Http\Controllers\CRUD;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected function spartaValidation($request, $id = "")
    {
        $required = "";
        if ($id == "") {
            $required = "required";
        }
        $rules = [
            'product_nm' => 'required',
        ];

        $messages = [
            'product_nm.required' => 'Nama Product harus diisi.',
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
        $data = Product::with('category')
            ->where(function ($query) use ($search) {
                $query->where('product_nm', 'like', "%$search%");
            })
            ->orderBy($sortby ?? 'product_nm', $order ?? 'asc')
            ->paginate(10);
        return new CrudResource('success', 'Data Product', $data);
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
        Product::create($data_req);

        $data = Product::with('category')
            ->latest()->first();

        return new CrudResource('success', 'Data Berhasil Disimpan', $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Product::with('category')
            ->find($id);
        return new CrudResource('success', 'Data Product', $data);
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

        Product::find($id)->update($data_req);

        $data = Product::with('category')
            ->find($id);

        return new CrudResource('success', 'Data Berhasil Diubah', $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Product::findOrFail($id);
        // delete data
        $data->delete();

        return new CrudResource('success', 'Data Berhasil Dihapus', $data);
    }
}
