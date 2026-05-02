<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mandor extends Model
{
    protected $table = 'mandors';

    protected $fillable = [
        'user_id',
        'sertifikasi',
        'path_foto_profil',
        'area_kerja',
        'path_foto_ktp',
        'lama_pengalaman',
        'status',
        'rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Proyek yang sedang aktif (mandor_id mengarah ke id mandor ini)
    public function proyekAktif()
    {
        return $this->hasOne(Proyek::class, 'mandor_id')
                    ->whereNotIn('status_proyek', ['Selesai', 'Dibatalkan']);
    }

    public function penawaranRenovasi()
    {
        return $this->hasMany(PenawaranRenovasi::class, 'mandor_id');
    }
}
