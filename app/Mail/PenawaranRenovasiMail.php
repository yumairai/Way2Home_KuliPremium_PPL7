<?php

namespace App\Mail;

use App\Models\PenawaranRenovasi;
use App\Models\RequestRenovasi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email notifikasi penawaran renovasi dari mandor ke customer.
 * Dikirim saat mandor submit penawaran atau mengirim pesan negosiasi.
 */
class PenawaranRenovasiMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RequestRenovasi   $requestRenovasi,
        public PenawaranRenovasi $penawaran,
        public string            $tipe = 'penawaran', // 'penawaran' | 'negosiasi'
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->tipe === 'negosiasi' ? 'Tanggapan Negosiasi' : 'Penawaran Baru';

        return new Envelope(
            subject: '[Way2Home] ' . $label . ' Renovasi #' . sprintf('REV-%03d', $this->requestRenovasi->id),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'email.penawaran_renovasi');
    }
}
