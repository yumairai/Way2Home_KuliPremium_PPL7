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
}