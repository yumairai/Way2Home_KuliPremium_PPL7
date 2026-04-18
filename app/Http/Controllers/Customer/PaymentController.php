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

class PaymentController extends Controller
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

        // Validasi: Pastikan user sudah login
        if (!$user) {
            return response()->json(['message' => 'Sesi berakhir, silakan login kembali.'], 401);
        }
        
        // 1. Cari data di tabel customers berdasarkan user_id
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Profil Customer tidak ditemukan. Pastikan Anda sudah terdaftar sebagai pelanggan.'
            ], 404);
        }
        
        // 2. Ambil isi keranjang belanja
        $cartItems = Cart::where('user_id', $user->id)->with('material')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Keranjang Anda kosong.'], 400);
        }

        // 3. Hitung Subtotal
        $subtotal = $cartItems->sum(function($item) {
            return $item->jumlah * $item->material->harga;
        });

        // 4. Hitung Biaya Layanan (2%, Min 5rb, Max 50rb)
        $serviceFee = $subtotal * 0.02;
        if ($serviceFee < 5000) $serviceFee = 5000;
        if ($serviceFee > 50000) $serviceFee = 50000;

        $grandTotal = $subtotal + $serviceFee;

        DB::beginTransaction();

        try {
            // 5. Buat ID unik untuk Midtrans
            $orderIdMidtrans = 'W2H-' . time() . '-' . $user->id;
            
            // 6. Simpan ke tabel OrderMaterial
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

            // 7. Pindahkan item dari Cart ke DetailOrder & Hapus Cart
            foreach ($cartItems as $item) {
                DetailOrder::create([
                    'order_material_id' => $order->id,
                    'material_id'       => $item->material_id,
                    'jumlah'            => $item->jumlah,
                    'harga_satuan'      => $item->material->harga,
                    'subtotal'          => $item->jumlah * $item->material->harga,
                ]);
                
                $item->delete(); // Hapus item dari keranjang
            }

            // 8. Parameter Midtrans
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

            // 9. Dapatkan Snap Token
            $snapToken = Snap::getSnapToken($params);
            
            // 10. Update Snap Token ke database
            $order->update(['snap_token' => $snapToken]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'token'   => $snapToken,
                'order_id'=> $orderIdMidtrans
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}