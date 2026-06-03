<?php

namespace App\Http\Middleware;

use App\Models\PembayaranProyek;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BypassTesterRequest
{
    // ─── Supabase Public Asset Paths ──────────────────────────────────────────

    private const DOCS = [
        'imb'              => 'public-assets/testing/dokumen/imb.jpg',
        'ktp'              => 'public-assets/testing/dokumen/ktp.jpg',
        'sertifikat_tanah' => 'public-assets/testing/dokumen/sertifikat_tanah.jpg',
        'surat_kuasa'      => 'public-assets/testing/dokumen/surat_kuasa.jpg',
    ];

    private const RENOVASI_FOTO = 'public-assets/testing/renovasi/foto_renovasi.jpg';

    // ─── Middleware Entry Point ────────────────────────────────────────────────

    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production')) {
            return $next($request);
        }

        $user = auth()->user();
        if (! $user || ! $user->is_tester) {
            return $next($request);
        }

        $this->autoFillDocuments($request);

        if ($this->isPaymentCheckoutRequest($request)) {
            return $this->handleTesterPayment($request);
        }

        if ($this->isCheckoutMaterialRequest($request)) {
            return $this->handleTesterCheckoutMaterial($request);
        }

        return $next($request);
    }

    // ─── Intercept 1: Document / Photo Auto-Fill ──────────────────────────────

    private function autoFillDocuments(Request $request): void
    {
        $bangunDocFields = [
            'imb'              => self::DOCS['imb'],
            'ktp'              => self::DOCS['ktp'],
            'sertifikat_tanah' => self::DOCS['sertifikat_tanah'],
            'surat_kuasa'      => self::DOCS['surat_kuasa'],
        ];

        foreach ($bangunDocFields as $field => $dummyPath) {
            if ($request->isMethod('POST') && empty($request->input($field))) {
                $request->merge([$field => $dummyPath]);
            }
        }

    if ($request->isMethod('POST') && !$request->hasFile('foto_detail')) {
        try {
            $supabaseUrl = config('services.supabase.url', 'https://ovyjfudrdwrlyioygotq.supabase.co')
                . '/storage/v1/object/public/' . self::RENOVASI_FOTO;

            $tmpPath = tempnam(sys_get_temp_dir(), 'tester_foto_') . '.jpg';
            file_put_contents($tmpPath, file_get_contents($supabaseUrl));

            $fakeFile = new \Illuminate\Http\UploadedFile(
                $tmpPath,
                'foto_renovasi.jpg',
                'image/jpeg',
                null,
                true // test mode — skip is_uploaded_file() check
            );

            $request->files->set('foto_detail', [$fakeFile]);

            Log::debug('[Tester] Injected fake UploadedFile for foto_detail.');
        } catch (\Throwable $e) {
            Log::warning('[Tester] Gagal inject fake foto: ' . $e->getMessage());
        }
    }
    }

    // ─── Intercept 2: Proyek Payment Bypass ──────────────────────────────────

    private function isPaymentCheckoutRequest(Request $request): bool
    {
        if ($request->routeIs('proyek.bayar') || $request->is('proyek/bayar')) {
            return true;
        }

        if ($request->isMethod('POST') && $request->filled('pembayaran_id')) {
            return true;
        }

        return false;
    }

    private function handleTesterPayment(Request $request): Response
    {
        $pembayaranId = $request->input('pembayaran_id');

        if (! $pembayaranId) {
            return $this->respondError($request, 'ID pembayaran tidak ditemukan dalam request.');
        }

        try {
            $pembayaran = PembayaranProyek::with('proyek.customer.user')
                ->whereHas('proyek.customer.user', fn ($q) => $q->where('is_tester', 1))
                ->findOrFail($pembayaranId);

            // Validasi: periode sebelumnya harus sudah lunas
            $adaYangBelumLunas = PembayaranProyek::where('proyek_id', $pembayaran->proyek_id)
                ->where('periode', '<', $pembayaran->periode)
                ->where('status_pembayaran', '!=', 'berhasil')
                ->exists();

            if ($adaYangBelumLunas) {
                return $this->respondError($request, 'Harap selesaikan pembayaran sebelumnya terlebih dahulu.');
            }

            // Reset ke belum_bayar tanpa events
            PembayaranProyek::withoutEvents(function () use ($pembayaran) {
                $pembayaran->status_pembayaran = 'belum_bayar';
                $pembayaran->save();
            });

            // Refresh dari database supaya tidak ada stale cache
            $pembayaran->refresh();

            // Set ke pending — trigger HandleTesterWorkflow
            $pembayaran->status_pembayaran = 'pending';
            $pembayaran->save();

            return $this->respondSuccess($request, 'Pembayaran tester berhasil diproses secara otomatis.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->respondError($request, 'Record pembayaran tidak ditemukan atau bukan milik akun tester.');
        } catch (\Throwable $e) {
            Log::error('[Tester] handleTesterPayment error: ' . $e->getMessage(), [
                'pembayaran_id' => $pembayaranId,
                'trace'         => $e->getTraceAsString(),
            ]);
            return $this->respondError($request, 'Terjadi kesalahan saat memproses pembayaran tester.');
        }
    }

    // ─── Intercept 3: Material Checkout Bypass ───────────────────────────────

    private function isCheckoutMaterialRequest(Request $request): bool
    {
        return $request->isMethod('POST') && $request->is('payment/checkout');
    }

    private function handleTesterCheckoutMaterial(Request $request): Response
    {
        try {
            $user     = auth()->user();
            $customer = $user->customer;

            if (! $customer) {
                return $this->respondError($request, 'Profil customer tidak ditemukan.');
            }

            $cartItems = \App\Models\Cart::where('user_id', $user->id)->with('material')->get();

            if ($cartItems->isEmpty()) {
                return $this->respondError($request, 'Keranjang kosong.', 400);
            }

            $subtotal = $cartItems->sum(fn ($item) => $item->jumlah * $item->material->harga);

            $serviceFee = $subtotal * 0.02;
            if ($serviceFee < 5000)  $serviceFee = 5000;
            if ($serviceFee > 50000) $serviceFee = 50000;
            $grandTotal = $subtotal + $serviceFee;

            DB::beginTransaction();

            $orderIdMidtrans = 'W2H-TESTER-' . time() . '-' . $user->id;

            $order = \App\Models\OrderMaterial::create([
                'customer_id'       => $customer->id,
                'order_id_midtrans' => $orderIdMidtrans,
                'tanggal_order'     => now(),
                'alamat_pengiriman' => $request->input('alamat') ?? $user->address ?? 'Alamat tester',
                'subtotal_material' => $subtotal,
                'biaya_layanan'     => $serviceFee,
                'total_harga'       => $grandTotal,
                'status_order'      => 'paid',
            ]);

            foreach ($cartItems as $item) {
                \App\Models\DetailOrder::create([
                    'order_material_id' => $order->id,
                    'material_id'       => $item->material_id,
                    'jumlah'            => $item->jumlah,
                    'harga_satuan'      => $item->material->harga,
                    'subtotal'          => $item->jumlah * $item->material->harga,
                ]);

                $item->material->stok -= $item->jumlah;
                if ($item->material->stok < 0) $item->material->stok = 0;
                $item->material->save();
            }

            \App\Models\Cart::where('user_id', $user->id)->delete();

            DB::commit();

            Log::info("[Tester] Checkout material bypass sukses untuk User#{$user->id}, Order#{$order->id}.");

            return $this->respondSuccess($request, 'Pembayaran material tester berhasil diproses.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[Tester] handleTesterCheckoutMaterial error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->respondError($request, 'Terjadi kesalahan saat memproses checkout tester.');
        }
    }

    // ─── Response Helpers ────────────────────────────────────────────────────

    private function respondSuccess(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    private function respondError(Request $request, string $message, int $status = 422): Response
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'error',
                'message' => $message,
            ], $status);
        }

        return redirect()->back()->with('error', $message);
    }
}