<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

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
    public function getPathFotoMaterialAttribute($value): string
    {
        if (!$value) {
            return asset('images/aset/placeholder-material.png');
        }

        if (str_starts_with($value, 'http')) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }
}