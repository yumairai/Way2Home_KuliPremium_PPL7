<?php

namespace App\Listeners;

use App\Models\DetailProyekBangun;
use App\Models\DetailProyekRenovasi;
use App\Models\Mandor;
use App\Models\NegosiasiRenovasi;
use App\Models\PembayaranProyek;
use App\Models\PenawaranRenovasi;
use App\Models\ProgressProyek;
use App\Models\Proyek;
use App\Models\RequestRenovasi;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * HandleTesterWorkflow — Eloquent Event Subscriber
 *
 * This class subscribes to Eloquent model events and automates the entire
 * application workflow for QA tester accounts, enabling full end-to-end
 * testing without manual intervention at each step.
 *
 * CRITICAL CONSTRAINTS (enforced in every handler):
 *  - Only activates when the relevant user has `is_tester == 1`.
 *  - Completely skipped when `app()->environment('production')`.
 *
 * ─── Milestone & Payment Rules ───────────────────────────────────────────────
 *
 *  $syaratPembayaran = [
 *      'Fondasi'   => 0,   // Unlocked when periode 0 (DP) is paid
 *      'Struktur'  => 0,   // Unlocked when periode 0 (DP) is paid
 *      'Atap'      => 1,   // Unlocked when periode 1 is paid
 *      'MEP'       => 2,   // Unlocked when periode 2 is paid
 *      'Finishing' => 3,   // Unlocked when periode 3 is paid
 *  ];
 *
 *  $bobotMilestone = [
 *      'Fondasi'   => 15,
 *      'Struktur'  => 35,
 *      'Atap'      => 15,
 *      'MEP'       => 15,
 *      'Finishing' => 20,
 *  ];
 *
 * Cumulative progress by periode:
 *  - DP (0)      → Fondasi (15) + Struktur (35) = 50%,  milestone = 'Struktur'
 *  - Cicilan 1   → +Atap   (15)                = 65%,  milestone = 'Atap'
 *  - Cicilan 2   → +MEP    (15)                = 80%,  milestone = 'MEP'
 *  - Cicilan 3+  → +Finishing (20)             = 100%, milestone = 'Finishing', proyek = 'Selesai'
 * ─────────────────────────────────────────────────────────────────────────────
 */
class HandleTesterWorkflow
{
    // ─── Milestone Configuration ─────────────────────────────────────────────

    /**
     * Maps each milestone name to the minimum `periode` that must be paid
     * before that milestone is unlocked.
     */
    private const SYARAT_PEMBAYARAN = [
        'Fondasi'   => 0,
        'Struktur'  => 0,
        'Atap'      => 1,
        'MEP'       => 2,
        'Finishing' => 3,
    ];

    /**
     * The percentage weight contributed by each milestone to total progress.
     */
    private const BOBOT_MILESTONE = [
        'Fondasi'   => 15,
        'Struktur'  => 35,
        'Atap'      => 15,
        'MEP'       => 15,
        'Finishing' => 20,
    ];

    // ─── Subscriber Registration ─────────────────────────────────────────────

