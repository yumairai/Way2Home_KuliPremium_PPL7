<?php

namespace App\Http\Middleware;

use App\Models\PembayaranProyek;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
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

    /**
     * Dashboard/entry routes that trigger the state reset.
     */
    private const RESET_ROUTE_NAMES = [
        'login',
        'logout',
    ];

    private const RESET_ROUTE_PATHS = [
        'login',
        'logout',
    ];

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

        // Trigger state reset on entry/exit routes
        if ($this->isTriggerRoute($request) && ! defined('TESTER_STATE_RESET_DONE')) {
            define('TESTER_STATE_RESET_DONE', true);
            self::resetTesterState();
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

    // ─── Route Detection ─────────────────────────────────────────────────────

    private function isTriggerRoute(Request $request): bool
    {
        foreach (self::RESET_ROUTE_NAMES as $name) {
            if ($request->routeIs($name)) {
                return true;
            }
        }
        foreach (self::RESET_ROUTE_PATHS as $path) {
            if ($request->is($path)) {
                return true;
            }
        }
        return false;
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
                $supabaseUrl = 'https://ovyjfudrdwrlyioygotq.supabase.co/storage/v1/object/public/public-assets/testing/renovasi/foto_renovasi.jpg';

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
                Log::warning('[Tester] Gagal inject fake foto_detail: ' . $e->getMessage());
            }
        }

        if ($request->isMethod('POST') && !$request->hasFile('foto')) {
            try {
                $supabaseUrl = 'https://ovyjfudrdwrlyioygotq.supabase.co/storage/v1/object/public/public-assets/testing/renovasi/foto_renovasi.jpg';

                $tmpPath = tempnam(sys_get_temp_dir(), 'tester_foto_') . '.jpg';
                file_put_contents($tmpPath, file_get_contents($supabaseUrl));

                $fakeFile = new \Illuminate\Http\UploadedFile(
                    $tmpPath,
                    'foto_dokumentasi.jpg',
                    'image/jpeg',
                    null,
                    true // test mode — skip is_uploaded_file() check
                );

                $request->files->set('foto', $fakeFile);

                Log::debug('[Tester] Injected fake UploadedFile for foto (dokumentasi).');
            } catch (\Throwable $e) {
                Log::warning('[Tester] Gagal inject fake foto dokumentasi: ' . $e->getMessage());
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

    // ─── Highly Optimized Status Reset (Fast SQL Updates) ────────────────────

    /**
     * Resets the testing environment for Customer ID = 2.
     * Uses fast SQL update queries instead of deleting and re-inserting records.
     */
    public static function resetTesterState(): void
    {
        if (app()->environment('production')) {
            return;
        }

        try {
            // Check if tester projects exist. If not, run TesterDataSeeder to create them.
            $exists = DB::table('proyek')->where('customer_id', 2)->exists();
            if (!$exists) {
                Log::info('[Tester] Tester projects not found. Running TesterDataSeeder...');
                Artisan::call('db:seed', ['--class' => 'TesterDataSeeder']);
                return;
            }

            DB::transaction(function () {
                $proyekIds = DB::table('proyek')
                    ->where('customer_id', 2)
                    ->pluck('id');

                if ($proyekIds->isEmpty()) {
                    return;
                }

                // 1. Reset all tasks in proyek_milestone to incomplete
                DB::table('proyek_milestone')
                    ->whereIn('proyek_id', $proyekIds)
                    ->update([
                        'is_selesai' => false,
                        'updated_at' => now(),
                    ]);

                // 2. Reset progress percentage to 0% and milestone to 'Fondasi'
                DB::table('progress_proyek')
                    ->whereIn('proyek_id', $proyekIds)
                    ->update([
                        'milestone_aktif' => 'Fondasi',
                        'persentase'      => 0,
                        'catatan'         => 'Tester State Reset',
                        'tanggal_update'  => now(),
                        'updated_at'      => now(),
                    ]);

                // 3. Reset ALL payments (DP + all installments) to 'berhasil' (fully paid)
                DB::table('pembayaran_proyek')
                    ->whereIn('proyek_id', $proyekIds)
                    ->update([
                        'status_pembayaran' => 'berhasil',
                        'tanggal_bayar'     => now()->toDateString(),
                        'metode_pembayaran' => 'Tester Auto-Pay',
                        'updated_at'        => now(),
                    ]);

                // 5. Clean up dynamic testing data (activities, documentations)
                DB::table('proyek_aktivitas')->whereIn('proyek_id', $proyekIds)->delete();
                DB::table('proyek_dokumentasi')->whereIn('proyek_id', $proyekIds)->delete();

                // 6. Reset project status and mandors
                $m1 = DB::table('mandors')->where('user_id', 32)->value('id') ?? 1;
                $m2 = DB::table('mandors')->where('user_id', 33)->value('id') ?? 2;
                $m3 = DB::table('mandors')->where('user_id', 34)->value('id') ?? 3;

                DB::table('proyek')
                    ->where('customer_id', 2)
                    ->where('alamat_proyek', 'Jl. Pembangunan No. 32')
                    ->update(['status_proyek' => 'In Progress', 'mandor_id' => $m1, 'updated_at' => now()]);

                DB::table('proyek')
                    ->where('customer_id', 2)
                    ->where('alamat_proyek', 'Jl. Fondasi Raya No. 33')
                    ->update(['status_proyek' => 'In Progress', 'mandor_id' => $m2, 'updated_at' => now()]);

                // ─── Renovasi Wahyu Reset ─────────────────────────────────────
                // Reset ke state awal: hanya request 'pending', hapus penawaran & proyek lama.
                // Wahyu harus kirim penawaran ulang → auto-accepted via HandleTesterWorkflow.

                $wahyuPenIds = DB::table('penawaran_renovasi')
                    ->where('mandor_id', $m3)->pluck('id');

                if ($wahyuPenIds->isNotEmpty()) {
                    // Dapatkan request IDs sebelum hapus penawaran
                    $wahyuReqIds = DB::table('penawaran_renovasi')
                        ->whereIn('id', $wahyuPenIds)->pluck('request_renovasi_id');

                    // Hapus proyek renovasi yang sudah terbuat
                    $oldProjIds = DB::table('detail_proyek_renovasi')
                        ->whereIn('penawaran_renovasi_id', $wahyuPenIds)->pluck('proyek_id');
                    if ($oldProjIds->isNotEmpty()) {
                        DB::table('detail_proyek_renovasi')
                            ->whereIn('proyek_id', $oldProjIds)->delete();
                        DB::table('proyek')->whereIn('id', $oldProjIds)->delete();
                    }

                    // Hapus negosiasi & penawaran
                    DB::table('negosiasi_renovasi')
                        ->whereIn('penawaran_renovasi_id', $wahyuPenIds)->delete();
                    DB::table('penawaran_renovasi')
                        ->whereIn('id', $wahyuPenIds)->delete();

                    // Reset request ke 'pending' (bukan dihapus, cukup reset statusnya)
                    if ($wahyuReqIds->isNotEmpty()) {
                        DB::table('request_renovasi')
                            ->whereIn('id', $wahyuReqIds)
                            ->update(['status_request' => 'pending', 'updated_at' => now()]);
                    }

                    // Wahyu kembali aktif (belum mengerjakan renovasi)
                    DB::table('mandors')
                        ->where('id', $m3)
                        ->update(['status' => 'aktif', 'updated_at' => now()]);
                }

                // Reset all 3 Admin projects (A & B for each admin)
                for ($i = 1; $i <= 3; $i++) {
                    $addressA = "Cluster Admin {$i} Verifikasi Blok QA-0" . (2 * $i - 1);
                    $addressB = "Kawasan Alokasi Mandor {$i} No. QA-0" . (2 * $i);

                    // Project A: Menunggu Verifikasi
                    DB::table('proyek')
                        ->where('customer_id', 2)
                        ->where('alamat_proyek', $addressA)
                        ->update(['status_proyek' => 'Menunggu Verifikasi', 'mandor_id' => null, 'updated_at' => now()]);

                    $pAid = DB::table('proyek')
                        ->where('customer_id', 2)
                        ->where('alamat_proyek', $addressA)
                        ->value('id');
                    if ($pAid) {
                        $detailAId = DB::table('detail_proyek_bangun')->where('proyek_id', $pAid)->value('id');
                        if ($detailAId) {
                            DB::table('dokumen_proyek')
                                ->where('detail_bangun_id', $detailAId)
                                ->update(['status_verifikasi' => 'pending', 'updated_at' => now()]);
                        }
                    }

                    // Project A2: Reset ke Menunggu Verifikasi (kedua)
                    $addressA2 = "Cluster Admin {$i} Verifikasi Blok QA-0" . (6 + $i);

                    DB::table('proyek')
                        ->where('customer_id', 2)
                        ->where('alamat_proyek', $addressA2)
                        ->update(['status_proyek' => 'Menunggu Verifikasi', 'mandor_id' => null, 'updated_at' => now()]);

                    $pA2id = DB::table('proyek')
                        ->where('customer_id', 2)
                        ->where('alamat_proyek', $addressA2)
                        ->value('id');

                    if ($pA2id) {
                        $detailA2Id = DB::table('detail_proyek_bangun')->where('proyek_id', $pA2id)->value('id');
                        if ($detailA2Id) {
                            DB::table('dokumen_proyek')
                                ->where('detail_bangun_id', $detailA2Id)
                                ->update(['status_verifikasi' => 'pending', 'updated_at' => now()]);
                        }
                    }

                    // Project B: Pengalokasian Mandor
                    DB::table('proyek')
                        ->where('customer_id', 2)
                        ->where('alamat_proyek', $addressB)
                        ->update(['status_proyek' => 'Pengalokasian Mandor', 'mandor_id' => null, 'updated_at' => now()]);

                    $pBid = DB::table('proyek')
                        ->where('customer_id', 2)
                        ->where('alamat_proyek', $addressB)
                        ->value('id');
                    if ($pBid) {
                        $detailBId = DB::table('detail_proyek_bangun')->where('proyek_id', $pBid)->value('id');
                        if ($detailBId) {
                            DB::table('dokumen_proyek')
                                ->where('detail_bangun_id', $detailBId)
                                ->update(['status_verifikasi' => 'disetujui', 'updated_at' => now()]);
                        }
                    }
                }

                // Reset mandor user_id 5, 6, 7 status to active (aktif)
                DB::table('mandors')
                    ->whereIn('user_id', [5, 6, 7])
                    ->update(['status' => 'aktif', 'updated_at' => now()]);

                DB::table('order_material')
                    ->where('order_id_midtrans', 'like', 'W2H-TESTER-QA%')
                    ->update(['status_order' => 'paid', 'updated_at' => now()]);

                Log::info('[Tester] Optimized smart state reset successfully completed.');
            });
        } catch (\Throwable $e) {
            Log::error('[Tester] Optimized smart state reset failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
    
}