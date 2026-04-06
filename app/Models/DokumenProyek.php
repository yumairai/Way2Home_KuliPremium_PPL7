<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenProyek extends Model
{
    protected $table = 'dokumen_proyek';
    protected $fillable = [
        'detail_bangun_id',
        'jenis_dokumen',
        'file_path',
        'status_verifikasi',
        'catatan_admin'
    ];
}
