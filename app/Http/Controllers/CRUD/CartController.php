<?php

namespace App\Http\Controllers\CRUD;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CrudResource;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function getCartData()
    {
        if (Auth::check()) {
            // Pengguna sudah login, ambil data cart dari database
            $cartItems = Cart::where('user_id', Auth::id())->get();
        } else {
            // Pengguna belum login, ambil data cart dari sesi
            $cartItems = collect(session()->get('cart', []))->map(function ($item, $productId) {
                // Ubah format jika diperlukan
                return [
                    'product_id' => $productId,
                    'quantity' => $item['quantity']
                ];
            });
        }

        return new CrudResource('success', 'Data Category', $cartItems);
    }
    // Menyimpan Cart di Sesi saat Belum Login
    public function addToCartSession(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);
        return response()->json(['message' => 'Added to session cart']);
    }
    // Menghapus Item dari Cart di Sesi
    public function removeFromCartSession(Request $request)
    {
        $productId = $request->input('product_id');

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]); // Hapus item berdasarkan product_id
            session()->put('cart', $cart); // Perbarui session
            return response()->json(['message' => 'Item removed from session cart']);
        }

        return response()->json(['message' => 'Item not found in session cart'], 404);
    }

    // Menyalin Isi Sesi ke Tabel Cart Saat Login
    public function copySessionCartToDatabase()
    {
        $cart = session()->get('cart', []);

        foreach ($cart as $productId => $item) {
            Cart::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'product_id' => $productId
                ],
                [
                    'quantity' => $item['quantity']
                ]
            );
        }

        session()->forget('cart'); // Hapus sesi setelah memindahkan ke tabel
    }
    // Menyimpan Cart di Tabel Jika Sudah Login
    public function addToCartDatabase(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        // Cek apakah item sudah ada di cart
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            // Jika sudah ada, tambahkan quantity
            $cartItem->increment('quantity', $quantity);
        } else {
            // Jika belum ada, buat baru dengan quantity
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        return response()->json(['message' => 'Added to database cart']);
    }

    public function setCartQuantity(Request $request)
    {
        $productId = $request->input('product_id');
        $newQuantity = $request->input('quantity');

        // Cek apakah item ada di cart
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            // Jika item ditemukan, update quantity
            if ($newQuantity > 0) {
                $cartItem->update(['quantity' => $newQuantity]);
                return response()->json(['message' => 'Product quantity updated in database cart']);
            } else {
                // Jika quantity baru kurang dari atau sama dengan 0, hapus item dari cart
                $cartItem->delete();
                return response()->json(['message' => 'Product removed from cart due to zero quantity']);
            }
        } else {
            // Jika item tidak ditemukan di cart, dan newQuantity lebih dari 0, tambahkan ke cart
            if ($newQuantity > 0) {
                Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
                    'quantity' => $newQuantity
                ]);
                return response()->json(['message' => 'Product added to cart with specified quantity']);
            } else {
                // Jika newQuantity tidak lebih dari 0, tidak perlu menambahkan apa-apa
                return response()->json(['message' => 'No action taken, quantity specified is zero or less']);
            }
        }
    }

    public function removeFromCartDatabase(Request $request)
    {
        $productId = $request->input('product_id');

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->delete();
            return response()->json(['message' => 'Removed from database cart']);
        }

        return response()->json(['message' => 'Product not found in database cart'], 404);
    }
}
