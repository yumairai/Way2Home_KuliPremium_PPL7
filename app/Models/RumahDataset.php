<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RumahDataset extends Model
{
    protected $table = 'rumah_dataset';

    protected $fillable = [
        'nama_rumah',
        'lokasi',
        'luas_tanah',
        'jumlah_kamar',
        'jumlah_lantai',
        'tahun_bangun',
        'harga',
        'material_digunakan',
    ];
}
