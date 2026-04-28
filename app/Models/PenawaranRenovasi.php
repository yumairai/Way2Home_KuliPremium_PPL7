<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenawaranRenovasi extends Model
{
    use HasFactory;

    protected $table = 'penawaran_renovasi';

    protected $fillable = [
        'request_renovasi_id',
        'mandor_id',
        'analisis_dari_mandor',
        'estimasi_biaya',
        'estimasi_durasi',
        'status_penawaran',
    ];

    public function requestRenovasi()
    {
        return $this->belongsTo(RequestRenovasi::class, 'request_renovasi_id');
    }

    public function mandor()
    {
        return $this->belongsTo(Mandor::class, 'mandor_id');
    }

    public function materialRenovasi()
    {
        return $this->hasMany(MaterialRenovasi::class, 'penawaran_renovasi_id');
    }

    public function negosiasi()
    {
        return $this->hasMany(NegosiasiRenovasi::class, 'penawaran_renovasi_id');
    }
}
