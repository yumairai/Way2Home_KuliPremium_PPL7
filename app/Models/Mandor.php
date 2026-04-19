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
                    ->where('status_proyek', 'Pengalokasian Mandor');
        // Ganti status sesuai flow kamu saat mandor sudah diassign
    }
}