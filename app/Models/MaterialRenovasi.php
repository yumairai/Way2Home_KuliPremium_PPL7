<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRenovasi extends Model
{
    use HasFactory;

    protected $table = 'material_renovasi';

    protected $fillable = [
        'material_id',
        'penawaran_renovasi_id',
        'jumlah',
        'satuan',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function penawaran()
    {
        return $this->belongsTo(PenawaranRenovasi::class, 'penawaran_renovasi_id');
    }
}
