<?php

namespace App\Mail;

use App\Models\Proyek;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email notifikasi perubahan status proyek pembangunan rumah.
 * Dikirim ke customer saat admin memverifikasi atau mengubah status proyek.
 */
class ProyekStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Proyek  $proyek,
        public string  $statusBaru,
        public ?string $catatanAdmin = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Way2Home] Update Status Proyek #' . $this->proyek->id . ' - ' . $this->statusBaru,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.proyek_status',
        );
    }
}
