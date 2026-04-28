<?php

namespace App\Services;

use App\Models\Mandor;
use App\Models\PenawaranRenovasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RenovasiService
{
    public const OFFER_VALID_DAYS = 3;

    public function expirePendingOffers(): void
    {
        $expiredOffers = PenawaranRenovasi::with(['mandor', 'requestRenovasi'])
            ->where('status_penawaran', 'pending')
            ->where('created_at', '<', now()->subDays(self::OFFER_VALID_DAYS))
            ->get();

        foreach ($expiredOffers as $offer) {
            DB::transaction(function () use ($offer) {
                $offer->update(['status_penawaran' => 'ditolak']);

                if ($offer->requestRenovasi && $offer->requestRenovasi->status_request !== 'selesai') {
                    $offer->requestRenovasi->update(['status_request' => 'selesai']);
                }

                if ($offer->mandor) {
                    $this->syncMandorStatus($offer->mandor);
                }
            });
        }
    }

    public function isOfferExpired(PenawaranRenovasi $offer): bool
    {
        return $offer->status_penawaran === 'pending'
            && $offer->created_at instanceof Carbon
            && $offer->created_at->lt(now()->subDays(self::OFFER_VALID_DAYS));
    }

    public function offerExpiresAt(PenawaranRenovasi $offer): ?Carbon
    {
        return $offer->created_at ? $offer->created_at->copy()->addDays(self::OFFER_VALID_DAYS) : null;
    }

    public function syncMandorStatus(Mandor $mandor): void
    {
        $hasAcceptedOffer = PenawaranRenovasi::where('mandor_id', $mandor->id)
            ->where('status_penawaran', 'diterima')
            ->whereHas('requestRenovasi', function ($query) {
                $query->where('status_request', '!=', 'selesai');
            })
            ->exists();

        $mandor->update([
            'status' => $hasAcceptedOffer ? 'nonaktif' : 'aktif',
        ]);
    }

    public function formatRupiah(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
