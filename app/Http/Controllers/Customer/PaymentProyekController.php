<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PembayaranProyek;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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

    // ─────────────────────────────────────────────────────────────
    // INISIASI PEMBAYARAN (DP maupun Cicilan)
    // POST /proyek/bayar
    // ─────────────────────────────────────────────────────────────

    public function bayar(Request $request)
    {
        $request->validate([
            'pembayaran_id' => 'required|exists:pembayaran_proyek,id',
        ]);

        $customer   = Auth::user()->customer;
        $pembayaran = PembayaranProyek::with('proyek.detailBangun.desainRumah')
            ->whereHas('proyek', fn($q) => $q->where('customer_id', $customer->id))
            ->findOrFail($request->pembayaran_id);

        // Hanya status aktif yang bisa diproses
        if (!$pembayaran->isAktif()) {
            return response()->json([
                'message' => 'Pembayaran tidak dapat diproses saat ini.',
            ], 422);
        }

        // Pastikan periode sebelumnya sudah lunas
        $adaYangBelumLunas = PembayaranProyek::where('proyek_id', $pembayaran->proyek_id)
            ->where('periode', '<', $pembayaran->periode)
            ->where('status_pembayaran', '!=', 'berhasil')
            ->exists();

        if ($adaYangBelumLunas) {
            return response()->json([
                'message' => 'Harap selesaikan pembayaran sebelumnya terlebih dahulu.',
            ], 422);
        }

        $desain  = $pembayaran->proyek->detailBangun->desainRumah;
        $prefix  = $pembayaran->isDP() ? 'DP' : 'CICILAN-P' . $pembayaran->periode;
        $orderId = $prefix . '-' . $pembayaran->proyek_id . '-' . time();

        $itemName = $pembayaran->isDP()
            ? 'Down Payment - ' . $desain->tipe_rumah
            : 'Cicilan Periode ' . $pembayaran->periode . ' - ' . $desain->tipe_rumah;

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $pembayaran->jumlah_bayar,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email'      => Auth::user()->email,
            ],
            'item_details' => [[
                'id'       => $prefix . '-' . $pembayaran->proyek_id,
                'price'    => $pembayaran->jumlah_bayar,
                'quantity' => 1,
                'name'     => $itemName,
            ]],
        ];

        $snapToken = Snap::getSnapToken($params);

        $pembayaran->update([
            'snap_token'       => $snapToken,
            'order_id'         => $orderId,
            'status_pembayaran' => 'pending',
        ]);

        return response()->json([
            'snap_token' => $snapToken,
            'nominal'    => $pembayaran->jumlah_bayar,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // VERIFIKASI SETELAH MIDTRANS REDIRECT (client-side)
    // POST /proyek/payment-success
    // ─────────────────────────────────────────────────────────────

    public function handleSuccess(Request $request)
    {
        $request->validate(['order_id' => 'required']);

        $pembayaran = PembayaranProyek::with('proyek')
            ->where('order_id', $request->order_id)
            ->firstOrFail();

        $serverKey = config('midtrans.server_key');
        $baseUrl   = config('midtrans.is_production')
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';

        $response = Http::withBasicAuth($serverKey, '')
            ->get("{$baseUrl}/{$request->order_id}/status");

        if (!$response->successful()) {
            return response()->json(['message' => 'Gagal verifikasi ke Midtrans'], 500);
        }

        $data              = $response->json();
        $transactionStatus = $data['transaction_status'] ?? null;
        $fraudStatus       = $data['fraud_status'] ?? null;
        $paymentType       = $data['payment_type'] ?? null;

        if (
            ($transactionStatus === 'capture' && $fraudStatus === 'accept') ||
            $transactionStatus === 'settlement'
        ) {
            $this->tandaiBerhasil($pembayaran, $paymentType);
            return response()->json(['message' => 'Pembayaran tervalidasi']);
        }

        return response()->json([
            'message' => 'Status belum valid',
            'status'  => $transactionStatus,
        ], 400);
    }

    // ─────────────────────────────────────────────────────────────
    // MIDTRANS SERVER-TO-SERVER CALLBACK
    // POST /midtrans/callback
    // ─────────────────────────────────────────────────────────────

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

        $pembayaran = PembayaranProyek::with('proyek')
            ->where('order_id', $request->order_id)
            ->first();

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran not found'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $fraudStatus       = $request->fraud_status ?? null;
        $paymentType       = $request->payment_type ?? null;

        if (
            ($transactionStatus === 'capture' && $fraudStatus === 'accept') ||
            $transactionStatus === 'settlement'
        ) {
            $this->tandaiBerhasil($pembayaran, $paymentType);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $pembayaran->update(['status_pembayaran' => 'gagal']);
        }

        return response()->json(['message' => 'OK']);
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE HELPER
    // ─────────────────────────────────────────────────────────────

    private function tandaiBerhasil(PembayaranProyek $pembayaran, ?string $paymentType): void
    {
        $pembayaran->update([
            'status_pembayaran'  => 'berhasil',
            'tanggal_bayar'      => now()->toDateString(),
            'metode_pembayaran'  => $paymentType,
        ]);

        // Jika ini DP, update status proyek ke Pengalokasian Mandor
        if ($pembayaran->isDP() && $pembayaran->proyek) {
            $pembayaran->proyek->update(['status_proyek' => 'Pengalokasian Mandor']);
        }

        Log::info("Pembayaran periode {$pembayaran->periode} proyek #{$pembayaran->proyek_id} berhasil.");
    }
}