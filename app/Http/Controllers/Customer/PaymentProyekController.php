<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Proyek;
use App\Models\PembayaranProyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentProyekController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    public function bayarDP(Request $request)
    {
        $request->validate([
            'proyek_id' => 'required|exists:proyek,id',
        ]);

        $customer = Auth::user()->customer;
        $proyek   = Proyek::with('detailBangun.desainRumah')
            ->where('id', $request->proyek_id)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        $desain    = $proyek->detailBangun->desainRumah;
        $nominalDP = (int) round($desain->estimasi_biaya * 0.30);
        $orderId   = 'DP-' . $proyek->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $nominalDP,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email'      => Auth::user()->email,
            ],
            'item_details' => [
                [
                    'id'       => 'DP-' . $proyek->id,
                    'price'    => $nominalDP,
                    'quantity' => 1,
                    'name'     => 'Down Payment - ' . $desain->tipe_rumah,
                ],
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        // Simpan record pembayaran dengan status pending
        PembayaranProyek::create([
            'proyek_id'         => $proyek->id,
            'snap_token'        => $snapToken,
            'order_id'          => $orderId,
            'jumlah_bayar'      => $nominalDP,
            'tipe_pembayaran'   => 'DP',
            'metode_pembayaran' => 'Midtrans',
            'status_pembayaran' => 'pending',
            'tanggal_pembayaran' => now()->toDateString(),
        ]);

        return response()->json([
            'snap_token' => $snapToken,
            'nominal_dp' => $nominalDP,
        ]);
    }

    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashedKey = hash(
            'sha512',
            $request->order_id .
                $request->status_code .
                $request->gross_amount .
                $serverKey
        );

        if ($hashedKey !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $pembayaran = PembayaranProyek::where('order_id', $request->order_id)->first();

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran not found'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $fraudStatus       = $request->fraud_status ?? null;

        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            $this->tandaiDP($pembayaran);
        } elseif ($transactionStatus === 'settlement') {
            $this->tandaiDP($pembayaran);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $pembayaran->update(['status_pembayaran' => 'gagal']);
        }

        return response()->json(['message' => 'OK']);
    }

    private function tandaiDP(PembayaranProyek $pembayaran)
    {
        // Cek apakah relasi proyek ditemukan
        if ($pembayaran->proyek) {
            $pembayaran->update(['status_pembayaran' => 'berhasil']);
            $pembayaran->proyek->update(['status_proyek' => 'Pengalokasian Mandor']);
        } else {
            Log::error('Relasi proyek tidak ditemukan untuk Order ID: ' . $pembayaran->order_id);
        }
    }
}
