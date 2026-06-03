<?php

namespace App\Services;

use App\Models\Mandor;
use App\Models\PenawaranRenovasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RenovasiService
{
    private function getLastMandorReply(PenawaranRenovasi $offer)
    {
        return $offer->negosiasi()
            ->where('pengirim', 'mandor')
            ->latest()
            ->first();
    }
    public const OFFER_VALID_DAYS = 3;

    public function expirePendingOffers(): void
    {
        $expiredOffers = PenawaranRenovasi::with(['mandor', 'requestRenovasi', 'negosiasi'])
            ->where('status_penawaran', 'pending')
            ->get()
            ->filter(function ($offer) {
                $lastReply = $offer->negosiasi()
                    ->where('pengirim', 'mandor')
                    ->latest()
                    ->first();

                if (!$lastReply) {
                    return false; // belum dibalas mandor = tidak expired
                }

                return Carbon::parse($lastReply->created_at)
                    ->addDay()
                    ->lt(now());
            });

        foreach ($expiredOffers as $offer) {
            DB::transaction(function () use ($offer) {
                $offer->update(['status_penawaran' => 'ditolak']);

                if ($offer->requestRenovasi && $offer->requestRenovasi->status_request !== 'selesai') {
                    $offer->requestRenovasi->update(['status_request' => 'selesai']);
                }

                if ($offer->mandor) {
                    // Set mandor status to aktif karena penawaran expired
                    $offer->mandor->update(['status' => 'aktif']);
                }
            });
        }
    }

    public function isOfferExpired(PenawaranRenovasi $offer): bool
    {
        $expiresAt = $this->offerExpiresAt($offer);

        // kalau belum ada expiry → belum expired
        if (!$expiresAt) {
            return false;
        }

        return now()->greaterThan($expiresAt);
    }

    public function offerExpiresAt(PenawaranRenovasi $offer): ?Carbon
    {
        $lastReply = $this->getLastMandorReply($offer);

        // belum ada balasan mandor → TIDAK ADA EXPIRY
        if (!$lastReply) {
            return null;
        }

        // expiry = 1 hari setelah balasan terakhir mandor
        return Carbon::parse($lastReply->created_at)->addDay();
    }

    public function formatRupiah(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
