<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProyekMilestone extends Model
{
    protected $table = 'proyek_milestone';
    protected $fillable = ['proyek_id', 'nama_task', 'milestone', 'is_selesai', 'urutan'];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }
}