    /**
     * Register the listener callbacks with the Eloquent event dispatcher.
     * Called automatically by Laravel when this class is registered as a
     * subscriber in EventServiceProvider.
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            'eloquent.created: ' . DetailProyekBangun::class  => 'onDetailProyekBangunCreated',
            'eloquent.created: ' . PembayaranProyek::class    => 'onPembayaranProyekChanged',
            'eloquent.updated: ' . PembayaranProyek::class    => 'onPembayaranProyekChanged',
            'eloquent.created: ' . RequestRenovasi::class     => 'onRequestRenovasiCreated',
            'eloquent.created: ' . NegosiasiRenovasi::class   => 'onNegosiasiRenovasiCreated',
        ];
    }

    // ─── Guard Helpers ───────────────────────────────────────────────────────

    /**
     * Returns false (aborting the handler) when running in production,
     * ensuring zero impact on real user data.
     */
    private function guardEnvironment(): bool
    {
        if (app()->environment('production')) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether a given User model is a registered tester account.
     */
    private function isTesterUser(mixed $user): bool
    {
        return $user && (bool) $user->is_tester;
    }

    // ─── Handler 1: DetailProyekBangun Created ───────────────────────────────

    /**
     * Fires when a new `detail_proyek_bangun` row is created.
     *
     * Workflow:
     *  1. Verify the project's customer is a tester.
     *  2. Auto-approve all related `DokumenProyek` records.
     *  3. Advance parent Proyek status to 'Pembayaran DP' (saveQuietly).
     *  4. Generate the DP billing record via Proyek::generateDP().
     *
     * NOTE: Mandor is NOT allocated here. Allocation happens after DP is paid.
     */
    public function onDetailProyekBangunCreated(DetailProyekBangun $detailBangun): void
    {
        if (! $this->guardEnvironment()) {
            return;
        }

        // ── Load relations ────────────────────────────────────────────────────
        $proyek = $detailBangun->proyek()->with('customer.user')->first();

        if (! $proyek) {
            Log::warning('[Tester] onDetailProyekBangunCreated: Proyek not found for DetailProyekBangun#' . $detailBangun->id);
            return;
        }

        // ── Guard: tester only ────────────────────────────────────────────────
        $owner = $proyek->customer?->user;
        if (! $this->isTesterUser($owner)) {
            return;
        }

        Log::info("[Tester] onDetailProyekBangunCreated triggered for Proyek#{$proyek->id}");

        try {
            // ── Step 1: Auto-approve all related documents ────────────────────
            // DokumenProyek rows are linked via detail_bangun_id.
            $detailBangun->dokumenProyek()
                ->where('status_verifikasi', '!=', 'disetujui')
                ->update(['status_verifikasi' => 'disetujui']);

            Log::info("[Tester] All DokumenProyek for DetailProyekBangun#{$detailBangun->id} approved.");

            // ── Step 2: Advance project status to 'Pembayaran DP' ────────────
            // Use saveQuietly() to avoid triggering Proyek's own observers.
            $proyek->status_proyek = 'Pembayaran DP';
            $proyek->saveQuietly();

            Log::info("[Tester] Proyek#{$proyek->id} status advanced to 'Pembayaran DP'.");

            // ── Step 3: Generate the DP billing record ────────────────────────
            // generateDP() is idempotent (checks for existing DP before creating).
            $proyek->generateDP();

            Log::info("[Tester] DP billing record generated for Proyek#{$proyek->id}.");
        } catch (\Throwable $e) {
            Log::error('[Tester] onDetailProyekBangunCreated error: ' . $e->getMessage(), [
                'detail_bangun_id' => $detailBangun->id,
                'proyek_id'        => $proyek->id,
                'trace'            => $e->getTraceAsString(),
            ]);
        }
    }

    // ─── Handler 2: PembayaranProyek Created / Updated ───────────────────────

    /**
     * Fires when a PembayaranProyek row is created or updated.
     *
     * This is the core financial automation handler. It responds to a status
     * transition to 'pending' and:
     *  1. Forces the payment to 'berhasil' (bypassing events to avoid recursion).
     *  2. On DP (periode == 0): allocates a random active tester mandor and
     *     transitions the project to 'In Progress', then generates cicilan.
     *  3. Calculates and upserts cumulative progress into `progress_proyek`.
     *  4. On final payment (periode >= 3): marks the project as 'Selesai'.
     */
    public function onPembayaranProyekChanged(PembayaranProyek $pembayaran): void
    {
        if (! $this->guardEnvironment()) {
            return;
        }

        // ── Guard: only react to 'pending' status ─────────────────────────────
        if ($pembayaran->status_pembayaran !== 'pending') {
            return;
        }

        // ── Load proyek with full customer chain ──────────────────────────────
        $pembayaran->loadMissing(['proyek.customer.user']);
        $proyek = $pembayaran->proyek;

        if (! $proyek) {
            Log::warning('[Tester] onPembayaranProyekChanged: Proyek not found for PembayaranProyek#' . $pembayaran->id);
            return;
        }

        // ── Guard: tester only ────────────────────────────────────────────────
        $owner = $proyek->customer?->user;
        if (! $this->isTesterUser($owner)) {
            return;
        }

        Log::info("[Tester] onPembayaranProyekChanged triggered — PembayaranProyek#{$pembayaran->id}, periode={$pembayaran->periode}, proyek={$proyek->id}");

        try {
            // ── Step 1: Force-mark payment as 'berhasil' ──────────────────────
            // Use withoutEvents() to prevent this update from re-triggering
            // this same listener and causing an infinite recursion loop.
            PembayaranProyek::withoutEvents(function () use ($pembayaran) {
                $pembayaran->status_pembayaran  = 'berhasil';
                $pembayaran->tanggal_bayar      = now()->toDateString();
                $pembayaran->metode_pembayaran  = 'Tester Auto-Pay';
                $pembayaran->save();
            });

            Log::info("[Tester] PembayaranProyek#{$pembayaran->id} force-marked as 'berhasil'.");

            // ── Step 2: DP-specific workflow (periode == 0) ───────────────────
            if ($pembayaran->periode == 0) {
                $this->handleDpPaid($proyek);
            }

            // ── Step 3: Accumulate & upsert progress ──────────────────────────
            $this->accumulateProgress($proyek, $pembayaran->periode);
        } catch (\Throwable $e) {
            Log::error('[Tester] onPembayaranProyekChanged error: ' . $e->getMessage(), [
                'pembayaran_id' => $pembayaran->id,
                'proyek_id'     => $proyek->id,
                'trace'         => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Executes additional automation specifically for the Down Payment (periode 0):
     *  1. Finds a random active mandor whose user account is also a tester
     *     (so multiple testers can share the same dummy account pool).
     *  2. Assigns the mandor to the project.
     *  3. Transitions project status to 'In Progress'.
     *  4. Generates the 3 installment billing records via generateCicilan().
     */
    private function handleDpPaid(Proyek $proyek): void
    {
        $mandor = Mandor::where('is_ghost', true)->first();

        if (! $mandor) {
            Log::error('[Tester] handleDpPaid: Mandor hantu tidak ditemukan.');
            return;
        }

        $proyek->mandor_id     = $mandor->id;
        $proyek->status_proyek = 'In Progress';
        $proyek->saveQuietly();

        Log::info("[Tester] Mandor ghost#{$mandor->id} dialokasikan ke Proyek#{$proyek->id}.");

        $proyek->generateCicilan();

        Log::info("[Tester] Cicilan records generated untuk Proyek#{$proyek->id}.");
    }

    /**
     * Calculates the cumulative progress percentage and active milestone name
     * based on the paid `periode`, then upserts into `progress_proyek`.
     *
     * Also finalizes the project (status → 'Selesai') when all payments are done.
     *
     * Progression rules:
     *  - periode 0 (DP)   → Fondasi (15%) + Struktur (35%) = 50% total, milestone = 'Struktur'
     *  - periode 1        → + Atap  (15%)                  = 65% total, milestone = 'Atap'
     *  - periode 2        → + MEP   (15%)                  = 80% total, milestone = 'MEP'
     *  - periode >= 3     → + Finishing (20%)              = 100% total, milestone = 'Finishing'
     */
    private function accumulateProgress(Proyek $proyek, int $periode): void
    {
        // Determine which milestones are now unlocked based on the paid periode.
        // A milestone is unlocked when its syarat (minimum periode) <= current paid periode.
        $unlockedMilestones = array_filter(
            self::SYARAT_PEMBAYARAN,
            fn (int $syarat) => $syarat <= $periode
        );

        // Sum the weights of all unlocked milestones.
        $totalPersentase = array_sum(
            array_intersect_key(self::BOBOT_MILESTONE, $unlockedMilestones)
        );

        // The "active" milestone is the last one unlocked (highest weight in order).
        // We reverse the BOBOT_MILESTONE key order to find it efficiently.
        $milestoneAktif = array_key_last(
            array_intersect_key(self::BOBOT_MILESTONE, $unlockedMilestones)
        ) ?? 'Fondasi';

        // Upsert into progress_proyek (update if exists, insert if new).
        ProgressProyek::updateOrCreate(
            ['proyek_id' => $proyek->id],
            [
                'milestone_aktif' => $milestoneAktif,
                'persentase'      => $totalPersentase,
                'catatan'         => 'Otomatisasi Progres via Tester',
                'tanggal_update'  => now(),
            ]
        );

        Log::info("[Tester] Progress upserted for Proyek#{$proyek->id}: {$totalPersentase}% (milestone: {$milestoneAktif}).");

        // ── Finalize project on 100% completion ───────────────────────────────
        if ($totalPersentase >= 100) {
            $proyek->status_proyek = 'Selesai';
            $proyek->saveQuietly();

            Log::info("[Tester] Proyek#{$proyek->id} finalized → 'Selesai'.");
        }
    }

    // ─── Handler 3: RequestRenovasi Created ─────────────────────────────────

    /**
     * Fires when a new `request_renovasi` row is created.
     *
     * Workflow for tester customers:
     *  1. Auto-approve the renovation request (status → 'disetujui').
     *  2. Find a random active tester mandor.
     *  3. Create a dummy PenawaranRenovasi from that mandor (status → 'pending').
     *  4. Seed the first NegosiasiRenovasi row to open the negotiation portal
     *     (pengirim = 'mandor', tipe = 'penawaran').
     */
    public function onRequestRenovasiCreated(RequestRenovasi $requestRenovasi): void
    {
        if (! $this->guardEnvironment()) {
            return;
        }

        // ── Load customer → user ──────────────────────────────────────────────
        $requestRenovasi->loadMissing(['customer.user']);
        $owner = $requestRenovasi->customer?->user;

        if (! $this->isTesterUser($owner)) {
            return;
        }

        Log::info("[Tester] onRequestRenovasiCreated triggered for RequestRenovasi#{$requestRenovasi->id}");

        try {
            // ── Step 1: Auto-approve the request ─────────────────────────────
            // Use withoutEvents to avoid triggering any other observers on
            // RequestRenovasi itself.
            RequestRenovasi::withoutEvents(function () use ($requestRenovasi) {
                $requestRenovasi->status_request = 'disetujui';
                $requestRenovasi->save();
            });

            Log::info("[Tester] RequestRenovasi#{$requestRenovasi->id} auto-approved.");

            // ── Step 2: Cari mandor hantu ─────────────────────────────────────
            $mandor = Mandor::where('is_ghost', true)->first();

            if (! $mandor) {
                Log::error('[Tester] onRequestRenovasiCreated: Mandor hantu tidak ditemukan. Penawaran creation skipped.');
                return;
            }

            // ── Step 3: Create dummy PenawaranRenovasi ────────────────────────
            $penawaran = PenawaranRenovasi::create([
                'request_renovasi_id'  => $requestRenovasi->id,
                'mandor_id'            => $mandor->id,
                'analisis_dari_mandor' => 'Ini adalah pesan analisis awal dari mandor (tester auto-generated).',
                'estimasi_biaya'       => $requestRenovasi->budget_estimasi ?? 0,
                'estimasi_durasi'      => 30,
                'status_penawaran'     => 'pending',
            ]);

            Log::info("[Tester] PenawaranRenovasi#{$penawaran->id} created by Mandor#{$mandor->id}.");

            // ── Step 4: Seed opening NegosiasiRenovasi row ───────────────────
            // This opens the negotiation portal for the customer UI.
            // Use withoutEvents to prevent this listener from recursively
            // handling the negosiasi.created event at this stage.
            NegosiasiRenovasi::withoutEvents(function () use ($requestRenovasi, $penawaran) {
                NegosiasiRenovasi::create([
                    'request_renovasi_id'   => $requestRenovasi->id,
                    'penawaran_renovasi_id' => $penawaran->id,
                    'pengirim'              => 'mandor',
                    'tipe'                  => 'penawaran',
                    'pesan'                 => 'Penawaran awal dari mandor. Silakan lanjutkan negosiasi.',
                    'nominal_tawaran'       => $penawaran->estimasi_biaya,
                ]);
            });

            Log::info("[Tester] Opening NegosiasiRenovasi seeded for RequestRenovasi#{$requestRenovasi->id}.");
        } catch (\Throwable $e) {
            Log::error('[Tester] onRequestRenovasiCreated error: ' . $e->getMessage(), [
                'request_renovasi_id' => $requestRenovasi->id,
                'trace'               => $e->getTraceAsString(),
            ]);
        }
    }

    // ─── Handler 4: NegosiasiRenovasi Created ───────────────────────────────

    /**
     * Fires when a new `negosiasi_renovasi` row is created.
     *
     * Only reacts when:
     *  - pengirim = 'customer' (a tester customer just sent a negotiation message).
     *  - tipe = 'negosiasi' (customer is proposing a new price).
     *
     * Automated workflow:
     *  1. Instantly reply on the mandor's behalf with tipe = 'setuju'.
     *  2. Accept the customer's offer (update PenawaranRenovasi → 'diterima').
     *  3. Create the Proyek record (jenis = 'Renovasi', status = 'Pembayaran DP').
     *  4. Link via DetailProyekRenovasi.
     */
    public function onNegosiasiRenovasiCreated(NegosiasiRenovasi $negosiasi): void
    {
        if (! $this->guardEnvironment()) {
            return;
        }

        // ── React ke pesan mandor (penawaran awal ke tester customer) ────────
        // Ketika mandor kirim penawaran ke tester customer, otomatis buat 'setuju'
        // dari customer dan langsung accept (tanpa perlu klik manual).
        if ($negosiasi->pengirim === 'mandor' && in_array($negosiasi->tipe, ['penawaran', 'tanggapan'])) {
            $negosiasi->loadMissing([
                'requestRenovasi.customer.user',
                'penawaranRenovasi',
            ]);

            $reqRenov = $negosiasi->requestRenovasi;
            $mandorOwner = $reqRenov?->customer?->user;

            if (! $this->isTesterUser($mandorOwner)) {
                return; // Bukan tester customer, skip
            }

            $pen = $negosiasi->penawaranRenovasi;
            if (! $pen) {
                return;
            }

            Log::info("[Tester] Mandor penawaran → auto-accept untuk RequestRenovasi#{$reqRenov->id}");

            try {
                // Tambah negosiasi 'setuju' dari customer (tanpa trigger event lagi)
                NegosiasiRenovasi::withoutEvents(function () use ($reqRenov, $pen) {
                    NegosiasiRenovasi::create([
                        'request_renovasi_id'   => $reqRenov->id,
                        'penawaran_renovasi_id' => $pen->id,
                        'pengirim'              => 'customer',
                        'tipe'                  => 'setuju',
                        'pesan'                 => 'Penawaran disetujui (Tester Auto-Accept).',
                        'nominal_tawaran'       => null,
                    ]);
                });

                // Langsung proses acceptance
                $this->handleRenovasiAccepted($reqRenov, $pen);
            } catch (\Throwable $e) {
                Log::error('[Tester] auto-accept renovasi error: ' . $e->getMessage(), [
                    'negosiasi_id' => $negosiasi->id,
                    'trace'        => $e->getTraceAsString(),
                ]);
            }
            return;
        }

        // ── Hanya react ke pesan dari customer di bawah ini ──────────────────
        if ($negosiasi->pengirim !== 'customer') {
            return;
        }

        // ── Load relations ────────────────────────────────────────────────────
        $negosiasi->loadMissing([
            'requestRenovasi.customer.user',
            'penawaranRenovasi.mandor',
        ]);

        $requestRenovasi = $negosiasi->requestRenovasi;
        $owner           = $requestRenovasi?->customer?->user;

        if (! $this->isTesterUser($owner)) {
            return;
        }

        $penawaran = $negosiasi->penawaranRenovasi;
        if (! $penawaran) {
            Log::warning('[Tester] onNegosiasiRenovasiCreated: PenawaranRenovasi not found for Negosiasi#' . $negosiasi->id);
            return;
        }

        Log::info("[Tester] onNegosiasiRenovasiCreated triggered — tipe={$negosiasi->tipe}, Negosiasi#{$negosiasi->id}");

        try {
            // ── Customer kirim 'setuju' → buat proyek renovasi ───────────────
            if ($negosiasi->tipe === 'setuju') {
                $this->handleRenovasiAccepted($requestRenovasi, $penawaran);
                return;
            }

            // ── Customer kirim 'negosiasi' → mandor balas dengan nominal sama ─
            if ($negosiasi->tipe === 'negosiasi') {
                NegosiasiRenovasi::withoutEvents(function () use ($negosiasi, $penawaran, $requestRenovasi) {
                    NegosiasiRenovasi::create([
                        'request_renovasi_id'   => $requestRenovasi->id,
                        'penawaran_renovasi_id' => $penawaran->id,
                        'pengirim'              => 'mandor',
                        'tipe'                  => 'negosiasi',
                        'pesan'                 => 'Kami tetap pada penawaran ini. Silakan pertimbangkan kembali.',
                        'nominal_tawaran'       => $penawaran->estimasi_biaya, // tetap nominal awal
                    ]);
                });

                Log::info("[Tester] Mandor auto-reply nego untuk RequestRenovasi#{$requestRenovasi->id}.");
                return;
            }

            // ── Customer kirim 'tolak' → tidak perlu reply ───────────────────
            Log::info("[Tester] Customer tolak penawaran, tidak ada auto-reply.");

        } catch (\Throwable $e) {
            Log::error('[Tester] onNegosiasiRenovasiCreated error: ' . $e->getMessage(), [
                'negosiasi_id'        => $negosiasi->id,
                'request_renovasi_id' => $requestRenovasi->id,
                'trace'               => $e->getTraceAsString(),
            ]);
        }
    }

    private function handleRenovasiAccepted(RequestRenovasi $requestRenovasi, PenawaranRenovasi $penawaran): void
    {
        // 1. Cek apakah detail proyek sudah pernah dibuat untuk menghindari duplikasi
        $sudahAda = DetailProyekRenovasi::where('request_renovasi_id', $requestRenovasi->id)->exists();
        if ($sudahAda) {
            Log::info("[Tester] Proyek renovasi sudah ada untuk RequestRenovasi#{$requestRenovasi->id}, skip.");
            return;
        }

        // 2. Bungkus dengan Database Transaction agar data tidak menggantung jika terjadi error
        DB::transaction(function () use ($requestRenovasi, $penawaran) {

            // Tandai penawaran sebagai diterima
            PenawaranRenovasi::withoutEvents(function () use ($penawaran) {
                $penawaran->status_penawaran = 'diterima';
                $penawaran->save();
            });

            // Set status request ke 'disetujui' (BUKAN 'selesai') agar tracking mandor masih bisa tampil.
            // 'selesai' hanya di-set saat mandor klik "Tandai Selesai" via markDone().
            RequestRenovasi::withoutEvents(function () use ($requestRenovasi) {
                $requestRenovasi->status_request = 'disetujui';
                $requestRenovasi->save();
            });

            $customer = $requestRenovasi->customer;

            // Buat proyek Renovasi dengan status 'In Progress' agar mandor bisa tracking
            $proyek = Proyek::create([
                'customer_id'   => $customer->id,
                'mandor_id'     => $penawaran->mandor_id,
                'jenis_proyek'  => 'Renovasi',
                'alamat_proyek' => $requestRenovasi->alamat ?? 'Alamat tester default',
                'status_proyek' => 'In Progress',
                'tanggal_mulai' => now()->toDateString(),
            ]);

            // Hubungkan proyek dengan detail renovasi
            DetailProyekRenovasi::create([
                'proyek_id'             => $proyek->id,
                'request_renovasi_id'   => $requestRenovasi->id,
                'penawaran_renovasi_id' => $penawaran->id,
            ]);

            // Set Wahyu mandor sebagai nonaktif (sedang mengerjakan)
            \Illuminate\Support\Facades\DB::table('mandors')
                ->where('id', $penawaran->mandor_id)
                ->update(['status' => 'nonaktif', 'updated_at' => now()]);

            Log::info("[Tester] Proyek#{$proyek->id} (Renovasi) 'In Progress' berhasil dibuat. Request status='disetujui'.");
        });
    }

}
