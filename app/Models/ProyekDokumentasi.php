<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProyekDokumentasi extends Model
{
    protected $table = 'proyek_dokumentasi';
    protected $fillable = ['proyek_id', 'path_foto', 'storage_path'];   

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }
}