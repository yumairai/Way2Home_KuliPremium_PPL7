<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    use HasFactory;

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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function detailBangun()
    {
        return $this->hasOne(DetailProyekBangun::class, 'proyek_id');
    }
    
    public function pembayaran()
    {
        return $this->hasMany(PembayaranProyek::class, 'proyek_id');
    }

    public function pembayaranDP()
    {
        return $this->hasOne(PembayaranProyek::class, 'proyek_id')
            ->where('tipe_pembayaran', 'DP')
            ->where('status_pembayaran', 'berhasil');
    }
}
