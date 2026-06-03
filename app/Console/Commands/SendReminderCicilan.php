<?php

namespace App\Console\Commands;

use App\Mail\ReminderCicilanMail;
use App\Models\PembayaranProyek;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReminderCicilan extends Command
{
    protected $signature   = 'cicilan:reminder';
    protected $description = 'Kirim email reminder cicilan yang jatuh tempo 7 hari lagi';

    public function handle(): void
    {
        // Ambil semua cicilan yang jatuh tempo tepat 7 hari lagi
        // dan belum dibayar
        $cicilans = PembayaranProyek::with([
                'proyek.customer.user',
            ])
            ->whereDate('tanggal_jatuh_tempo', now()->addDays(7)->toDateString())
            ->whereIn('status_pembayaran', ['belum_bayar', 'pending', 'gagal'])
            ->where('periode', '>', 0) // hanya cicilan, bukan DP
            ->get();

        if ($cicilans->isEmpty()) {
            $this->info('Tidak ada cicilan yang perlu diingatkan hari ini.');
            return;
        }

        foreach ($cicilans as $cicilan) {
            $email = $cicilan->proyek?->customer?->user?->email;

            if (!$email) {
                $this->warn("Cicilan ID {$cicilan->id} — email customer tidak ditemukan, skip.");
                continue;
            }

            Mail::to($email)->send(new ReminderCicilanMail($cicilan));
            $this->info("Reminder terkirim ke {$email} untuk cicilan periode {$cicilan->periode}.");
        }

        $this->info("Selesai. Total {$cicilans->count()} reminder terkirim.");
    }
}