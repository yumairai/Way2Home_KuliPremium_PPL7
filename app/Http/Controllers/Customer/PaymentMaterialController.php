<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\OrderMaterial;
use App\Models\DetailOrder;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentMaterialController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function checkout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Sesi berakhir, silakan login kembali.'], 401);
        }

        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Profil Customer tidak ditemukan.'
            ], 404);
        }

        $cartItems = Cart::where('user_id', $user->id)->with('material')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Keranjang Anda kosong.'], 400);
        }

        $existingPending = OrderMaterial::where('customer_id', $customer->id)
            ->where('status_order', 'pending')
            ->latest()
            ->first();

        if ($existingPending) {
            // Kembalikan snap token yang sudah ada
            return response()->json([
                'status'   => 'success',
                'token'    => $existingPending->snap_token,
                'order_id' => $existingPending->order_id_midtrans
            ]);
        }

        $subtotal = $cartItems->sum(fn($item) => $item->jumlah * $item->material->harga);

        $serviceFee = $subtotal * 0.02;
        if ($serviceFee < 5000)  $serviceFee = 5000;
        if ($serviceFee > 50000) $serviceFee = 50000;

        $grandTotal = $subtotal + $serviceFee;

        DB::beginTransaction();

        try {
            $orderIdMidtrans = 'W2H-' . time() . '-' . $user->id;

            $order = OrderMaterial::create([
                'customer_id'       => $customer->id,
                'order_id_midtrans' => $orderIdMidtrans,
                'tanggal_order'     => now(),
                'alamat_pengiriman' => $request->alamat ?? 'Alamat belum diisi',
                'subtotal_material' => $subtotal,
                'biaya_layanan'     => $serviceFee,
                'total_harga'       => $grandTotal,
                'status_order'      => 'pending',
            ]);

            // Salin item ke detail_order TAPI cart BELUM dihapus
            foreach ($cartItems as $item) {
                DetailOrder::create([
                    'order_material_id' => $order->id,
                    'material_id'       => $item->material_id,
                    'jumlah'            => $item->jumlah,
                    'harga_satuan'      => $item->material->harga,
                    'subtotal'          => $item->jumlah * $item->material->harga,
                ]);
                // $item->delete() ← DIHAPUS dari sini
            }

            $params = [
                'transaction_details' => [
                    'order_id'     => $orderIdMidtrans,
                    'gross_amount' => (int) $grandTotal,
                ],
                'customer_details' => [
                    'first_name' => $customer->nama ?? $user->name,
                    'email'      => $user->email,
                    'phone'      => $customer->telepon ?? $request->telepon ?? '',
                ],
                'item_details' => [
                    [
                        'id'       => 'MAT-TOTAL',
                        'price'    => (int) $subtotal,
                        'quantity' => 1,
                        'name'     => 'Total Material Belanja'
                    ],
                    [
                        'id'       => 'FEE-01',
                        'price'    => (int) $serviceFee,
                        'quantity' => 1,
                        'name'     => 'Biaya Layanan Sistem'
                    ]
                ]
            ];

            $snapToken = Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);

            DB::commit();

            return response()->json([
                'status'   => 'success',
                'token'    => $snapToken,
                'order_id' => $orderIdMidtrans
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleSuccess(Request $request)
    {
        $order = OrderMaterial::where('order_id_midtrans', $request->order_id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        if ($order->status_order === 'pending') {
            $order->update(['status_order' => 'paid']);
        }

        if ($transactionStatus === 'settlement' || 
        ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
            $status = 'paid';
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $status = $transactionStatus;
        } else {
            $status = 'pending';
        }
        // Baru hapus cart setelah pembayaran sukses
        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'OK']);
    }
}