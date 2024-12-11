<?php

namespace App\Http\Controllers\API;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;
use Midtrans\Transaction;
use Illuminate\Http\Request;
use App\Models\ShippingStatus;
use App\Http\Controllers\Controller;

class PaymentAPI extends Controller
{
    protected $serverKey;
    public function __construct()
    {
        // Konfigurasi Midtrans

        $this->serverKey = Config::$serverKey = Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.sanitized');
        Config::$is3ds = config('services.midtrans.3ds');
    }

    public function submitPayment(Request $request)
    {
        $order = Order::with(['orderItems.productVariant.productVariantImages', 'orderItems.productVariant.product',  'user.userInfo', 'shippingCost'])
            ->find($request->order_id); // Dapatkan order berdasarkan ID
        $item_details = [];
        foreach ($order->orderItems as $item) {
            $item_details[] = [
                'id' => $item['product_variant_id'],
                'price' => $item['productVariant']['price'],
                'quantity' => $item['quantity'],
                'name' => $item['productVariant']['product']['product_nm']
            ];
        }
        $item_details[] = [
            'id' => 'shipping_cost',
            'price' => $order->shippingCost->shipping_cost,
            'quantity' => 1,
            'name' => 'Ongkos Kirim'
        ];

        $params = [
            'transaction_details' => [
                'order_id' => $order->id,
                'gross_amount' => $order->total_payment,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->userInfo[0]->phone_number,
            ],
            'item_details' => $item_details,
            'expiry' => [
                'start_time' => date("Y-m-d H:i:s T"), // Waktu mulai transaksi
                'unit' => 'minute', // Unit waktu kedaluwarsa (minute, hour, day)
                'duration' => 60 // Durasi kedaluwarsa, contoh 60 menit
            ],
        ];

        $snapToken = Snap::getSnapToken($params);
        $order->update(['snap_token' => $snapToken]);
        return response()->json($snapToken);
    }

    public function getTransactionStatus($orderId)
    {
        try {
            $status = Transaction::status($orderId);
            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function paymentCallback(Request $request)
    {
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $this->serverKey);
        // Inisialisasi notifikasi
        try {
            // Proses notifikasi berdasarkan status
            if ($hashed == $request->signature_key) {
                if ($request->transaction_status == 'capture') {
                    // Transaksi berhasil
                    $order = Order::findOrFail($request->order_id);
                    $order->update(['status' => 'dibayar']);
                    // add shipping status
                    ShippingStatus::create([
                        'order_id' => $request->order_id,
                        'user_id' => $order->user_id,
                        'status' => 'dikemas'
                    ]);
                    // reduce stock
                    foreach ($order->orderItems as $item) {
                        $productVariant = $item->productVariant;
                        $productVariant->update(['stock' => $productVariant->stock - $item->quantity]);
                    }
                } elseif ($request->transaction_status == 'settlement') {
                    // Transaksi selesai
                    $order = Order::findOrFail($request->order_id);
                    $order->update(['status' => 'dibayar']);
                    ShippingStatus::create([
                        'order_id' => $request->order_id,
                        'user_id' => $order->user_id,
                        'status' => 'dikemas'
                    ]);
                    // reduce stock
                    foreach ($order->orderItems as $item) {
                        $productVariant = $item->productVariant;
                        $productVariant->update(['stock' => $productVariant->stock - $item->quantity]);
                    }
                } elseif ($request->transaction_status == 'pending') {
                    // Transaksi menunggu pembayaran
                    Order::findOrFail($request->order_id)->update(['status' => 'tunggu']);
                } elseif ($request->transaction_status == 'deny') {
                    // Transaksi ditolak
                    Order::findOrFail($request->order_id)->update(['status' => 'batal']);
                } elseif ($request->transaction_status == 'expire') {
                    // Transaksi kadaluarsa
                    Order::findOrFail($request->order_id)->update(['status' => 'batal']);
                } elseif ($request->transaction_status == 'cancel') {
                    // Transaksi dibatalkan
                    Order::findOrFail($request->order_id)->update(['status' => 'batal']);
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error processing notification',
                'error' => $th
            ], 500);
        }
    }
}
