<?php

namespace App\Mail;

use App\Models\RequestRenovasi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email notifikasi renovasi selesai dari mandor.
 * Dikirim ke customer saat mandor menandai renovasi sebagai selesai.
 */
class RenovasiSelesaiMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RequestRenovasi $requestRenovasi,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Way2Home] 🎉 Renovasi Anda Telah Selesai - #' . sprintf('REV-%03d', $this->requestRenovasi->id),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'email.renovasi_selesai');
    }
}
