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
