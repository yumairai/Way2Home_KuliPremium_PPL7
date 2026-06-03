<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NegosiasiRenovasi extends Model
{
    use HasFactory;

    protected $table = 'negosiasi_renovasi';

    protected $fillable = [
        'request_renovasi_id',
        'penawaran_renovasi_id',
        'pengirim',
        'tipe',
        'pesan',
        'nominal_tawaran',
    ];

    public function requestRenovasi()
    {
        return $this->belongsTo(RequestRenovasi::class, 'request_renovasi_id');
    }

    public function penawaranRenovasi()
    {
        return $this->belongsTo(PenawaranRenovasi::class, 'penawaran_renovasi_id');
    }
}
