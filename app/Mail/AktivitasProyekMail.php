<?php

namespace App\Mail;

use App\Models\Proyek;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email notifikasi aktivitas baru yang ditambahkan mandor ke proyek.
 * Dikirim ke customer saat mandor menambahkan laporan kegiatan harian.
 */
class AktivitasProyekMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Proyek  $proyek,
        public string  $judulAktivitas,
        public string  $deskripsiAktivitas,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Way2Home] Laporan Aktivitas Proyek #' . $this->proyek->id . ' - ' . $this->judulAktivitas,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'email.aktivitas_proyek');
    }
}
