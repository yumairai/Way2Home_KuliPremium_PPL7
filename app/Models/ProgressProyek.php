<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProgressProyek extends Model
{
    protected $table = 'progress_proyek';
    protected $fillable = ['proyek_id', 'milestone_aktif', 'persentase', 'catatan', 'tanggal_update'];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }
}