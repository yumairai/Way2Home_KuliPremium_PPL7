<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenProyek extends Model
{
    use HasFactory;

    protected $table = 'dokumen_proyek';

    protected $fillable = [
        'detail_bangun_id', 
        'jenis_dokumen', 
        'file_path', 
        'status_verifikasi'
    ];

    public function detailBangun()
    {
        return $this->belongsTo(DetailProyekBangun::class, 'detail_bangun_id');
    }
}