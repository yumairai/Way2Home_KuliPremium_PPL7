<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestRenovasi extends Model
{
    use HasFactory;

    protected $table = 'request_renovasi';

    protected $fillable = [
        'customer_id',
        'deskripsi_renovasi',
        'budget_estimasi',
        'path_foto_detail',
        'alamat',
        'status_request',
        'tanggal_request',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_request' => 'date',
        ];
    }

    public function getFotoDetailPaths(): array
    {
        $rawValue = $this->path_foto_detail;

        if (blank($rawValue)) {
            return [];
        }

        if (is_array($rawValue)) {
            return array_values(array_filter($rawValue));
        }

        $decodedValue = json_decode($rawValue, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedValue)) {
            return array_values(array_filter($decodedValue));
        }

        return [$rawValue];
    }

    public function getFotoDetailUrls(): array
    {
        return array_map(
            fn(string $path) => asset('storage/' . $path),
            $this->getFotoDetailPaths()
        );
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function penawaran()
    {
        return $this->hasMany(PenawaranRenovasi::class, 'request_renovasi_id');
    }

    public function negosiasi()
    {
        return $this->hasMany(NegosiasiRenovasi::class, 'request_renovasi_id');
    }
}
