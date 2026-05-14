<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materials';

    protected $fillable = [
        'nama_material',
        'kategori',
        'harga',
        'deskripsi',
        'stok',
        'satuan',
        'path_foto_material'
    ];

    /**
     * Relasi many-to-many ke DesainRumah
     */
    public function desainRumah()
    {
        return $this->belongsToMany(DesainRumah::class, 'desain_material')
            ->withPivot('quantity', 'unit')
            ->withTimestamps();
    }
}