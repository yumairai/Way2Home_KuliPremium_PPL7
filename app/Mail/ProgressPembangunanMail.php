<?php

namespace App\Mail;

use App\Models\Proyek;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email notifikasi update progress pembangunan rumah.
 * Dikirim ke customer saat mandor menyelesaikan milestone / task.
 */
class ProgressPembangunanMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Proyek  $proyek,
        public string  $milestoneAktif,
        public int     $persentase,
        public bool    $isSelesai = false,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->isSelesai
            ? '[Way2Home] 🎉 Pembangunan Rumah Anda Selesai!'
            : '[Way2Home] Update Progress Pembangunan - ' . $this->persentase . '% Selesai';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'email.progress_pembangunan');
    }
}
