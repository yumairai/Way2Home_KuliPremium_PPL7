<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesainRumah extends Model
{
    use HasFactory;

    protected $table = 'desain_rumah';

    protected $fillable = [
        'tipe_rumah',
        'deskripsi',
        'luas_tanah',
        'luas_bangunan',
        'jumlah_kamar_tidur',
        'jumlah_kamar_mandi',
        'estimasi_biaya',
        'estimasi_durasi',
        'material_utama',
        'path_gambar_desain',
        'fasilitas',
    ];

    /**
     * Relasi ke DetailProyekBangun
     */
    public function detailProyek()
    {
        return $this->hasMany(DetailProyekBangun::class, 'desain_rumah_id');
    }
}