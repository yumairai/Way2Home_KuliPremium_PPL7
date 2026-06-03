<?php

namespace App\Mail;

use App\Models\PembayaranProyek;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderCicilanMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PembayaranProyek $cicilan
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengingat Cicilan Proyek - Jatuh Tempo ' . $this->cicilan->tanggal_jatuh_tempo->format('d M Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.reminder_cicilan',
        );
    }
}