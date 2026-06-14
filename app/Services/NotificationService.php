<?php

namespace App\Services;

use App\Mail\AktivitasProyekMail;
use App\Mail\PenawaranRenovasiMail;
use App\Mail\ProgressPembangunanMail;
use App\Mail\ProyekStatusMail;
use App\Mail\RenovasiSelesaiMail;
use App\Models\PenawaranRenovasi;
use App\Models\Proyek;
use App\Models\RequestRenovasi;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * NotificationService
 *
 * Titik pusat pengiriman semua email notifikasi ke customer.
 * Selalu gunakan service ini — jangan panggil Mail::to() langsung dari controller.
 */
class NotificationService
{
    /**
     * Dapatkan instance mailer (gunakan log jika di local/tester).
     */
    private function getMailer(string $email)
    {
        if (app()->environment('local') || (auth()->check() && auth()->user()->is_tester)) {
            return Mail::mailer('log')->to($email);
        }

        return Mail::to($email);
    }

    // ─────────────────────────────────────────────
    // 1. NOTIFIKASI STATUS PROYEK (oleh Admin)
    // ─────────────────────────────────────────────

    /**
     * Kirim email saat admin mengubah/memverifikasi status proyek pembangunan.
     *
     * Cara pakai di VerifikasiProyekController::update():
     *   app(NotificationService::class)->kirimStatusProyek($proyek, $statusFinal, $catatanAdmin);
     */
    public function kirimStatusProyek(Proyek $proyek, string $statusBaru, ?string $catatan = null): void
    {
        $email = $proyek->customer?->user?->email;
        if (!$email) {
            return;
        }

        try {
            $this->getMailer($email)->send(new ProyekStatusMail($proyek, $statusBaru, $catatan));
        } catch (\Throwable $e) {
            Log::error('[NotificationService] Gagal kirim ProyekStatusMail', [
                'proyek_id' => $proyek->id,
                'email'     => $email,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────
    // 2. NOTIFIKASI PROGRESS PEMBANGUNAN (oleh Mandor)
    // ─────────────────────────────────────────────

    /**
     * Kirim email saat mandor menyelesaikan task / milestone pembangunan.
     *
     * Cara pakai di Mandor/TrackingProyekController::completeTask():
     *   app(NotificationService::class)->kirimProgressPembangunan($proyek, $milestoneAktif, $persentase, $isSelesai);
     */
    public function kirimProgressPembangunan(
        Proyek $proyek,
        string $milestoneAktif,
        int    $persentase,
        bool   $isSelesai = false,
    ): void {
        $email = $proyek->customer?->user?->email;
        if (!$email) {
            return;
        }

        try {
            $this->getMailer($email)->send(
                new ProgressPembangunanMail($proyek, $milestoneAktif, $persentase, $isSelesai)
            );
        } catch (\Throwable $e) {
            Log::error('[NotificationService] Gagal kirim ProgressPembangunanMail', [
                'proyek_id'  => $proyek->id,
                'persentase' => $persentase,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────
    // 3. NOTIFIKASI AKTIVITAS HARIAN (oleh Mandor)
    // ─────────────────────────────────────────────

    /**
     * Kirim email saat mandor menambah laporan aktivitas proyek.
     *
     * Cara pakai di Mandor/TrackingProyekController::tambahAktivitas():
     *   app(NotificationService::class)->kirimAktivitasProyek($proyek, $judul, $deskripsi);
     */
    public function kirimAktivitasProyek(
        Proyek $proyek,
        string $judul,
        string $deskripsi,
    ): void {
        $email = $proyek->customer?->user?->email;
        if (!$email) {
            return;
        }

        try {
            $this->getMailer($email)->send(new AktivitasProyekMail($proyek, $judul, $deskripsi));
        } catch (\Throwable $e) {
            Log::error('[NotificationService] Gagal kirim AktivitasProyekMail', [
                'proyek_id' => $proyek->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────
    // 4. NOTIFIKASI PENAWARAN RENOVASI (oleh Mandor)
    // ─────────────────────────────────────────────

    /**
     * Kirim email saat mandor submit penawaran atau tanggapan negosiasi renovasi.
     *
     * Cara pakai di Mandor/RenovasiController::submitOffer() dan negotiate():
     *   app(NotificationService::class)->kirimPenawaranRenovasi($request, $penawaran, 'penawaran');
     *   app(NotificationService::class)->kirimPenawaranRenovasi($request, $penawaran, 'negosiasi');
     */
    public function kirimPenawaranRenovasi(
        RequestRenovasi   $requestRenovasi,
        PenawaranRenovasi $penawaran,
        string            $tipe = 'penawaran',
    ): void {
        $email = $requestRenovasi->customer?->user?->email;
        if (!$email) {
            return;
        }

        try {
            $this->getMailer($email)->send(new PenawaranRenovasiMail($requestRenovasi, $penawaran, $tipe));
        } catch (\Throwable $e) {
            Log::error('[NotificationService] Gagal kirim PenawaranRenovasiMail', [
                'request_renovasi_id' => $requestRenovasi->id,
                'tipe'                => $tipe,
                'error'               => $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────
    // 5. NOTIFIKASI RENOVASI SELESAI (oleh Mandor)
    // ─────────────────────────────────────────────

    /**
     * Kirim email saat mandor menandai renovasi sebagai selesai.
     *
     * Cara pakai di Mandor/RenovasiController::markDone():
     *   app(NotificationService::class)->kirimRenovasiSelesai($requestRenovasi);
     */
    public function kirimRenovasiSelesai(RequestRenovasi $requestRenovasi): void
    {
        $email = $requestRenovasi->customer?->user?->email;
        if (!$email) {
            return;
        }

        try {
            $this->getMailer($email)->send(new RenovasiSelesaiMail($requestRenovasi));
        } catch (\Throwable $e) {
            Log::error('[NotificationService] Gagal kirim RenovasiSelesaiMail', [
                'request_renovasi_id' => $requestRenovasi->id,
                'error'               => $e->getMessage(),
            ]);
        }
    }
}
