<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MandorActivityHistory extends Model
{
    use HasFactory;

    protected $table = 'mandor_activity_histories';

    protected $fillable = [
        'mandor_id',
        'activity_type',
        'reference_type',
        'reference_id',
        'description',
        'metadata'
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function mandor()
    {
        return $this->belongsTo(Mandor::class, 'mandor_id');
    }

    // Helper methods untuk membuat history
    public static function logAssignedProject(Mandor $mandor, Proyek $proyek)
    {
        return static::create([
            'mandor_id' => $mandor->id,
            'activity_type' => 'assigned_project',
            'reference_type' => 'proyek',
            'reference_id' => $proyek->id,
            'description' => "Diassign ke proyek pembangunan #" . ($proyek->id ?? 'Unknown'),
            'metadata' => [
                'project_name' => $proyek->id,
                'customer_name' => $proyek->customer?->user?->name,
                'location' => $proyek->alamat_proyek
            ]
        ]);
    }

    public static function logCompletedProject(Mandor $mandor, Proyek $proyek)
    {
        return static::create([
            'mandor_id' => $mandor->id,
            'activity_type' => 'completed_project',
            'reference_type' => 'proyek',
            'reference_id' => $proyek->id,
            'description' => "Menyelesaikan proyek pembangunan " . ($proyek->id ?? 'Unknown'),
            'metadata' => [
                'project_name' => $proyek->id,
                'customer_name' => $proyek->customer?->user?->name,
                'location' => $proyek->alamat_proyek
            ]
        ]);
    }

    public static function logNegotiationReceived(Mandor $mandor, RequestRenovasi $request, NegosiasiRenovasi $negotiation)
    {
        return static::create([
            'mandor_id' => $mandor->id,
            'activity_type' => 'negotiation_received',
            'reference_type' => 'request_renovasi',
            'reference_id' => $request->id,
            'description' => "Menerima negosiasi dari customer untuk renovasi REV-" . str_pad($request->id, 3, '0', STR_PAD_LEFT),
            'metadata' => [
                'request_id' => $request->id,
                'customer_name' => $request->customer?->user?->name,
                'negotiation_type' => $negotiation->tipe,
                'nominal' => $negotiation->nominal_tawaran,
                'message' => $negotiation->pesan
            ]
        ]);
    }

    public static function logOfferAccepted(Mandor $mandor, PenawaranRenovasi $offer)
    {
        $request = $offer->requestRenovasi;
        return static::create([
            'mandor_id' => $mandor->id,
            'activity_type' => 'offer_accepted',
            'reference_type' => 'request_renovasi',
            'reference_id' => $request->id,
            'description' => "Tawaran renovasi REV-" . str_pad($request->id, 3, '0', STR_PAD_LEFT) . " diterima customer",
            'metadata' => [
                'request_id' => $request->id,
                'customer_name' => $request->customer?->user?->name,
                'offer_cost' => $offer->estimasi_biaya
            ]
        ]);
    }

    public static function logOfferSubmitted(Mandor $mandor, RequestRenovasi $requestRenovasi, float $offerCost): void
    {
        static::create([
            'mandor_id' => $mandor->id,
            'activity_type' => 'offer_submitted',
            'reference_type' => 'request_renovasi',
            'reference_id' => $requestRenovasi->id,
            'description' => "Mengambil dan mereview renovasi REV-" . str_pad($requestRenovasi->id, 3, '0', STR_PAD_LEFT),
            'metadata' => [
                'request_id' => $requestRenovasi->id,
                'customer_name' => $requestRenovasi->customer?->user?->name,
                'offer_cost' => $offerCost
            ]
        ]);
    }

    public static function logRenovationCompleted(Mandor $mandor, RequestRenovasi $request)
    {
        return static::create([
            'mandor_id' => $mandor->id,
            'activity_type' => 'renovation_completed',
            'reference_type' => 'request_renovasi',
            'reference_id' => $request->id,
            'description' => "Menyelesaikan renovasi REV-" . str_pad($request->id, 3, '0', STR_PAD_LEFT),
            'metadata' => [
                'request_id' => $request->id,
                'customer_name' => $request->customer?->user?->name,
                'location' => $request->alamat
            ]
        ]);
    }
}
