<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailProyekRenovasi extends Model
{
    use HasFactory;

    protected $table = 'detail_proyek_renovasi';

    protected $fillable = [
        'proyek_id',
        'request_renovasi_id',
        'penawaran_renovasi_id',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    public function requestRenovasi()
    {
        return $this->belongsTo(RequestRenovasi::class, 'request_renovasi_id');
    }

    public function penawaranRenovasi()
    {
        return $this->belongsTo(PenawaranRenovasi::class, 'penawaran_renovasi_id');
    }
}
