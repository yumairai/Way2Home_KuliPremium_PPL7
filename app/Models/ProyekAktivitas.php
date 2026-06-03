<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProyekAktivitas extends Model
{
    protected $table = 'proyek_aktivitas';
    protected $fillable = ['proyek_id', 'judul', 'deskripsi'];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }
}