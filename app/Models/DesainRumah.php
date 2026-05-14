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
        'lokasi',
        'gaya_arsitektur',
        'luas_tanah',
        'luas_bangunan',
        'jumlah_kamar_tidur',
        'jumlah_kamar_mandi',
        'jumlah_lantai',
        'tahun_bangun',
        'estimasi_biaya',
        'estimasi_durasi',
        'material_utama',
        'material_digunakan',
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

    /**
     * Relasi many-to-many ke Material
     */
    public function materials()
    {
        return $this->belongsToMany(Material::class, 'desain_material')
            ->withPivot('quantity', 'unit')
            ->withTimestamps();
    }
}