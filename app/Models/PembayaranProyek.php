<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranProyek extends Model
{
    protected $table = 'pembayaran_proyek';

    protected $fillable = [
        'proyek_id',
        'order_id',
        'snap_token',
        'jumlah_bayar',
        'tipe_pembayaran',
        'status_pembayaran',
        'tanggal_pembayaran',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }
}
