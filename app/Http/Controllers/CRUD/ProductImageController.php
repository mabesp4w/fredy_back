<?php

namespace App\Http\Controllers\CRUD;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TOOLS\ImgToolsController;

class ProductImageController extends Controller
{
    protected $imgController;
    // make construct
    public function __construct()
    {
        // memanggil controller image
        $this->imgController = new ImgToolsController();
    }

    protected function spartaValidation($request, $id = "")
    {
        $required = "";
        if ($id == "") {
            $required = "required";
        }
        $rules = [
            'position' => 'required',
        ];

        $messages = [
            'position.required' => 'Nama ProductImage harus diisi.',
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
        $product_variant_id = $request->product_variant_id;
        $data = ProductImage::with('productVariant')
            ->where(function ($query) use ($search) {
                $query->where('position', 'like', "%$search%");
            })
            ->where(function ($query) use ($product_variant_id) {
                $query->where('product_variant_id', $product_variant_id);
            })
            ->orderBy($sortby ?? 'position', $order ?? 'asc')
            ->paginate(10);
        return new CrudResource('success', 'Data ProductImage', $data);
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
        $validate = $this->spartaValidation($data_req);
        if ($validate) {
            return $validate;
        }
        // unset user
        unset($data_req['user']);
        DB::beginTransaction();
        try {
            // export foto
            if ($request->hasFile('product_img')) {
                foreach ($request->file('product_img') as $file) {
                    // Simpan setiap gambar ke storage
                    $product_img = $this->imgController->addImage('product_img', $file);

                    // Jika gagal upload, rollback transaksi dan kirim pesan error
                    if (!$product_img) {
                        DB::rollback();
                        return new CrudResource('error', 'Gagal Upload Foto', null);
                    }

                    // Buat entri baru di ProductImage untuk setiap gambar
                    ProductImage::create([
                        'position' => $data_req['position'],
                        'product_variant_id' => $data_req['product_variant_id'],
                        'product_img' => "storage/$product_img",
                    ]);
                }
            }
            // get last data
            $data = ProductImage::with('productVariant')
                ->where('product_variant_id', $data_req['product_variant_id'])->latest()->get();
            // add options
            DB::commit();
            return new CrudResource('success', 'Data Berhasil Disimpan', $data);
        } catch (\Throwable $th) {
            // jika terdapat kesalahan
            DB::rollback();
            $message = [
                'judul' => 'Gagal',
                'type' => 'error',
                'message' => $th->getMessage(),
            ];
            return response()->json($message, 400);
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
        $validate = $this->spartaValidation($data_req, $id);
        if ($validate) {
            return $validate;
        }
        // unset user
        unset($data_req['user']);
        unset($data_req['_method']);
        DB::beginTransaction();
        try {
            $productImage = ProductImage::with('productVariant')
                ->findOrFail($id);
            // find file product_img
            $product_img = $productImage->product_img;
            // export product_img
            if ($request->hasFile('product_img')) {
                // remove file product_img jika ada
                if ($product_img) {
                    File::delete($product_img);
                }
                foreach ($request->file('product_img') as $file) {
                    // Simpan setiap gambar ke storage
                    $product_img = $this->imgController->addImage('product_img', $file);

                    // Jika gagal upload, rollback transaksi dan kirim pesan error
                    if (!$product_img) {
                        DB::rollback();
                        return new CrudResource('error', 'Gagal Upload Foto', null);
                    }
                }
                $data_req['product_img'] = "storage/$product_img";
            } else {
                $data_req['product_img'] = $product_img;
            }
            // Update the content
            $productImage->update($data_req);
            DB::commit();
            return new CrudResource('success', 'Data Berhasil Diperbarui', $productImage);
        } catch (\Throwable $th) {
            // Jika terdapat kesalahan
            DB::rollback();
            $message = [
                'judul' => 'Gagal',
                'type' => 'error',
                'message' => $th->getMessage(),
            ];
            return response()->json($message, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = ProductImage::findOrFail($id);
        // get product_img
        $product_img = $data->product_img;
        // remove product_img product_img
        if ($product_img) {
            File::delete($product_img);
        }
        // delete data
        $data->delete();

        return new CrudResource('success', 'Data Berhasil Dihapus', $data);
    }
}
