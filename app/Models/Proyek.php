<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    protected $table = 'proyek';
    protected $fillable = [
        'customer_id',
        'mandor_id',
        'jenis_proyek',
        'alamat_proyek',
        'tanggal_mulai',
        'status_proyek',
        'jumlah_cicilan'
    ];
}
