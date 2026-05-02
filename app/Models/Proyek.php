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

    public function mandor()
    {
        return $this->belongsTo(Mandor::class, 'mandor_id');
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

    public function tasks()
    {
        return $this->hasMany(ProyekTask::class, 'proyek_id')->orderBy('urutan');
    }

    public function aktivitas()
    {
        return $this->hasMany(ProyekAktivitas::class, 'proyek_id')->latest();
    }

    public function dokumentasi()
    {
        return $this->hasMany(ProyekDokumentasi::class, 'proyek_id')->latest();
    }

    public function progress()
    {
        return $this->hasOne(ProgressProyek::class, 'proyek_id')->latest();
    }

}