<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProyekTask extends Model
{
    protected $table = 'proyek_task';
    protected $fillable = ['proyek_id', 'nama_task', 'is_selesai', 'urutan'];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }
}